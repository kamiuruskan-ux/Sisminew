<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $roles = explode('|', $role);
        $hasAccess = false;
        foreach ($roles as $r) {
            if ($request->user()->hasRole(trim($r))) {
                $hasAccess = true;
                break;
            }
        }

        if (!$hasAccess) {
            // Redirect to appropriate dashboard based on user's role
            $user = $request->user()->load('roles');
            
            if ($user->hasRole('student')) {
                return redirect()->route('student.dashboard');
            } elseif ($user->hasRole('calon-siswa')) {
                return redirect()->route('spmb.dashboard.index');
            } elseif ($user->hasRole('kantin')) {
                return redirect()->route('canteen.vendor.dashboard');
            } elseif ($user->hasRole(['super-admin', 'admin', 'guru', 'teacher', 'guru-quran', 'kepala-sekolah', 'wakasek-kesiswaan', 'wakasek-kurikulum', 'wakasek-kehumasan', 'bendahara', 'operator', 'staff', 'tata-usaha', 'guru-bk', 'bk'])) {
                return redirect()->route('admin.dashboard');
            }
            
            // If no specific role match above but user is authenticated staff/employee, fallback to admin dashboard
            return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki hak akses ke halaman tersebut.');
        }

        return $next($request);
    }
}
