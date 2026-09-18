<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginSecurityLog;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SecurityController extends Controller
{
    /**
     * Display Security Dashboard, Logs, Locked Accounts, and Settings.
     */
    public function index(Request $request)
    {
        $statusFilter = $request->query('status');
        $searchQuery = trim($request->query('q', ''));

        // Query Logs
        $logsQuery = LoginSecurityLog::with('user')->latest();

        if (!empty($statusFilter)) {
            $logsQuery->where('status', $statusFilter);
        }

        if (!empty($searchQuery)) {
            $logsQuery->where(function ($q) use ($searchQuery) {
                $q->where('identifier', 'like', "%{$searchQuery}%")
                  ->orWhere('ip_address', 'like', "%{$searchQuery}%")
                  ->orWhere('failure_reason', 'like', "%{$searchQuery}%");
            });
        }

        $logs = $logsQuery->paginate(20)->withQueryString();

        // Locked Users
        $lockedUsers = User::whereNotNull('locked_until')
            ->where('locked_until', '>', now())
            ->latest('locked_until')
            ->get();

        // Statistics
        $today = now()->startOfDay();
        $stats = [
            'total_today' => LoginSecurityLog::where('created_at', '>=', $today)->count(),
            'failed_today' => LoginSecurityLog::where('created_at', '>=', $today)
                ->whereIn('status', ['failed_password', 'failed_otp', 'captcha_failed', 'locked_account', 'blocked_ip'])
                ->count(),
            'locked_users_count' => $lockedUsers->count(),
            'unique_ips_failed' => LoginSecurityLog::where('created_at', '>=', $today)
                ->where('status', '!=', 'success')
                ->distinct('ip_address')
                ->count('ip_address'),
        ];

        // Settings Values
        $settings = [
            'max_attempts' => (int) Setting::get('security_max_login_attempts', 5),
            'lockout_duration' => (int) Setting::get('security_lockout_duration', 15),
            'captcha_threshold' => (int) Setting::get('security_captcha_threshold', 3),
            'ip_max_attempts' => (int) Setting::get('security_ip_blacklist_attempts', 10),
            'session_timeout' => (int) Setting::get('security_session_timeout', 30),
            'notify_failed_login' => Setting::get('security_notify_failed_login', '1') == '1',
        ];

        return view('admin.security.index', compact('logs', 'lockedUsers', 'stats', 'settings', 'statusFilter', 'searchQuery'));
    }

    /**
     * Unlock locked user account manually.
     */
    public function unlockUser($id)
    {
        $user = User::findOrFail($id);
        $user->unlockAccount();

        return redirect()->back()->with('success', "Kunci akun {$user->name} ({$user->email}) berhasil dibuka kembali.");
    }

    /**
     * Manually block an IP address.
     */
    public function blockIp(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip',
            'duration_minutes' => 'nullable|integer|min:1|max:10080',
        ]);

        $ip = $request->ip_address;
        $minutes = (int) ($request->duration_minutes ?? 60);

        $ipBlockedKey = 'blocked_ip_' . md5($ip);
        Cache::put($ipBlockedKey, true, now()->addMinutes($minutes));
        Cache::put($ipBlockedKey . '_ttl', $minutes * 60, now()->addMinutes($minutes));

        LoginSecurityLog::create([
            'user_id' => auth()->id(),
            'identifier' => 'ADMIN_MANUAL',
            'ip_address' => $ip,
            'user_agent' => $request->userAgent(),
            'status' => 'blocked_ip',
            'failure_reason' => "IP {$ip} diblokir manual oleh Administrator (" . auth()->user()->name . ") selama {$minutes} menit.",
        ]);

        return redirect()->back()->with('success', "Alamat IP {$ip} berhasil diblokir selama {$minutes} menit.");
    }

    /**
     * Manually unblock an IP address.
     */
    public function unlockIp(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip',
        ]);

        $ip = $request->ip_address;
        $ipBlockedKey = 'blocked_ip_' . md5($ip);
        Cache::forget($ipBlockedKey);
        Cache::forget($ipBlockedKey . '_ttl');

        return redirect()->back()->with('success', "Blokir pada alamat IP {$ip} telah dibuka.");
    }

    /**
     * Update security configuration parameters.
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'security_max_login_attempts' => 'required|integer|min:2|max:20',
            'security_lockout_duration' => 'required|integer|min:1|max:1440',
            'security_captcha_threshold' => 'required|integer|min:1|max:10',
            'security_ip_blacklist_attempts' => 'required|integer|min:3|max:50',
            'security_session_timeout' => 'required|integer|min:5|max:480',
            'security_notify_failed_login' => 'nullable|boolean',
        ]);

        Setting::set('security_max_login_attempts', (string) $validated['security_max_login_attempts']);
        Setting::set('security_lockout_duration', (string) $validated['security_lockout_duration']);
        Setting::set('security_captcha_threshold', (string) $validated['security_captcha_threshold']);
        Setting::set('security_ip_blacklist_attempts', (string) $validated['security_ip_blacklist_attempts']);
        Setting::set('security_session_timeout', (string) $validated['security_session_timeout']);
        Setting::set('security_notify_failed_login', $request->has('security_notify_failed_login') ? '1' : '0');

        return redirect()->back()->with('success', 'Pengaturan parameter keamanan sistem berhasil diperbarui.');
    }
}
