<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInstalledMiddleware
{
    /**
     * Handle an incoming request.
     * Redirect to /install if the application has not been installed yet.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isInstalled = $this->isInstalled();
        $isInstallRoute = $request->is('install') || $request->is('install/*');

        // If NOT installed and NOT on install route → redirect to installer
        if (!$isInstalled && !$isInstallRoute) {
            return redirect('/install');
        }

        // If IS installed and ON install route → redirect to home
        if ($isInstalled && $isInstallRoute) {
            return redirect('/');
        }

        return $next($request);
    }

    /**
     * Check if application is installed and DB tables exist.
     */
    private function isInstalled(): bool
    {
        if (file_exists(storage_path('installed'))) {
            return true;
        }

        try {
            // Cek apakah tabel utama sudah ada di database
            if (\Illuminate\Support\Facades\Schema::hasTable('users') && \Illuminate\Support\Facades\Schema::hasTable('settings')) {
                if (\Illuminate\Support\Facades\DB::table('users')->count() > 0) {
                    // Auto-create installed file if DB tables & admin users exist
                    @file_put_contents(storage_path('installed'), json_encode([
                        'installed_at' => date('Y-m-d H:i:s'),
                        'auto_detected' => true,
                    ]));
                    return true;
                }
            }
        } catch (\Throwable $e) {
            // Abaikan error koneksi agar tidak terjebak redirect loop
        }

        return false;
    }
}
