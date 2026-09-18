@extends('layouts.email')

@section('subject', 'Test Email Berhasil - ' . Setting::get('school_name', 'Sekolah'))

@section('content')
    <div style="background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 14px; padding: 20px; text-align: center; margin-bottom: 24px;">
        <h3 style="color: #065f46; margin: 0 0 4px; font-size: 16px; font-weight: 700;">Email Terkirim!</h3>
        <p style="color: #047857; margin: 0; font-size: 13px;">Konfigurasi email Anda berfungsi dengan baik.</p>
    </div>
    
    <p class="body-text">
        Email ini adalah test dari sistem {{ Setting::get('school_name', 'Sekolah') }}. Jika Anda menerima email ini, 
        berarti konfigurasi SMTP sudah benar dan siap digunakan untuk mengirim notifikasi.
    </p>
    
    <table class="info-table">
        <tr>
            <td class="label">Sekolah</td>
            <td class="value">{{ Setting::get('school_name', 'Sekolah') }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal</td>
            <td class="value">{{ date('d M Y, H:i:s') }}</td>
        </tr>
        <tr>
            <td class="label">Dikirim Ke</td>
            <td class="value">{{ $testData['to'] ?? '-' }}</td>
        </tr>
    </table>
    
    <p class="body-text" style="font-size: 13px; color: #64748b; margin-top: 30px;">
        Hormat kami,<br>
        <strong>Sistem {{ Setting::get('school_name', 'Sekolah') }}</strong>
    </p>
@endsection
