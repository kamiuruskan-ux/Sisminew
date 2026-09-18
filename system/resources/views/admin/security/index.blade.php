@extends('layouts.admin')

@section('title', 'Keamanan Sistem & Audit Security')

@section('content')
<div class="space-y-6 w-full pb-12" x-data="{ tab: '{{ request()->has('tab') ? request()->query('tab') : 'logs' }}' }">
    <!-- Hero Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 p-6 sm:p-8 text-white border border-slate-800 shadow-2xl">
        <!-- Ambient Glowing Orbs -->
        <div class="absolute -right-16 -top-16 w-80 h-80 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-16 -bottom-16 w-80 h-80 bg-cyan-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center space-x-2 px-3 py-1 bg-indigo-500/20 rounded-full text-xs font-extrabold text-indigo-300 border border-indigo-500/30 backdrop-blur-md">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span>MONITORING SECURITY <strong class="text-white uppercase tracking-wider">AKTIF</strong></span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white flex items-center gap-2.5">
                    <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span>Keamanan Sistem & Audit Log</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 font-medium leading-relaxed">
                    Pantau log percobaan masuk real-time, kelola penguncian akun/IP otomatis, serta atur ambang batas proteksi anti brute-force.
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('admin.security.index') }}" 
                   class="px-4 py-2.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-extrabold text-xs transition-all border border-white/15 backdrop-blur-md flex items-center space-x-2 shadow-xs">
                    <svg class="w-4 h-4 text-indigo-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Refresh Log</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Flash Alerts -->
    @if(session('success'))
    <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-2xl text-emerald-700 dark:text-emerald-300 text-xs font-bold flex items-center justify-between shadow-xs">
        <div class="flex items-center space-x-3">
            <div class="w-7 h-7 rounded-xl bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <span>{{ session('success') }}</span>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 bg-rose-500/10 border border-rose-500/30 rounded-2xl text-rose-700 dark:text-rose-300 text-xs font-bold flex items-center justify-between shadow-xs">
        <div class="flex items-center space-x-3">
            <div class="w-7 h-7 rounded-xl bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <span>{{ session('error') }}</span>
        </div>
    </div>
    @endif

    <!-- Metric KPI Cards (Proportional Grid) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <!-- Log Aktivitas Hari Ini -->
        <div class="bg-white dark:bg-[#1A222C] p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-all flex items-center justify-between group">
            <div class="space-y-1">
                <p class="text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Log Aktivitas Hari Ini</p>
                <h3 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ number_format($stats['total_today']) }}</h3>
                <p class="text-[11px] font-semibold text-indigo-600 dark:text-indigo-400 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                    <span>Total percobaan masuk</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-100 dark:border-indigo-900/50 shrink-0 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </div>
        </div>

        <!-- Percobaan Gagal -->
        <div class="bg-white dark:bg-[#1A222C] p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-all flex items-center justify-between group">
            <div class="space-y-1">
                <p class="text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Percobaan Gagal</p>
                <h3 class="text-2xl sm:text-3xl font-black text-rose-600 dark:text-rose-400 tracking-tight">{{ number_format($stats['failed_today']) }}</h3>
                <p class="text-[11px] font-semibold text-rose-500 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                    <span>Dideteksi hari ini</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center border border-rose-100 dark:border-rose-900/50 shrink-0 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- Akun Terkunci -->
        <div class="bg-white dark:bg-[#1A222C] p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-all flex items-center justify-between group">
            <div class="space-y-1">
                <p class="text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Akun Terkunci</p>
                <h3 class="text-2xl sm:text-3xl font-black text-amber-600 dark:text-amber-400 tracking-tight">{{ number_format($stats['locked_users_count']) }}</h3>
                <p class="text-[11px] font-semibold text-amber-500 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    <span>Penguncian aktif saat ini</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-100 dark:border-amber-900/50 shrink-0 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
        </div>

        <!-- IP Mencurigakan -->
        <div class="bg-white dark:bg-[#1A222C] p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-all flex items-center justify-between group">
            <div class="space-y-1">
                <p class="text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">IP Mencurigakan</p>
                <h3 class="text-2xl sm:text-3xl font-black text-cyan-600 dark:text-cyan-400 tracking-tight">{{ number_format($stats['unique_ips_failed']) }}</h3>
                <p class="text-[11px] font-semibold text-cyan-500 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-500"></span>
                    <span>Unik IP gagal hari ini</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-cyan-50 dark:bg-cyan-950/60 text-cyan-600 dark:text-cyan-400 flex items-center justify-center border border-cyan-100 dark:border-cyan-900/50 shrink-0 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
            </div>
        </div>
    </div>

    <!-- Main Navigation Tabs -->
    <div class="bg-white dark:bg-[#1A222C] rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <!-- Floating Tab Bar Header -->
        <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/50 p-2 overflow-x-auto">
            <button @click="tab = 'logs'" 
                    :class="tab === 'logs' ? 'bg-white dark:bg-[#1A222C] text-indigo-600 dark:text-indigo-400 shadow-sm border border-slate-200 dark:border-slate-700 font-extrabold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-semibold'"
                    class="px-5 py-3 rounded-2xl text-xs transition-all flex items-center space-x-2 shrink-0 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span>Audit Log Keamanan</span>
            </button>

            <button @click="tab = 'locked'" 
                    :class="tab === 'locked' ? 'bg-white dark:bg-[#1A222C] text-amber-600 dark:text-amber-400 shadow-sm border border-slate-200 dark:border-slate-700 font-extrabold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-semibold'"
                    class="px-5 py-3 rounded-2xl text-xs transition-all flex items-center space-x-2 shrink-0 cursor-pointer relative">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span>Akun Terkunci & IP</span>
                @if(count($lockedUsers) > 0)
                    <span class="ml-1.5 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-500 text-white shadow-xs animate-pulse">{{ count($lockedUsers) }}</span>
                @endif
            </button>

            <button @click="tab = 'settings'" 
                    :class="tab === 'settings' ? 'bg-white dark:bg-[#1A222C] text-indigo-600 dark:text-indigo-400 shadow-sm border border-slate-200 dark:border-slate-700 font-extrabold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-semibold'"
                    class="px-5 py-3 rounded-2xl text-xs transition-all flex items-center space-x-2 shrink-0 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
                <span>Parameter Keamanan</span>
            </button>
        </div>

        <!-- TAB 1: AUDIT LOGS -->
        <div x-show="tab === 'logs'" class="p-6 space-y-5">
            <!-- Filter Bar Card -->
            <form method="GET" action="{{ route('admin.security.index') }}" class="p-4 rounded-2xl bg-slate-50/80 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-center gap-3">
                <input type="hidden" name="tab" value="logs">
                <div class="relative flex-1 w-full">
                    <input type="text" name="q" value="{{ $searchQuery }}" 
                           @input.debounce.400ms="$el.closest('form').submit()"
                           x-init="if ('{{ $searchQuery }}') { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length); }"
                           placeholder="Cari IP Address, Email, Username, NISN, atau keterangan..."
                           class="w-full pl-10 pr-8 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#1A222C] text-xs text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    @if($searchQuery)
                        <a href="{{ request()->fullUrlWithQuery(['q' => null]) }}" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</a>
                    @endif
                </div>
                
                <select name="status" @change="$el.closest('form').submit()" class="w-full sm:w-auto py-2.5 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#1A222C] text-xs font-semibold text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Status Log</option>
                    <option value="success" {{ $statusFilter == 'success' ? 'selected' : '' }}>Sukses (Success)</option>
                    <option value="failed_password" {{ $statusFilter == 'failed_password' ? 'selected' : '' }}>Password Salah</option>
                    <option value="captcha_failed" {{ $statusFilter == 'captcha_failed' ? 'selected' : '' }}>CAPTCHA Salah</option>
                    <option value="locked_account" {{ $statusFilter == 'locked_account' ? 'selected' : '' }}>Akun Terkunci</option>
                    <option value="blocked_ip" {{ $statusFilter == 'blocked_ip' ? 'selected' : '' }}>IP Terblokir</option>
                </select>
            </form>

            <!-- Audit Logs Table -->
            <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/60 text-slate-500 uppercase tracking-wider font-extrabold border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-5 py-3.5">Waktu Percobaan</th>
                            <th class="px-5 py-3.5">Identifier & User</th>
                            <th class="px-5 py-3.5">IP Address</th>
                            <th class="px-5 py-3.5 text-center">Status Security</th>
                            <th class="px-5 py-3.5">Keterangan / Alasan Audit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                        @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <p class="font-bold text-slate-800 dark:text-white">{{ $log->created_at->translatedFormat('d M Y, H:i:s') }}</p>
                                <span class="text-[10px] text-slate-400 font-medium">{{ $log->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center font-extrabold text-[11px] border border-slate-200 dark:border-slate-700 shrink-0">
                                        {{ strtoupper(substr($log->identifier, 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-900 dark:text-white truncate">{{ $log->identifier }}</p>
                                        @if($log->user)
                                        <p class="text-[10px] text-indigo-600 dark:text-indigo-400 font-semibold truncate">{{ $log->user->name }} (#{{ $log->user_id }})</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <span class="font-mono text-[11px] px-2.5 py-1 bg-slate-100 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200 font-semibold">
                                    {{ $log->ip_address }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                @if($log->status === 'success')
                                    <span class="inline-flex items-center space-x-1.5 px-3 py-1 text-[10px] font-extrabold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 rounded-full border border-emerald-200 dark:border-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>SUKSES</span>
                                    </span>
                                @elseif($log->status === 'locked_account')
                                    <span class="inline-flex items-center space-x-1.5 px-3 py-1 text-[10px] font-extrabold bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 rounded-full border border-amber-200 dark:border-amber-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        <span>AKUN TERKUNCI</span>
                                    </span>
                                @elseif($log->status === 'blocked_ip')
                                    <span class="inline-flex items-center space-x-1.5 px-3 py-1 text-[10px] font-extrabold bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 rounded-full border border-rose-200 dark:border-rose-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        <span>IP DIBLOKIR</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center space-x-1.5 px-3 py-1 text-[10px] font-extrabold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 rounded-full border border-slate-200 dark:border-slate-700">
                                        <span>{{ strtoupper(str_replace('_', ' ', $log->status)) }}</span>
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-slate-600 dark:text-slate-300 max-w-xs">
                                <p class="text-xs truncate" title="{{ $log->failure_reason ?? '-' }}">{{ $log->failure_reason ?? '-' }}</p>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-slate-400 text-xs font-medium">
                                Tidak ada log audit keamanan ditemukan sesuai kriteria filter.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="pt-2">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>

        <!-- TAB 2: LOCKED ACCOUNTS & MANUAL IP BLOCKING (Proportional 2-Column Grid) -->
        <div x-show="tab === 'locked'" class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <!-- Left Section: Locked Accounts Table (8 Cols) -->
                <div class="lg:col-span-8 space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center space-x-2">
                                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span>Daftar Akun yang Sedang Terkunci</span>
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Daftar pengguna yang diblokir sementara karena salah password berulang.</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                            {{ count($lockedUsers) }} Terkunci
                        </span>
                    </div>

                    <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 dark:bg-slate-900/60 text-slate-500 uppercase tracking-wider font-extrabold border-b border-slate-200 dark:border-slate-800">
                                <tr>
                                    <th class="px-5 py-3.5">Nama Pengguna</th>
                                    <th class="px-5 py-3.5">Email / Identifier</th>
                                    <th class="px-5 py-3.5 text-center">Gagal Login</th>
                                    <th class="px-5 py-3.5">Terkunci Sampai</th>
                                    <th class="px-5 py-3.5 text-right">Aksi Buka Kunci</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                                @forelse($lockedUsers as $u)
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300 font-black flex items-center justify-center text-xs shrink-0 border border-amber-200 dark:border-amber-800">
                                                {{ strtoupper(substr($u->name, 0, 2)) }}
                                            </div>
                                            <span class="font-bold text-slate-900 dark:text-white">{{ $u->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 font-mono text-[11px]">
                                        {{ $u->email }}
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                            {{ $u->failed_login_attempts }}x Gagal
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <p class="font-semibold text-amber-600 dark:text-amber-400 text-xs">{{ $u->locked_until?->translatedFormat('d M Y H:i:s') }}</p>
                                        <span class="text-[10px] text-slate-400 font-medium">({{ ceil(now()->diffInSeconds($u->locked_until) / 60) }} menit lagi)</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <form action="{{ route('admin.security.unlock-user', $u->id) }}" method="POST" onsubmit="return confirm('Buka kunci akun {{ addslashes($u->name) }} sekarang?')">
                                            @csrf
                                            <button type="submit" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all shadow-xs cursor-pointer inline-flex items-center space-x-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                                <span>Buka Kunci</span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center text-slate-400 text-xs font-medium">
                                        Saat ini tidak ada akun pengguna yang sedang terkunci.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Right Section: Manual IP Block Form Card (4 Cols) -->
                <div class="lg:col-span-4 space-y-4">
                    <div class="bg-gradient-to-br from-slate-50 to-white dark:from-slate-900/80 dark:to-[#1A222C] p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-5" x-data="{ duration: 60 }">
                        <div class="flex items-center space-x-3 border-b border-slate-200 dark:border-slate-800 pb-4">
                            <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 dark:bg-rose-950/60 dark:text-rose-400 flex items-center justify-center font-bold border border-rose-100 dark:border-rose-900/50 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-extrabold text-slate-900 dark:text-white">Blokir IP Address Manual</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Blokir alamat IP tertentu yang terindikasi melancarkan serangan brute-force.</p>
                            </div>
                        </div>

                        <form action="{{ route('admin.security.block-ip') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Alamat IP Target <span class="text-rose-500">*</span></label>
                                <input type="text" name="ip_address" required placeholder="Contoh: 192.168.1.50"
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#1A222C] text-xs font-mono font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:border-transparent transition-all">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Durasi Blokir (Menit) <span class="text-rose-500">*</span></label>
                                <input type="number" name="duration_minutes" x-model="duration" min="1" max="10080" required
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#1A222C] text-xs font-mono font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 focus:border-transparent transition-all">
                                
                                <!-- Preset Buttons -->
                                <div class="flex items-center gap-2 mt-2">
                                    <button type="button" @click="duration = 60" class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-rose-500 hover:text-white transition-all cursor-pointer">1 Jam</button>
                                    <button type="button" @click="duration = 1440" class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-rose-500 hover:text-white transition-all cursor-pointer">24 Jam</button>
                                    <button type="button" @click="duration = 10080" class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-rose-500 hover:text-white transition-all cursor-pointer">7 Hari</button>
                                </div>
                            </div>

                            <button type="submit" class="w-full py-3 bg-rose-600 hover:bg-rose-700 text-white text-xs font-extrabold rounded-xl transition-all shadow-md cursor-pointer flex items-center justify-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span>Terapkan Blokir IP</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 3: PARAMETER CONFIGURATION (Proportional Layout Grid) -->
        <div x-show="tab === 'settings'" class="p-6">
            <form action="{{ route('admin.security.settings.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Parameter Cards Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <!-- Left Card: Batas Ambang & Lockout Policy (7 Cols) -->
                    <div class="lg:col-span-7 bg-slate-50/70 dark:bg-slate-900/40 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 space-y-5">
                        <div class="flex items-center space-x-3 border-b border-slate-200 dark:border-slate-800 pb-4">
                            <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-400 flex items-center justify-center font-bold border border-indigo-100 dark:border-indigo-900 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Kebijakan Batas Login & Penguncian</h3>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Atur batas kegagalan password per-akun sebelum dikunci otomatis.</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <!-- Max Login Attempts -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Batas Maksimal Gagal Login (Per-Akun)</label>
                                <div class="relative">
                                    <input type="number" name="security_max_login_attempts" value="{{ $settings['max_attempts'] }}" min="2" max="20" required
                                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#1A222C] text-xs font-mono font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all">
                                    <span class="absolute right-3.5 top-2.5 text-xs font-bold text-slate-400">kali</span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1">Jumlah kesempatan kesalahan password sebelum akun dikunci (rekomendasi: 5 kali).</p>
                            </div>

                            <!-- Lockout Duration -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Durasi Penguncian Akun</label>
                                <div class="relative">
                                    <input type="number" name="security_lockout_duration" value="{{ $settings['lockout_duration'] }}" min="1" max="1440" required
                                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#1A222C] text-xs font-mono font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all">
                                    <span class="absolute right-3.5 top-2.5 text-xs font-bold text-slate-400">menit</span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1">Lama waktu pembekuan akun jika batas kegagalan terlampaui (rekomendasi: 15 menit).</p>
                            </div>

                            <!-- CAPTCHA Threshold -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Ambang Batas CAPTCHA Challenge</label>
                                <div class="relative">
                                    <input type="number" name="security_captcha_threshold" value="{{ $settings['captcha_threshold'] }}" min="1" max="10" required
                                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#1A222C] text-xs font-mono font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all">
                                    <span class="absolute right-3.5 top-2.5 text-xs font-bold text-slate-400">kali</span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1">Gagal berapa kali sebelum halaman login menampilkan verifikasi CAPTCHA (rekomendasi: 3 kali).</p>
                            </div>
                        </div>
                    </div>

                    <!-- Right Card: Keamanan IP & Sesi Pengguna (5 Cols) -->
                    <div class="lg:col-span-5 bg-slate-50/70 dark:bg-slate-900/40 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 space-y-5">
                        <div class="flex items-center space-x-3 border-b border-slate-200 dark:border-slate-800 pb-4">
                            <div class="w-9 h-9 rounded-xl bg-cyan-50 text-cyan-600 dark:bg-cyan-950 dark:text-cyan-400 flex items-center justify-center font-bold border border-cyan-100 dark:border-cyan-900 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Keamanan IP & Sesi</h3>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Pengaturan blacklist IP otomatis & inaktivitas sesi.</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <!-- IP Blacklist Attempts -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Batas Maksimal Gagal Per IP Address</label>
                                <div class="relative">
                                    <input type="number" name="security_ip_blacklist_attempts" value="{{ $settings['ip_max_attempts'] }}" min="3" max="50" required
                                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#1A222C] text-xs font-mono font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all">
                                    <span class="absolute right-3.5 top-2.5 text-xs font-bold text-slate-400">kali</span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1">Batas akumulasi gagal per IP sebelum IP diblokir otomatis.</p>
                            </div>

                            <!-- Session Timeout -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Timeout Sesi Idle Pengguna</label>
                                <div class="relative">
                                    <input type="number" name="security_session_timeout" value="{{ $settings['session_timeout'] }}" min="5" max="480" required
                                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#1A222C] text-xs font-mono font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 transition-all">
                                    <span class="absolute right-3.5 top-2.5 text-xs font-bold text-slate-400">menit</span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1">Lama inaktivitas pengguna sebelum sesi otomatis di-logout.</p>
                            </div>

                            <!-- WhatsApp Alert Toggle -->
                            <div class="pt-2">
                                <label class="flex items-start space-x-3 p-3.5 rounded-2xl bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-700 cursor-pointer hover:border-indigo-300 transition-all group">
                                    <input type="checkbox" name="security_notify_failed_login" value="1" {{ $settings['notify_failed_login'] ? 'checked' : '' }}
                                           class="w-4 h-4 mt-0.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                    <div>
                                        <span class="block text-xs font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 transition-colors">Alert WhatsApp Saat Akun Dikunci</span>
                                        <p class="text-[10px] text-slate-400 font-medium">Kirim notifikasi otomatis ke WA admin jika ada akun terkunci.</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Submit Bar -->
                <div class="pt-4 flex items-center justify-end">
                    <button type="submit" class="w-full sm:w-auto px-8 py-3.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl transition-all shadow-lg shadow-indigo-600/30 cursor-pointer flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Parameter Keamanan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
