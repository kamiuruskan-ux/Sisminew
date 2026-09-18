<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    public function edit()
    {
        $bankAccounts = \App\Models\BankAccount::withCount('financialTransactions')->latest()->get();
        $totalBalance = \App\Models\BankAccount::where('is_active', true)->sum('current_balance');
        return view('admin.settings', compact('bankAccounts', 'totalBalance'));
    }

    public function profileEdit()
    {
        return view('admin.profile-settings');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            // General
            'school_name' => 'required|string|max:255',
            'school_short_name' => 'nullable|string|max:100',
            'school_tagline' => 'nullable|string|max:255',
            'school_type' => 'in:SD,SMP,SMA,SMK',
            'app_timezone' => 'nullable|string|in:Asia/Jakarta,Asia/Makassar,Asia/Jayapura,UTC',
            'is_vocational' => 'in:0,1',
            'school_description' => 'nullable|string',
            'npsn' => 'nullable|string|max:20',
            'school_founded_year' => 'nullable|string|max:10',
            'school_video_url' => 'nullable|string',
            'school_principal_name' => 'nullable|string|max:255',
            'school_principal_nip' => 'nullable|string|max:100',
            'school_principal_title' => 'nullable|string|max:255',
            'school_principal_welcome' => 'nullable|string',
            'school_vision' => 'nullable|string',
            'school_mission' => 'nullable|string',
            'school_history' => 'nullable|string',
            'curriculum_description' => 'nullable|string',
            'curriculum_pillars' => 'nullable|string',
            'school_principal_photo' => 'nullable|string',
            'school_principal_photo_file' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:5120',
            'stats_achievements' => 'nullable|string|max:50',
            'stats_alumni' => 'nullable|string|max:50',

            // Kop Surat
            'letterhead_header_top' => 'nullable|string|max:255',
            'letterhead_sub' => 'nullable|string|max:255',
            'letterhead_logo' => 'nullable|file|mimes:jpeg,png,jpg,svg,webp|max:5120',
            'letterhead_logo_right' => 'nullable|file|mimes:jpeg,png,jpg,svg,webp|max:5120',

            // Contact
            'school_email' => 'nullable|email',
            'school_phone' => 'nullable|string',
            'school_whatsapp' => 'nullable|string',
            'school_operating_hours' => 'nullable|string',
            'school_address' => 'nullable|string',
            'school_city' => 'nullable|string',
            'school_province' => 'nullable|string',
            'school_postal_code' => 'nullable|string',
            'school_latitude' => 'nullable|numeric',
            'school_longitude' => 'nullable|numeric',
            'school_attendance_radius' => 'nullable|integer|min:10',
            'school_timezone_label' => 'nullable|string|in:WIB,WITA,WIT',

            // Branding & Theme
            'theme_preset' => 'nullable|string',
            'theme_mode_default' => 'nullable|string|in:light,dark,system',
            'primary_color' => 'nullable|string',
            'primary_color_text' => 'nullable|string',
            'secondary_color' => 'nullable|string',
            'secondary_color_text' => 'nullable|string',
            'logo' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp,ico|max:5120',
            'favicon' => 'nullable|file|mimes:png,ico,svg,jpg,jpeg,gif,webp|max:5120',
            'school_org_chart' => 'nullable|file|mimes:png,jpg,jpeg,webp|max:5120',

            // SPMB
            'spmb_enabled' => 'in:0,1',
            'spmb_registration_fee' => 'nullable|numeric',
            'spmb_info_text' => 'nullable|string',

            // Social
            'facebook_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'youtube_url' => 'nullable|url',
            'maps_url' => 'nullable|string',

            // WA Gateway
            'wa_gateway_provider' => 'nullable|string|in:disabled,fonnte,onesender',
            'wa_fonnte_token' => 'nullable|string',
            'wa_fonnte_url' => 'nullable|string',
            'wa_onesender_api_key' => 'nullable|string',
            'wa_onesender_url' => 'nullable|string',
            'wa_notify_attendance' => 'nullable|in:0,1',
            'wa_notify_spmb' => 'nullable|in:0,1',
            'wa_notify_payment' => 'nullable|in:0,1',
            'wa_notify_otp' => 'nullable|in:0,1',

            // Payment Gateway
            'payment_manual_enabled' => 'nullable|in:0,1',
            'payment_midtrans_enabled' => 'nullable|in:0,1',
            'payment_midtrans_server_key' => 'nullable|string',
            'payment_midtrans_client_key' => 'nullable|string',
            'payment_midtrans_mode' => 'nullable|in:sandbox,production',
            'payment_tripay_enabled' => 'nullable|in:0,1',
            'payment_tripay_api_key' => 'nullable|string',
            'payment_tripay_private_key' => 'nullable|string',
            'payment_tripay_merchant_code' => 'nullable|string',
            'payment_tripay_mode' => 'nullable|in:sandbox,production',
            'payment_duitku_enabled' => 'nullable|in:0,1',
            'payment_duitku_merchant_code' => 'nullable|string',
            'payment_duitku_api_key' => 'nullable|string',
            'payment_duitku_mode' => 'nullable|in:sandbox,production',
            
            // Canteen
            'canteen_min_withdrawal' => 'nullable|numeric|min:0',

            // Email Sender Settings
            'mail_mailer' => 'nullable|in:smtp,mail,sendmail',
            'mail_host' => 'nullable',
            'mail_port' => 'nullable|numeric',
            'mail_username' => 'nullable',
            'mail_password' => 'nullable',
            'mail_encryption' => 'nullable|in:tls,ssl,none',
            'mail_from_address' => 'nullable|email',
            'mail_from_name' => 'nullable|string',
            'email_notify_otp' => 'nullable|in:0,1',
            'email_notify_payment' => 'nullable|in:0,1',
            'email_notify_spmb' => 'nullable|in:0,1',
            'email_notify_attendance' => 'nullable|in:0,1',

            // Raport Dynamic Notes
            'raport_note_a' => 'nullable|string',
            'raport_note_b' => 'nullable|string',
            'raport_note_c' => 'nullable|string',
            'raport_note_d' => 'nullable|string',
        ]);

        // Map fields to settings
        $settingsMap = [
            'school_name' => 'school_name',
            'school_short_name' => 'school_short_name',
            'school_tagline' => 'school_tagline',
            'school_type' => 'school_type',
            'app_timezone' => 'app_timezone',
            'is_vocational' => 'is_vocational',
            'school_description' => 'school_description',
            'npsn' => 'npsn',
            'school_founded_year' => 'school_founded_year',
            'school_video_url' => 'school_video_url',
            'school_principal_name' => 'school_principal_name',
            'school_principal_nip' => 'school_principal_nip',
            'school_principal_title' => 'school_principal_title',
            'school_principal_welcome' => 'school_principal_welcome',
            'school_vision' => 'school_vision',
            'school_mission' => 'school_mission',
            'school_history' => 'school_history',
            'curriculum_description' => 'curriculum_description',
            'curriculum_pillars' => 'curriculum_pillars',
            'school_principal_photo' => 'school_principal_photo',
            'letterhead_header_top' => 'letterhead_header_top',
            'letterhead_sub' => 'letterhead_sub',
            'stats_achievements' => 'stats_achievements',
            'stats_alumni' => 'stats_alumni',
            'school_email' => 'school_email',
            'school_phone' => 'school_phone',
            'school_whatsapp' => 'school_whatsapp',
            'school_operating_hours' => 'school_operating_hours',
            'school_address' => 'school_address',
            'school_city' => 'school_city',
            'school_province' => 'school_province',
            'school_postal_code' => 'school_postal_code',
            'school_latitude' => 'school_latitude',
            'school_longitude' => 'school_longitude',
            'school_attendance_radius' => 'school_attendance_radius',
            'school_timezone_label' => 'school_timezone_label',
            'theme_preset' => 'theme_preset',
            'theme_mode_default' => 'theme_mode_default',
            'primary_color' => 'primary_color',
            'secondary_color' => 'secondary_color',
            'spmb_enabled' => 'spmb_enabled',
            'spmb_registration_fee' => 'spmb_registration_fee',
            'spmb_info_text' => 'spmb_info_text',
            'facebook_url' => 'facebook_url',
            'instagram_url' => 'instagram_url',
            'twitter_url' => 'twitter_url',
            'youtube_url' => 'youtube_url',
            'maps_url' => 'maps_url',
            'wa_gateway_provider' => 'wa_gateway_provider',
            'wa_fonnte_token' => 'wa_fonnte_token',
            'wa_fonnte_url' => 'wa_fonnte_url',
            'wa_onesender_api_key' => 'wa_onesender_api_key',
            'wa_onesender_url' => 'wa_onesender_url',
            'wa_notify_attendance' => 'wa_notify_attendance',
            'wa_notify_spmb' => 'wa_notify_spmb',
            'wa_notify_payment' => 'wa_notify_payment',
            'wa_notify_otp' => 'wa_notify_otp',
            'student_card_rules' => 'student_card_rules',
            'student_card_accent_color' => 'student_card_accent_color',
            'student_card_accent_color_end' => 'student_card_accent_color_end',
            'student_card_orientation' => 'student_card_orientation',
            'payment_manual_enabled' => 'payment_manual_enabled',
            'payment_midtrans_enabled' => 'payment_midtrans_enabled',
            'payment_midtrans_server_key' => 'payment_midtrans_server_key',
            'payment_midtrans_client_key' => 'payment_midtrans_client_key',
            'payment_midtrans_mode' => 'payment_midtrans_mode',
            'payment_tripay_enabled' => 'payment_tripay_enabled',
            'payment_tripay_api_key' => 'payment_tripay_api_key',
            'payment_tripay_private_key' => 'payment_tripay_private_key',
            'payment_tripay_merchant_code' => 'payment_tripay_merchant_code',
            'payment_tripay_mode' => 'payment_tripay_mode',
            'payment_duitku_enabled' => 'payment_duitku_enabled',
            'payment_duitku_merchant_code' => 'payment_duitku_merchant_code',
            'payment_duitku_api_key' => 'payment_duitku_api_key',
            'payment_duitku_mode' => 'payment_duitku_mode',
            'canteen_min_withdrawal' => 'canteen_min_withdrawal',
            
            // Email settings keys mapping
            'mail_mailer' => 'email_mail_mailer',
            'mail_host' => 'email_mail_host',
            'mail_port' => 'email_mail_port',
            'mail_username' => 'email_mail_username',
            'mail_password' => 'email_mail_password',
            'mail_encryption' => 'email_mail_encryption',
            'mail_from_address' => 'email_mail_from_address',
            'mail_from_name' => 'email_mail_from_name',
            'email_notify_otp' => 'email_notify_otp',
            'email_notify_payment' => 'email_notify_payment',
            'email_notify_spmb' => 'email_notify_spmb',
            'email_notify_attendance' => 'email_notify_attendance',

            // Raport Notes mapping
            'raport_note_a' => 'raport_note_a',
            'raport_note_b' => 'raport_note_b',
            'raport_note_c' => 'raport_note_c',
            'raport_note_d' => 'raport_note_d',
        ];

        foreach ($settingsMap as $input => $key) {
            if ($request->has($input)) {
                Setting::set($key, $request->input($input));
            }
        }



        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $ext = strtolower($logo->getClientOriginalExtension());
            $logoName = 'logo_' . time() . '.' . $ext;

            $oldLogoPath = Setting::get('logo_path') ?? Setting::get('school_logo');
            if ($oldLogoPath && !in_array(basename($oldLogoPath), ['logo.png', 'fav.png']) && File::exists(public_path($oldLogoPath))) {
                delete_public_file($oldLogoPath);
            }

            $savedLogo = save_uploaded_public_file($logo, 'img', $logoName);
            Setting::set('logo_path', $savedLogo);
            Setting::set('school_logo', $savedLogo);
            Setting::set('logo', $savedLogo);
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            $favicon = $request->file('favicon');
            $ext = strtolower($favicon->getClientOriginalExtension());
            $faviconName = 'fav_' . time() . '.' . $ext;

            $oldFavPath = Setting::get('favicon_path') ?? Setting::get('school_favicon');
            if ($oldFavPath && !in_array(basename($oldFavPath), ['fav.png', 'logo.png']) && File::exists(public_path($oldFavPath))) {
                delete_public_file($oldFavPath);
            }

            $savedFavicon = save_uploaded_public_file($favicon, 'img', $faviconName);
            Setting::set('favicon_path', $savedFavicon);
            Setting::set('school_favicon', $savedFavicon);
            Setting::set('favicon', $savedFavicon);
        }

        // Handle Principal Photo Upload
        if ($request->hasFile('school_principal_photo_file')) {
            $photo = $request->file('school_principal_photo_file');
            $ext = strtolower($photo->getClientOriginalExtension());
            $photoName = 'principal_' . time() . '.' . $ext;

            $oldPhoto = Setting::get('school_principal_photo');
            if ($oldPhoto && File::exists(public_path($oldPhoto))) {
                delete_public_file($oldPhoto);
            }

            $savedPhoto = save_uploaded_public_file($photo, 'img/avatars', $photoName);
            Setting::set('school_principal_photo', 'img/avatars/' . basename($savedPhoto));
        }

        // Handle Kop Surat Logo Upload (Logo Kiri)
        if ($request->hasFile('letterhead_logo')) {
            $logo = $request->file('letterhead_logo');
            $ext = strtolower($logo->getClientOriginalExtension());
            $logoName = 'letterhead_logo_' . time() . '.' . $ext;

            $oldLetterheadLogo = Setting::get('letterhead_logo_path');
            if ($oldLetterheadLogo && File::exists(public_path($oldLetterheadLogo))) {
                delete_public_file($oldLetterheadLogo);
            }

            $savedLetterheadLogo = save_uploaded_public_file($logo, 'img', $logoName);
            Setting::set('letterhead_logo_path', 'img/' . basename($savedLetterheadLogo));
        } elseif ($request->boolean('delete_letterhead_logo')) {
            $oldPath = Setting::get('letterhead_logo_path');
            if ($oldPath && function_exists('delete_public_file')) {
                delete_public_file($oldPath);
            }
            Setting::set('letterhead_logo_path', null);
        }

        // Handle Kop Surat Logo Kanan Upload
        if ($request->hasFile('letterhead_logo_right')) {
            $logoR = $request->file('letterhead_logo_right');
            $extR = strtolower($logoR->getClientOriginalExtension());
            $logoRName = 'letterhead_logo_right_' . time() . '.' . $extR;

            $oldLetterheadLogoR = Setting::get('letterhead_logo_right_path');
            if ($oldLetterheadLogoR && File::exists(public_path($oldLetterheadLogoR))) {
                delete_public_file($oldLetterheadLogoR);
            }

            $savedLetterheadLogoR = save_uploaded_public_file($logoR, 'img', $logoRName);
            Setting::set('letterhead_logo_right_path', 'img/' . basename($savedLetterheadLogoR));
        } elseif ($request->boolean('delete_letterhead_logo_right')) {
            $oldPathR = Setting::get('letterhead_logo_right_path');
            if ($oldPathR && function_exists('delete_public_file')) {
                delete_public_file($oldPathR);
            }
            Setting::set('letterhead_logo_right_path', null);
        }

        // Handle School Organizational Chart PNG Upload
        if ($request->hasFile('school_org_chart')) {
            $orgChart = $request->file('school_org_chart');
            $orgChartName = 'org_chart.' . $orgChart->getClientOriginalExtension();

            $orgChartPath = public_path('img');
            if (!File::exists($orgChartPath)) {
                File::makeDirectory($orgChartPath, 0755, true, true);
            }

            foreach (['png', 'jpg', 'jpeg'] as $ext) {
                $oldOrgChart = public_path('img/org_chart.' . $ext);
                if (File::exists($oldOrgChart)) {
                    File::delete($oldOrgChart);
                }
            }

            $savedOrgChart = save_uploaded_public_file($orgChart, 'img', $orgChartName);
            Setting::set('school_org_chart_path', 'img/' . basename($savedOrgChart));
        } elseif ($request->boolean('delete_school_org_chart')) {
            $oldPath = Setting::get('school_org_chart_path');
            if ($oldPath && function_exists('delete_public_file')) {
                delete_public_file($oldPath);
            }
            Setting::set('school_org_chart_path', null);
        }

        // Handle Student Card Front Background Image Upload
        if ($request->hasFile('student_card_bg_front')) {
            $file = $request->file('student_card_bg_front');
            $fileName = 'card_bg_front_' . time() . '.' . $file->getClientOriginalExtension();
            $savedFront = save_uploaded_public_file($file, 'img/cards', $fileName);
            Setting::set('student_card_bg_front_path', 'img/cards/' . basename($savedFront));
        } elseif ($request->boolean('delete_student_card_bg_front')) {
            Setting::set('student_card_bg_front_path', null);
        }

        // Handle Student Card Back Background Image Upload
        if ($request->hasFile('student_card_bg_back')) {
            $file = $request->file('student_card_bg_back');
            $fileName = 'card_bg_back_' . time() . '.' . $file->getClientOriginalExtension();
            $savedBack = save_uploaded_public_file($file, 'img/cards', $fileName);
            Setting::set('student_card_bg_back_path', 'img/cards/' . basename($savedBack));
        } elseif ($request->boolean('delete_student_card_bg_back')) {
            Setting::set('student_card_bg_back_path', null);
        }

        // Handle School Stamp Image Upload
        if ($request->hasFile('student_card_stamp')) {
            $file = $request->file('student_card_stamp');
            $fileName = 'card_stamp_' . time() . '.' . $file->getClientOriginalExtension();
            $savedStamp = save_uploaded_public_file($file, 'img/cards', $fileName);
            $relPath = 'img/cards/' . basename($savedStamp);
            Setting::set('student_card_stamp_path', $relPath);
            Setting::set('raport_stamp_path', $relPath);
        } elseif ($request->boolean('delete_student_card_stamp')) {
            $oldPath = Setting::get('student_card_stamp_path') ?? Setting::get('raport_stamp_path');
            if ($oldPath && function_exists('delete_public_file')) {
                delete_public_file($oldPath);
            }
            Setting::set('student_card_stamp_path', null);
            Setting::set('raport_stamp_path', null);
        }

        // Handle Headmaster Signature Upload
        if ($request->hasFile('student_card_signature')) {
            $file = $request->file('student_card_signature');
            $fileName = 'card_signature_' . time() . '.' . $file->getClientOriginalExtension();
            $savedSig = save_uploaded_public_file($file, 'img/cards', $fileName);
            $relSig = 'img/cards/' . basename($savedSig);
            Setting::set('student_card_signature_path', $relSig);
            Setting::set('raport_signature_path', $relSig);
        } elseif ($request->boolean('delete_student_card_signature')) {
            $oldPath = Setting::get('student_card_signature_path') ?? Setting::get('raport_signature_path');
            if ($oldPath && function_exists('delete_public_file')) {
                delete_public_file($oldPath);
            }
            Setting::set('student_card_signature_path', null);
            Setting::set('raport_signature_path', null);
        }

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }

    public function profile()
    {
        return view('admin.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string',
            'avatar' => 'nullable|image|max:2048',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? $user->phone;

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                delete_public_file('img/avatars/' . basename($user->avatar));
            }
            $savedAvatar = save_uploaded_public_file($request->file('avatar'), 'img/avatars');
            $user->avatar = basename($savedAvatar);
        }

        if (!empty($validated['current_password'])) {
            if (Hash::check($validated['current_password'], $user->password)) {
                $user->password = Hash::make($validated['new_password']);
            } else {
                return back()->withErrors(['current_password' => 'Password saat ini salah.']);
            }
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }


}
