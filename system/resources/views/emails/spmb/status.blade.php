@extends('layouts.email')

@section('subject', 'Update Status Pendaftaran - ' . Setting::get('school_name', 'Sekolah'))

@section('content')
    @php
        $statusColors = [
            'accepted' => 'background-color: #d1fae5; color: #065f46;',
            'rejected' => 'background-color: #fee2e2; color: #991b1b;',
            'verified' => 'background-color: #dbeafe; color: #1e40af;'
        ];
        $badgeStyle = $statusColors[$status] ?? $statusColors['verified'];
        
        $statusText = match($status) {
            'accepted' => 'Selamat! Pendaftaran Diterima',
            'rejected' => 'Mohon Maaf, Pendaftaran Ditolak',
            'verified' => 'Pendaftaran Terverifikasi',
            default => 'Status Pendaftaran Diperbarui',
        };
    @endphp

    <p class="greeting">Halo <strong>{{ $registration->full_name }}</strong>,</p>
    
    <p class="body-text">
        Kami ingin mengabarkan bahwa status pendaftaran Anda di <strong>{{ Setting::get('school_name', 'Sekolah') }}</strong> telah diperbarui:
    </p>

    <div style="text-align: center; margin: 24px 0;">
        <span style="display: inline-block; padding: 8px 24px; border-radius: 50px; font-weight: 800; font-size: 14px; text-transform: uppercase; {{ $badgeStyle }}">
            {{ $statusText }}
        </span>
    </div>
    
    <table class="info-table">
        <tr>
            <td class="label">No. Pendaftaran</td>
            <td class="value">{{ $registration->registration_number }}</td>
        </tr>
        <tr>
            <td class="label">Waktu Pembaruan</td>
            <td class="value">{{ $registration->updated_at->format('d M Y, H:i') }}</td>
        </tr>
    </table>

    @if($registration->verification_notes)
        <div class="warning-box" style="background-color: #fffbeb; border: 1px solid #fde68a; color: #b45309;">
            <strong>Catatan Panitia:</strong><br>
            {{ $registration->verification_notes }}
        </div>
    @endif
    
    @if($status === 'accepted')
        <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 14px; padding: 20px; margin: 24px 0; color: #166534; font-size: 14px; line-height: 1.6;">
            <strong>Selamat Bergabung!</strong> Anda telah diterima sebagai calon siswa baru. Silakan masuk ke dashboard portal untuk melakukan registrasi/daftar ulang dan penyelesaian administrasi.
        </div>
        <div class="button-wrapper">
            <a href="{{ route('login') }}" class="action-button">Masuk & Daftar Ulang</a>
        </div>
    @elseif($status === 'verified')
        <p class="body-text">
            Dokumen pendaftaran Anda telah berhasil terverifikasi. Silakan pantau terus dashboard Anda untuk jadwal wawancara, tes seleksi, atau informasi penting berikutnya.
        </p>
        <div class="button-wrapper">
            <a href="{{ route('login') }}" class="action-button">Cek Dashboard</a>
        </div>
    @else
        <p class="body-text">
            Terima kasih telah berpartisipasi dalam proses seleksi penerimaan siswa baru kami. Silakan hubungi panitia jika ada hal yang kurang jelas terkait keputusan ini.
        </p>
    @endif
    
    <p class="body-text" style="font-size: 13px; color: #64748b; margin-top: 30px;">
        Hormat kami,<br>
        <strong>Panitia SPMB {{ Setting::get('school_name', 'Sekolah') }}</strong>
    </p>
@endsection
