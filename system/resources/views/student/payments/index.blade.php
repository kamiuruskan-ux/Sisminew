@extends('layouts.student-mobile')

@section('title', 'Daftar Pos Pembayaran Tagihan')
@section('header_title', 'Pos Pembayaran')

@section('content')
<div class="space-y-6">

    <!-- Toast/Alert Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-xs font-semibold">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <!-- Header Summary Card -->
    <div class="bg-gradient-to-br from-indigo-600 via-indigo-700 to-blue-700 dark:from-indigo-900 dark:to-blue-950 rounded-2xl p-5 text-white shadow-lg relative overflow-hidden">
        <div class="absolute right-0 top-0 w-36 h-36 bg-white/10 rounded-full blur-md -translate-y-6 translate-x-6 pointer-events-none"></div>
        <div class="relative z-10 flex flex-col justify-between h-full space-y-4">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-indigo-200 block mb-1">Portal Pembayaran Siswa</span>
                <h2 class="text-lg sm:text-xl font-extrabold tracking-tight">{{ $student->name }}</h2>
                <p class="text-xs text-indigo-100 font-medium mt-0.5">Kelas: {{ $student->class?->name ?? 'Belum ada kelas' }} &bull; NISN: {{ $student->nisn ?? '-' }}</p>
            </div>
            
            <div class="pt-3 border-t border-white/15 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <p class="text-[10px] text-indigo-200 font-bold uppercase tracking-wider">Total Tunggakan Keseluruhan</p>
                    <p class="text-xl sm:text-2xl font-black text-white mt-0.5 font-mono">
                        Rp {{ number_format($totalUnpaidOverall, 0, ',', '.') }}
                    </p>
                </div>
                <div class="self-start sm:self-auto flex items-center gap-2">
                    <span class="px-3 py-1 bg-white/15 text-indigo-100 rounded-full text-[10px] font-extrabold uppercase border border-white/20">
                        {{ $totalUnpaidItemsOverall }} Item Tagihan Pending
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- PENDING TRANSACTIONS ALERT (If Any) -->
    @if(session('pending_manual_transaction') || ($pendingCount ?? 0) > 0)
        <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/80 flex items-center justify-between gap-3">
            <div class="flex items-center space-x-3 min-w-0">
                <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 font-bold shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="font-extrabold text-amber-900 dark:text-amber-200 text-xs">Bukti Transfer Menunggu Unggah</p>
                    <p class="text-[11px] text-amber-700 dark:text-amber-400 truncate mt-0.5">Ada transaksi manual yang belum dikirim bukti pembayarannya.</p>
                </div>
            </div>
        </div>
    @endif

    <!-- SECTION TITLE -->
    <div class="flex items-center justify-between pt-2">
        <div>
            <h3 class="text-sm font-extrabold text-slate-800 dark:text-white tracking-tight uppercase">Daftar Pos Pembayaran</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Klik salah satu pos pembayaran untuk masuk ke halaman transaksi & bayar.</p>
        </div>
    </div>

    <!-- POST GROUPS GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @forelse($postGroups as $postId => $group)
            @php
                $post = $group['post'];
                $hasUnpaid = $group['unpaid_amount'] > 0;
                $isBulanan = ($group['type'] === 'bulanan');
            @endphp
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-xs hover:border-indigo-500 dark:hover:border-indigo-600 hover:shadow-md transition-all flex flex-col justify-between space-y-4 group relative">
                <!-- Top Header: Icon + Code + Type -->
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center space-x-3 min-w-0">
                        <div class="w-11 h-11 rounded-2xl {{ $hasUnpaid ? 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800' : 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800' }} flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V9a2 2 0 012-2h2a2 2 0 012 2v12"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center space-x-2 mb-0.5">
                                <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-mono text-[9px] font-extrabold rounded">
                                    {{ $post->code }}
                                </span>
                                <span class="px-2 py-0.5 {{ $isBulanan ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-purple-50 text-purple-700 border-purple-200' }} text-[9px] font-bold rounded border">
                                    {{ $isBulanan ? 'Bulanan' : 'Bebas' }}
                                </span>
                            </div>
                            <h4 class="font-extrabold text-slate-900 dark:text-white text-sm truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                {{ $post->name }}
                            </h4>
                        </div>
                    </div>
                </div>

                <!-- Nominal & Status -->
                <div class="pt-2 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Tunggakan</p>
                        <p class="text-base font-black font-mono {{ $hasUnpaid ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }} mt-0.5">
                            Rp {{ number_format($group['unpaid_amount'], 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="text-right">
                        @if($hasUnpaid)
                            <span class="inline-flex items-center px-2.5 py-1 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 text-[10px] font-extrabold rounded-full border border-rose-200 dark:border-rose-800">
                                {{ $group['unpaid_bills'] }} Tagihan Pending
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-[10px] font-extrabold rounded-full border border-emerald-200 dark:border-emerald-800">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Lunas
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Enter Action Button -->
                <a href="{{ route('student.payments.show', $post->id) }}" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold rounded-xl text-xs flex items-center justify-center gap-2 transition-all shadow-xs group-hover:bg-indigo-700">
                    <span>Transaksi & Bayar Tagihan</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        @empty
            <div class="col-span-1 sm:col-span-2 text-center py-12 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6">
                <svg class="w-12 h-12 mx-auto text-slate-300 dark:text-slate-600 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold">Belum ada pos tagihan pembayaran yang diterbitkan untuk akun Anda.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection
