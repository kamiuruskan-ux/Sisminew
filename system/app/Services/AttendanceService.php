<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\TeacherAttendance;
use App\Models\User;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class AttendanceService
{
    protected AttendanceRepositoryInterface $attendanceRepository;
    protected FingerprintService $fingerprintService;
    protected FingerprintDeviceService $deviceService;
    protected AttendanceSessionService $sessionService;
    protected AttendanceVerificationService $verificationService;

    public function __construct(
        AttendanceRepositoryInterface $attendanceRepository,
        FingerprintService $fingerprintService,
        FingerprintDeviceService $deviceService,
        AttendanceSessionService $sessionService,
        AttendanceVerificationService $verificationService
    ) {
        $this->attendanceRepository = $attendanceRepository;
        $this->fingerprintService = $fingerprintService;
        $this->deviceService = $deviceService;
        $this->sessionService = $sessionService;
        $this->verificationService = $verificationService;
    }

    /**
     * Unified pipeline for GPS / Mobile Attendance
     */
    public function processGpsAttendance(User $user, array $params): array
    {
        $params['method'] = 'mobile_gps';
        return $this->executeUnifiedPipeline($user, $params);
    }

    /**
     * Unified pipeline for USB Fingerprint Scanner (HID DigitalPersona 4500)
     */
    public function processFingerprintAttendance(array $params): array
    {
        $params['method'] = 'fingerprint';
        $sample = $params['fingerprint_sample'] ?? null;
        $targetUserId = !empty($params['user_id']) ? (int) $params['user_id'] : null;

        // 1. Identify teacher through Biometric Fingerprint Engine
        $matchResult = $this->fingerprintService->identifyTeacher($sample, $targetUserId);

        if (!$matchResult['success'] || !$matchResult['user']) {
            $this->deviceService->logDeviceEvent('capture_failed', [
                'reason' => $matchResult['message'],
                'user_id' => $targetUserId,
                'device_name' => $params['device_name'] ?? 'HID DigitalPersona 4500',
            ]);

            return [
                'success' => false,
                'message' => $matchResult['message'],
                'confidence' => $matchResult['confidence'] ?? 0,
                'status_code' => 422,
            ];
        }

        $user = $matchResult['user'];
        $params['confidence'] = $matchResult['confidence'];
        $params['verification_status'] = 'biometric_verified';

        return $this->executeUnifiedPipeline($user, $params);
    }

    /**
     * Production Fingerprint Enrollment with 3-Scan Matching Verification
     */
    public function processFingerprintEnrollment(int $userId, array $samples): array
    {
        $user = User::find($userId);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Guru / Pegawai tidak ditemukan dalam sistem.',
                'status_code' => 404,
            ];
        }

        $enrollmentCheck = $this->fingerprintService->verifyEnrollmentScans($samples);
        if (!$enrollmentCheck['valid']) {
            $this->deviceService->logDeviceEvent('enrollment_failed', [
                'user_id' => $user->id,
                'reason' => $enrollmentCheck['message'],
            ]);

            return [
                'success' => false,
                'message' => $enrollmentCheck['message'],
                'status_code' => 422,
            ];
        }

        $user->fingerprint_template = $enrollmentCheck['template'];
        $user->fingerprint_registered_at = now();
        $user->save();

        $this->deviceService->logDeviceEvent('enrollment_completed', [
            'user_id' => $user->id,
            'similarity' => $enrollmentCheck['similarity'] ?? 100,
        ]);

        return [
            'success' => true,
            'message' => "Sidik jari untuk {$user->name} berhasil direkam ke dalam sistem biometrik!",
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'registered_at' => $user->fingerprint_registered_at->format('d/m/Y H:i'),
            ],
        ];
    }

    /**
     * Unified pipeline for AI Face ID Biometric Attendance
     */
    public function processFaceIdAttendance(User $user, array $params): array
    {
        $params['method'] = 'face_id';
        $params['verification_status'] = 'face_verified';
        return $this->executeUnifiedPipeline($user, $params);
    }

    /**
     * Log device telemetry event directly
     */
    public function logHardwareEvent(string $event, array $context = []): void
    {
        $this->deviceService->logDeviceEvent($event, $context);
    }

    /**
     * Core unified attendance execution pipeline
     *
     * Flow:
     * Request -> Validation (Teacher, Session, Lock, GPS) -> Attendance Repository -> Database & Audit Log -> Live Broadcast
     */
    protected function executeUnifiedPipeline(User $user, array $params): array
    {
        $now = Carbon::now();
        $today = $now->format('Y-m-d');
        $nowTime = $now->format('H:i:s');
        $method = $params['method'] ?? 'manual';
        $ip = $params['ip_address'] ?? request()->ip();
        $userAgent = $params['user_agent'] ?? request()->userAgent();
        $deviceName = $params['device_name'] ?? ($params['device_info'] ?? $userAgent);

        // 1. VALIDATE TEACHER STATUS & ROLE
        $teacherCheck = $this->verificationService->validateTeacherAccount($user);
        if (!$teacherCheck['valid']) {
            $this->logAudit($user, null, 'validate_teacher', $method, 'rejected', [
                'reason' => $teacherCheck['message'],
                'ip' => $ip,
            ]);

            return [
                'success' => false,
                'message' => $teacherCheck['message'],
                'status_code' => 403,
            ];
        }

        // 2. RETRIEVE OR INSTANTIATE ATTENDANCE VIA REPOSITORY
        $attendance = $this->attendanceRepository->findOrNew($user->id, $today);

        // 3. DETERMINE ATTENDANCE SESSION DYNAMICALLY
        $requestedType = $params['type'] ?? null;
        $sessionInfo = $this->sessionService->resolveActiveSession($now, $requestedType, $attendance);
        $sessionType = $sessionInfo['type'];
        $sessionName = $sessionInfo['name'];

        // 4. VALIDATE ATTENDANCE SESSION WINDOW
        $sessionValidation = $this->sessionService->validateSessionWindow($sessionType, $now);
        if (!$sessionValidation['valid']) {
            $this->logAudit($user, $attendance, $sessionType, $method, 'rejected', [
                'reason' => $sessionValidation['message'],
                'ip' => $ip,
            ]);

            return [
                'success' => false,
                'message' => $sessionValidation['message'],
                'status_code' => 422,
            ];
        }

        // 5. PREVENT DUPLICATE ATTENDANCE & APPLY SMART LOCKING
        $duplicateCheck = $this->verificationService->checkDuplicateAndLock($attendance, $sessionType);
        if ($duplicateCheck['locked']) {
            $this->logAudit($user, $attendance, $sessionType, $method, 'locked', [
                'reason' => $duplicateCheck['message'],
                'ip' => $ip,
            ]);

            return [
                'success' => $duplicateCheck['already_complete'] ?? false,
                'locked' => true,
                'already_complete' => $duplicateCheck['already_complete'] ?? false,
                'message' => $duplicateCheck['message'],
                'attendance' => $attendance,
                'status_code' => 422,
            ];
        }

        // 6. GPS GEOFENCING VALIDATION
        $workLoc = $params['work_location'] ?? 'school';
        $userLat = isset($params['latitude']) ? (float)$params['latitude'] : null;
        $userLong = isset($params['longitude']) ? (float)$params['longitude'] : null;
        $distance = null;

        if ($method === 'mobile_gps' || !empty($userLat)) {
            $gpsCheck = $this->verificationService->validateGpsGeofence($userLat, $userLong, $workLoc);
            $distance = $gpsCheck['distance'];

            if (!$gpsCheck['valid']) {
                $this->logAudit($user, $attendance, $sessionType, $method, 'out_of_radius', [
                    'distance' => $distance,
                    'max_radius' => $gpsCheck['max_radius'],
                    'latitude' => $userLat,
                    'longitude' => $userLong,
                ]);

                return [
                    'success' => false,
                    'message' => $gpsCheck['message'],
                    'distance' => $distance,
                    'max_radius' => $gpsCheck['max_radius'],
                    'out_of_radius' => true,
                    'status_code' => 422,
                ];
            }
        }

        // 7. PROCESS PHOTO ATTACHMENT
        $photoName = $this->processPhotoUpload($user, $params['photo'] ?? ($params['live_photo'] ?? null));

        // 8. APPLY BUSINESS LOGIC & SET ATTENDANCE FIELDS
        $attendance->method = $method;
        $attendance->device_info = $deviceName;
        $attendance->work_location = $workLoc;
        $attendance->session_name = $sessionName;
        $attendance->verification_status = $params['verification_status'] ?? 'verified';

        if (empty($attendance->recorded_by)) {
            $attendance->recorded_by = auth()->id() ?? $user->id;
        }

        $actionMessage = '';

        if ($sessionType === 'briefing') {
            $briefingTitle = Setting::get('briefing_title', 'Briefing Pagi Dewan Guru');

            if (empty($attendance->check_in)) {
                $attendance->check_in = $nowTime;
                $attendance->check_in_lat = $userLat;
                $attendance->check_in_long = $userLong;
                if ($photoName) {
                    $attendance->check_in_photo = $photoName;
                }
                $attendance->status = $this->sessionService->calculateLateStatus($now);
            }

            $briefingTag = "[Hadir Briefing: {$briefingTitle} @ {$nowTime}]";
            $attendance->notes = $this->appendNote($attendance->notes, $briefingTag);
            $actionMessage = "Kehadiran Briefing '{$briefingTitle}' berhasil dicatat ({$nowTime})";

        } elseif ($sessionType === 'midday') {
            $attendance->midday_at = $nowTime;
            $middayTag = "[Hadir Sesi Siang @ {$nowTime}]";
            $attendance->notes = $this->appendNote($attendance->notes, $middayTag);
            $actionMessage = "Presensi Sesi Siang (Dzuhur) berhasil dicatat ({$nowTime})";

        } elseif ($sessionType === 'check_out') {
            $attendance->check_out = $nowTime;
            $attendance->check_out_lat = $userLat;
            $attendance->check_out_long = $userLong;
            if ($photoName) {
                $attendance->check_out_photo = $photoName;
            }
            $actionMessage = ($workLoc === 'dinas_luar' ? 'Presensi PULANG (Dinas Luar)' : 'Presensi PULANG') . " berhasil dicatat ({$nowTime})";

        } else {
            // Default Morning Check In
            $attendance->check_in = $nowTime;
            $attendance->check_in_lat = $userLat;
            $attendance->check_in_long = $userLong;
            if ($photoName) {
                $attendance->check_in_photo = $photoName;
            }
            $attendance->status = $this->sessionService->calculateLateStatus($now);
            $actionMessage = ($workLoc === 'dinas_luar' ? 'Presensi MASUK (Dinas Luar)' : 'Presensi MASUK') . " berhasil dicatat ({$nowTime})";
        }

        // Merge custom user notes
        if (!empty($params['notes'])) {
            $attendance->notes = $this->appendNote($attendance->notes, trim($params['notes']));
        }

        // 9. PERSIST TO DATABASE
        $this->attendanceRepository->save($attendance);

        // 10. LOG AUDIT TRAIL
        $auditLog = $this->logAudit($user, $attendance, $sessionType, $method, 'success', [
            'session_name' => $sessionName,
            'ip' => $ip,
            'user_agent' => $userAgent,
            'device_info' => $deviceName,
            'latitude' => $userLat,
            'longitude' => $userLong,
            'distance_meters' => $distance,
            'confidence' => $params['confidence'] ?? null,
            'status' => $attendance->status,
        ]);

        // 11. BROADCAST LIVE ACTIVITY FOR INSTANT REAL-TIME DASHBOARD (SSE Cache Pool)
        $this->broadcastLiveEvent([
            'id' => $attendance->id,
            'teacher_id' => $user->id,
            'teacher_name' => $user->name,
            'nip' => $user->nip ?? '-',
            'avatar' => $user->avatar ? get_public_file_url($user->avatar, 'img/avatars') : null,
            'time' => $nowTime,
            'session' => $sessionName,
            'session_type' => $sessionType,
            'method' => $method,
            'method_label' => $attendance->method_label,
            'status' => $attendance->status,
            'status_label' => $attendance->status_label,
            'timestamp' => $now->timestamp,
        ]);

        // 12. TRIGGER NOTIFICATIONS IF ENABLED
        $this->triggerNotifications($user, $attendance, $sessionType);

        return [
            'success' => true,
            'action_type' => $sessionType,
            'session_name' => $sessionName,
            'message' => "{$actionMessage} untuk {$user->name}",
            'teacher' => [
                'id' => $user->id,
                'name' => $user->name,
                'nip' => $user->nip ?? '-',
                'avatar' => $user->avatar ? get_public_file_url($user->avatar, 'img/avatars') : null,
            ],
            'attendance' => [
                'id' => $attendance->id,
                'check_in' => $attendance->check_in,
                'midday_at' => $attendance->midday_at,
                'check_out' => $attendance->check_out,
                'status' => $attendance->status,
                'status_label' => $attendance->status_label,
                'method' => $attendance->method,
                'method_label' => $attendance->method_label,
                'session_name' => $attendance->session_name,
            ],
            'audit_id' => $auditLog?->id,
        ];
    }

    /**
     * Broadcast live attendance event to SSE subscribers cache pool
     */
    protected function broadcastLiveEvent(array $eventData): void
    {
        try {
            $events = Cache::get('live_attendance_events', []);
            array_unshift($events, $eventData);
            $events = array_slice($events, 0, 50); // Keep last 50 events
            Cache::put('live_attendance_events', $events, 86400); // 1 day TTL
            Cache::put('last_attendance_event_at', microtime(true), 86400);
        } catch (\Throwable $e) {
            Log::warning('SSE broadcast event cache failed: ' . $e->getMessage());
        }
    }

    /**
     * Trigger WhatsApp notification
     */
    public function triggerNotifications(User $user, TeacherAttendance $attendance, string $sessionType): void
    {
        try {
            $waNotifyEnabled = Setting::get('attendance_wa_notification', '0') == '1';
            if ($waNotifyEnabled && !empty($user->phone)) {
                $schoolName = Setting::get('school_name', config('app.name', 'Sekolah'));
                $time = Carbon::now()->format('H:i');
                $date = Carbon::now()->isoFormat('dddd, D MMMM Y');
                $sessionLabel = $attendance->session_name ?? strtoupper($sessionType);

                $msg = "📢 *Notifikasi Presensi Pegawai*\n";
                $msg .= "Yth. {$user->name},\n\n";
                $msg .= "Presensi *{$sessionLabel}* Anda di {$schoolName} berhasil dicatat:\n";
                $msg .= "📅 Tanggal: {$date}\n";
                $msg .= "⏰ Waktu: {$time} WIB\n";
                $msg .= "📌 Metode: {$attendance->method_label}\n";
                $msg .= "📍 Lokasi: {$attendance->location_label}\n\n";
                $msg .= "Terima kasih atas kedisiplinan Anda.";

                WhatsAppService::sendMessage($user->phone, $msg);
            }
        } catch (\Throwable $e) {
            Log::warning('Attendance notification dispatch failed: ' . $e->getMessage());
        }
    }

    /**
     * Log audit trail
     */
    public function logAudit(?User $user, ?TeacherAttendance $attendance, string $action, string $method, string $status, array $meta = [])
    {
        return $this->attendanceRepository->recordAuditLog([
            'user_id' => $user?->id,
            'teacher_attendance_id' => $attendance?->id,
            'action' => $action,
            'method' => $method,
            'status' => $status,
            'session_type' => $meta['session_type'] ?? null,
            'ip_address' => $meta['ip'] ?? request()->ip(),
            'user_agent' => $meta['user_agent'] ?? request()->userAgent(),
            'device_info' => $meta['device_info'] ?? null,
            'latitude' => $meta['latitude'] ?? null,
            'longitude' => $meta['longitude'] ?? null,
            'distance_meters' => $meta['distance_meters'] ?? null,
            'details' => $meta,
        ]);
    }

    /**
     * Helper to process selfie photo upload
     */
    protected function processPhotoUpload(User $user, ?string $imageData): ?string
    {
        if (empty($imageData)) {
            return null;
        }

        try {
            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
                $ext = strtolower($type[1]);
                $binary = base64_decode($imageData);

                if ($binary !== false) {
                    $photoName = 'selfie_' . $user->id . '_' . time() . '.' . $ext;
                    $uploadPath = public_path('img/teacher_attendances');
                    if (!File::exists($uploadPath)) {
                        File::makeDirectory($uploadPath, 0755, true, true);
                    }
                    File::put($uploadPath . '/' . $photoName, $binary);
                    return $photoName;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Failed saving attendance photo: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Helper to cleanly append tagged notes
     */
    protected function appendNote(?string $existing, string $newTag): string
    {
        if (empty($existing)) {
            return $newTag;
        }
        if (!str_contains($existing, $newTag)) {
            return trim($existing) . ' | ' . $newTag;
        }
        return $existing;
    }
}
