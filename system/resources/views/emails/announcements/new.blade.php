@extends('layouts.email')

@section('subject', 'Pengumuman Baru: ' . $announcement->title . ' - ' . Setting::get('school_name', 'Sekolah'))

@section('content')
    @php
        $typeClass = $announcement->type ?? 'general';
        $badgeColors = [
            'general' => 'background-color: #f1f5f9; color: #475569;',
            'academic' => 'background-color: #dbeafe; color: #1e40af;',
            'event' => 'background-color: #f3e8ff; color: #6b21a8;',
            'urgent' => 'background-color: #fee2e2; color: #991b1b;'
        ];
        $badgeStyle = $badgeColors[$typeClass] ?? $badgeColors['general'];
    @endphp

    <span style="display: inline-block; padding: 4px 12px; border-radius: 50px; font-weight: 700; font-size: 11px; text-transform: uppercase; margin-bottom: 16px; {{ $badgeStyle }}">
        {{ $announcement->type ?? 'GENERAL' }}
    </span>
    
    <h2 style="font-size: 20px; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 8px; letter-spacing: -0.02em;">{{ $announcement->title }}</h2>
    
    <p style="font-size: 12px; color: #64748b; margin-top: 0; margin-bottom: 24px; font-weight: 600;">
        {{ $announcement->created_at->format('d M Y') }} &bull; Oleh {{ $announcement->author->name ?? 'Admin' }}
    </p>
    
    <div class="body-text" style="white-space: pre-line;">
        {{ $announcement->content }}
    </div>
    
    <div class="button-wrapper">
        <a href="{{ route('student.announcements') }}" class="action-button">Lihat Semua Pengumuman</a>
    </div>
    
    <p class="body-text" style="font-size: 13px; color: #64748b; margin-top: 30px;">
        Hormat kami,<br>
        <strong>Sistem {{ Setting::get('school_name', 'Sekolah') }}</strong>
    </p>
@endsection
