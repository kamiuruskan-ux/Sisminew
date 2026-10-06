<?php

namespace App\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Gate;
use Hashids\Hashids;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Contracts\AttendanceRepositoryInterface::class,
            \App\Repositories\TeacherAttendanceRepository::class
        );

        $parentDir = dirname($this->app->basePath());
        if (file_exists($parentDir . '/img') && file_exists($parentDir . '/index.php')) {
            $this->app->usePublicPath($parentDir);
            config([
                'filesystems.disks.public.root' => $parentDir . DIRECTORY_SEPARATOR . 'img',
                'filesystems.disks.public.url'  => url('img'),
                'filesystems.disks.doc.root'    => $parentDir . DIRECTORY_SEPARATOR . 'doc',
                'filesystems.disks.doc.url'     => url('doc'),
            ]);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force Tailwind Pagination View
        \Illuminate\Pagination\Paginator::useTailwind();

        // Fix for MySQL key length error
        Schema::defaultStringLength(191);

        // Register Gate Authorization & Blade Permission Helper
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('super-admin')) {
                return true;
            }
            if (method_exists($user, 'hasPermission')) {
                return $user->hasPermission($ability) ? true : null;
            }
        });

        Blade::if('permission', function ($permission) {
            return auth()->check() && auth()->user()->hasPermission($permission);
        });

        // Global Dynamic Timezone Configuration from Admin Setting
        try {
            if (Schema::hasTable('settings')) {
                $timezone = \App\Models\Setting::get('app_timezone', config('app.timezone', 'Asia/Jakarta'));
                if (!empty($timezone)) {
                    date_default_timezone_set($timezone);
                    config(['app.timezone' => $timezone]);
                }

                // Global Dynamic Mail / SMTP Configuration from Admin Setting
                $mailMailer = \App\Models\Setting::get('email_mail_mailer', 'smtp');
                $mailHost = \App\Models\Setting::get('email_mail_host', config('mail.mailers.smtp.host', 'smtp.gmail.com'));
                $mailPort = \App\Models\Setting::get('email_mail_port', config('mail.mailers.smtp.port', 587));
                $mailUsername = \App\Models\Setting::get('email_mail_username', config('mail.mailers.smtp.username'));
                $mailPassword = \App\Models\Setting::get('email_mail_password', config('mail.mailers.smtp.password'));
                $mailEncryption = \App\Models\Setting::get('email_mail_encryption', config('mail.mailers.smtp.encryption', 'tls'));
                $mailFromAddress = \App\Models\Setting::get('email_mail_from_address', config('mail.from.address'));
                $mailFromName = \App\Models\Setting::get('email_mail_from_name', config('mail.from.name'));

                if (!empty($mailUsername) && !empty($mailPassword)) {
                    // Clean spaces if Google App Password was copied with spaces
                    if (str_contains(strtolower($mailHost ?? ''), 'gmail')) {
                        $mailPassword = str_replace(' ', '', $mailPassword);
                    }

                    config([
                        'mail.default' => $mailMailer ?: 'smtp',
                        'mail.mailers.smtp.transport' => 'smtp',
                        'mail.mailers.smtp.host' => $mailHost ?: 'smtp.gmail.com',
                        'mail.mailers.smtp.port' => (int) ($mailPort ?: 587),
                        'mail.mailers.smtp.encryption' => ($mailEncryption === 'none' || empty($mailEncryption)) ? null : $mailEncryption,
                        'mail.mailers.smtp.username' => $mailUsername,
                        'mail.mailers.smtp.password' => $mailPassword,
                        'mail.from.address' => $mailFromAddress ?: $mailUsername,
                        'mail.from.name' => $mailFromName ?: config('app.name'),
                    ]);
                }
            }
        } catch (\Throwable $e) {
            date_default_timezone_set(config('app.timezone', 'Asia/Jakarta'));
        }

        // Auto-migrate employee_permits table if not yet present in database
        try {
            if (!Schema::hasTable('employee_permits')) {
                \Illuminate\Support\Facades\Artisan::call('migrate', [
                    '--path' => 'database/migrations/2026_09_19_000005_create_employee_permits_table.php',
                    '--force' => true,
                ]);
            }
        } catch (\Throwable $e) {
            // Failsafe: avoid breaking if DB has connection delays
        }

        // Set Carbon & System locale to Indonesian
        \Carbon\Carbon::setLocale('id');
        setlocale(LC_TIME, 'id_ID.utf8', 'id_ID', 'id', 'indonesian');

        // Blade directive for encoding IDs
        Blade::directive('encode', function ($expression) {
            return "<?php echo (new Hashids(config('hashids.connections.main.salt'), (int) config('hashids.connections.main.length')))->encode($expression); ?>";
        });

        // Blade directives for Indonesian Date & Day formatting
        Blade::directive('tanggal', function ($expression) {
            return "<?php echo tanggal_indo($expression); ?>";
        });

        Blade::directive('tanggal_indo', function ($expression) {
            return "<?php echo tanggal_indo($expression); ?>";
        });

        Blade::directive('hari', function ($expression) {
            return "<?php echo hari_indo($expression); ?>";
        });

        Blade::directive('bulan', function ($expression) {
            return "<?php echo bulan_indo($expression); ?>";
        });

        Blade::directive('waktu_lalu', function ($expression) {
            return "<?php echo waktu_lalu($expression); ?>";
        });

        // View Composer: Inject pending order count to canteen-vendor layout globally
        View::composer('layouts.canteen-vendor', function ($view) {
            try {
                if (Schema::hasTable('canteen_orders')) {
                    $count = \App\Models\CanteenOrder::where('order_status', 'pending')->count();
                } else {
                    $count = 0;
                }
            } catch (\Throwable $e) {
                $count = 0;
            }
            $view->with('vendorPendingOrderCount', $count);
        });

        // View Composer: Inject active master subjects to all views globally
        View::composer('*', function ($view) {
            try {
                if (Schema::hasTable('subjects')) {
                    $globalSubjects = \App\Models\Subject::where('is_active', true)
                        ->orderBy('order')
                        ->orderBy('name')
                        ->get();
                    $view->with('globalSubjects', $globalSubjects);
                }
            } catch (\Throwable $e) {
                // Table might not exist during initial migration
            }
        });

        // Failsafe License Integrity Check (Secondary Defense Layer)
        if (!class_exists(\App\Services\LicenseManager::class) || !class_exists(\App\Http\Middleware\CheckLicenseMiddleware::class)) {
            abort(403, 'CRITICAL SECURITY ERROR: License Security Core Module is Missing or Compromised.');
        }
    }
}
