<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display the full notifications page.
     */
    public function index(Request $request)
    {
        $category = $request->query('category', 'all');
        $user = auth()->user();

        if ($category !== 'all' && $user) {
            $permissionMap = [
                'spmb' => 'view-spmb',
                'payment' => 'view-financial',
                'assignment' => 'view-learning',
                'announcement' => 'view-announcements',
                'canteen' => 'view-canteen-admin',
            ];

            if (isset($permissionMap[$category]) && !$user->hasPermission($permissionMap[$category])) {
                abort(403, 'Anda tidak memiliki akses ke kategori notifikasi ini.');
            }
        }

        $notifications = NotificationService::getNotifications($category);
        $counts = NotificationService::getCounts();

        return view('admin.notifications.index', compact('notifications', 'counts', 'category'));
    }

    /**
     * Mark all notifications as read in session.
     */
    public function markAllAsRead(Request $request)
    {
        session(['notifications_read_at' => now()]);

        if (auth()->check() && \Illuminate\Support\Facades\Schema::hasTable('app_notifications')) {
            \App\Models\AppNotification::where('user_id', auth()->id())
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);
        }

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }
}
