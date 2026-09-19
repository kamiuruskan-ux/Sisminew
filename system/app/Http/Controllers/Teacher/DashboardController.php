<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show teacher dashboard
     */
    public function index()
    {
        // Deteksi apakah pengguna mengakses dari perangkat seluler (smartphone / tablet)
        $userAgent = request()->header('User-Agent', '');
        $isMobile = preg_match('/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos|iphone|ipad|ipod)/i', $userAgent);

        if ($isMobile) {
            return redirect()->route('admin.teacher-attendances.mobile');
        }

        $user = Auth::user();

        $stats = [
            'total_students' => User::role('student')->count(),
            'announcements' => Announcement::latest()->take(5)->get(),
            'recent_posts' => Post::latest()->take(5)->get(),
        ];

        return view('teacher.dashboard', compact('stats', 'user'));
    }
}
