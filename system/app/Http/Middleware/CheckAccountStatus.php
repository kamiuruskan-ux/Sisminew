<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAccountStatus
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->status !== 'active') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('error', 'Akun Anda non-aktif. Silakan hubungi Administrator.');
            }

            if ($user->isLockedOut()) {
                $seconds = $user->getLockoutRemainingSeconds();
                $minutes = ceil($seconds / 60);

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('error', "Akun Anda sedang dikunci sementara demi keamanan. Silakan coba lagi dalam {$minutes} menit.");
            }
        }

        return $next($request);
    }
}
