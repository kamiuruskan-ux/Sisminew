<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    /**
     * Show the forgot password form for students
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle sending password reset email for students
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $request->email)->first();

        // Generate reset token
        $token = Str::random(60);

        // Store token in password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => hash('sha256', $token),
                'created_at' => Carbon::now(),
            ]
        );

        // Send email & WhatsApp notification
        try {
            $user->sendPasswordResetNotification($token);
            
            // Send WA Notification if phone exists and wa_notify_otp enabled
            if ($user->phone && \App\Models\Setting::get('wa_notify_otp', '1') == '1') {
                $schoolName = \App\Models\Setting::get('school_name', 'Sekolah');
                $resetUrl = route('reset-password', ['token' => $token]) . '?email=' . urlencode($user->email);
                $waMsg = "Reset Password - {$schoolName}\n\nHalo *{$user->name}*,\nAnda menerima pesan ini karena ada permintaan reset password untuk akun Anda.\n\nKlik tautan berikut untuk mereset password Anda:\n{$resetUrl}\n\nTautan ini berlaku selama 60 menit.";
                \App\Services\WhatsAppService::sendMessage($user->phone, $waMsg);
            }

            return back()->with('success', 'Link reset password telah dikirim ke Email & WhatsApp Anda. Link berlaku selama 60 menit.');
        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => 'Gagal mengirim email reset password. Silakan coba lagi atau hubungi administrator.',
            ])->onlyInput('email');
        }
    }

    /**
     * Show the reset password form
     */
    public function showResetForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Handle password reset
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        // Find token in database
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', hash('sha256', $request->token))
            ->first();

        if (!$resetRecord) {
            return back()->withErrors([
                'email' => 'Token reset password tidak valid.',
            ])->onlyInput('email');
        }

        // Check if token is expired (60 minutes)
        if (Carbon::parse($resetRecord->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            
            return back()->withErrors([
                'email' => 'Token reset password telah kadaluarsa.',
            ])->onlyInput('email');
        }

        // Find user and reset password
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Email tidak ditemukan.',
            ])->onlyInput('email');
        }

        // Update password
        $user->password = bcrypt($request->password);
        $user->remember_token = Str::random(60);
        $user->save();

        // Delete used token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login dengan password baru Anda.');
    }
}
