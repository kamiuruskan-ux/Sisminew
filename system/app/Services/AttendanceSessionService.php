<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\TeacherAttendance;
use Carbon\Carbon;

class AttendanceSessionService
{
    /**
     * Resolve currently active attendance session based on server time, schedule configuration, and teacher record
     *
     * Returns:
     * - type: 'check_in' | 'midday' | 'check_out' | 'outside_window' | 'briefing'
     * - name: Human-friendly session name
     * - action_label: Button label
     * - is_active: boolean
     * - is_already_done: boolean
     * - status_preview: 'present' | 'late' | 'very_late' | 'outside_window'
     * - delay_minutes: int
     * - next_window: string
     */
    public function resolveActiveSession(Carbon $now, ?string $requestedType = null, ?TeacherAttendance $attendance = null): array
    {
        $currentTime = $now->format('H:i');

        // 1. Load All Schedule Configurations from Settings
        $schedule = $this->getScheduleSettings();

        // 2. Check if today is a configured holiday or non-working day
        $holidayCheck = $this->checkHoliday($now);

        // 3. Manual override or Briefing check
        $override = Setting::get('attendance_manual_override', '0') == '1';
        $briefingActive = Setting::get('briefing_session_active', '0') == '1';
        $briefingOpenedDate = Setting::get('briefing_opened_date', null);
        $inMorningWindow = ($currentTime >= ($schedule['morning_open'] ?? '06:00') && $currentTime <= ($schedule['morning_close'] ?? '11:59'));

        // Briefing has priority ONLY if opened today and within morning hours
        if ($briefingActive && $briefingOpenedDate === $now->format('Y-m-d') && $inMorningWindow && (!$attendance || empty($attendance->notes) || !str_contains($attendance->notes, 'Hadir Briefing:'))) {
            return [
                'type' => 'briefing',
                'name' => Setting::get('briefing_title', 'Briefing Pagi Dewan Guru'),
                'action_label' => 'Konfirmasi Hadir Briefing',
                'is_active' => true,
                'is_already_done' => false,
                'status_preview' => 'present',
                'delay_minutes' => 0,
                'message' => 'Sesi briefing resmi dibuka oleh Kepala Sekolah.',
                'holiday' => $holidayCheck,
            ];
        }

        // 4. If explicit requested type is provided (e.g. from API/form)
        if (!empty($requestedType) && $requestedType !== 'auto') {
            return $this->buildSessionPayload($requestedType, $now, $schedule, $attendance, $holidayCheck);
        }

        // 5. Automatic Session Determination based on server time and today's attendance state
        // Continuous, gap-free time windows:
        // A. Pagi (Masuk): 05:00 s/d 11:30
        // B. Siang (Dzuhur): 11:30 s/d jam pulang (misal 15:00 / afternoon_open)
        // C. Pulang: jam pulang s/d malam (23:59)

        $morningOpen = $schedule['morning_open'] ?? '06:00';
        $morningClose = $schedule['morning_close'] ?? '11:30';
        $dzuhurOpen = min($schedule['dzuhur_open'] ?? '11:30', '11:30');
        $afternoonOpen = $schedule['afternoon_open'] ?? '15:00';
        $afternoonClose = $schedule['afternoon_close'] ?? '20:00';

        // Check if teacher already has check_in / check_out today
        $hasCheckedIn = $attendance && !empty($attendance->check_in);
        $hasDzuhur = $attendance && (!empty($attendance->midday_at) || (!empty($attendance->notes) && str_contains($attendance->notes, 'Hadir Sesi Siang')));
        $hasCheckedOut = $attendance && !empty($attendance->check_out);

        // A. MORNING SESSION WINDOW (Masuk)
        // Active if time is before dzuhur OR (teacher hasn't checked in yet and before 12:00)
        if ($currentTime < $dzuhurOpen || (!$hasCheckedIn && $currentTime < '12:00')) {
            $statusData = $this->calculateLateDetails($now, $schedule);

            return [
                'type' => 'check_in',
                'name' => 'Sesi Pagi (Masuk)',
                'action_label' => $hasCheckedIn ? 'Sudah Check-In Masuk' : 'Presensi Masuk (Check-In)',
                'is_active' => !$hasCheckedIn || $override,
                'is_already_done' => $hasCheckedIn,
                'status_preview' => $statusData['status'],
                'delay_minutes' => $statusData['delay_minutes'],
                'time_range' => "{$morningOpen} - {$morningClose} WITA",
                'late_threshold' => $schedule['morning_late'],
                'message' => $hasCheckedIn
                    ? "Presensi Masuk telah tercatat pada {$attendance->check_in}."
                    : ($statusData['delay_minutes'] > 0
                        ? "Terlambat {$statusData['delay_minutes']} menit dari batas {$schedule['morning_late']}."
                        : "Waktu masuk tepat waktu (Batas: {$schedule['morning_late']})."),
                'holiday' => $holidayCheck,
            ];
        }

        // B. DZUHUR / SIANG SESSION WINDOW
        // Active from dzuhur open (11:30) up to afternoon open (jam pulang)
        if ($currentTime >= $dzuhurOpen && $currentTime < $afternoonOpen) {
            return [
                'type' => 'midday',
                'name' => 'Sesi Siang (Dzuhur)',
                'action_label' => $hasDzuhur ? 'Sudah Hadir Sesi Dzuhur' : 'Konfirmasi Presensi Dzuhur',
                'is_active' => !$hasDzuhur || $override,
                'is_already_done' => $hasDzuhur,
                'status_preview' => 'present',
                'delay_minutes' => 0,
                'time_range' => "{$dzuhurOpen} - {$afternoonOpen} WITA",
                'message' => $hasDzuhur
                    ? "Presensi Sesi Siang (Dzuhur) telah terverifikasi hari ini."
                    : "Waktu presensi sesi siang / sholat Dzuhur aktif.",
                'holiday' => $holidayCheck,
            ];
        }

        // C. AFTERNOON / PULANG SESSION WINDOW
        // Active from afternoon open (jam pulang) onwards
        if ($currentTime >= $afternoonOpen) {
            return [
                'type' => 'check_out',
                'name' => 'Sesi Pulang (Check-Out)',
                'action_label' => $hasCheckedOut ? 'Sudah Presensi Pulang' : 'Presensi Pulang (Check-Out)',
                'is_active' => !$hasCheckedOut || $override,
                'is_already_done' => $hasCheckedOut,
                'status_preview' => 'present',
                'delay_minutes' => 0,
                'time_range' => "{$afternoonOpen} - {$afternoonClose} WITA",
                'message' => $hasCheckedOut
                    ? "Presensi Pulang telah tercatat pada {$attendance->check_out}."
                    : "KBM Selesai. Silakan lakukan presensi kepulangan.",
                'holiday' => $holidayCheck,
            ];
        }

        // D. Fallback default
        return [
            'type' => 'check_in',
            'name' => 'Sesi Presensi',
            'action_label' => 'Presensi Kehadiran',
            'is_active' => true,
            'is_already_done' => false,
            'status_preview' => 'present',
            'delay_minutes' => 0,
            'time_range' => 'Aktif',
            'message' => 'Sesi presensi aktif.',
            'holiday' => $holidayCheck,
        ];
    }

