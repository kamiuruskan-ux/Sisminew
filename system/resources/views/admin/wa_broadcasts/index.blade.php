@extends('layouts.admin')

@section('title', 'Broadcast WhatsApp')
@section('page_title', 'Manajemen Broadcast WhatsApp')

@section('content')
<div class="space-y-6 pb-16 w-full" x-data="{
    showDeleteModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    confirmDelete(id, title) {
        this.deleteTarget = { id: id, name: title };
        this.deleteFormAction = '{{ url('admin/wa-broadcasts') }}/' + id;
        this.showDeleteModal = true;
    }
}">
    @include('components.delete-modal', ['title' => 'Hapus Broadcast WhatsApp', 'message' => 'Apakah Anda yakin ingin menghapus riwayat broadcast :name ini? Tindakan ini tidak dapat dibatalkan.'])

    <!-- Compact Hero Header -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-8 lg:p-8 text-white shadow-xl shadow-emerald-950/20 border border-slate-800/80">
        <!-- Ambient Glowing Lights -->
        <div class="absolute -right-16 -top-16 w-80 h-80 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 -bottom-24 w-96 h-96 bg-teal-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="max-w-xl space-y-2.5">
                <div class="inline-flex items-center space-x-2 px-3.5 py-1 bg-white/10 backdrop-blur-md rounded-full border border-white/10 text-xs font-extrabold text-emerald-200 shadow-sm">
                    <span class="w-2 h-2 rounded-full {{ $activeProvider === 'disabled' ? 'bg-amber-400' : 'bg-emerald-400 animate-pulse' }}"></span>
                    <span>Provider WA Gateway: <strong class="uppercase text-white font-black tracking-wider">{{ $activeProvider }}</strong></span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white leading-tight">
                    Broadcast WhatsApp Massal
                </h1>
                <p class="text-xs sm:text-sm text-emerald-100/80 leading-relaxed font-medium">
                    Kirim pengumuman penting, notifikasi presensi, dan tagihan sekolah secara serentak ke Siswa, Orang Tua, Guru, atau Kelas tertentu.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3 shrink-0 self-start sm:self-auto">
                <a href="{{ route('admin.settings', ['tab' => 'wagateway']) }}" 
                   class="inline-flex items-center justify-center h-11 px-5 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-extrabold text-xs uppercase tracking-wider transition-all duration-200 border border-white/15 backdrop-blur-md shadow-sm space-x-2 hover:scale-[1.02] active:scale-95">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Setting Gateway</span>
                </a>
                <a href="{{ route('admin.wa-broadcasts.create') }}" 
                   class="inline-flex items-center justify-center h-11 px-5 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-400 hover:to-teal-300 text-slate-950 font-extrabold text-xs uppercase tracking-wider transition-all duration-200 shadow-lg shadow-emerald-500/25 hover:scale-[1.02] active:scale-95 space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
                    <span>Buat Broadcast Baru</span>
                </a>
            </div>
        </div>
    </div>


    @if($activeProvider === 'disabled')
        <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-amber-500/15 via-orange-500/10 to-amber-500/15 border border-amber-500/30 text-amber-900 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm">
            <div class="flex items-center space-x-3.5">
                <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-600 flex items-center justify-center shrink-0 border border-amber-500/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h4 class="font-extrabold text-sm text-slate-900">WhatsApp Gateway Sedang Nonaktif</h4>
                    <p class="text-xs text-slate-600 font-medium mt-0.5">Aktifkan provider Fonnte atau Onesender agar pengiriman notifikasi & broadcast dapat berjalan.</p>
                </div>
            </div>
            <a href="{{ route('admin.settings', ['tab' => 'wagateway']) }}" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-extrabold text-xs uppercase tracking-wider shrink-0 shadow-sm transition-all text-center">
                Aktifkan Provider Sekarang →
            </a>
        </div>
    @endif

    <!-- Metrics Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <!-- Total Sessions -->
        <div class="premium-card p-5 relative overflow-hidden group hover:border-indigo-300 transition-all duration-300">
            <div class="flex items-center justify-between relative z-10">
                <div class="space-y-1">
                    <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Total Sesi Broadcast</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($totalBroadcasts) }}</p>
                    <p class="text-[10px] text-slate-400 font-medium">Sesi pengiriman massal</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-extrabold border border-indigo-100 shadow-2xs group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                </div>
            </div>
        </div>

        <!-- Total Success -->
        <div class="premium-card p-5 relative overflow-hidden group hover:border-emerald-300 transition-all duration-300">
            <div class="flex items-center justify-between relative z-10">
                <div class="space-y-1">
                    <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Pesan Berhasil Terkirim</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-emerald-600 tracking-tight">{{ number_format($totalSent) }}</p>
                    <p class="text-[10px] text-emerald-600/80 font-semibold">Tergapai ke nomor tujuan</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-extrabold border border-emerald-100 shadow-2xs group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
        </div>

        <!-- Total Failed -->
        <div class="premium-card p-5 relative overflow-hidden group hover:border-rose-300 transition-all duration-300">
            <div class="flex items-center justify-between relative z-10">
                <div class="space-y-1">
                    <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Pesan Gagal Terkirim</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-rose-600 tracking-tight">{{ number_format($totalFailed) }}</p>
                    <p class="text-[10px] text-rose-500/80 font-semibold">Nomor tidak valid / error</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-extrabold border border-rose-100 shadow-2xs group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Container Card -->
    <div class="premium-card overflow-hidden shadow-md border-slate-200">
        <!-- Card Header with Filters -->
        <div class="px-6 sm:px-8 py-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/50">
            <div>
                <h3 class="font-extrabold text-slate-900 text-base tracking-tight">Riwayat Pengiriman Broadcast</h3>
                <p class="text-xs text-slate-400 font-semibold mt-0.5">Daftar riwayat pesan massal yang telah diproses oleh gateway</p>
            </div>

            <!-- Filter & Search Form -->
            <form method="GET" action="{{ route('admin.wa-broadcasts.index') }}" class="flex flex-wrap items-center gap-3">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           @input.debounce.400ms="$el.closest('form').submit()"
                           x-init="if ('{{ request('search') }}') { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length); }"
                           placeholder="Cari judul broadcast..."
                           class="w-56 sm:w-64 pl-9 pr-8 py-2 bg-white border border-slate-200/90 rounded-xl text-xs font-semibold text-slate-800 placeholder-slate-400 outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-2xs">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    @if(request('search'))
                        <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</a>
                    @endif
                </div>

                <select name="status" onchange="this.form.submit()" 
                        class="px-3.5 py-2 bg-white border border-slate-200/90 rounded-xl text-xs font-bold text-slate-700 outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 shadow-2xs transition-all cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </form>
        </div>

        <!-- Table View -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/80 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                        <th class="px-6 py-3.5 w-2/5">Judul Broadcast & Tanggal</th>
                        <th class="px-6 py-3.5 w-1/5">Target Audiens</th>
                        <th class="px-6 py-3.5 w-1/6">Total Penerima</th>
                        <th class="px-6 py-3.5 w-1/5">Status & Persentase Selesai</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-semibold text-slate-700">
                    @forelse($broadcasts as $item)
                        <tr class="hover:bg-slate-50/60 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <a href="{{ route('admin.wa-broadcasts.show', $item->id) }}" class="font-extrabold text-slate-900 group-hover:text-emerald-600 text-sm transition-colors block leading-snug">
                                        {{ $item->title }}
                                    </a>
                                    <div class="flex items-center space-x-2 text-[11px] text-slate-400 font-medium">
                                        <span>{{ $item->created_at->format('d M Y, H:i') }} WIB</span>
                                        <span>•</span>
                                        <span class="text-slate-500 font-bold">Oleh {{ $item->creator->name ?? 'Admin' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200/80 shadow-2xs">
                                    {{ $item->target_type_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-800">
                                <div class="flex items-center space-x-1.5">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                    <span>{{ number_format($item->total_recipients) }} Kontak</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1.5 min-w-[150px]">
                                    <div class="flex items-center justify-between text-[11px] font-extrabold">
                                        <span class="text-emerald-600 flex items-center space-x-1">
                                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                            <span>{{ $item->success_count }} Berhasil</span>
                                        </span>
                                        @if($item->failed_count > 0)
                                            <span class="text-rose-500 flex items-center space-x-1">
                                                <span class="w-1.5 h-1.5 bg-rose-500 rounded-full"></span>
                                                <span>{{ $item->failed_count }} Gagal</span>
                                            </span>
                                        @endif
                                    </div>
                                    @php
                                        $percent = $item->total_recipients > 0 ? round(($item->success_count / $item->total_recipients) * 100) : 0;
                                    @endphp
                                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden p-0.5 border border-slate-200/60">
                                        <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-full rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.wa-broadcasts.show', $item->id) }}" 
                                       class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white font-extrabold text-xs transition-all border border-emerald-200/80 shadow-2xs flex items-center space-x-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Detail Log</span>
                                    </a>
                                    <button type="button" @click="confirmDelete({{ $item->id }}, '{{ addslashes($item->title ?: 'Broadcast #' . $item->id) }}')" class="p-1.5 rounded-xl bg-slate-100 hover:bg-rose-600 hover:text-white text-slate-500 transition-all border border-slate-200 cursor-pointer" title="Hapus Broadcast">
                                         <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                     </button>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-slate-400">
                                <div class="max-w-sm mx-auto space-y-4">
                                    <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto border border-slate-200 shadow-sm">
                                        <svg class="w-7 h-7 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                    </div>
                                    <div class="space-y-1">
                                        <h4 class="font-extrabold text-base text-slate-800">Belum ada riwayat broadcast</h4>
                                        <p class="text-xs text-slate-400 leading-relaxed font-medium">Buat broadcast WhatsApp baru untuk mempublikasikan informasi penting atau pengumuman ke seluruh siswa dan orang tua.</p>
                                    </div>
                                    <a href="{{ route('admin.wa-broadcasts.create') }}" class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs uppercase tracking-wider shadow-md transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
                                        <span>Buat Broadcast Sekarang</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($broadcasts->hasPages())
            <div class="px-6 sm:px-8 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $broadcasts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
