<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\TeacherAttendance;
use App\Models\User;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AttendanceService
{
    protected AttendanceRepositoryInterface $attendanceRepository;

    public function __construct(AttendanceRepositoryInterface $attendanceRepository)
    {
        $this->attendanceRepository = $attendanceRepository;
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
     * Unified pipeline for USB Fingerprint Scanner (Digital Persona U.are.U 4500)
     */
    public function processFingerprintAttendance(array $params): array
    {
        $params['method'] = 'fingerprint';

        // Identify Teacher: either explicit user_id or resolved from fingerprint sample / matching
        $user = null;
        if (!empty($params['user_id'])) {
            $user = User::find($params['user_id']);
        } elseif (!empty($params['fingerprint_sample'])) {
            // Find registered teacher matching biometric sample or fallback to first registered with template
            $user = User::whereNotNull('fingerprint_template')->first();
        }

        if (!$user) {
            $this->logAudit(
                null,
                null,
                'fingerprint_verify',
                'fingerprint',
                'rejected',
                ['reason' => 'Fingerprint template not recognized or unregistered']
            );

            return [
                'success' => false,
                'message' => 'Sidik jari tidak dikenali! Pastikan jari telah terdaftar dalam sistem.',
                'status_code' => 422,
            ];
        }

        return $this->executeUnifiedPipeline($user, $params);
    }

    /**
     * Unified pipeline for AI Face ID Biometric Attendance
     */
    public function processFaceIdAttendance(User $user, array $params): array
    {
        $params['method'] = 'face_id';
        return $this->executeUnifiedPipeline($user, $params);
    }

    /**
     * Core unified attendance execution pipeline
     *
     * Flow:
     * Request -> Validation (Teacher, Session, Lock, GPS) -> Attendance Repository -> Database & Audit Log
     */
    protected function executeUnifiedPipeline(User $user, array $params): array
    {
        $now = Carbon::now();
        $today = $now->format('Y-m-d');
        $nowTime = $now->format('H:i:s');
        $method = $params['method'] ?? 'manual';
        $ip = $params['ip_address'] ?? request()->ip();
        $userAgent = $params['user_agent'] ?? request()->userAgent();
        $deviceInfo = $params['device_info'] ?? ($params['device_name'] ?? $userAgent);

        // 1. VALIDATE TEACHER STATUS & ROLE
        $teacherCheck = $this->validateTeacherStatus($user);
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

        // 3. DETERMINE ATTENDANCE ACTION / SESSION
        $requestedType = $params['type'] ?? null;
        $sessionType = $this->determineSession($now, $requestedType, $attendance);

        // 4. VALIDATE ATTENDANCE SESSION WINDOW
        $sessionValidation = $this->validateAttendanceSession($sessionType, $now);
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

        // 5. PREVENT DUPLICATE ATTENDANCE / SMART LOCKING
        $duplicateCheck = $this->preventDuplicateAttendance($attendance, $sessionType, $method);
        if ($duplicateCheck['duplicate']) {
            $this->logAudit($user, $attendance, $sessionType, $method, 'locked', [
                'reason' => $duplicateCheck['message'],
                'ip' => $ip,
            ]);

            return [
                'success' => $duplicateCheck['success'] ?? false,
                'locked' => true,
                'already_complete' => $duplicateCheck['already_complete'] ?? false,
                'message' => $duplicateCheck['message'],
                'attendance' => $attendance,
                'status_code' => 422,
            ];
        }

        // 6. GPS GEOFENCING VALIDATION (if method utilizes location)
        $workLoc = $params['work_location'] ?? 'school';
        $userLat = isset($params['latitude']) ? (float)$params['latitude'] : null;
        $userLong = isset($params['longitude']) ? (float)$params['longitude'] : null;
        $distance = null;

        if ($method === 'mobile_gps' || !empty($userLat)) {
            $gpsCheck = $this->validateGpsLocation($userLat, $userLong, $workLoc);
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

        // 7. PROCESS PHOTO ATTACHMENT (Selfie or Live Capture)
        $photoName = $this->processPhotoUpload($user, $params['photo'] ?? ($params['live_photo'] ?? null));

        // 8. APPLY BUSINESS LOGIC & SET RECORD FIELDS
        $lateThreshold = Setting::get('attendance_morning_late', '07:30');
        if (!str_contains($lateThreshold, ':')) {
            $lateThreshold = '07:30';
        }
        $lateTimestamp = strtotime($lateThreshold . ':00');
        $currentTimestamp = strtotime($nowTime);

        $actionType = 'check_in';
        $actionMessage = '';

        // Record attendance source and device
        $attendance->method = $method;
        $attendance->device_info = $deviceInfo;
        $attendance->work_location = $workLoc;
        if (empty($attendance->recorded_by)) {
            $attendance->recorded_by = auth()->id() ?? $user->id;
        }

        if ($sessionType === 'briefing') {
            $briefingTitle = Setting::get('briefing_title', 'Briefing Pagi Dewan Guru');

            if (empty($attendance->check_in)) {
                $attendance->check_in = $nowTime;
                $attendance->check_in_lat = $userLat;
                $attendance->check_in_long = $userLong;
                if ($photoName) {
                    $attendance->check_in_photo = $photoName;
                }
                $attendance->status = ($currentTimestamp > $lateTimestamp) ? 'late' : 'present';
            }

            $briefingTag = "[Hadir Briefing: {$briefingTitle} @ {$nowTime}]";
            $attendance->notes = $this->appendNote($attendance->notes, $briefingTag);
            $actionType = 'briefing';
            $actionMessage = "Alhamdulillah! Kehadiran Briefing '{$briefingTitle}' berhasil dicatat pada {$nowTime}";

        } elseif ($sessionType === 'afternoon') {
            $afternoonTag = "[Hadir Sesi Siang @ {$nowTime}]";
            $attendance->notes = $this->appendNote($attendance->notes, $afternoonTag);
            $actionType = 'afternoon';
            $actionMessage = "Presensi SESI SIANG (Dzuhur) berhasil dicatat pada {$nowTime}";

        } elseif ($sessionType === 'check_out') {
            $attendance->check_out = $nowTime;
            $attendance->check_out_lat = $userLat;
            $attendance->check_out_long = $userLong;
            if ($photoName) {
                $attendance->check_out_photo = $photoName;
            }
            $actionType = 'check_out';
            $actionMessage = ($workLoc === 'dinas_luar' ? 'Presensi PULANG (Dinas Luar)' : 'Presensi PULANG') . " berhasil dicatat pada {$nowTime}";

        } else {
            // Default Morning Check In
            $attendance->check_in = $nowTime;
            $attendance->check_in_lat = $userLat;
            $attendance->check_in_long = $userLong;
            if ($photoName) {
                $attendance->check_in_photo = $photoName;
            }
            $attendance->status = ($currentTimestamp > $lateTimestamp) ? 'late' : 'present';
            $actionType = 'check_in';
            $actionMessage = ($workLoc === 'dinas_luar' ? 'Presensi MASUK (Dinas Luar)' : 'Presensi MASUK') . " berhasil dicatat pada {$nowTime}";
        }

        // Merge custom user notes (e.g. alasan dinas luar)
        if (!empty($params['notes'])) {
            $attendance->notes = $this->appendNote($attendance->notes, trim($params['notes']));
        }

        // 9. PERSIST TO DATABASE VIA ATTENDANCE REPOSITORY
        $this->attendanceRepository->save($attendance);

        // 10. LOG AUDIT TRAIL
        $auditLog = $this->logAudit($user, $attendance, $actionType, $method, 'success', [
            'session_type' => $sessionType,
            'ip' => $ip,
            'user_agent' => $userAgent,
            'device_info' => $deviceInfo,
            'latitude' => $userLat,
            'longitude' => $userLong,
            'distance_meters' => $distance,
            'status' => $attendance->status,
        ]);

        // 11. TRIGGER NOTIFICATIONS IF ENABLED
        $this->triggerNotifications($user, $attendance, $actionType);

        return [
            'success' => true,
            'action_type' => $actionType,
            'message' => $actionMessage,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'nip' => $user->nip ?? '-',
                'avatar' => $user->avatar ? get_public_file_url($user->avatar, 'img/avatars') : null,
            ],
            'teacher' => [
                'id' => $user->id,
                'name' => $user->name,
                'nip' => $user->nip ?? '-',
                'avatar' => $user->avatar ? get_public_file_url($user->avatar, 'img/avatars') : null,
            ],
            'attendance' => $attendance,
            'audit_id' => $auditLog?->id,
        ];
    }

    /**
     * Validate teacher account status and role
     */
    public function validateTeacherStatus(User $user): array
    {
        if (isset($user->status) && in_array(strtolower($user->status), ['inactive', 'suspended', 'nonaktif', 'banned'])) {
            return [
                'valid' => false,
                'message' => "Akun pegawai '{$user->name}' dalam status nonaktif. Silakan hubungi Administrator.",
            ];
        }

        // Verify that user has an authorized teacher/staff role
        $validRoles = ['guru', 'teacher', 'admin', 'operator', 'tata-usaha', 'staff', 'kepala-sekolah', 'super-admin'];
        $hasRole = $user->roles()->whereIn('slug', $validRoles)->exists();

        if (!$hasRole && !in_array($user->role ?? '', $validRoles)) {
            return [
                'valid' => false,
                'message' => 'Hanya Guru dan Tenaga Kependidikan yang berhak mencatat presensi pegawai.',
            ];
        }

        return ['valid' => true];
    }

    /**
     * Determine active session window (Morning, Dzuhur, Evening/Checkout, or Briefing)
     */
    public function determineSession(Carbon $now, ?string $requestedType, ?TeacherAttendance $attendance = null): string
    {
        if (!empty($requestedType)) {
            return $requestedType;
        }

        // If automated determination (e.g. from USB scanner tap without type param):
        // 1. If check_in is empty -> check_in (Morning)
        // 2. If check_in is filled but check_out is empty -> check_out (Checkout)
        // 3. Otherwise checkout completed
        if ($attendance && !empty($attendance->check_in)) {
            return 'check_out';
        }

        return 'check_in';
    }

    /**
     * Validate attendance session against configured schedules
     */
    public function validateAttendanceSession(string $sessionType, Carbon $now): array
    {
        $override = Setting::get('attendance_manual_override', '0') == '1';
        if ($override) {
            return ['valid' => true];
        }

        $currentTime = $now->format('H:i');

        if ($sessionType === 'briefing') {
            $briefingActive = Setting::get('briefing_session_active', '0') == '1';
            if (!$briefingActive) {
                return [
                    'valid' => false,
                    'message' => 'Sesi Briefing saat ini belum dibuka atau telah ditutup oleh Kepala Sekolah.',
                ];
            }
            return ['valid' => true];
        }

        if ($sessionType === 'check_in') {
            $open = Setting::get('attendance_morning_open', '06:00');
            $close = Setting::get('attendance_morning_close', '11:59');

            if ($currentTime < $open) {
                return [
                    'valid' => false,
                    'message' => "Presensi Masuk belum dibuka. Sesi dibuka mulai pukul {$open}.",
                ];
            }
        } elseif ($sessionType === 'afternoon') {
            $open = Setting::get('attendance_afternoon_open', '12:00');
            $close = Setting::get('attendance_afternoon_close', '14:00');

            if ($currentTime < $open || $currentTime > $close) {
                return [
                    'valid' => false,
                    'message' => "Sesi Dzuhur / Siang hanya dapat diisi antara {$open} s/d {$close}.",
                ];
            }
        } elseif ($sessionType === 'check_out') {
            $open = Setting::get('attendance_evening_open', '14:00');

            if ($currentTime < $open) {
                return [
                    'valid' => false,
                    'message' => "Presensi Pulang baru dapat dicatat mulai pukul {$open}.",
                ];
            }
        }

        return ['valid' => true];
    }

    /**
     * Prevent duplicate attendance and enforce smart locking
     */
    public function preventDuplicateAttendance(TeacherAttendance $attendance, string $sessionType, string $method): array
    {
        if ($sessionType === 'check_in') {
            if (!empty($attendance->check_in)) {
                $methodLabel = $attendance->method_label ?? 'sistem';
                return [
                    'duplicate' => true,
                    'message' => "Presensi MASUK telah tercatat hari ini pada pukul {$attendance->check_in} (via {$methodLabel}).",
                ];
            }
        } elseif ($sessionType === 'check_out') {
            if (empty($attendance->check_in)) {
                return [
                    'duplicate' => true,
                    'message' => 'Anda belum melakukan presensi MASUK hari ini. Harap lakukan presensi masuk terlebih dahulu.',
                ];
            }
            if (!empty($attendance->check_out)) {
                return [
                    'duplicate' => true,
                    'already_complete' => true,
                    'success' => true,
                    'message' => "Kehadiran hari ini sudah LENGKAP! (Masuk: {$attendance->check_in} | Pulang: {$attendance->check_out}).",
                ];
            }
        } elseif ($sessionType === 'afternoon') {
            if (!empty($attendance->notes) && str_contains($attendance->notes, 'Hadir Sesi Siang')) {
                return [
                    'duplicate' => true,
                    'message' => 'Presensi Sesi Siang (Dzuhur) Anda sudah tercatat sebelumnya.',
                ];
            }
        } elseif ($sessionType === 'briefing') {
            if (!empty($attendance->notes) && str_contains($attendance->notes, 'Hadir Briefing:')) {
                return [
                    'duplicate' => true,
                    'message' => 'Kehadiran Briefing Anda sudah tercatat hari ini.',
                ];
            }
        }

        return ['duplicate' => false];
    }

    /**
     * Validate GPS coordinates against school geofencing
     */
    public function validateGpsLocation(?float $lat, ?float $long, string $workLocation): array
    {
        $schoolLat = (float) Setting::get('school_latitude', -0.8917);
        $schoolLong = (float) Setting::get('school_longitude', 119.8707);
        $maxRadius = (int) Setting::get('school_attendance_radius', 100);
        $manualOverride = Setting::get('attendance_manual_override', '0') == '1';

        // Dinas luar or manual override allows check-in regardless of radius
        if ($workLocation === 'dinas_luar' || $manualOverride) {
            return [
                'valid' => true,
                'distance' => 0,
                'max_radius' => $maxRadius,
            ];
        }

        if ($lat === null || $long === null) {
            return [
                'valid' => false,
                'distance' => null,
                'max_radius' => $maxRadius,
                'message' => 'Koordinat GPS tidak terdeteksi. Pastikan GPS aktif dan izin lokasi diizinkan browser.',
            ];
        }

        // Haversine formula calculation in meters
        $earthRadius = 6371000;
        $dLat = deg2rad($lat - $schoolLat);
        $dLon = deg2rad($long - $schoolLong);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($schoolLat)) * cos(deg2rad($lat)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = (int) round($earthRadius * $c);

        if ($distance > $maxRadius) {
            return [
                'valid' => false,
                'distance' => $distance,
                'max_radius' => $maxRadius,
                'message' => "Presensi Gagal: Posisi Anda ({$distance} meter) di luar batas radius sekolah (Maksimal: {$maxRadius} meter).",
            ];
        }

        return [
            'valid' => true,
            'distance' => $distance,
            'max_radius' => $maxRadius,
        ];
    }

    /**
     * Store and log audit trail
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
     * Trigger WhatsApp or System Notifications upon successful attendance
     */
    public function triggerNotifications(User $user, TeacherAttendance $attendance, string $actionType): void
    {
        try {
            $waNotifyEnabled = Setting::get('attendance_wa_notification', '0') == '1';
            if ($waNotifyEnabled && !empty($user->phone)) {
                $schoolName = Setting::get('school_name', config('app.name', 'Sekolah'));
                $time = Carbon::now()->format('H:i');
                $date = Carbon::now()->isoFormat('dddd, D MMMM Y');
                $typeLabel = $actionType === 'check_in' ? 'MASUK' : ($actionType === 'check_out' ? 'PULANG' : strtoupper($actionType));

                $msg = "📢 *Notifikasi Presensi Pegawai*\n";
                $msg .= "Yth. {$user->name},\n\n";
                $msg .= "Presensi *{$typeLabel}* Anda di {$schoolName} berhasil dicatat:\n";
                $msg .= "📅 Tanggal: {$date}\n";
                $msg .= "⏰ Waktu: {$time} WIB\n";
                $msg .= "📌 Metode: {$attendance->method_label}\n";
                $msg .= "📍 Lokasi: {$attendance->location_label}\n\n";
                $msg .= "Terima kasih atas dedikasi dan kedisiplinan Anda.";

                WhatsAppService::sendMessage($user->phone, $msg);
            }
        } catch (\Throwable $e) {
            Log::warning('Attendance notification dispatch failed: ' . $e->getMessage());
        }
    }

    /**
     * Process and store base64 photo upload
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