    /**
     * Validate session window rules for check-in submissions
     */
    public function validateSessionWindow(string $sessionType, Carbon $now): array
    {
        $override = Setting::get('attendance_manual_override', '0') == '1';
        if ($override) {
            return ['valid' => true];
        }

        $currentTime = $now->format('H:i');
        $schedule = $this->getScheduleSettings();

        // Check holiday
        $holiday = $this->checkHoliday($now);
        if ($holiday['is_holiday']) {
            return [
                'valid' => false,
                'message' => "Hari ini adalah {$holiday['reason']}. Presensi tidak dapat diproses.",
            ];
        }

        if ($sessionType === 'briefing') {
            $active = Setting::get('briefing_session_active', '0') == '1';
            if (!$active) {
                return [
                    'valid' => false,
                    'message' => 'Sesi Briefing saat ini belum dibuka atau telah ditutup oleh Kepala Sekolah.',
                ];
            }
            return ['valid' => true];
        }

        if ($sessionType === 'check_in') {
            if ($currentTime < ($schedule['morning_open'] ?? '06:00')) {
                return [
                    'valid' => false,
                    'message' => "Presensi Masuk belum dibuka. Sesi dibuka mulai pukul " . ($schedule['morning_open'] ?? '06:00') . ".",
                ];
            }
            // Allow check-in up to midday threshold as late check-in
            if ($currentTime > ($schedule['morning_close'] ?? '11:59') && $currentTime >= '12:00') {
                return [
                    'valid' => false,
                    'message' => "Sesi Presensi Masuk telah ditutup pada pukul " . ($schedule['morning_close'] ?? '11:59') . ".",
                ];
            }
        } elseif ($sessionType === 'midday' || $sessionType === 'afternoon') {
            $dzOpen = min($schedule['dzuhur_open'] ?? '11:30', '11:30');
            $dzClose = max($schedule['dzuhur_close'] ?? '14:30', $schedule['afternoon_open'] ?? '15:00');
            if ($currentTime < $dzOpen) {
                return [
                    'valid' => false,
                    'message' => "Sesi Dzuhur / Siang dibuka mulai pukul {$dzOpen}.",
                ];
            }
            if ($currentTime > $dzClose) {
                return [
                    'valid' => false,
                    'message' => "Sesi Dzuhur / Siang telah berakhir pada pukul {$dzClose}.",
                ];
            }
        } elseif ($sessionType === 'check_out' || $sessionType === 'evening') {
            $pmOpen = $schedule['afternoon_open'] ?? '14:00';
            if ($currentTime < $pmOpen) {
                return [
                    'valid' => false,
                    'message' => "Presensi Pulang baru dapat dicatat mulai pukul {$pmOpen}.",
                ];
            }
        }

        return ['valid' => true];
    }

