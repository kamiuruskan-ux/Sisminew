<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\CheckInstalledMiddleware::class);
        $middleware->append(\App\Http\Middleware\CheckLicenseMiddleware::class);
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
            'verify.pin' => \App\Http\Middleware\VerifyStudentPin::class,
            'account.status' => \App\Http\Middleware\CheckAccountStatus::class,
            'session.timeout' => \App\Http\Middleware\SessionTimeout::class,
        ]);

        $middleware->redirectUsersTo(function (\Illuminate\Http\Request $request) {
            $user = $request->user();
            if ($user) {
                if ($user->hasRole('kantin')) {
                    return route('canteen.vendor.dashboard');
                }
                if ($user->hasRole('student')) {
                    return route('student.dashboard');
                }
                if ($user->hasRole('calon-siswa')) {
                    return route('spmb.dashboard.index');
                }
                if ($user->hasRole('guru')) {
                    return route('admin.dashboard');
                }
                if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
                    return route('admin.dashboard');
                }
            }
            return '/';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Database\QueryException $e, \Illuminate\Http\Request $request) {
            if (!file_exists(storage_path('installed')) && !$request->is('install') && !$request->is('install/*') && !$request->expectsJson()) {
                return redirect('/install');
            }
        });

        $exceptions->render(function (\PDOException $e, \Illuminate\Http\Request $request) {
            if (!file_exists(storage_path('installed')) && !$request->is('install') && !$request->is('install/*') && !$request->expectsJson()) {
                return redirect('/install');
            }
        });
    })->create();

// Auto-detect public path: if running with parent directory as web root (e.g. WAMP / cPanel)
$parentDir = dirname($app->basePath());
if (file_exists($parentDir . '/img') && file_exists($parentDir . '/index.php')) {
    $app->usePublicPath($parentDir);
}

return $app;
