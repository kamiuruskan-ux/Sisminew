<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\PushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    /**
     * Daftarkan endpoint Web Push browser pengguna
     */
    public function subscribe(Request $request): JsonResponse
    {
        $request->validate([
            'endpoint' => 'required|string|max:500',
            'public_key' => 'nullable|string',
            'auth_token' => 'nullable|string',
            'device_type' => 'nullable|string|in:mobile,desktop,tablet',
        ]);

        $userId = auth()->id();
        $endpoint = $request->input('endpoint');
        $publicKey = $request->input('public_key');
        $authToken = $request->input('auth_token');
        $deviceType = $request->input('device_type', 'desktop');
        $userAgent = $request->userAgent();

        $sub = PushSubscription::updateOrCreate(
            ['endpoint' => $endpoint],
            [
                'user_id' => $userId,
                'public_key' => $publicKey,
                'auth_token' => $authToken,
                'device_type' => $deviceType,
                'user_agent' => $userAgent,
                'is_active' => true,
                'last_active_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Perangkat berhasil terdaftar untuk notifikasi push.',
            'id' => $sub->id,
        ]);
    }

    /**
     * Berhenti berlangganan push
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $endpoint = $request->input('endpoint');

        if ($endpoint) {
            PushSubscription::where('endpoint', $endpoint)->update(['is_active' => false]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menonaktifkan notifikasi push.',
        ]);
    }

    /**
     * Polling notifikasi real-time untuk tab/aplikasi aktif di HP & PC
     */
    public function poll(Request $request): JsonResponse
    {
        $userId = auth()->id();

        if (!$userId) {
            return response()->json(['notifications' => []]);
        }

        // Ambil notifikasi 15 menit terakhir yang belum dibaca
        $notifications = AppNotification::where('user_id', $userId)
            ->where('is_read', false)
            ->where('created_at', '>=', now()->subMinutes(15))
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'title' => $n->title,
                    'body' => $n->message,
                    'type' => $n->type,
                    'category' => $n->category,
                    'url' => $n->action_url ?: route('admin.dashboard'),
                    'created_at' => $n->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'count' => $notifications->count(),
        ]);
    }

    /**
     * Tandai notifikasi perorangan sebagai dibaca
     */
    public function markAsRead(Request $request, $id): JsonResponse
    {
        $userId = auth()->id();
        $notif = AppNotification::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if ($notif) {
            $notif->markAsRead();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }
}
