<?php

namespace App\Http\Controllers\Spmb;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\SpmbRegistration;
use App\Models\User;
use App\Models\Wave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    public function create()
    {
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->hasRole('calon-siswa')) {
                return redirect()->route('spmb.dashboard.index')
                    ->with('info', 'Anda sudah terdaftar sebagai Calon Siswa SPMB.');
            }
            if ($user->hasRole('student')) {
                return redirect()->route('student.dashboard')
                    ->with('info', 'Anda sudah terdaftar sebagai Siswa Aktif.');
            }
        }

        $spmbEnabled = \App\Models\Setting::get('spmb_enabled', '1') === '1';

        $wave = Wave::where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first() ?? Wave::where('status', 'active')->first() ?? Wave::latest()->first();

        return view('spmb.register', compact('wave', 'spmbEnabled'));
    }

    public function store(Request $request)
    {
        $isAjax = $request->wantsJson() || $request->ajax();

        if (\App\Models\Setting::get('spmb_enabled', '1') !== '1') {
            if ($isAjax) {
                return response()->json(['errors' => ['Pendaftaran SPMB Online saat ini sedang ditutup.']], 422);
            }
            return redirect()->route('spmb.register')
                ->withErrors(['error' => 'Mohon maaf, pendaftaran SPMB Online saat ini sedang ditutup oleh pihak sekolah.'])
                ->withInput();
        }

        try {
            $validated = $request->validate([
                'email'      => 'required|email|unique:users,email',
                'password'   => 'required|min:8|confirmed',
                'full_name'  => 'required|string|max:255',
                'gender'     => 'required|in:male,female',
                'birth_place'=> 'required|string|max:100',
                'birth_date' => 'required|date',
                'phone'      => 'required|string|max:20',
            ], [
                'email.required'        => 'Email wajib diisi.',
                'email.email'           => 'Format email tidak valid.',
                'email.unique'          => 'Email sudah terdaftar. Silakan gunakan email lain atau masuk ke akun Anda.',
                'password.required'     => 'Password wajib diisi.',
                'password.min'          => 'Password minimal harus 8 karakter.',
                'password.confirmed'    => 'Konfirmasi password tidak sesuai.',
                'full_name.required'    => 'Nama lengkap wajib diisi.',
                'gender.required'       => 'Jenis kelamin wajib dipilih.',
                'phone.required'        => 'Nomor WhatsApp wajib diisi.',
                'birth_place.required'  => 'Tempat lahir wajib diisi.',
                'birth_date.required'   => 'Tanggal lahir wajib diisi.',
            ]);
        } catch (ValidationException $e) {
            if ($isAjax) {
                $errors = collect($e->errors())->flatten()->values()->toArray();
                return response()->json(['errors' => $errors], 422);
            }
            return redirect()->route('spmb.register')->withErrors($e->errors())->withInput();
        }

        DB::beginTransaction();
        try {
            $wave = Wave::where('status', 'active')
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->first() ?? Wave::where('status', 'active')->first() ?? Wave::latest()->first();

            if (!$wave) {
                $msg = 'Gelombang pendaftaran belum dibuka. Silakan hubungi admin sekolah.';
                if ($isAjax) return response()->json(['errors' => [$msg]], 422);
                return redirect()->route('spmb.register')->withErrors(['error' => $msg])->withInput();
            }

            $user = User::create([
                'name'     => $validated['full_name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone'    => $validated['phone'],
            ]);

            $calonSiswaRole = Role::where('slug', 'calon-siswa')->first();
            if ($calonSiswaRole) {
                $user->assignRole($calonSiswaRole);
            }

            $registrationNumber = 'SPMB/' . date('Ymd') . '/' . Str::upper(Str::random(6));

            SpmbRegistration::create([
                'user_id'             => $user->id,
                'wave_id'             => $wave->id,
                'registration_number' => $registrationNumber,
                'full_name'           => $validated['full_name'],
                'gender'              => $validated['gender'],
                'birth_place'         => $validated['birth_place'],
                'birth_date'          => $validated['birth_date'],
                'phone'               => $validated['phone'],
                'email'               => $validated['email'],
                'status'              => 'draft',
                'payment_status'      => 'unpaid',
            ]);

            DB::commit();

            $waTarget = $validated['phone'];
            if ($waTarget && \App\Models\Setting::get('wa_notify_spmb', '1') == '1') {
                $schoolName = \App\Models\Setting::get('school_name', 'Sekolah');
                $waMsg = "Pendaftaran Akun SPMB Berhasil - {$schoolName}\n\nHalo *{$validated['full_name']}*,\nSelamat! Akun pendaftaran SPMB Anda telah berhasil dibuat.\n\nNomor Pendaftaran: *{$registrationNumber}*\nStatus: Menunggu Pembayaran Uang Pendaftaran\n\nSilakan masuk ke portal dashboard untuk melakukan pembayaran.\nTerima kasih.";
                \App\Services\WhatsAppService::sendMessage($waTarget, $waMsg);
            }

            auth()->login($user);

            $redirectUrl = route('spmb.dashboard.index');

            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => 'Akun pendaftaran berhasil dibuat! Silakan lakukan pembayaran untuk melanjutkan pendaftaran.',
                    'redirect' => $redirectUrl,
                ]);
            }

            return redirect()->to($redirectUrl)
                ->with('success', 'Akun pendaftaran berhasil dibuat! Silakan lakukan pembayaran untuk melanjutkan pendaftaran.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SPMB Guest Register Error: ' . $e->getMessage());
            $msg = 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.';
            if ($isAjax) return response()->json(['errors' => [$msg]], 500);
            return redirect()->route('spmb.register')->withErrors(['error' => $msg])->withInput();
        }
    }

    public function success(SpmbRegistration $registration)
    {
        return view('spmb.success', compact('registration'));
    }
}
