<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;

class PwaController extends Controller
{
    /**
     * Generate dynamic Web App Manifest using live system logo and settings
     */
    public function manifest(): JsonResponse
    {
        $schoolName = Setting::get('school_name', config('app.name', 'SISMI'));
        $schoolShortName = Setting::get('school_short_name', 'SISMI');
        $schoolDesc = Setting::get('school_description', 'Sistem Informasi Manajemen Sekolah');
        $primaryColor = Setting::get('primary_color', '#3C50E0');
        $v = Setting::getLogoVersion();

        $manifest = [
            'name' => $schoolName,
            'short_name' => $schoolShortName,
            'description' => $schoolDesc,
            'start_url' => '/',
            'scope' => '/',
            'display' => 'standalone',
            'background_color' => '#FFFFFF',
            'theme_color' => $primaryColor,
            'orientation' => 'any',
            'icons' => [
                [
                    'src' => '/pwa-icon/192?v=' . $v,
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'any',
                ],
                [
                    'src' => '/pwa-icon/192?v=' . $v,
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'maskable',
                ],
                [
                    'src' => '/pwa-icon/512?v=' . $v,
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'any',
                ],
                [
                    'src' => '/pwa-icon/512?v=' . $v,
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'maskable',
                ],
            ],
            'categories' => ['education', 'productivity'],
        ];

        return response()->json($manifest, 200, [
            'Content-Type' => 'application/manifest+json; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }

    /**
     * Serve dynamic PWA icon resized and formatted for browsers
     */
    public function icon(?string $size = null)
    {
        $filePath = Setting::getLogoFilePath();

        if (!$filePath || !File::exists($filePath)) {
            $defaultPath = public_path('img/logo.png');
            if (File::exists($defaultPath)) {
                $filePath = $defaultPath;
            }
        }

        if (!$filePath || !File::exists($filePath)) {
            return response('', 404);
        }

        $targetSize = in_array((int)$size, [72, 96, 128, 144, 152, 180, 192, 384, 512]) ? (int)$size : null;

        // If GD is available and size is requested, generate a crisp square PNG icon
        if ($targetSize && extension_loaded('gd') && function_exists('imagecreatefromstring')) {
            try {
                $raw = @file_get_contents($filePath);
                $srcImg = @imagecreatefromstring($raw);
                if ($srcImg) {
                    $srcW = imagesx($srcImg);
                    $srcH = imagesy($srcImg);

                    $canvas = imagecreatetruecolor($targetSize, $targetSize);
                    imagealphablending($canvas, false);
                    imagesavealpha($canvas, true);
                    $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
                    imagefilledrectangle($canvas, 0, 0, $targetSize, $targetSize, $transparent);
                    imagealphablending($canvas, true);

                    // Add 10% padding so maskable/circular clipping in Android/Chrome never cuts off edges
                    $padding = (int) round($targetSize * 0.10);
                    $availW = $targetSize - ($padding * 2);
                    $availH = $targetSize - ($padding * 2);

                    $scale = min($availW / $srcW, $availH / $srcH);
                    $dstW = (int) round($srcW * $scale);
                    $dstH = (int) round($srcH * $scale);
                    $dstX = (int) round(($targetSize - $dstW) / 2);
                    $dstY = (int) round(($targetSize - $dstH) / 2);

                    imagecopyresampled($canvas, $srcImg, $dstX, $dstY, 0, 0, $dstW, $dstH, $srcW, $srcH);

                    ob_start();
                    imagepng($canvas, null, 8);
                    $pngContent = ob_get_clean();

                    imagedestroy($canvas);
                    imagedestroy($srcImg);

                    return response($pngContent, 200, [
                        'Content-Type' => 'image/png',
                        'Cache-Control' => 'public, max-age=86400, stale-while-revalidate=604800',
                        'Access-Control-Allow-Origin' => '*',
                    ]);
                }
            } catch (\Throwable $e) {
                // Fallback to direct file stream
            }
        }

        $mime = File::mimeType($filePath) ?: 'image/png';
        return response()->file($filePath, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=86400, stale-while-revalidate=604800',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }

    /**
     * Serve Service Worker file with valid headers
     */
    public function serviceWorker()
    {
        $swPath = public_path('sw.js');
        if (!File::exists($swPath)) {
            $swPath = base_path('public/sw.js');
        }

        if (File::exists($swPath)) {
            return response(File::get($swPath), 200, [
                'Content-Type' => 'application/javascript; charset=utf-8',
                'Service-Worker-Allowed' => '/',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
            ]);
        }

        return response("// SISMI PWA Service Worker\nself.addEventListener('install', () => self.skipWaiting());", 200, [
            'Content-Type' => 'application/javascript; charset=utf-8',
            'Service-Worker-Allowed' => '/',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }
}
