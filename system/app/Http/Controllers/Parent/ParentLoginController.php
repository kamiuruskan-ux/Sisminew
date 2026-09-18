<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class ParentLoginController extends Controller
{
    /**
     * Show parent login form.
     */
    public function showLoginForm(Request $request, \App\Services\LoginSecurityService $securityService)
    {
        if (session()->has('parent_student_id')) {
            return redirect()->route('parent.dashboard');
        }

        $ip = $request->ip();
        $identifier = (string) old('nisn', '');
        $showCaptcha = $securityService->shouldRequireCaptcha($identifier, $ip) || session('show_captcha');

        $captchaSvg = null;
        if ($showCaptcha) {
            $captchaData = $securityService->generateCaptchaSvg();
            $captchaSvg = $captchaData['svg'];
        }

        return view('parent.login', compact('showCaptcha', 'captchaSvg'));
    }

    /**
     * Authenticate parent using Student NISN and Birth Date / Phone.
     */
    public function login(Request $request, \App\Services\LoginSecurityService $securityService)
    {
        $request->validate([
            'nisn' => 'required|string',
            'birth_date' => 'required|date',
            'captcha_code' => 'nullable|string',
        ], [
            'nisn.required' => 'NISN wajib diisi.',
            'birth_date.required' => 'Tanggal lahir siswa wajib diisi.',
            'birth_date.date' => 'Format tanggal lahir tidak valid.',
        ]);

        $nisn = trim($request->nisn);
        $birthDate = $request->birth_date;
        $ip = $request->ip();
        $userAgent = $request->userAgent();

        // 1. Check IP or Account Lockout
        $lockoutStatus = $securityService->checkLockout("PARENT_{$nisn}", $ip);
        if ($lockoutStatus['is_locked']) {
            return back()->withInput()->with('error', $lockoutStatus['reason']);
        }

        // 2. Check CAPTCHA if required
        $showCaptcha = $securityService->shouldRequireCaptcha("PARENT_{$nisn}", $ip) || session('show_captcha');
        if ($showCaptcha) {
            if (!$securityService->verifyCaptcha($request->input('captcha_code'))) {
                $failedResult = $securityService->recordFailedAttempt("PARENT_{$nisn}", $ip, $userAgent, null, 'Tantangan CAPTCHA Orang Tua Salah');
                $captchaData = $securityService->generateCaptchaSvg();

                return back()->withInput()->withErrors([
                    'captcha_code' => 'Jawaban CAPTCHA yang Anda masukkan tidak tepat.',
                ])->with([
                    'error' => $failedResult['message'],
                    'show_captcha' => true,
                    'captcha_svg' => $captchaData['svg'],
                ]);
            }
        }

        $student = Student::with(['user', 'class', 'major'])
            ->where('nisn', $nisn)
            ->whereDate('birth_date', $birthDate)
            ->first();

        if (!$student) {
            $failedResult = $securityService->recordFailedAttempt("PARENT_{$nisn}", $ip, $userAgent, null, 'Kombinasi NISN/Tgl Lahir Orang Tua Salah');

            $redirect = back()->withInput()->with('error', $failedResult['message']);
            if ($failedResult['captcha_required']) {
                $captchaData = $securityService->generateCaptchaSvg();
                $redirect->with([
                    'show_captcha' => true,
                    'captcha_svg' => $captchaData['svg'],
                ]);
            }

            return $redirect;
        }

        if ($student->user) {
            $securityService->recordSuccessfulLogin($student->user, "PARENT_{$nisn}", $ip, $userAgent);
        }

        // Check if OTP via WA or Email is enabled
        $otpViaWa = (\App\Models\Setting::get('wa_notify_otp', '1') == '1' && \App\Services\WhatsAppService::getActiveProvider() !== 'disabled') && ($student->parent_phone ?? $student->phone);
        $otpViaEmail = (\App\Models\Setting::get('email_notify_otp', '0') == '1') && $student->user->email;

        if ($otpViaWa || $otpViaEmail) {
            $otp = rand(100000, 999999);

            session([
                'parent_otp' => $otp,
                'parent_otp_student_id' => $student->id,
                'parent_otp_expires' => now()->addMinutes(10),
            ]);

            $methods = [];

            if ($otpViaWa) {
                $phone = $student->parent_phone ?? $student->phone;
                $schoolName = \App\Models\Setting::get('school_name', 'Sekolah');
                $msg = "Kode OTP Portal Orang Tua - {$schoolName}\n\nHalo Bpk/Ibu Orang Tua dari *{$student->user->name}*,\nKode OTP Anda untuk masuk ke Portal Orang Tua adalah: *{$otp}*\n\nKode berlaku selama 10 menit. Jangan bagikan kode ini kepada siapapun.";
                \App\Services\WhatsAppService::sendMessage($phone, $msg);
                $methods[] = 'WhatsApp (' . substr($phone, 0, 4) . '****' . substr($phone, -3) . ')';
            }

            if ($otpViaEmail) {
                try {
                    \Illuminate\Support\Facades\Mail::to($student->user->email)->send(new \App\Mail\OtpNotificationMail($student->user, $otp, 'Orang Tua'));
                    $parts = explode('@', $student->user->email);
                    $maskedName = substr($parts[0], 0, 2) . str_repeat('*', max(1, strlen($parts[0]) - 2));
                    $maskedEmail = $maskedName . '@' . ($parts[1] ?? '');
                    $methods[] = 'Email (' . $maskedEmail . ')';
                } catch (\Exception $e) {
                    \Log::error('Gagal mengirim email OTP orang tua: ' . $e->getMessage());
                }
            }

            if (count($methods) > 0) {
                $methodText = implode(' dan ', $methods);
                return back()->with('success_otp', "Kode OTP 6-digit telah dikirimkan ke {$methodText}. Masukkan kode untuk login.");
            }
        }

        // Store student ID in session if OTP is disabled or no phone available
        session([
            'parent_student_id' => $student->id,
            'parent_logged_in' => true,
        ]);

        return redirect()->route('parent.dashboard')->with('success', 'Selamat datang di Portal Orang Tua ' . $student->user->name);
    }

    /**
     * Send OTP via WhatsApp to parent.
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string',
        ], [
            'nisn.required' => 'NISN wajib diisi.',
        ]);

        $nisn = trim($request->nisn);
        $throttleKey = 'send-parent-otp|' . $nisn . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withInput()->with('error', "Silakan tunggu {$seconds} detik sebelum meminta kode OTP kembali.");
        }

        $student = Student::with('user')->where('nisn', $nisn)->first();

        if (!$student) {
            return back()->withInput()->with('error', 'NISN tidak ditemukan.');
        }

        RateLimiter::hit($throttleKey, 60);

        $phone = $student->parent_phone ?? $student->phone;
        if (!$phone) {
            return back()->withInput()->with('error', 'Nomor WhatsApp Orang Tua belum terdaftar pada data siswa ini.');
        }

        $otp = rand(100000, 999999);

        session([
            'parent_otp' => $otp,
            'parent_otp_student_id' => $student->id,
            'parent_otp_expires' => now()->addMinutes(10),
        ]);

        $schoolName = \App\Models\Setting::get('school_name', 'Sekolah');
        $msg = "Kode OTP Portal Orang Tua - {$schoolName}\n\nHalo Bpk/Ibu Orang Tua dari *{$student->user->name}*,\nKode OTP untuk login ke Portal Orang Tua Anda adalah: *{$otp}*\n\nKode berlaku selama 10 menit. Jangan bagikan kode ini kepada siapapun.";

        $result = \App\Services\WhatsAppService::sendMessage($phone, $msg);

        if ($result['success']) {
            return back()->with('success_otp', 'Kode OTP 6-digit telah dikirim ke WhatsApp ' . substr($phone, 0, 4) . '****' . substr($phone, -3) . '. Masukkan kode untuk login.');
        }

        return back()->withInput()->with('error', 'Gagal mengirimkan OTP via WhatsApp: ' . $result['message']);
    }

    /**
     * Verify OTP code and login parent.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|size:6',
        ]);

        $studentId = session('parent_otp_student_id');
        $throttleKey = 'verify-parent-otp|' . ($studentId ?? $request->ip()) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->with('error', "Terlalu banyak percobaan verifikasi OTP. Silakan coba lagi dalam {$seconds} detik.");
        }

        $storedOtp = session('parent_otp');
        $expiresAt = session('parent_otp_expires');

        if (!$storedOtp || !$studentId || now()->greaterThan($expiresAt)) {
            RateLimiter::hit($throttleKey, 60);
            return back()->with('error', 'Kode OTP sudah kadaluarsa atau tidak valid. Silakan minta kode OTP baru.');
        }

        if (trim($request->otp_code) != $storedOtp) {
            RateLimiter::hit($throttleKey, 60);
            return back()->with('error', 'Kode OTP yang Anda masukkan salah.');
        }

        $student = Student::with('user')->find($studentId);

        RateLimiter::clear($throttleKey);

        session([
            'parent_student_id' => $student->id,
            'parent_logged_in' => true,
        ]);

        session()->forget(['parent_otp', 'parent_otp_student_id', 'parent_otp_expires']);

        return redirect()->route('parent.dashboard')->with('success', 'Selamat datang di Portal Orang Tua ' . $student->user->name);
    }

    /**
     * Logout parent session.
     */
    public function logout(Request $request)
    {
        session()->forget(['parent_student_id', 'parent_logged_in', 'parent_otp', 'parent_otp_student_id', 'parent_otp_expires']);
        return redirect()->route('parent.login')->with('success', 'Anda telah keluar dari Portal Orang Tua.');
    }
}
