@extends('layouts.admin')

@section('title', 'Konfirmasi Setoran Manual')

@section('content')
<div class="space-y-6" x-data="{ 
    imgModalOpen: false, 
    imgModalSrc: '',
    approveModalOpen: false,
    rejectModalOpen: false,
    targetStudentName: '',
    targetAmount: '',
    targetActionUrl: '',
    openApprove(name, amount, url) {
        this.targetStudentName = name;
        this.targetAmount = amount;
        this.targetActionUrl = url;
        this.approveModalOpen = true;
    },
    openReject(name, amount, url) {
        this.targetStudentName = name;
        this.targetAmount = amount;
        this.targetActionUrl = url;
        this.rejectModalOpen = true;
    }
}">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Konfirmasi Setoran Manual</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Tinjau dan konfirmasi bukti transfer setoran manual ke tabungan siswa</p>
        </div>
        
        <!-- View Switcher -->
        <div class="inline-flex p-1.5 bg-slate-200/70 dark:bg-slate-800/80 rounded-2xl border border-slate-300 dark:border-slate-700 text-xs font-bold shadow-xs">
            <a href="{{ route('admin.savings.index') }}" 
               class="px-4 py-2 rounded-xl transition-all {{ request()->routeIs('admin.savings.index') ? 'bg-indigo-600 text-white shadow-md font-extrabold' : 'text-slate-700 dark:text-slate-200 hover:text-slate-900 dark:hover:text-white hover:bg-slate-300/50 dark:hover:bg-slate-700/50' }}">
                Daftar Rekening Tabungan
            </a>
            <a href="{{ route('admin.savings.manual-confirm') }}" 
               class="px-4 py-2 rounded-xl transition-all flex items-center gap-1.5 {{ request()->routeIs('admin.savings.manual-confirm') ? 'bg-indigo-600 text-white shadow-md font-extrabold' : 'text-slate-700 dark:text-slate-200 hover:text-slate-900 dark:hover:text-white hover:bg-slate-300/50 dark:hover:bg-slate-700/50' }}">
                <span>Konfirmasi Setoran Manual</span>
                @if(isset($pendingCount) && $pendingCount > 0)
                    <span class="px-1.5 py-0.5 rounded-full {{ request()->routeIs('admin.savings.manual-confirm') ? 'bg-white text-indigo-650' : 'bg-rose-500 text-white' }} text-[9px] font-black leading-none">{{ $pendingCount }}</span>
                @endif
            </a>
        </div>
    </div>


    <!-- Table Container -->
    @if($pendingManualDeposits->isEmpty())
        <div class="premium-card p-12 text-center flex flex-col items-center justify-center space-y-4">
            <div class="w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-950/30 flex items-center justify-center text-emerald-600 dark:text-emerald-450 shadow-xs">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="space-y-1">
                <h3 class="font-black text-slate-800 dark:text-white text-base">Semua Bersih!</h3>
                <p class="text-sm text-slate-450 dark:text-slate-400 font-semibold">Tidak ada bukti transfer setoran tabungan siswa yang pending verifikasi saat ini.</p>
            </div>
        </div>
    @else
        <div class="premium-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Tanggal & Invoice</th>
                            <th>Siswa</th>
                            <th>Jumlah Setoran</th>
                            <th>Catatan Siswa</th>
                            <th>Bukti TF</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingManualDeposits as $pendingPay)
                            @php
                                $studentObj = $pendingPay->user->student ?? null;
                            @endphp
                            @if($studentObj)
                                <tr>
                                    <td>
                                        <span class="block font-black text-slate-800 dark:text-white text-xs font-mono">{{ $pendingPay->invoice_number }}</span>
                                        <span class="block text-[10px] text-slate-450 font-bold mt-0.5">{{ $pendingPay->created_at->format('d-M-Y H:i') }} WIB</span>
                                    </td>
                                    <td>
                                        <span class="block font-extrabold text-slate-800 text-sm">{{ $studentObj->name }}</span>
                                        <span class="block text-[10px] text-slate-450 font-bold mt-0.5">NISN: {{ $studentObj->nisn }} &bull; Kelas: {{ $studentObj->schoolClass ? $studentObj->schoolClass->name : '-' }}</span>
                                    </td>
                                    <td>
                                        <strong class="text-slate-900 dark:text-white font-mono text-sm">Rp {{ number_format($pendingPay->amount, 0, ',', '.') }}</strong>
                                    </td>
                                    <td>
                                        @if($pendingPay->notes)
                                            <span class="text-slate-650 dark:text-slate-350 italic text-[11px] max-w-xs block truncate" title="{{ $pendingPay->notes }}">{{ $pendingPay->notes }}</span>
                                        @else
                                            <span class="text-slate-400 italic text-[11px]">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($pendingPay->payment_proof)
                                            <button type="button" @click="imgModalSrc = '{{ get_public_file_url($pendingPay->payment_proof, 'img/savings/proofs') }}'; imgModalOpen = true" class="px-3 py-1.5 bg-indigo-50 dark:bg-indigo-950/40 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 text-[#3C50E0] dark:text-indigo-400 font-extrabold text-[10px] uppercase tracking-wider rounded-lg border border-indigo-200 dark:border-indigo-855 transition">
                                                Lihat Bukti
                                            </button>
                                        @else
                                            <span class="text-[10px] text-rose-500 italic font-bold">Tidak ada file</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <div class="inline-flex items-center gap-2">
                                            <button type="button" @click="openApprove('{{ $studentObj->name }}', '{{ number_format($pendingPay->amount, 0, ',', '.') }}', '{{ route('admin.savings.approve-deposit', ['student' => $studentObj->id, 'transaction' => $pendingPay->id]) }}')" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] text-white font-extrabold text-[10px] uppercase tracking-wider rounded-lg transition-all">
                                                Setuju
                                            </button>
                                            <button type="button" @click="openReject('{{ $studentObj->name }}', '{{ number_format($pendingPay->amount, 0, ',', '.') }}', '{{ route('admin.savings.reject-deposit', ['student' => $studentObj->id, 'transaction' => $pendingPay->id]) }}')" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 active:scale-[0.98] text-white font-extrabold text-[10px] uppercase tracking-wider rounded-lg transition-all">
                                                Tolak
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Image Viewer Modal (Alpine.js) -->
    <div x-show="imgModalOpen" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 overflow-y-auto flex items-center justify-center p-4" x-transition x-cloak>
        <div class="relative bg-white dark:bg-slate-900 max-w-2xl w-full rounded-2xl p-5 shadow-2xl border border-slate-200 dark:border-slate-800" @click.away="imgModalOpen = false">
            <button @click="imgModalOpen = false" class="absolute top-4 right-4 p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:text-slate-200 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider mb-4 flex items-center gap-1.5">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Pratinjau Bukti Transfer
            </h3>
            <div class="flex justify-center bg-slate-50 dark:bg-slate-950 rounded-xl p-2 border border-slate-100 dark:border-slate-800 max-h-[500px] overflow-auto">
                <img :src="imgModalSrc" class="max-w-full h-auto object-contain rounded-lg" alt="Bukti Transfer">
            </div>
            <div class="flex justify-end gap-3 mt-4">
                <a :href="imgModalSrc" target="_blank" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-855 font-extrabold text-xs rounded-xl transition">Buka Penuh ↗</a>
                <button @click="imgModalOpen = false" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs rounded-xl transition">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Confirm Approve Modal (Alpine.js) -->
    <div x-show="approveModalOpen" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 overflow-y-auto flex items-center justify-center p-4" x-transition x-cloak>
        <div class="relative bg-white dark:bg-slate-900 max-w-md w-full rounded-2xl p-6 shadow-2xl border border-slate-200 dark:border-slate-850" @click.away="approveModalOpen = false">
            <div class="flex items-center space-x-3 mb-4">
                <span class="p-2 bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-450 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
                <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">Setujui Setoran</h3>
            </div>
            
            <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold leading-relaxed mb-6">
                Apakah Anda yakin ingin menyetujui setoran manual dari <strong class="text-slate-800 dark:text-white font-bold" x-text="targetStudentName"></strong> sebesar <strong class="text-slate-850 dark:text-slate-100 font-mono font-bold" x-text="'Rp ' + targetAmount"></strong>? Saldo tabungan siswa akan otomatis bertambah.
            </p>

            <div class="flex justify-end gap-3">
                <button type="button" @click="approveModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-205 text-slate-855 font-extrabold text-xs rounded-xl transition">Batal</button>
                <form :action="targetActionUrl" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl transition">Setujui & Proses</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Confirm Reject Modal (Alpine.js) -->
    <div x-show="rejectModalOpen" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 overflow-y-auto flex items-center justify-center p-4" x-transition x-cloak>
        <div class="relative bg-white dark:bg-slate-900 max-w-md w-full rounded-2xl p-6 shadow-2xl border border-slate-200 dark:border-slate-850" @click.away="rejectModalOpen = false">
            <div class="flex items-center space-x-3 mb-4">
                <span class="p-2 bg-rose-100 dark:bg-rose-955 text-rose-600 dark:text-rose-450 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </span>
                <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">Tolak Setoran</h3>
            </div>
            
            <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold leading-relaxed mb-6">
                Apakah Anda yakin ingin menolak bukti setoran manual dari <strong class="text-slate-800 dark:text-white font-bold" x-text="targetStudentName"></strong> sebesar <strong class="text-slate-850 dark:text-slate-100 font-mono font-bold" x-text="'Rp ' + targetAmount"></strong>? Transaksi ini akan ditandai gagal.
            </p>

            <div class="flex justify-end gap-3">
                <button type="button" @click="rejectModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-205 text-slate-855 font-extrabold text-xs rounded-xl transition">Batal</button>
                <form :action="targetActionUrl" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-xl transition">Tolak Setoran</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
