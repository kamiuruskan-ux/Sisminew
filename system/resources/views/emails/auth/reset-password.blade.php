@extends('layouts.email')

@section('subject', 'Reset Password - ' . Setting::get('school_name', 'Sekolah'))

@section('content')
    <p class="greeting">Halo <strong>{{ $user->name }}</strong>,</p>
    
    <p class="body-text">
        Kami menerima permintaan untuk mereset password akun Anda di {{ Setting::get('school_name', 'Sekolah') }}.
        Silakan klik tombol di bawah ini untuk membuat password baru:
    </p>
    
    <div class="button-wrapper">
        <a href="{{ url('reset-password/' . $token) . '?email=' . urlencode($user->email) }}" class="action-button">Reset Password</a>
    </div>
    
    <div class="highlight-box">
        <p style="margin: 0 0 8px; color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; tracking-wider;">Atau salin token berikut:</p>
        <div style="font-family: monospace; font-size: 18px; font-weight: 700; color: #0f172a; letter-spacing: 2px;">{{ $token }}</div>
    </div>
    
    <div class="warning-box">
        <strong>PENTING:</strong> Tautan dan token reset password ini hanya berlaku selama <strong>60 menit</strong>. 
        Jika Anda tidak meminta pengaturan ulang sandi ini, abaikan email ini dengan aman.
    </div>
    
    <p class="body-text" style="font-size: 13px; color: #64748b; margin-top: 30px;">
        Hormat kami,<br>
        <strong>Tim IT {{ Setting::get('school_name', 'Sekolah') }}</strong>
    </p>
@endsection
