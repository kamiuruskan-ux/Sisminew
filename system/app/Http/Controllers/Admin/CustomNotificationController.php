<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\CustomNotification;
use App\Models\PushSubscription;
use App\Models\User;
use App\Services\AttendanceReminderService;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;

class CustomNotificationController extends Controller
{
    /**
     * Tampilkan halaman siaran / broadcast notifikasi custom
     */
    public function broadcastIndex(Request $request)
    {
        $broadcasts = CustomNotification::with('sender')
            ->latest()
            ->paginate(15);

        $classes = ClassModel::orderBy('name')->get();

        $roles = [
            ['slug' => 'guru', 'name' => 'Dewan Guru & Pengajar'],
            ['slug' => 'staff', 'name' => 'Tenaga Kependidikan / Staff'],
            ['slug' => 'student', 'name' => 'Siswa / Santri'],
            ['slug' => 'parent', 'name' => 'Orang Tua / Wali Santri'],
            ['slug' => 'kantin', 'name' => 'Vendor Kantin Sekolah'],
            ['slug' => 'calon-siswa', 'name' => 'Calon Siswa Baru (SPMB)'],
        ];

        $users = User::select('id', 'name', 'email')
            ->orderBy('name')
            ->take(200)
            ->get();

        $totalBroadcasts = CustomNotification::count();
        $totalPushedRecipients = CustomNotification::sum('sent_count');
        $activePushDevices = PushSubscription::where('is_active', true)->count();

        return view('admin.notifications.broadcast', compact(
            'broadcasts',
            'classes',
            'roles',
            'users',
            'totalBroadcasts',
            'totalPushedRecipients',
            'activePushDevices'
        ));
    }

    /**
     * Kirim notifikasi broadcast custom
     */
    public function sendBroadcast(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,warning,urgent,attendance_reminder,success',
            'target_type' => 'required|in:all,role,class,users',
            'roles' => 'nullable|array',
            'class_id' => 'nullable|integer',
            'user_ids' => 'nullable|array',
            'action_url' => 'nullable|string|max:255',
        ]);

        $senderId = auth()->id();
        $title = $request->input('title');
        $body = $request->input('message');
        $type = $request->input('type');
        $targetType = $request->input('target_type');
        $actionUrl = $request->input('action_url') ?: route('admin.dashboard');

        // Simpan master record broadcast
        $broadcast = CustomNotification::create([
            'sender_id' => $senderId,
            'title' => $title,
            'message' => $body,
            'type' => $type,
            'target_type' => $targetType,
            'target_payload' => [
                'roles' => $request->input('roles', []),
                'class_id' => $request->input('class_id'),
                'user_ids' => $request->input('user_ids', []),
            ],
            'action_url' => $actionUrl,
            'channels' => ['push', 'in_app'],
            'sent_count' => 0,
            'status' => 'processing',
        ]);

        $sentCount = 0;

        switch ($targetType) {
            case 'all':
                $sentCount = PushNotificationService::broadcastAll($title, $body, $actionUrl, $type, $broadcast->id);
                break;

            case 'role':
                $roles = $request->input('roles', []);
                if (!empty($roles)) {
                    $sentCount = PushNotificationService::sendToRoles($roles, $title, $body, $actionUrl, $type, $broadcast->id);
                }
                break;

            case 'class':
                $classId = (int)$request->input('class_id');
                if ($classId) {
                    $sentCount = PushNotificationService::sendToClass($classId, $title, $body, $actionUrl, $type, $broadcast->id);
                }
                break;

            case 'users':
                $userIds = $request->input('user_ids', []);
                if (!empty($userIds)) {
                    $sentCount = PushNotificationService::sendToUsers($userIds, $title, $body, $actionUrl, $type, $broadcast->id);
                }
                break;
        }

        $broadcast->update([
            'sent_count' => $sentCount,
            'status' => 'sent',
        ]);

        return redirect()->route('admin.notifications.broadcast')
            ->with('success', "Notifikasi siaran berhasil dikirimkan ke {$sentCount} pengguna!");
    }

    /**
     * Trigger manual pengingat presensi
     */
    public function triggerAttendanceReminder(Request $request)
    {
        $session = $request->input('session', 'auto');
        $result = AttendanceReminderService::runReminders($session);

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message'] ?? 'Gagal mengirim pengingat.');
    }

    /**
     * Hapus riwayat siaran notifikasi
     */
    public function deleteBroadcast($id)
    {
        $broadcast = CustomNotification::findOrFail($id);
        $broadcast->recipients()->delete();
        $broadcast->delete();

        return redirect()->back()->with('success', 'Riwayat siaran notifikasi berhasil dihapus.');
    }
}
