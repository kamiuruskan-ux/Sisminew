<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\TeacherAttendance;
use App\Models\User;

class AttendanceVerificationService
{
    /**
     * Validate teacher account status and role authorization
     */
    public function validateTeacherAccount(User $user): array
    {
        if (isset($user->status) && in_array(strtolower($user->status), ['inactive', 'suspended', 'nonaktif', 'banned'])) {
            return [
                'valid' => false,
                'message' => "Akun pegawai '{$user->name}' dalam status nonaktif. Hubungi Administrator.",
            ];
        }

        $validRoles = ['guru', 'teacher', 'admin', 'operator', 'tata-usaha', 'staff', 'kepala-sekolah', 'super-admin'];
        $hasRole = $user->roles()->whereIn('slug', $validRoles)->exists();

        if (!$hasRole && !in_array($user->role ?? '', $validRoles)) {
            return [
                'valid' => false,
                'message' => 'Hanya Guru dan Tenaga Kependidikan yang berhak melakukan presensi pegawai.',
            ];
        }

        return ['valid' => true];
    }

    /**
     * Prevent duplicate attendance and enforce session smart locking
     */
    public function checkDuplicateAndLock(TeacherAttendance $attendance, string $sessionType): array
    {
        if ($sessionType === 'check_in') {
            if (!empty($attendance->check_in)) {
                $methodLabel = $attendance->method_label ?? 'sistem';
                return [
                    'locked' => true,
                    'already_complete' => false,
                    'message' => "Presensi MASUK telah tercatat pada pukul {$attendance->check_in} (via {$methodLabel}).",
                ];
            }
        } elseif ($sessionType === 'midday') {
            $middayRecorded = !empty($attendance->midday_at) || (!empty($attendance->notes) && str_contains($attendance->notes, 'Hadir Sesi Siang'));
            if ($middayRecorded) {
                $time = $attendance->midday_at ?? 'sebelumnya';
                return [
                    'locked' => true,
                    'already_complete' => false,
                    'message' => "Presensi Sesi Siang (Dzuhur) sudah tercatat hari ini ({$time}).",
                ];
            }
        } elseif ($sessionType === 'check_out') {
            if (empty($attendance->check_in)) {
                return [
                    'locked' => true,
                    'already_complete' => false,
                    'message' => 'Anda belum melakukan presensi MASUK hari ini. Harap presensi masuk terlebih dahulu.',
                ];
            }
            if (!empty($attendance->check_out)) {
                return [
                    'locked' => true,
                    'already_complete' => true,
                    'message' => "Kehadiran hari ini sudah LENGKAP! (Masuk: {$attendance->check_in} | Pulang: {$attendance->check_out}).",
                ];
            }
        } elseif ($sessionType === 'briefing') {
            if (!empty($attendance->notes) && str_contains($attendance->notes, 'Hadir Briefing:')) {
                return [
                    'locked' => true,
                    'already_complete' => false,
                    'message' => 'Kehadiran Briefing Anda sudah tercatat hari ini.',
                ];
            }
        }

        return ['locked' => false];
    }

    /**
     * Validate GPS coordinates against geofence boundary using Haversine formula
     */
    public function validateGpsGeofence(?float $lat, ?float $long, string $workLocation): array
    {
        $schoolLat = (float) Setting::get('school_latitude', -0.8917);
        $schoolLong = (float) Setting::get('school_longitude', 119.8707);
        $maxRadius = (int) Setting::get('school_attendance_radius', 100);
        $manualOverride = Setting::get('attendance_manual_override', '0') == '1';

        // Dinas luar or manual override bypasses radius check
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
                'message' => 'Koordinat GPS tidak terdeteksi. Aktifkan GPS dan izinkan akses lokasi.',
            ];
        }

        $earthRadius = 6371000; // in meters
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
                'message' => "Presensi Gagal: Posisi Anda ({$distance}m) di luar batas radius sekolah (Maks: {$maxRadius}m).",
            ];
        }

        return [
            'valid' => true,
            'distance' => $distance,
            'max_radius' => $maxRadius,
        ];
    }
}
