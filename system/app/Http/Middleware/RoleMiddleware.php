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
            } elseif ($user->hasRole('admin') || $user->hasRole('super-admin')) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->hasRole('teacher') || $user->hasRole('guru')) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->hasRole('kantin')) {
                return redirect()->route('canteen.vendor.dashboard');
            }
            
            // If no matching role, logout and redirect to login
            Auth::logout();
            return redirect()->route('login')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
