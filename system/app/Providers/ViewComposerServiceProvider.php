<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('layouts.admin', function ($view) {
            $user = auth()->user();
            
            $menuItems = [
                [
                    'label' => 'Dashboard',
                    'url' => route('admin.dashboard'),
                    'active' => 'admin/dashboard',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
                    'visible' => true,
                ],
                [
                    'label' => 'Users',
                    'url' => route('admin.users.index'),
                    'active' => 'admin/users*',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
                    'visible' => $user->hasPermission('view-users'),
                ],
                [
                    'label' => 'Roles',
                    'url' => route('admin.roles.index'),
                    'active' => 'admin/roles*',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
                    'visible' => $user->hasPermission('view-roles'),
                ],
                [
                    'label' => 'Siswa',
                    'url' => route('admin.students.index'),
                    'active' => 'admin/students*',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>',
                    'visible' => $user->hasPermission('view-students'),
                ],
                [
                    'label' => 'SPMB',
                    'url' => route('admin.spmb.index'),
                    'active' => 'admin/spmb*',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>',
                    'visible' => $user->hasPermission('view-spmb'),
                ],
                [
                    'label' => 'Kelas',
                    'url' => route('admin.classes.index'),
                    'active' => 'admin/classes*',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
                    'visible' => $user->hasPermission('view-classes'),
                ],
                [
                    'label' => 'Jurusan',
                    'url' => route('admin.majors.index'),
                    'active' => 'admin/majors*',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
                    'visible' => \App\Models\Setting::get('is_vocational', '1') == '1' && $user->hasPermission('view-majors'),
                ],
                [
                    'label' => 'Gelombang',
                    'url' => route('admin.waves.index'),
                    'active' => 'admin/waves*',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
                    'visible' => $user->hasPermission('view-spmb'),
                ],
                [
                    'label' => 'Berita',
                    'url' => route('admin.posts.index'),
                    'active' => 'admin/posts*',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-9 0H3"/>',
                    'visible' => $user->hasPermission('view-posts'),
                ],
                [
                    'label' => 'Galeri',
                    'url' => route('admin.gallery.index'),
                    'active' => 'admin/gallery*',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
                    'visible' => $user->hasPermission('view-gallery'),
                ],
                [
                    'label' => 'Pengumuman',
                    'url' => route('admin.announcements.index'),
                    'active' => 'admin/announcements*',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>',
                    'visible' => $user->hasPermission('view-announcements'),
                ],
                [
                    'label' => 'Pengaturan',
                    'url' => route('admin.settings'),
                    'active' => 'admin/settings*',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
                    'visible' => $user->hasPermission('view-settings'),
                ],
            ];

            // Dynamic Notifications via NotificationService
            $recentNotifications = \App\Services\NotificationService::getNotifications(null, 6);
            $notificationCounts = \App\Services\NotificationService::getCounts();
            $notificationCount = $notificationCounts['urgent'] ?? $notificationCounts['all'] ?? 0;

            $view->with('menuItems', $menuItems)
                 ->with('notificationCount', $notificationCount)
                 ->with('recentNotifications', $recentNotifications)
                 ->with('notificationCounts', $notificationCounts);
        });

        View::composer(['admin.*', 'spmb.*'], function ($view) {
            if (!isset($view->getData()['majors'])) {
                $view->with('majors', \App\Models\Major::where('is_active', true)->orderBy('name')->get());
            }
            if (!isset($view->getData()['classes'])) {
                $view->with('classes', \App\Models\ClassModel::orderBy('name')->get());
            }
        });
    }
}
