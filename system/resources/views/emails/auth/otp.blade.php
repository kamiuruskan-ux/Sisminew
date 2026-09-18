@extends('layouts.email')

@section('subject', 'Kode OTP Login - ' . Setting::get('school_name', 'Sekolah'))

@section('content')
    <p class="greeting">Halo <strong>{{ $user->name }}</strong>,</p>
    
    <p class="body-text">
        Kami mendeteksi aktivitas login baru ke akun Anda di portal <strong>{{ $portalName }}</strong> {{ Setting::get('school_name', 'Sekolah') }}.
        Gunakan kode OTP berikut untuk melanjutkan verifikasi masuk:
    </p>
    
    <div class="highlight-box">
        <p style="margin: 0 0 8px; color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; tracking-wider;">Kode verifikasi OTP Anda:</p>
        <div style="font-family: 'Courier New', monospace; font-size: 32px; font-weight: 800; color: {{ Setting::get('primary_color', '#6366f1') }}; letter-spacing: 6px; padding: 10px 0;">{{ $otp }}</div>
    </div>
    
    <div class="warning-box">
        <strong>PENTING:</strong> Kode OTP ini hanya berlaku selama <strong>10 menit</strong>. 
        Jangan pernah membagikan kode verifikasi ini kepada siapapun, termasuk pihak sekolah.
    </div>
    
    <p class="body-text" style="font-size: 13px; color: #64748b; margin-top: 30px;">
        Hormat kami,<br>
        <strong>Tim IT {{ Setting::get('school_name', 'Sekolah') }}</strong>
    </p>
@endsection
