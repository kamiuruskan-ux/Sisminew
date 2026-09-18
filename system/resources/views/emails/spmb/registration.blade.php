@extends('layouts.email')

@section('subject', 'Konfirmasi Pendaftaran - ' . Setting::get('school_name', 'Sekolah'))

@section('content')
    <p class="greeting">Halo <strong>{{ $registration->full_name }}</strong>,</p>
    
    <p class="body-text">
        Terima kasih telah mendaftar di <strong>{{ Setting::get('school_name', 'Sekolah') }}</strong>. 
        Kami telah menerima pendaftaran Anda dengan detail berikut:
    </p>
    
    <table class="info-table">
        <tr>
            <td class="label">No. Pendaftaran</td>
            <td class="value">{{ $registration->registration_number }}</td>
        </tr>
        <tr>
            <td class="label">Gelombang</td>
            <td class="value">{{ $registration->wave->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Daftar</td>
            <td class="value">{{ $registration->created_at->format('d M Y, H:i') }}</td>
        </tr>
        <tr>
            <td class="label">Status Awal</td>
            <td class="value" style="color: #d97706;">Menunggu Verifikasi</td>
        </tr>
    </table>
    
    <p class="body-text">
        Silakan masuk ke portal dashboard Anda untuk melengkapi berkas pendaftaran dan melakukan pembayaran biaya pendaftaran.
    </p>
    
    <div class="button-wrapper">
        <a href="{{ route('login') }}" class="action-button">Masuk Ke Dashboard</a>
    </div>
    
    <div class="warning-box" style="margin-top: 16px;">
        Jika Anda tidak merasa melakukan pendaftaran ini, silakan hubungi tim dukungan kami melalui kontak resmi sekolah.
    </div>
    
    <p class="body-text" style="font-size: 13px; color: #64748b; margin-top: 30px;">
        Hormat kami,<br>
        <strong>Panitia SPMB {{ Setting::get('school_name', 'Sekolah') }}</strong>
    </p>
@endsection
