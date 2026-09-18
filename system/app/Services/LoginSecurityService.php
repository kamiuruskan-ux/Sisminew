<?php

namespace App\Services;

use App\Models\LoginSecurityLog;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class LoginSecurityService
{
    /**
     * Get maximum failed attempts allowed before locking account.
     */
    public static function getMaxAttempts(): int
    {
        return (int) Setting::get('security_max_login_attempts', 5);
    }

    /**
     * Get account lockout duration in minutes.
     */
    public static function getLockoutDuration(): int
    {
        return (int) Setting::get('security_lockout_duration', 15);
    }

    /**
     * Get CAPTCHA trigger threshold (failed attempts count).
     */
    public static function getCaptchaThreshold(): int
    {
        return (int) Setting::get('security_captcha_threshold', 3);
    }

    /**
     * Get IP failure limit before blocking IP.
     */
    public static function getIpMaxAttempts(): int
    {
        return (int) Setting::get('security_ip_blacklist_attempts', 10);
    }

    /**
     * Check if identifier or IP is locked out.
     */
    public function checkLockout(string $identifier, string $ip): array
    {
        // 1. Check IP Blacklist
        $ipBlockedKey = 'blocked_ip_' . md5($ip);
        if (Cache::has($ipBlockedKey)) {
            $seconds = (int) Cache::get($ipBlockedKey . '_ttl', 300);
            return [
                'is_locked' => true,
                'reason' => 'IP Anda (' . $ip . ') telah diblokir sementara karena aktivitas mencurigakan.',
                'seconds' => $seconds,
                'type' => 'ip',
            ];
        }

        // 2. Check User Lockout if User exists
        $user = $this->findUserByIdentifier($identifier);
        if ($user && $user->isLockedOut()) {
            $seconds = $user->getLockoutRemainingSeconds();
            $minutes = ceil($seconds / 60);
            return [
                'is_locked' => true,
                'reason' => "Akun ini telah dikunci sementara demi keamanan karena terlalu banyak percobaan login yang gagal. Silakan coba lagi dalam {$minutes} menit ({$seconds} detik).",
                'seconds' => $seconds,
                'type' => 'account',
            ];
        }

        // 3. Check IP Rate Limiter
        $ipThrottleKey = 'ip_attempts_' . md5($ip);
        if (RateLimiter::tooManyAttempts($ipThrottleKey, self::getIpMaxAttempts())) {
            $seconds = RateLimiter::availableIn($ipThrottleKey);
            return [
                'is_locked' => true,
                'reason' => "Terlalu banyak percobaan login gagal dari jaringan Anda. Silakan coba lagi dalam {$seconds} detik.",
                'seconds' => $seconds,
                'type' => 'ip_rate_limit',
            ];
        }

        return [
            'is_locked' => false,
            'reason' => '',
            'seconds' => 0,
            'type' => 'none',
        ];
    }

    /**
     * Record a failed login attempt.
     */
    public function recordFailedAttempt(string $identifier, string $ip, ?string $userAgent, ?User $user = null, string $reason = 'Password Salah'): array
    {
        if (!$user) {
            $user = $this->findUserByIdentifier($identifier);
        }

        $ipThrottleKey = 'ip_attempts_' . md5($ip);
        RateLimiter::hit($ipThrottleKey, 1800); // 30 min window for IP

        $failedCount = 1;

        if ($user) {
            $user->increment('failed_login_attempts');
            $failedCount = $user->failed_login_attempts;
            $maxAttempts = self::getMaxAttempts();

            if ($failedCount >= $maxAttempts) {
                $lockoutMinutes = self::getLockoutDuration();
                $user->update([
                    'locked_until' => now()->addMinutes($lockoutMinutes),
                ]);

                // Security log for locked account
                LoginSecurityLog::create([
                    'user_id' => $user->id,
                    'identifier' => $identifier,
                    'ip_address' => $ip,
                    'user_agent' => substr($userAgent ?? '', 0, 500),
                    'status' => 'locked_account',
                    'failure_reason' => "Akun dikunci otomatis selama {$lockoutMinutes} menit setelah {$failedCount} kali gagal.",
                ]);

                $this->sendSecurityAlertNotification($user, $ip, $failedCount);

                return [
                    'locked' => true,
                    'failed_count' => $failedCount,
                    'message' => "Akun Anda telah dikunci selama {$lockoutMinutes} menit karena terlalu banyak percobaan login yang salah.",
                    'captcha_required' => true,
                ];
            }
        }

        // Check if IP exceeds limit across accounts
        if (RateLimiter::tooManyAttempts($ipThrottleKey, self::getIpMaxAttempts())) {
            $ipBlockedKey = 'blocked_ip_' . md5($ip);
            Cache::put($ipBlockedKey, true, now()->addMinutes(30));
            Cache::put($ipBlockedKey . '_ttl', 1800, now()->addMinutes(30));

            LoginSecurityLog::create([
                'user_id' => $user?->id,
                'identifier' => $identifier,
                'ip_address' => $ip,
                'user_agent' => substr($userAgent ?? '', 0, 500),
                'status' => 'blocked_ip',
                'failure_reason' => "IP {$ip} diblokir otomatis selama 30 menit karena percobaan masif.",
            ]);
        }

        // Create log record
        LoginSecurityLog::create([
            'user_id' => $user?->id,
            'identifier' => $identifier,
            'ip_address' => $ip,
            'user_agent' => substr($userAgent ?? '', 0, 500),
            'status' => 'failed_password',
            'failure_reason' => $reason,
        ]);

        $maxAttempts = self::getMaxAttempts();
        $remaining = max(0, $maxAttempts - $failedCount);
        $captchaRequired = $this->shouldRequireCaptcha($identifier, $ip, $failedCount);

        return [
            'locked' => false,
            'failed_count' => $failedCount,
            'remaining_attempts' => $remaining,
            'message' => $reason . ". Sisa percobaan login: {$remaining} kali.",
            'captcha_required' => $captchaRequired,
        ];
    }

    /**
     * Record a successful login.
     */
    public function recordSuccessfulLogin(User $user, string $identifier, string $ip, ?string $userAgent): void
    {
        $user->recordLoginSuccess($ip);

        // Clear IP rate limiters
        $ipThrottleKey = 'ip_attempts_' . md5($ip);
        RateLimiter::clear($ipThrottleKey);

        // Log successful event
        LoginSecurityLog::create([
            'user_id' => $user->id,
            'identifier' => $identifier,
            'ip_address' => $ip,
            'user_agent' => substr($userAgent ?? '', 0, 500),
            'status' => 'success',
            'failure_reason' => null,
        ]);

        Session::forget(['captcha_code', 'captcha_attempts']);
    }

    /**
     * Check if CAPTCHA verification should be required.
     */
    public function shouldRequireCaptcha(string $identifier, string $ip, ?int $failedCount = null): bool
    {
        $threshold = self::getCaptchaThreshold();

        if ($failedCount !== null) {
            return $failedCount >= $threshold;
        }

        $user = $this->findUserByIdentifier($identifier);
        if ($user && $user->failed_login_attempts >= $threshold) {
            return true;
        }

        $ipThrottleKey = 'ip_attempts_' . md5($ip);
        if (RateLimiter::attempts($ipThrottleKey) >= $threshold) {
            return true;
        }

        return false;
    }

    /**
     * Generate dynamic SVG CAPTCHA challenge.
     */
    public function generateCaptchaSvg(): array
    {
        $num1 = rand(10, 99);
        $num2 = rand(1, 9);
        $operators = ['+', '*', '-'];
        $op = $operators[rand(0, 2)];

        if ($op === '+') {
            $answer = $num1 + $num2;
            $text = "{$num1} + {$num2} = ?";
        } elseif ($op === '-') {
            $answer = $num1 - $num2;
            $text = "{$num1} - {$num2} = ?";
        } else {
            $num1 = rand(2, 9);
            $num2 = rand(2, 9);
            $answer = $num1 * $num2;
            $text = "{$num1} x {$num2} = ?";
        }

        Session::put('captcha_code', (string) $answer);

        // Render SVG image with colorful random noise lines crossing through
        $width = 140;
        $height = 44;

        $colors = ['#ef4444', '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4'];
        
        // Generate a clean, balanced set of 2-3 colorful noise lines passing over text
        $noiseLines = '';
        
        // 2 Straight diagonal lines crossing through middle zone
        for ($i = 0; $i < 2; $i++) {
            $x1 = rand(0, 20);
            $y1 = rand(10, 34);
            $x2 = rand(120, $width);
            $y2 = rand(10, 34);
            $strokeColor = $colors[array_rand($colors)];
            $noiseLines .= "<line x1='{$x1}' y1='{$y1}' x2='{$x2}' y2='{$y2}' stroke='{$strokeColor}' stroke-width='1.8' opacity='0.6' stroke-linecap='round' />";
        }

        // 1 Smooth wavy curve crossing across the numbers
        $waveColor = $colors[array_rand($colors)];
        $yStart = rand(15, 25);
        $yMid = rand(12, 28);
        $yEnd = rand(15, 25);
        $noiseLines .= "<path d='M 0 {$yStart} Q 70 {$yMid}, 140 {$yEnd}' stroke='{$waveColor}' stroke-width='1.8' fill='none' opacity='0.6' />";

        $svg = "<svg xmlns='http://www.w3.org/2000/svg' width='{$width}' height='{$height}' viewBox='0 0 {$width} {$height}'>
            <defs>
                <linearGradient id='captchaBg' x1='0%' y1='0%' x2='100%' y2='100%'>
                    <stop offset='0%' stop-color='#f8fafc'/>
                    <stop offset='100%' stop-color='#e2e8f0'/>
                </linearGradient>
            </defs>
            <rect width='100%' height='100%' fill='url(#captchaBg)' rx='10' stroke='#cbd5e1' stroke-width='1'/>
            <!-- Text layer rendered first -->
            <text x='50%' y='52%' font-size='19' font-weight='900' font-family='Courier New, monospace, sans-serif' fill='#0f172a' text-anchor='middle' dominant-baseline='middle' letter-spacing='2'>
                {$text}
            </text>
            <!-- 3 Clean colorful noise lines rendered ON TOP of text -->
            {$noiseLines}
        </svg>";

        return [
            'svg' => 'data:image/svg+xml;base64,' . base64_encode($svg),
            'answer' => $answer,
        ];
    }

    /**
     * Verify input CAPTCHA code against session.
     */
    public function verifyCaptcha(?string $inputCode): bool
    {
        $storedCode = Session::get('captcha_code');
        if (!$storedCode || $inputCode === null) {
            return false;
        }

        return trim($inputCode) === trim((string) $storedCode);
    }

    /**
     * Send WhatsApp / Email alert on account lockout.
     */
    protected function sendSecurityAlertNotification(User $user, string $ip, int $failedCount): void
    {
        if (Setting::get('security_notify_failed_login', '1') != '1') {
            return;
        }

        $schoolName = Setting::get('school_name', 'Sekolah');
        $timeStr = now()->translatedFormat('d F Y H:i:s');
        $msg = "ALERT KEAMANAN - {$schoolName}\n\nHalo *{$user->name}*,\nSistem mendeteksi {$failedCount} kali percobaan login gagal berturut-turut pada akun Anda.\n\nWaktu: {$timeStr}\nIP Address: {$ip}\nStatus: Akun Anda telah DIKUNKI sementara selama " . self::getLockoutDuration() . " menit demi keamanan.\n\nJika ini bukan Anda, segera hubungi Administrator Sekolah.";

        // Send via WhatsApp if phone available
        if ($user->phone && WhatsAppService::getActiveProvider() !== 'disabled') {
            try {
                WhatsAppService::sendMessage($user->phone, $msg);
            } catch (\Exception $e) {
                \Log::error('Security Alert WA error: ' . $e->getMessage());
            }
        }
    }

    /**
     * Helper to find User model by Email, Phone Number (HP/WA), NISN, or NIP.
     */
    public function findUserByIdentifier(string $identifier): ?User
    {
        $clean = trim($identifier);

        if (empty($clean)) {
            return null;
        }

        // 1. Direct Email match
        if (filter_var($clean, FILTER_VALIDATE_EMAIL)) {
            return User::where('email', $clean)->first();
        }

        // 2. Try searching by Phone Number (Nomor HP / WhatsApp)
        $digits = preg_replace('/[^0-9]/', '', $clean);
        if (strlen($digits) >= 8) {
            $phoneVariations = [$clean, $digits];
            if (str_starts_with($digits, '0')) {
                $phoneVariations[] = '62' . substr($digits, 1);
                $phoneVariations[] = '+62' . substr($digits, 1);
            } elseif (str_starts_with($digits, '62')) {
                $phoneVariations[] = '0' . substr($digits, 2);
                $phoneVariations[] = '+' . $digits;
            }

            $phoneVariations = array_values(array_unique($phoneVariations));

            // Check User model by phone
            $userByPhone = User::whereIn('phone', $phoneVariations)->first();
            if ($userByPhone) {
                return $userByPhone;
            }

            // Check Student model by phone or parent_phone
            $studentByPhone = \App\Models\Student::whereIn('phone', $phoneVariations)
                ->orWhereIn('parent_phone', $phoneVariations)
                ->first();
            if ($studentByPhone && $studentByPhone->user) {
                return $studentByPhone->user;
            }
        }

        // 3. Try searching NISN via Student relation
        $student = \App\Models\Student::where('nisn', $clean)->first();
        if ($student && $student->user) {
            return $student->user;
        }

        // 4. Try searching by NIP or email
        return User::where('nip', $clean)->orWhere('email', $clean)->first();
    }
}
