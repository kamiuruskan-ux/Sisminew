<?php

namespace App\Http\Controllers;

use App\Services\LicenseManager;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    /**
     * Display License Activation Page
     */
    public function activate()
    {
        $currentHost = request()->getHost();
        $activeLicense = LicenseManager::getActiveLicense();
        $verification = LicenseManager::verifyLicense($activeLicense, $currentHost);

        // If license is already valid, redirect to home
        if ($verification['valid']) {
            return redirect()->route('admin.dashboard')->with('success', 'Lisensi aplikasi aktif dan terverifikasi.');
        }

        return view('license.activate', [
            'currentHost' => $currentHost,
            'activeLicense' => $activeLicense,
            'verification' => $verification,
            'errorMessage' => session('license_error') ?: $verification['message'],
            'errorCode' => session('license_code') ?: $verification['code']
        ]);
    }

    /**
     * Process License Key Form Submission
     */
    public function store(Request $request)
    {
        $request->validate([
            'license_key' => 'required|string',
        ], [
            'license_key.required' => 'Kunci lisensi wajib diisi.',
        ]);

        $licenseKey = trim($request->input('license_key'));
        $verification = LicenseManager::verifyLicense($licenseKey, request()->getHost());

        if (!$verification['valid']) {
            return back()->withInput()->with('error', $verification['message']);
        }

        LicenseManager::saveLicense($licenseKey);

        return redirect()->route('admin.dashboard')->with('success', 'Selamat! Lisensi aplikasi berhasil teraktivasi secara resmi untuk ' . ($verification['data']['client'] ?? 'Sekolah Anda') . '.');
    }
}
