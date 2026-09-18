<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Student;
use App\Models\User;
use App\Services\WhatsAppService;
use App\Services\LoginSecurityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    protected function isWaOtpEnabled(): bool
    {
        return Setting::get('wa_notify_otp', '1') == '1' 
            && WhatsAppService::getActiveProvider() !== 'disabled';
    }

    /**
     * Helper to check if Email OTP is globally enabled in Settings.
     */
    protected function isEmailOtpEnabled(): bool
    {
        return Setting::get('email_notify_otp', '0') == '1';
    }

    /**
     * Show admin login form (redirects to combined student/staff login)
     */
    public function showAdminLoginForm()
    {
        return redirect()->route('login');
    }

    /**
     * Handle admin/staff login (redirects to combined student/staff login)
     */
    public function adminLogin(Request $request)
    {
        return redirect()->route('login');
    }

    /**
     * Show Admin OTP Verification form.
     */
    public function showAdminOtpForm()
    {
        if (!session('admin_otp') || !session('admin_otp_user_id')) {
            return redirect()->route('login');
        }
        return view('auth.student-login');
    }

    /**
     * Verify Admin OTP Code.
     */
    public function verifyAdminOtp(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|size:6',
        ]);

        $userId = session('admin_otp_user_id');
        $throttleKey = 'verify-admin-otp|' . ($userId ?? $request->ip()) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->with('error', "Terlalu banyak percobaan verifikasi OTP. Silakan coba lagi dalam {$seconds} detik.");
        }

        $storedOtp = session('admin_otp');
        $remember = session('admin_otp_remember', false);
        $expiresAt = session('admin_otp_expires');

        if (!$storedOtp || !$userId || now()->greaterThan($expiresAt)) {
            RateLimiter::hit($throttleKey, 60);
            return redirect()->route('login')->with('error', 'Kode OTP telah kadaluarsa atau tidak valid. Silakan login kembali.');
        }

        if (trim($request->otp_code) != $storedOtp) {
            RateLimiter::hit($throttleKey, 60);
            return back()->with('error', 'Kode OTP yang Anda masukkan salah.');
        }

        $user = User::with('roles')->find($userId);
        if (!$user) {
            return redirect()->route('login')->with('error', 'Pengguna tidak ditemukan.');
        }

        RateLimiter::clear($throttleKey);

        Auth::login($user, $remember);
        $request->session()->regenerate();
        session()->forget(['admin_otp', 'admin_otp_user_id', 'admin_otp_remember', 'admin_otp_expires']);

        return $this->redirectAdminByRole($user);
    }

    /**
     * Redirect Admin user to appropriate dashboard by role.
     */
    protected function redirectAdminByRole(User $user)
    {
        if ($user->hasRole('kantin')) {
            return redirect()->route('canteen.vendor.dashboard')->with('success', 'Selamat datang di Panel Vendor Kantin, ' . $user->name . '!');
        }

        // Deteksi apakah user login menggunakan peramban mobile (smartphone/tablet)
        $userAgent = request()->header('User-Agent', '');
        $isMobile = preg_match('/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos|iphone|ipad|ipod)/i', $userAgent);

        if ($isMobile) {
            // Pengguna Guru, Staff, Kepala Sekolah, atau Admin diarahkan langsung ke Mobile Portal PWA
            if ($user->hasRole('guru') || $user->hasRole('staff') || $user->hasRole('kepala-sekolah') || $user->hasRole('admin') || $user->hasRole('super-admin')) {
                return redirect()->route('admin.teacher-attendances.mobile')->with('success', 'Selamat datang di Portal Mobile Asatidzah & Pegawai, ' . $user->name . '!');
            }
        }

        if ($user->hasRole('guru')) {
            return redirect()->route('admin.dashboard')->with('success', 'Selamat datang, ' . $user->name . '! Anda telah berhasil login.');
        }

        return redirect()->route('admin.dashboard')->with('success', 'Selamat datang, ' . $user->name . '! Anda telah berhasil login.');
    }

    /**
     * Show student login form
     */
    public function showStudentLoginForm(Request $request, LoginSecurityService $securityService)
    {
        $ip = $request->ip();
        $identifier = (string) old('email', '');
        $showCaptcha = $securityService->shouldRequireCaptcha($identifier, $ip) || session('show_captcha');

        $captchaSvg = null;
        if ($showCaptcha) {
            $captchaData = $securityService->generateCaptchaSvg();
            $captchaSvg = $captchaData['svg'];
        }

        return view('auth.student-login', compact('showCaptcha', 'captchaSvg'));
    }

    /**
     * Handle student & staff login via Email/NISN/Username & Password
     */
    public function studentLogin(Request $request, LoginSecurityService $securityService)
    {
        $credentials = $request->validate([
            'email' => ['required'],
            'password' => ['required'],
            'captcha_code' => ['nullable', 'string'],
        ]);

        $loginInput = trim($credentials['email']);
        $ip = $request->ip();
        $userAgent = $request->userAgent();

        // 1. Check IP or Account Lockout
        $lockoutStatus = $securityService->checkLockout($loginInput, $ip);
        if ($lockoutStatus['is_locked']) {
            return back()->withErrors([
                'email' => $lockoutStatus['reason'],
            ])->onlyInput('email');
        }

        // 2. Check CAPTCHA if required
        $showCaptcha = $securityService->shouldRequireCaptcha($loginInput, $ip) || session('show_captcha');
        if ($showCaptcha) {
            if (!$securityService->verifyCaptcha($request->input('captcha_code'))) {
                $failedResult = $securityService->recordFailedAttempt($loginInput, $ip, $userAgent, null, 'Tantangan CAPTCHA Salah');
                $captchaData = $securityService->generateCaptchaSvg();

                return back()->withErrors([
                    'captcha_code' => 'Jawaban CAPTCHA yang Anda masukkan tidak tepat.',
                    'email' => $failedResult['message'],
                ])->with([
                    'show_captcha' => true,
                    'captcha_svg' => $captchaData['svg'],
                ])->onlyInput('email');
            }
        }

        // 3. Find User
        $user = $securityService->findUserByIdentifier($loginInput);

        // 4. Validate Password
        if ($user && Hash::check($credentials['password'], $user->password)) {
            // Verify if user account is disabled or locked
            if ($user->status !== 'active') {
                return back()->withErrors([
                    'email' => 'Akun Anda berstatus non-aktif. Silakan hubungi Administrator.',
                ])->onlyInput('email');
            }

            if ($user->isLockedOut()) {
                $seconds = $user->getLockoutRemainingSeconds();
                $minutes = ceil($seconds / 60);
                return back()->withErrors([
                    'email' => "Akun ini sedang dikunci sementara karena keamanan. Silakan coba lagi dalam {$minutes} menit.",
                ])->onlyInput('email');
            }

            $user->load('roles');

            $isStudent = $user->hasRole('student') || $user->hasRole('calon-siswa');
            $isStaff = !$isStudent;

            // Record Login Success
            $securityService->recordSuccessfulLogin($user, $loginInput, $ip, $userAgent);

            if ($isStaff) {
                // Check if OTP via WA or Email is enabled
                $otpViaWa = $this->isWaOtpEnabled() && $user->phone;
                $otpViaEmail = $this->isEmailOtpEnabled() && $user->email;

                if ($otpViaWa || $otpViaEmail) {
                    $otp = rand(100000, 999999);
                    session([
                        'admin_otp' => $otp,
                        'admin_otp_user_id' => $user->id,
                        'admin_otp_remember' => $request->boolean('remember'),
                        'admin_otp_expires' => now()->addMinutes(10),
                    ]);

                    $methods = [];

                    if ($otpViaWa) {
                        $phone = $user->phone;
                        $schoolName = Setting::get('school_name', 'Sekolah');
                        $msg = "Kode OTP Login Admin/Staf - {$schoolName}\n\nHalo *{$user->name}*,\nKode OTP Anda untuk masuk ke sistem adalah: *{$otp}*\n\nKode berlaku selama 10 menit. Jangan bagikan kode ini kepada siapapun.";
                        WhatsAppService::sendMessage($phone, $msg);
                        $methods[] = 'WhatsApp (' . substr($phone, 0, 4) . '****' . substr($phone, -3) . ')';
                    }

                    if ($otpViaEmail) {
                        try {
                            Mail::to($user->email)->send(new \App\Mail\OtpNotificationMail($user, $otp, 'Admin/Staf'));
                            $parts = explode('@', $user->email);
                            $maskedName = substr($parts[0], 0, 2) . str_repeat('*', max(1, strlen($parts[0]) - 2));
                            $maskedEmail = $maskedName . '@' . ($parts[1] ?? '');
                            $methods[] = 'Email (' . $maskedEmail . ')';
                        } catch (\Exception $e) {
                            \Log::error('Gagal mengirim email OTP: ' . $e->getMessage());
                        }
                    }

                    if (count($methods) > 0) {
                        $methodText = implode(' dan ', $methods);
                        return redirect()->route('admin.verify-otp')->with('success_otp', "Kode OTP 6-digit telah dikirimkan ke {$methodText}.");
                    }
                }

                // Direct login if OTP disabled or phone empty
                Auth::login($user, $request->boolean('remember'));
                $request->session()->regenerate();

                return $this->redirectAdminByRole($user);
            } else {
                // Student login flow
                return $this->processStudentLogin($request, $user);
            }
        }

        // 5. Failed Password Attempt
        $failedResult = $securityService->recordFailedAttempt($loginInput, $ip, $userAgent, $user, 'Password Salah');

        $redirect = back()->withErrors(['email' => $failedResult['message']])->onlyInput('email');

        if ($failedResult['captcha_required']) {
            $captchaData = $securityService->generateCaptchaSvg();
            $redirect->with([
                'show_captcha' => true,
                'captcha_svg' => $captchaData['svg'],
            ]);
        }

        return $redirect;
    }

    /**
     * Handle student login via NISN & Tanggal Lahir
     */
    public function studentLoginNisn(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string',
            'birth_date' => 'required|date',
        ], [
            'nisn.required' => 'NISN wajib diisi.',
            'birth_date.required' => 'Tanggal lahir siswa wajib diisi.',
            'birth_date.date' => 'Format tanggal lahir tidak valid.',
        ]);

        $nisn = trim($request->nisn);
        $throttleKey = Str::transliterate($nisn . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'nisn' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
            ])->withInput();
        }

        $student = Student::with('user')
            ->where('nisn', $nisn)
            ->whereDate('birth_date', $request->birth_date)
            ->first();

        if (!$student || !$student->user) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withInput()->withErrors([
                'nisn' => 'Kombinasi NISN dan Tanggal Lahir siswa tidak ditemukan. Periksa kembali data Anda.',
            ]);
        }

        RateLimiter::clear($throttleKey);

        $user = $student->user->load('roles');

        return $this->processStudentLogin($request, $user, $student);
    }

    /**
     * Process student login (with OTP check or direct auth)
     */
    protected function processStudentLogin(Request $request, User $user, ?Student $student = null)
    {
        if (!$student) {
            $student = $user->student;
        }

        $otpViaWa = $this->isWaOtpEnabled() && ($student?->phone ?? $user->phone ?? $student?->parent_phone);
        $otpViaEmail = $this->isEmailOtpEnabled() && $user->email;

        if ($otpViaWa || $otpViaEmail) {
            $otp = rand(100000, 999999);
            session([
                'student_otp' => $otp,
                'student_otp_user_id' => $user->id,
                'student_otp_remember' => $request->boolean('remember'),
                'student_otp_expires' => now()->addMinutes(10),
            ]);

            $methods = [];

            if ($otpViaWa) {
                $phone = $student?->phone ?? $user->phone ?? $student?->parent_phone;
                $schoolName = Setting::get('school_name', 'Sekolah');
                $msg = "Kode OTP Portal Siswa - {$schoolName}\n\nHalo *{$user->name}*,\nKode OTP untuk masuk ke Portal Siswa Anda adalah: *{$otp}*\n\nKode berlaku selama 10 menit. Jangan bagikan kode ini kepada siapapun.";
                WhatsAppService::sendMessage($phone, $msg);
                $methods[] = 'WhatsApp (' . substr($phone, 0, 4) . '****' . substr($phone, -3) . ')';
            }

            if ($otpViaEmail) {
                try {
                    Mail::to($user->email)->send(new \App\Mail\OtpNotificationMail($user, $otp, 'Siswa'));
                    $parts = explode('@', $user->email);
                    $maskedName = substr($parts[0], 0, 2) . str_repeat('*', max(1, strlen($parts[0]) - 2));
                    $maskedEmail = $maskedName . '@' . ($parts[1] ?? '');
                    $methods[] = 'Email (' . $maskedEmail . ')';
                } catch (\Exception $e) {
                    \Log::error('Gagal mengirim email OTP siswa: ' . $e->getMessage());
                }
            }

            if (count($methods) > 0) {
                $methodText = implode(' dan ', $methods);
                return redirect()->route('student.verify-otp')->with('success_otp', "Kode OTP 6-digit telah dikirimkan ke {$methodText}.");
            }
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        if ($user->hasRole('student')) {
            return redirect()->route('student.dashboard')->with('success', 'Selamat datang, ' . $user->name . '! Anda telah berhasil login.');
        } else {
            return redirect()->route('spmb.dashboard.index')->with('success', 'Selamat datang, ' . $user->name . '! Anda telah berhasil login.');
        }
    }

    /**
     * Show Student OTP Verification form.
     */
    public function showStudentOtpForm()
    {
        if (!session('student_otp') || !session('student_otp_user_id')) {
            return redirect()->route('login');
        }
        return view('auth.student-login');
    }

    /**
     * Verify Student OTP Code.
     */
    public function verifyStudentOtp(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|size:6',
        ]);

        $userId = session('student_otp_user_id');
        $throttleKey = 'verify-student-otp|' . ($userId ?? $request->ip()) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->with('error', "Terlalu banyak percobaan verifikasi OTP. Silakan coba lagi dalam {$seconds} detik.");
        }

        $storedOtp = session('student_otp');
        $remember = session('student_otp_remember', false);
        $expiresAt = session('student_otp_expires');

        if (!$storedOtp || !$userId || now()->greaterThan($expiresAt)) {
            RateLimiter::hit($throttleKey, 60);
            return redirect()->route('login')->with('error', 'Kode OTP telah kadaluarsa atau tidak valid. Silakan login kembali.');
        }

        if (trim($request->otp_code) != $storedOtp) {
            RateLimiter::hit($throttleKey, 60);
            return back()->with('error', 'Kode OTP yang Anda masukkan salah.');
        }

        $user = User::with('roles')->find($userId);
        if (!$user) {
            return redirect()->route('login')->with('error', 'Akun tidak ditemukan.');
        }

        RateLimiter::clear($throttleKey);

        Auth::login($user, $remember);
        $request->session()->regenerate();
        session()->forget(['student_otp', 'student_otp_user_id', 'student_otp_remember', 'student_otp_expires']);

        if ($user->hasRole('student')) {
            return redirect()->route('student.dashboard')->with('success', 'Selamat datang, ' . $user->name . '! Anda telah berhasil login.');
        } else {
            return redirect()->route('spmb.dashboard.index')->with('success', 'Selamat datang, ' . $user->name . '! Anda telah berhasil login.');
        }
    }

    /**
     * Legacy login redirect
     */
    public function showLoginForm()
    {
        return redirect()->route('login');
    }

    public function login(Request $request)
    {
        return redirect()->route('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }

    public function logoutConfirm()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Anda telah logout. Silakan login kembali untuk mengakses sistem.');
    }
}