    /**
     * Calculate late status and delay in minutes dynamically
     *
     * Rules:
     * - <= Late Threshold: 'present' (On Time), delay = 0
     * - > Late Threshold and <= (Late Threshold + Late Tolerance): 'late' (Late)
     * - > (Late Threshold + Late Tolerance): 'very_late' (Very Late)
     */
    public function calculateLateDetails(Carbon $now, ?array $schedule = null): array
    {
        $schedule = $schedule ?? $this->getScheduleSettings();
        $lateTimeStr = $schedule['morning_late']; // e.g. '07:30'
        $toleranceMin = (int) $schedule['late_tolerance']; // e.g. 15

        $nowSec = strtotime($now->format('H:i:s'));
        $thresholdSec = strtotime($lateTimeStr . ':00');

        if ($nowSec <= $thresholdSec) {
            return [
                'status' => 'present',
                'status_label' => 'Tepat Waktu',
                'delay_minutes' => 0,
            ];
        }

        $diffSeconds = $nowSec - $thresholdSec;
        $delayMinutes = (int) ceil($diffSeconds / 60);

        if ($delayMinutes <= $toleranceMin) {
            return [
                'status' => 'late',
                'status_label' => "Terlambat ({$delayMinutes} mnt)",
                'delay_minutes' => $delayMinutes,
            ];
        }

        return [
            'status' => 'late',
            'status_label' => "Terlambat ({$delayMinutes} mnt)",
            'delay_minutes' => $delayMinutes,
        ];
    }

    /**
     * Calculate late status string for backward compatibility
     */
    public function calculateLateStatus(Carbon $now): string
    {
        $details = $this->calculateLateDetails($now);
        return $details['status'];
    }

    /**
     * Get all attendance schedule and threshold settings with standard defaults
     */
    public function getScheduleSettings(): array
    {
        return [
            'morning_open' => Setting::get('attendance_morning_open', '06:00'),
            'morning_close' => Setting::get('attendance_morning_close', '11:59'),
            'morning_late' => Setting::get('attendance_morning_late', '07:30'),
            'late_tolerance' => (int) Setting::get('attendance_late_tolerance', 15),

            'dzuhur_open' => Setting::get('attendance_dzuhur_open', Setting::get('attendance_afternoon_open', '12:00')),
            'dzuhur_close' => Setting::get('attendance_dzuhur_close', Setting::get('attendance_afternoon_close', '13:30')),

            'afternoon_open' => Setting::get('attendance_afternoon_open', Setting::get('attendance_evening_open', '14:00')),
            'afternoon_close' => Setting::get('attendance_afternoon_close', Setting::get('attendance_evening_close', '18:00')),

            'gps_radius' => (int) Setting::get('school_attendance_radius', 100),
            'gps_enabled' => Setting::get('attendance_gps_enabled', '1') == '1',
            'fingerprint_enabled' => Setting::get('attendance_fingerprint_enabled', '1') == '1',
            'face_enabled' => Setting::get('attendance_face_enabled', '1') == '1',
            'manual_enabled' => Setting::get('attendance_manual_enabled', '1') == '1',
        ];
    }

