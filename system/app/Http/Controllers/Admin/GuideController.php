<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuideController extends Controller
{
    /**
     * Display the Admin User Guide documentation page.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // Require super-admin or admin role access
        $user = auth()->user();
        if (!$user || !($user->hasRole('super-admin') || $user->hasRole('superadmin') || $user->hasRole('admin'))) {
            abort(403, 'Akses khusus Super Admin & Admin.');
        }

        $rolesCount = Role::count();
        $usersCount = User::count();
        $schoolName = Setting::get('school_name', 'Sekolah');

        return view('admin.guide.index', compact('rolesCount', 'usersCount', 'schoolName'));
    }
}
