<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;

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
        $logoUrl = Setting::getLogoUrl();

        $manifest = [
            'name' => $schoolName,
            'short_name' => $schoolShortName,
            'description' => $schoolDesc,
            'start_url' => url('/'),
            'scope' => url('/'),
            'display' => 'standalone',
            'background_color' => '#FFFFFF',
            'theme_color' => $primaryColor,
            'orientation' => 'any',
            'icons' => [
                [
                    'src' => $logoUrl,
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'any',
                ],
                [
                    'src' => $logoUrl,
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'any maskable',
                ],
            ],
            'categories' => ['education', 'productivity'],
        ];

        return response()->json($manifest, 200, [
            'Content-Type' => 'application/manifest+json; charset=utf-8',
        ]);
    }
}