    /**
     * Check if date is a configured weekend day or school holiday
     */
    public function checkHoliday(Carbon $date): array
    {
        // Weekend days config (e.g. '0' for Sunday, or '0,6' for Sunday & Saturday)
        $weekendConfig = Setting::get('attendance_weekend_days', '0');
        $weekendDays = array_map('intval', explode(',', $weekendConfig));

        if (in_array($date->dayOfWeek, $weekendDays)) {
            $dayName = $date->translatedFormat('l');
            return [
                'is_holiday' => true,
                'reason' => "Hari Libur Akhir Pekan ({$dayName})",
            ];
        }

        // Custom holiday dates (JSON or comma separated)
        $holidaysRaw = Setting::get('attendance_holidays', '');
        if (!empty($holidaysRaw)) {
            $holidaysList = json_decode($holidaysRaw, true);
            if (!is_array($holidaysList)) {
                $holidaysList = array_map('trim', explode(',', $holidaysRaw));
            }

            $dateFormatted = $date->format('Y-m-d');
            if (in_array($dateFormatted, $holidaysList)) {
                return [
                    'is_holiday' => true,
                    'reason' => 'Hari Libur Nasional / Sekolah',
                ];
            }
        }

        return [
            'is_holiday' => false,
            'reason' => null,
        ];
    }

    /**
     * Helper to build session payload for explicit request
     */
    protected function buildSessionPayload(string $type, Carbon $now, array $schedule, ?TeacherAttendance $attendance, array $holidayCheck): array
    {
        $normalizedType = match ($type) {
            'afternoon', 'midday', 'dzuhur' => 'midday',
            'check_out', 'pulang', 'evening' => 'check_out',
            'briefing' => 'briefing',
            default => 'check_in',
        };

        if ($normalizedType === 'midday') {
            $dzuhurRecorded = $attendance && (!empty($attendance->midday_at) || (!empty($attendance->notes) && str_contains($attendance->notes, 'Hadir Sesi Siang')));
            return [
                'type' => 'midday',
                'name' => 'Sesi Siang (Dzuhur)',
                'action_label' => $dzuhurRecorded ? 'Sudah Hadir Sesi Dzuhur' : 'Konfirmasi Presensi Dzuhur',
                'is_active' => !$dzuhurRecorded,
                'is_already_done' => $dzuhurRecorded,
                'status_preview' => 'present',
                'delay_minutes' => 0,
                'time_range' => "{$schedule['dzuhur_open']} - {$schedule['dzuhur_close']} WITA",
                'holiday' => $holidayCheck,
            ];
        }

        if ($normalizedType === 'check_out') {
            $hasCheckedOut = $attendance && !empty($attendance->check_out);
            return [
                'type' => 'check_out',
                'name' => 'Sesi Sore (Pulang)',
                'action_label' => $hasCheckedOut ? 'Sudah Presensi Pulang' : 'Presensi Pulang (Check-Out)',
                'is_active' => !$hasCheckedOut,
                'is_already_done' => $hasCheckedOut,
                'status_preview' => 'present',
                'delay_minutes' => 0,
                'time_range' => "{$schedule['afternoon_open']} - {$schedule['afternoon_close']} WITA",
                'holiday' => $holidayCheck,
            ];
        }

        $hasCheckedIn = $attendance && !empty($attendance->check_in);
        $statusData = $this->calculateLateDetails($now, $schedule);
        return [
            'type' => 'check_in',
            'name' => 'Sesi Pagi (Masuk)',
            'action_label' => $hasCheckedIn ? 'Sudah Check-In Masuk' : 'Presensi Masuk (Check-In)',
            'is_active' => !$hasCheckedIn,
            'is_already_done' => $hasCheckedIn,
            'status_preview' => $statusData['status'],
            'delay_minutes' => $statusData['delay_minutes'],
            'time_range' => "{$schedule['morning_open']} - {$schedule['morning_close']} WITA",
            'holiday' => $holidayCheck,
        ];
    }
}
