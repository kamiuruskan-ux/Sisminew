<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class InstallController extends Controller
{
    /**
     * Show the setup wizard page.
     */
    public function index()
    {
        // If already installed, redirect to home
        if ($this->isInstalled()) {
            return redirect('/');
        }

        $requirements = $this->checkRequirements();
        $permissions = $this->checkPermissions();

        return view('install.index', compact('requirements', 'permissions'));
    }

    /**
     * Test database connection via AJAX.
     */
    public function testDatabase(Request $request)
    {
        if ($this->isInstalled()) {
            return response()->json(['success' => false, 'message' => 'Aplikasi sudah terinstall.'], 403);
        }

        $request->validate([
            'db_host' => 'required|string',
            'db_port' => 'required|string',
            'db_database' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
        ]);

        try {
            $connection = @new \mysqli(
                $request->db_host,
                $request->db_username,
                $request->db_password ?? '',
                $request->db_database,
                (int) $request->db_port
            );

            if ($connection->connect_error) {
                return response()->json([
                    'success' => false,
                    'message' => 'Koneksi gagal: ' . $connection->connect_error
                ]);
            }

            $connection->close();

            return response()->json([
                'success' => true,
                'message' => 'Koneksi database berhasil!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Koneksi gagal: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Process the installation.
     */
    public function process(Request $request)
    {
        if ($this->isInstalled()) {
            return response()->json(['success' => false, 'message' => 'Aplikasi sudah terinstall.'], 403);
        }

        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_env' => 'required|in:local,production',
            'app_debug' => 'required|in:true,false',
            'app_url' => 'required|url',
            'asset_url' => 'required|url',
            'frontend_url' => 'required|url',
            'app_timezone' => 'nullable|string|in:Asia/Jakarta,Asia/Makassar,Asia/Jayapura,UTC',
            'db_host' => 'required|string',
            'db_port' => 'required|string',
            'db_database' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email',
            'admin_password' => 'required|string|min:6',
            'run_seed' => 'nullable|in:0,1',
            'is_vocational' => 'nullable|in:0,1',
        ]);

        try {
            // Step 1: Write .env file
            $this->writeEnvFile($request);

            // Step 2: Clear config cache so new .env values take effect
            Artisan::call('config:clear');

            // Step 3: Re-configure DB connection at runtime
            config([
                'database.connections.mysql.host' => $request->db_host,
                'database.connections.mysql.port' => $request->db_port,
                'database.connections.mysql.database' => $request->db_database,
                'database.connections.mysql.username' => $request->db_username,
                'database.connections.mysql.password' => $request->db_password ?? '',
            ]);
            DB::purge('mysql');
            DB::reconnect('mysql');

            // Step 4: APP_KEY is already generated in Step 1


            // Step 5: Run migrations (gunakan migrate:fresh agar sisa tabel dari percobaan sebelumnya dibersihkan)
            try {
                Artisan::call('migrate:fresh', ['--force' => true]);
            } catch (\Throwable $e) {
                Artisan::call('migrate', ['--force' => true]);
            }

            // Step 6: Run seeders if requested
            if ($request->run_seed == '1') {
                Artisan::call('db:seed', ['--force' => true]);
            } else {
                // Always seed roles & permissions (required for admin)
                Artisan::call('db:seed', [
                    '--class' => 'Database\\Seeders\\RolePermissionSeeder',
                    '--force' => true,
                ]);
                // Seed default settings
                Artisan::call('db:seed', [
                    '--class' => 'Database\\Seeders\\SettingSeeder',
                    '--force' => true,
                ]);
            }

            // Save school type / vocational setting
            if ($request->has('is_vocational')) {
                \App\Models\Setting::updateOrCreate(
                    ['key' => 'is_vocational'],
                    [
                        'value' => $request->is_vocational,
                        'type' => 'boolean',
                        'group' => 'features'
                    ]
                );
                \App\Models\Setting::updateOrCreate(
                    ['key' => 'school_type'],
                    [
                        'value' => $request->is_vocational == '1' ? 'SMK' : 'SMA',
                        'type' => 'select',
                        'group' => 'general'
                    ]
                );
            }

            // Save app_timezone setting
            \App\Models\Setting::updateOrCreate(
                ['key' => 'app_timezone'],
                [
                    'value' => $request->app_timezone ?? 'Asia/Jakarta',
                    'type' => 'select',
                    'group' => 'general'
                ]
            );

            // Step 7: Create admin user
            $this->createAdminUser($request);

            // Step 8: Create installed lock file
            File::put(storage_path('installed'), json_encode([
                'installed_at' => now()->toDateTimeString(),
                'version' => '1.0.0',
            ]));

            // Step 9: Optimize
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            return response()->json([
                'success' => true,
                'message' => 'Instalasi berhasil! Aplikasi Anda siap digunakan.',
                'redirect' => url('/admin/login'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Instalasi gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Write .env file based on wizard input.
     */
    private function writeEnvFile(Request $request): void
    {
        $appKey = 'base64:' . base64_encode(random_bytes(32));
        $timezone = $request->app_timezone ?? 'Asia/Jakarta';
        $envContent = <<<ENV
APP_NAME="{$request->app_name}"
APP_ENV={$request->app_env}
APP_KEY={$appKey}
APP_DEBUG={$request->app_debug}
APP_URL={$request->app_url}
APP_TIMEZONE={$timezone}

ASSET_URL={$request->asset_url}
FRONTEND_URL={$request->frontend_url}

APP_LOCALE=id
APP_FALLBACK_LOCALE=id
APP_FAKER_LOCALE=id_ID

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST={$request->db_host}
DB_PORT={$request->db_port}
DB_DATABASE={$request->db_database}
DB_USERNAME={$request->db_username}
DB_PASSWORD={$request->db_password}

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=false

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync

CACHE_STORE=file

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@sekolah.id"
MAIL_FROM_NAME="{$request->app_name}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="{$request->app_name}"
ENV;

        $envPath = base_path('.env');
        File::put($envPath, $envContent);
    }

    /**
     * Create the admin user from wizard input.
     */
    private function createAdminUser(Request $request): void
    {
        $userModel = app('App\\Models\\User');
        $roleModel = app('App\\Models\\Role');

        $admin = $userModel::updateOrCreate(
            ['email' => $request->admin_email],
            [
                'name' => $request->admin_name,
                'password' => Hash::make($request->admin_password),
                'phone' => '000000000000',
                'status' => 'active',
            ]
        );

        $superAdminRole = $roleModel::where('slug', 'super-admin')->first();
        if ($superAdminRole) {
            $admin->assignRole($superAdminRole);
        }
    }

    /**
     * Check system requirements (PHP version & extensions).
     */
    private function checkRequirements(): array
    {
        $required = [
            'php_version' => [
                'label' => 'PHP >= 8.1',
                'check' => version_compare(PHP_VERSION, '8.1.0', '>='),
                'current' => PHP_VERSION,
            ],
            'pdo_mysql' => [
                'label' => 'PDO MySQL',
                'check' => extension_loaded('pdo_mysql'),
            ],
            'mbstring' => [
                'label' => 'Mbstring',
                'check' => extension_loaded('mbstring'),
            ],
            'openssl' => [
                'label' => 'OpenSSL',
                'check' => extension_loaded('openssl'),
            ],
            'curl' => [
                'label' => 'cURL',
                'check' => extension_loaded('curl'),
            ],
            'json' => [
                'label' => 'JSON',
                'check' => extension_loaded('json'),
            ],
            'fileinfo' => [
                'label' => 'Fileinfo',
                'check' => extension_loaded('fileinfo'),
            ],
            'gd' => [
                'label' => 'GD Library',
                'check' => extension_loaded('gd'),
            ],
            'zip' => [
                'label' => 'Zip',
                'check' => extension_loaded('zip'),
            ],
            'xml' => [
                'label' => 'XML',
                'check' => extension_loaded('xml'),
            ],
            'tokenizer' => [
                'label' => 'Tokenizer',
                'check' => extension_loaded('tokenizer'),
            ],
            'ctype' => [
                'label' => 'Ctype',
                'check' => extension_loaded('ctype'),
            ],
        ];

        return $required;
    }

    /**
     * Check directory/file permissions.
     */
    private function checkPermissions(): array
    {
        return [
            'storage' => [
                'label' => 'storage/',
                'path' => storage_path(),
                'check' => is_writable(storage_path()),
            ],
            'bootstrap_cache' => [
                'label' => 'bootstrap/cache/',
                'path' => base_path('bootstrap/cache'),
                'check' => is_writable(base_path('bootstrap/cache')),
            ],
            'env_file' => [
                'label' => '.env',
                'path' => base_path('.env'),
                'check' => is_writable(base_path('.env')) || (!file_exists(base_path('.env')) && is_writable(base_path())),
            ],
        ];
    }

    /**
     * Check if the application is already installed.
     */
    private function isInstalled(): bool
    {
        if (file_exists(storage_path('installed'))) {
            return true;
        }

        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('users') && \Illuminate\Support\Facades\Schema::hasTable('settings')) {
                if (\Illuminate\Support\Facades\DB::table('users')->count() > 0) {
                    @file_put_contents(storage_path('installed'), json_encode([
                        'installed_at' => date('Y-m-d H:i:s'),
                        'auto_detected' => true,
                    ]));
                    return true;
                }
            }
        } catch (\Throwable $e) {
            return false;
        }

        return false;
    }
}
