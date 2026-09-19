<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\TeacherAttendance;
use Carbon\Carbon;

class AttendanceSessionService
{
    /**
     * Resolve currently active attendance session based on server time and user attendance state
     */
    public function resolveActiveSession(Carbon $now, ?string $requestedType = null, ?TeacherAttendance $attendance = null): array
    {
        $override = Setting::get('attendance_manual_override', '0') == '1';
        $currentTime = $now->format('H:i');

        // Session time windows from Setting
        $morningOpen = Setting::get('attendance_morning_open', '06:00');
        $morningClose = Setting::get('attendance_morning_close', '11:59');
        $morningLate = Setting::get('attendance_morning_late', '07:30');

        $afternoonOpen = Setting::get('attendance_afternoon_open', '12:00');
        $afternoonClose = Setting::get('attendance_afternoon_close', '14:00');

        $eveningOpen = Setting::get('attendance_evening_open', '14:01');
        $eveningClose = Setting::get('attendance_evening_close', '23:59');

        $briefingActive = Setting::get('briefing_session_active', '0') == '1';

        // 1. Explicit requested type handling
        if (!empty($requestedType)) {
            return $this->buildSessionInfo($requestedType, $now, $morningLate);
        }

        // 2. Automated evaluation based on server time & record status
        // Priority A: If briefing is explicitly opened by principal
        if ($briefingActive && (!$attendance || empty($attendance->notes) || !str_contains($attendance->notes, 'Hadir Briefing:'))) {
            return $this->buildSessionInfo('briefing', $now, $morningLate);
        }

        // Priority B: If user has not checked in at all today
        if (!$attendance || empty($attendance->check_in)) {
            // Even if server time is afternoon, first touch is Morning Check In
            return $this->buildSessionInfo('check_in', $now, $morningLate);
        }

        // Priority C: Midday / Dzuhur window
        if ($currentTime >= $afternoonOpen && $currentTime <= $afternoonClose) {
            // If midday has not yet been recorded
            $middayRecorded = !empty($attendance->midday_at) || (!empty($attendance->notes) && str_contains($attendance->notes, 'Hadir Sesi Siang'));
            if (!$middayRecorded) {
                return $this->buildSessionInfo('midday', $now, $morningLate);
            }
        }

        // Priority D: Checkout / Evening window or already checked in
        return $this->buildSessionInfo('check_out', $now, $morningLate);
    }

    /**
     * Validate session window rules
     */
    public function validateSessionWindow(string $sessionType, Carbon $now): array
    {
        $override = Setting::get('attendance_manual_override', '0') == '1';
        if ($override) {
            return ['valid' => true];
        }

        $currentTime = $now->format('H:i');

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
            $open = Setting::get('attendance_morning_open', '06:00');
            if ($currentTime < $open) {
                return [
                    'valid' => false,
                    'message' => "Presensi Masuk belum dibuka. Sesi dibuka mulai pukul {$open}.",
                ];
            }
        } elseif ($sessionType === 'midday' || $sessionType === 'afternoon') {
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
     * Calculate late status dynamically
     */
    public function calculateLateStatus(Carbon $now): string
    {
        $lateThreshold = Setting::get('attendance_morning_late', '07:30');
        if (!str_contains($lateThreshold, ':')) {
            $lateThreshold = '07:30';
        }

        $lateTimestamp = strtotime($lateThreshold . ':00');
        $currentTimestamp = strtotime($now->format('H:i:s'));

        return ($currentTimestamp > $lateTimestamp) ? 'late' : 'present';
    }

    /**
     * Helper to build session metadata
     */
    protected function buildSessionInfo(string $type, Carbon $now, string $lateThreshold): array
    {
        $normalizedType = match ($type) {
            'afternoon', 'midday' => 'midday',
            'check_out', 'pulang', 'evening' => 'check_out',
            'briefing' => 'briefing',
            default => 'check_in',
        };

        $sessionNames = [
            'check_in' => 'Sesi Pagi (Masuk)',
            'midday' => 'Sesi Siang (Dzuhur)',
            'check_out' => 'Sesi Sore (Pulang)',
            'briefing' => Setting::get('briefing_title', 'Briefing Pagi Dewan Guru'),
        ];

        return [
            'type' => $normalizedType,
            'name' => $sessionNames[$normalizedType] ?? 'Presensi Mandiri',
            'is_late' => $normalizedType === 'check_in' && ($now->format('H:i:s') > ($lateThreshold . ':00')),
        ];
    }
}
