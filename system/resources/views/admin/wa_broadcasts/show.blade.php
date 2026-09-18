@extends('layouts.admin')

@section('title', 'Detail Broadcast WhatsApp')
@section('page_title', 'Laporan Broadcast Pesan')

@section('content')
<div class="space-y-8 pb-20 w-full">
    <!-- Compact Hero Header -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-8 lg:p-10 text-white shadow-xl shadow-emerald-950/20 border border-slate-800/80">
        <div class="absolute -right-16 -top-16 w-80 h-80 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-3.5 py-1 bg-white/10 backdrop-blur-md rounded-full border border-white/10 text-xs font-extrabold text-emerald-200 shadow-sm">
                        Target: {{ $broadcast->target_type_label }}
                    </span>
                    <span class="px-3.5 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider {{ $broadcast->status === 'completed' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30' }}">
                        {{ $broadcast->status }}
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white leading-tight">
                    {{ $broadcast->title }}
                </h1>
                <p class="text-xs sm:text-sm text-emerald-100/80 font-medium">
                    Diproses pada {{ $broadcast->created_at->format('d M Y H:i') }} WIB • Oleh {{ $broadcast->creator->name ?? 'Admin' }}
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('admin.wa-broadcasts.index') }}" class="inline-flex items-center justify-center h-11 px-5 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-extrabold text-xs uppercase tracking-wider transition-all duration-200 border border-white/15 backdrop-blur-md shadow-sm space-x-2 hover:scale-[1.02] active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Analytical Metrics Cards Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="premium-card p-5 sm:p-6 space-y-1.5 border-slate-200">
            <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Total Penerima</p>
            <p class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($broadcast->total_recipients) }}</p>
            <p class="text-[10px] text-slate-400 font-medium">Kontak terdaftar</p>
        </div>

        <div class="premium-card p-5 sm:p-6 space-y-1.5 border-slate-200">
            <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Berhasil Terkirim</p>
            <p class="text-3xl font-extrabold text-emerald-600 tracking-tight">{{ number_format($broadcast->success_count) }}</p>
            <p class="text-[10px] text-emerald-600/80 font-semibold">Tergapai via Gateway</p>
        </div>

        <div class="premium-card p-5 sm:p-6 space-y-1.5 border-slate-200">
            <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Gagal Terkirim</p>
            <p class="text-3xl font-extrabold text-rose-600 tracking-tight">{{ number_format($broadcast->failed_count) }}</p>
            <p class="text-[10px] text-rose-500/80 font-semibold">Nomor tidak valid / error</p>
        </div>

        <div class="premium-card p-5 sm:p-6 space-y-1.5 border-slate-200">
            <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Tingkat Keberhasilan</p>
            @php
                $rate = $broadcast->total_recipients > 0 ? round(($broadcast->success_count / $broadcast->total_recipients) * 100, 1) : 0;
            @endphp
            <p class="text-3xl font-extrabold text-indigo-600 tracking-tight">{{ $rate }}%</p>
            <p class="text-[10px] text-indigo-600/80 font-semibold">Rasio sukses delivery</p>
        </div>
    </div>

    <!-- Content & Recipient Logs Proportional Split -->
    <div class="grid lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left: Master Message Card (5 Columns) -->
        <div class="lg:col-span-5 space-y-6">
            <div class="premium-card overflow-hidden shadow-sm border-slate-200" x-data="{ copied: false }">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/60 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <div class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></div>
                        <span class="font-extrabold text-xs text-slate-700 uppercase tracking-wider">Konten Pesan Master</span>
                    </div>
                    <button type="button" 
                            @click="navigator.clipboard.writeText('{{ e(addslashes($broadcast->message)) }}'); copied = true; setTimeout(() => copied = false, 2000)"
                            class="text-[11px] font-extrabold text-emerald-600 hover:text-emerald-700 flex items-center gap-1 cursor-pointer">
                        <span x-text="copied ? 'Tercopy!' : 'Salin Pesan'"></span>
                    </button>
                </div>

                <div class="p-6 bg-slate-950 text-slate-100 font-mono text-xs leading-relaxed border-t border-slate-800">
                    <p class="whitespace-pre-line">{{ $broadcast->message }}</p>
                </div>

                <div class="px-6 py-3 bg-slate-900 border-t border-slate-800 text-[10px] text-slate-400 font-medium flex items-center justify-between">
                    <span>Panjang karakter: {{ strlen($broadcast->message) }}</span>
                    <span>Provider: {{ strtoupper($activeProvider) }}</span>
                </div>
            </div>
        </div>

        <!-- Right: Recipient Logs Table (7 Columns) -->
        <div class="lg:col-span-7 space-y-6">
            <div class="premium-card overflow-hidden shadow-md border-slate-200">
                <div class="px-6 sm:px-8 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base tracking-tight">Log Detail Penerima</h3>
                        <p class="text-xs text-slate-400 font-semibold mt-0.5">Status pengiriman dan respon API gateway per nomor</p>
                    </div>
                    <span class="text-xs font-extrabold px-3 py-1 bg-slate-100 text-slate-700 rounded-full border border-slate-200">
                        {{ $broadcast->logs->count() }} Data Log
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/80 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                                <th class="px-6 py-3.5">Nama Penerima</th>
                                <th class="px-6 py-3.5">Nomor WA</th>
                                <th class="px-6 py-3.5">Status Delivery</th>
                                <th class="px-6 py-3.5">Respon Gateway</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs font-semibold text-slate-700">
                            @forelse($logs as $log)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="px-6 py-3.5 font-extrabold text-slate-900">
                                        {{ $log->recipient_name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-3.5 font-mono text-slate-600">
                                        <div class="flex items-center space-x-1.5">
                                            <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                            <span>{{ $log->recipient_phone }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        @if($log->status === 'sent')
                                            <span class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                                <span>TERKIRIM</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
                                                <span class="w-1.5 h-1.5 bg-rose-500 rounded-full"></span>
                                                <span>GAGAL</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3.5 text-slate-500 font-medium">
                                        {{ $log->response_message ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-400 font-medium">
                                        Tidak ada log penerima tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($logs->hasPages())
                    <div class="px-6 sm:px-8 py-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
