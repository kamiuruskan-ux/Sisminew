<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\TestEmailMail;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    /**
     * Show email settings form
     */
    public function index()
    {
        return redirect()->route('admin.settings', ['tab' => 'email']);
    }

    /**
     * Update email settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'mail_mailer' => 'required|in:smtp,mail,sendmail',
            'mail_host' => 'required_if:mail_mailer,smtp',
            'mail_port' => 'required_if:mail_mailer,smtp|numeric',
            'mail_username' => 'nullable',
            'mail_password' => 'nullable',
            'mail_encryption' => 'nullable|in:tls,ssl',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
        ]);

        // Save to .env file
        $envSettings = [
            'MAIL_MAILER' => $validated['mail_mailer'],
            'MAIL_HOST' => $validated['mail_host'] ?? '',
            'MAIL_PORT' => $validated['mail_port'] ?? '',
            'MAIL_USERNAME' => $validated['mail_username'] ?? '',
            'MAIL_PASSWORD' => $validated['mail_password'] ?? '',
            'MAIL_ENCRYPTION' => $validated['mail_encryption'] ?? '',
            'MAIL_FROM_ADDRESS' => $validated['mail_from_address'],
            'MAIL_FROM_NAME' => $validated['mail_from_name'],
        ];

        // Save to settings table for display
        foreach ($envSettings as $key => $value) {
            Setting::set('email_' . strtolower($key), $value);
        }

        // Update .env file
        $this->updateEnvFile($envSettings);

        return back()->with('success', 'Pengaturan email berhasil disimpan.');
    }

    /**
     * Send test email
     */
    public function sendTest(Request $request)
    {
        $validated = $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            $mailer = Setting::get('email_mail_mailer', 'smtp');
            $host = Setting::get('email_mail_host', 'smtp.gmail.com');
            $port = Setting::get('email_mail_port', 587);
            $username = Setting::get('email_mail_username');
            $password = Setting::get('email_mail_password');
            $encryption = Setting::get('email_mail_encryption', 'tls');
            $fromAddress = Setting::get('email_mail_from_address', $username);
            $fromName = Setting::get('email_mail_from_name', Setting::get('school_name', config('app.name')));

            if (empty($username) || empty($password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal: SMTP Username atau Password masih kosong di pengaturan.'
                ], 422);
            }

            if (str_contains(strtolower($host ?? ''), 'gmail')) {
                $password = str_replace(' ', '', $password);
            }

            config([
                'mail.default' => $mailer ?: 'smtp',
                'mail.mailers.smtp.transport' => 'smtp',
                'mail.mailers.smtp.host' => $host ?: 'smtp.gmail.com',
                'mail.mailers.smtp.port' => (int) ($port ?: 587),
                'mail.mailers.smtp.encryption' => ($encryption === 'none' || empty($encryption)) ? null : $encryption,
                'mail.mailers.smtp.username' => $username,
                'mail.mailers.smtp.password' => $password,
                'mail.from.address' => $fromAddress ?: $username,
                'mail.from.name' => $fromName ?: config('app.name'),
            ]);

            \Illuminate\Support\Facades\Mail::purge();

            Mail::to($validated['test_email'])
                ->send(new TestEmailMail(['to' => $validated['test_email']]));

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Email test berhasil dikirim ke ' . $validated['test_email']
                ]);
            }

            return back()->with('success', 'Email test berhasil dikirim ke ' . $validated['test_email']);
        } catch (\Exception $e) {
            \Log::error('Email test failed: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengirim email: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Gagal mengirim email: ' . $e->getMessage());
        }
    }

    /**
     * Update .env file
     */
    private function updateEnvFile($settings)
    {
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        foreach ($settings as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}={$value}";
            
            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envFile, $envContent);
        
        // Clear config cache
        \Artisan::call('config:clear');
    }
}
