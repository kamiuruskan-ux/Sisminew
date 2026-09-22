<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\CustomNotification;
use App\Models\PushSubscription;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    /**
     * Kirim notifikasi ke satu user
     */
    public static function sendToUser(
        int $userId,
        string $title,
        string $body,
        ?string $url = null,
        string $type = 'info',
        ?int $customNotificationId = null,
        string $category = 'general'
    ): ?AppNotification {
        try {
            // 1. Simpan in-app notification
            $notif = AppNotification::create([
                'custom_notification_id' => $customNotificationId,
                'user_id' => $userId,
                'title' => $title,
                'message' => $body,
                'type' => $type,
                'category' => $category,
                'action_url' => $url,
                'is_read' => false,
                'is_pushed' => false,
            ]);

            // 2. Kirim Web Push ke semua perangkat aktif milik user ini
            self::pushToUserDevices($userId, [
                'id' => $notif->id,
                'title' => $title,
                'body' => $body,
                'url' => $url ?: route('admin.dashboard'),
                'type' => $type,
                'icon' => '/pwa-icon/192',
                'badge' => '/pwa-icon/192',
            ]);

            $notif->update([
                'is_pushed' => true,
                'pushed_at' => now(),
            ]);

            return $notif;
        } catch (\Throwable $e) {
            Log::error('PushNotificationService::sendToUser failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Kirim notifikasi ke sekumpulan user IDs
     */
    public static function sendToUsers(
        array $userIds,
        string $title,
        string $body,
        ?string $url = null,
        string $type = 'info',
        ?int $customNotificationId = null,
        string $category = 'general'
    ): int {
        $count = 0;
        $uniqueUserIds = array_unique(array_filter($userIds));

        foreach ($uniqueUserIds as $uid) {
            if (self::sendToUser((int)$uid, $title, $body, $url, $type, $customNotificationId, $category)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Kirim notifikasi berdasarkan peran (Roles)
     */
    public static function sendToRoles(
        array $roles,
        string $title,
        string $body,
        ?string $url = null,
        string $type = 'info',
        ?int $customNotificationId = null
    ): int {
        $users = User::whereHas('roles', function ($q) use ($roles) {
            $q->whereIn('name', $roles);
        })->pluck('id')->toArray();

        return self::sendToUsers($users, $title, $body, $url, $type, $customNotificationId, 'role_broadcast');
    }

    /**
     * Kirim notifikasi ke siswa di kelas tertentu
     */
    public static function sendToClass(
        int $classId,
        string $title,
        string $body,
        ?string $url = null,
        string $type = 'info',
        ?int $customNotificationId = null
    ): int {
        $userIds = Student::where('class_id', $classId)
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->toArray();

        return self::sendToUsers($userIds, $title, $body, $url, $type, $customNotificationId, 'class_broadcast');
    }

    /**
     * Kirim notifikasi ke seluruh pengguna (Broadcast All)
     */
    public static function broadcastAll(
        string $title,
        string $body,
        ?string $url = null,
        string $type = 'info',
        ?int $customNotificationId = null
    ): int {
        $userIds = User::pluck('id')->toArray();
        return self::sendToUsers($userIds, $title, $body, $url, $type, $customNotificationId, 'broadcast_all');
    }

    /**
     * Push data ke perangkat terdaftar milik user
     */
    protected static function pushToUserDevices(int $userId, array $payload): void
    {
        $subs = PushSubscription::where('user_id', $userId)
            ->where('is_active', true)
            ->get();

        foreach ($subs as $sub) {
            self::sendWebPushPayload($sub, $payload);
        }
    }

    /**
     * Kirim payload ke endpoint Web Push browser
     */
    public static function sendWebPushPayload(PushSubscription $sub, array $payload): bool
    {
        try {
            $jsonPayload = json_encode($payload);

            // Kirim request HTTP POST ke endpoint browser push service
            $response = Http::timeout(5)
                ->withHeaders([
                    'TTL' => '86400',
                    'Content-Type' => 'application/json',
                ])
                ->post($sub->endpoint, $payload);

            // Jika endpoint sudah kadaluarsa (404 / 410 Gone), tandai inactive
            if ($response->status() === 404 || $response->status() === 410) {
                $sub->update(['is_active' => false]);
                return false;
            }

            $sub->update(['last_active_at' => now()]);
            return true;
        } catch (\Throwable $e) {
            // Browser endpoint might require VAPID or device might be offline
            Log::debug('PushNotificationService::sendWebPushPayload notice: ' . $e->getMessage());
            return false;
        }
    }
}
