<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionTimeout
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $timeoutMinutes = (int) Setting::get('security_session_timeout', 30);

            if ($timeoutMinutes > 0) {
                $lastActivity = session('last_activity_timestamp');

                if ($lastActivity && (time() - $lastActivity > $timeoutMinutes * 60)) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')->with('warning', "Sesi Anda telah berakhir karena tidak ada aktivitas selama {$timeoutMinutes} menit.");
                }

                session(['last_activity_timestamp' => time()]);
            }
        }

        return $next($request);
    }
}
