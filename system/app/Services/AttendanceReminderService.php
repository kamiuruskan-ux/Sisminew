<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\ClassModel;
use App\Models\CustomNotification;
use App\Models\Setting;
use App\Models\TeacherAttendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AttendanceReminderService
{
    /**
     * Jalankan pengingat presensi otomatis
     */
    public static function runReminders(string $session = 'auto'): array
    {
        $now = Carbon::now();
        $today = $now->toDateString();

        // 1. Cek apakah hari ini hari libur atau weekend
        if (self::isNonWorkingDay($now)) {
            return [
                'success' => true,
                'message' => 'Hari ini adalah hari libur / akhir pekan. Pengingat presensi dilewati.',
                'sent_count' => 0,
            ];
        }

        // Tentukan sesi jika 'auto'
        if ($session === 'auto') {
            $currentHour = (int)$now->format('H');
            $session = ($currentHour < 12) ? 'morning' : 'afternoon';
        }

        if ($session === 'morning') {
            return self::sendMorningReminders($today);
        } else {
            return self::sendAfternoonReminders($today);
        }
    }

    /**
     * Pengingat presensi pagi (Check-in)
     */
    public static function sendMorningReminders(string $today): array
    {
        $morningLate = Setting::get('attendance_morning_late', '07:30');
        $schoolName = Setting::get('school_short_name', 'SISMI');

        // Ambil semua pegawai / guru aktif
        $staffUsers = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['guru', 'teacher', 'staff', 'pegawai', 'operator', 'kepala-sekolah']);
        })->get();

        // Ambil ID user yang sudah check-in hari ini
        $checkedInUserIds = TeacherAttendance::whereDate('date', $today)
            ->whereNotNull('check_in')
            ->pluck('user_id')
            ->toArray();

        $unrecordedStaff = $staffUsers->filter(function ($user) use ($checkedInUserIds) {
            return !in_array($user->id, $checkedInUserIds);
        });

        $sentCount = 0;
        $title = "⏰ Pengingat Presensi Pagi {$schoolName}";
        $url = route('admin.teacher-attendances.index');

        foreach ($unrecordedStaff as $staff) {
            $body = "Halo Ustadz/Ustadzah {$staff->name}, jangan lupa melakukan presensi kehadiran pagi sebelum pukul {$morningLate} WITA. Semoga hari Anda penuh berkah!";

            $notif = PushNotificationService::sendToUser(
                userId: $staff->id,
                title: $title,
                body: $body,
                url: $url,
                type: 'attendance_reminder',
                category: 'attendance_morning'
            );

            if ($notif) {
                $sentCount++;
            }
        }

        // Cek juga wali kelas yang belum mengisi presensi santri di kelasnya
        $classReminderCount = self::sendClassAttendanceReminders($today);

        // Catat ke CustomNotification sebagai log riwayat pengingat otomatis
        if ($sentCount > 0) {
            CustomNotification::create([
                'sender_id' => auth()->id() ?? null,
                'title' => $title,
                'message' => "Pengingat presensi pagi dikirimkan ke {$sentCount} guru/pegawai yang belum check-in.",
                'type' => 'attendance_reminder',
                'target_type' => 'role',
                'target_payload' => ['roles' => ['guru', 'staff']],
                'action_url' => $url,
                'channels' => ['push', 'in_app'],
                'sent_count' => $sentCount,
                'status' => 'sent',
            ]);
        }

        return [
            'success' => true,
            'session' => 'morning',
            'sent_count' => $sentCount,
            'class_reminders' => $classReminderCount,
            'message' => "Pengingat presensi pagi berhasil dikirim ke {$sentCount} guru/pegawai.",
        ];
    }

    /**
     * Pengingat presensi sore (Check-out)
     */
    public static function sendAfternoonReminders(string $today): array
    {
        $schoolName = Setting::get('school_short_name', 'SISMI');

        // Cari guru yang sudah check-in tapi BELUM check-out hari ini
        $attendancesToCheckout = TeacherAttendance::with('user')
            ->whereDate('date', $today)
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->whereNotIn('status', ['sick', 'permission', 'absent'])
            ->get();

        $sentCount = 0;
        $title = "🔔 Pengingat Presensi Pulang {$schoolName}";
        $url = route('admin.teacher-attendances.index');

        foreach ($attendancesToCheckout as $att) {
            if (!$att->user) continue;

            $staff = $att->user;
            $body = "Halo {$staff->name}, waktu kepulangan telah tiba. Silakan lakukan presensi keluar (check-out) sebelum meninggalkan lingkungan sekolah.";

            $notif = PushNotificationService::sendToUser(
                userId: $staff->id,
                title: $title,
                body: $body,
                url: $url,
                type: 'attendance_reminder',
                category: 'attendance_afternoon'
            );

            if ($notif) {
                $sentCount++;
            }
        }

        if ($sentCount > 0) {
            CustomNotification::create([
                'sender_id' => auth()->id() ?? null,
                'title' => $title,
                'message' => "Pengingat presensi pulang dikirimkan ke {$sentCount} guru/pegawai.",
                'type' => 'attendance_reminder',
                'target_type' => 'role',
                'target_payload' => ['roles' => ['guru', 'staff']],
                'action_url' => $url,
                'channels' => ['push', 'in_app'],
                'sent_count' => $sentCount,
                'status' => 'sent',
            ]);
        }

        return [
            'success' => true,
            'session' => 'afternoon',
            'sent_count' => $sentCount,
            'message' => "Pengingat presensi sore berhasil dikirim ke {$sentCount} guru/pegawai.",
        ];
    }

    /**
     * Pengingat pengisian presensi kelas kepada wali kelas
     */
    protected static function sendClassAttendanceReminders(string $today): int
    {
        $classes = ClassModel::all();
        $sent = 0;

        foreach ($classes as $class) {
            $hasAttendance = Attendance::where('class_id', $class->id)
                ->whereDate('date', $today)
                ->exists();

            // Jika belum ada presensi di kelas ini dan kelas memiliki wali kelas (teacher_id / homeroom_teacher_id)
            if (!$hasAttendance) {
                $teacherId = $class->homeroom_teacher_id ?? $class->teacher_id ?? null;
                if ($teacherId) {
                    $notif = PushNotificationService::sendToUser(
                        userId: (int)$teacherId,
                        title: "📋 Pengingat Presensi Kelas {$class->name}",
                        body: "Presensi harian santri untuk Kelas {$class->name} belum diinput hari ini. Mohon segera melakukan pencatatan kehadiran.",
                        url: route('admin.attendances.index'),
                        type: 'attendance_reminder',
                        category: 'class_attendance_reminder'
                    );
                    if ($notif) $sent++;
                }
            }
        }

        return $sent;
    }

    /**
     * Cek apakah hari ini weekend atau hari libur
     */
    protected static function isNonWorkingDay(Carbon $date): bool
    {
        // Weekend check (0 = Minggu, 6 = Sabtu)
        $weekendConfig = Setting::get('attendance_weekend_days', '0');
        $weekendDays = array_map('trim', explode(',', $weekendConfig));

        if (in_array((string)$date->dayOfWeek, $weekendDays)) {
            return true;
        }

        // Holidays check
        $holidaysRaw = Setting::get('attendance_holidays', '');
        if (!empty($holidaysRaw)) {
            $holidays = array_map('trim', explode(',', $holidaysRaw));
            if (in_array($date->toDateString(), $holidays)) {
                return true;
            }
        }

        return false;
    }
}
