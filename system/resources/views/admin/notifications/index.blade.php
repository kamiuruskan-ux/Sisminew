@extends('layouts.admin')

@section('title', 'Notifikasi Sistem')
@section('page_title', 'Pusat Notifikasi')

@section('content')
<div class="space-y-6">

    <!-- Page Header & Summary Bar -->
    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight">Pusat Notifikasi Sistem</h1>
                <p class="text-xs text-slate-500 mt-1">
                    Pantau notifikasi pendaftaran SPMB baru, pembayaran yang belum lunas/telat, tugas akademik, dan edaran pengumuman.
                </p>
            </div>
            
            <div class="flex items-center space-x-2.5">
                <a href="{{ route('admin.notifications.broadcast') }}" class="inline-flex items-center space-x-2 px-3.5 py-2 bg-primary hover:bg-primary/90 text-white text-xs font-bold rounded-xl transition-all shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                    <span>Kirim Siaran (Broadcast)</span>
                </a>

                <form method="POST" action="{{ route('admin.notifications.mark-read') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center space-x-2 px-3.5 py-2 bg-slate-100 hover:bg-slate-200/80 text-slate-700 text-xs font-semibold rounded-xl transition-colors border border-slate-200">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Tandai Semua Dibaca</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="flex flex-wrap items-center gap-2 mt-6 pt-5 border-t border-slate-100">
            <a href="{{ route('admin.notifications.index', ['category' => 'all']) }}"
               class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $category === 'all' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <span>Semua</span>
                <span class="px-1.5 py-0.5 text-[10px] rounded-full {{ $category === 'all' ? 'bg-indigo-700 text-indigo-100' : 'bg-slate-200 text-slate-700' }}">
                    {{ $counts['all'] }}
                </span>
            </a>

            @if(auth()->user()->hasPermission('view-spmb'))
            <a href="{{ route('admin.notifications.index', ['category' => 'spmb']) }}"
               class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $category === 'spmb' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <span>Pendaftaran SPMB</span>
                <span class="px-1.5 py-0.5 text-[10px] rounded-full {{ $category === 'spmb' ? 'bg-blue-700 text-blue-100' : 'bg-slate-200 text-slate-700' }}">
                    {{ $counts['spmb'] }}
                </span>
            </a>
            @endif

            @if(auth()->user()->hasPermission('view-financial'))
            <a href="{{ route('admin.notifications.index', ['category' => 'payment']) }}"
               class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $category === 'payment' ? 'bg-amber-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <span>Pembayaran</span>
                <span class="px-1.5 py-0.5 text-[10px] rounded-full {{ $category === 'payment' ? 'bg-amber-700 text-amber-100' : 'bg-slate-200 text-slate-700' }}">
                    {{ $counts['payment'] }}
                </span>
            </a>
            @endif

            @if(auth()->user()->hasPermission('view-learning'))
            <a href="{{ route('admin.notifications.index', ['category' => 'assignment']) }}"
               class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $category === 'assignment' ? 'bg-purple-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <span>Tugas &amp; Akademik</span>
                <span class="px-1.5 py-0.5 text-[10px] rounded-full {{ $category === 'assignment' ? 'bg-purple-700 text-purple-100' : 'bg-slate-200 text-slate-700' }}">
                    {{ $counts['assignment'] }}
                </span>
            </a>
            @endif

            @if(auth()->user()->hasPermission('view-announcements'))
            <a href="{{ route('admin.notifications.index', ['category' => 'announcement']) }}"
               class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $category === 'announcement' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <span>Pengumuman</span>
                <span class="px-1.5 py-0.5 text-[10px] rounded-full {{ $category === 'announcement' ? 'bg-emerald-700 text-emerald-100' : 'bg-slate-200 text-slate-700' }}">
                    {{ $counts['announcement'] }}
                </span>
            </a>
            @endif

            @if(auth()->user()->hasPermission('view-canteen-admin'))
            <a href="{{ route('admin.notifications.index', ['category' => 'canteen']) }}"
               class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $category === 'canteen' ? 'bg-rose-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <span>E-Kantin</span>
                <span class="px-1.5 py-0.5 text-[10px] rounded-full {{ $category === 'canteen' ? 'bg-rose-700 text-rose-100' : 'bg-slate-200 text-slate-700' }}">
                    {{ $counts['canteen'] ?? 0 }}
                </span>
            </a>
            @endif
        </div>
    </div>

    <!-- Notification Items List -->
    <div class="space-y-3">
        @forelse($notifications as $notif)
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs hover:border-indigo-300 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start space-x-3.5">
                    <div class="w-10 h-10 rounded-xl {{ $notif['icon_bg'] }} flex items-center justify-center shrink-0">
                        @if($notif['category'] === 'spmb')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        @elseif($notif['category'] === 'payment')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @elseif($notif['category'] === 'assignment')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        @elseif($notif['category'] === 'canteen')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        @endif
                    </div>
                    <div class="space-y-1">
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider rounded border {{ $notif['badge_class'] }}">
                                {{ $notif['badge'] }}
                            </span>
                            <span class="text-xs text-slate-400 font-medium">
                                {{ $notif['created_at'] ? $notif['created_at']->isoFormat('D MMMM Y, HH:mm') : '-' }} ({{ $notif['created_at'] ? $notif['created_at']->diffForHumans() : '-' }})
                            </span>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 leading-snug">{{ $notif['title'] }}</h3>
                        <p class="text-xs text-slate-600 leading-relaxed max-w-3xl">{{ $notif['message'] }}</p>
                    </div>
                </div>

                <div class="shrink-0 pt-2 sm:pt-0">
                    <a href="{{ $notif['url'] }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 text-xs font-semibold rounded-xl transition-colors border border-indigo-100">
                        <span>Lihat Detail</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-slate-200 p-12 text-center">
                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-slate-800">Tidak ada notifikasi dalam kategori ini</h3>
                <p class="text-xs text-slate-500 mt-1">Pilih kategori lain untuk melihat notifikasi sistem.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection
