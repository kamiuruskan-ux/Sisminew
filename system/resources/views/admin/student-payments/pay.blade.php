@extends('layouts.admin')

@section('title', 'Transaksi Pembayaran Siswa')
@section('page_title', 'Detail Pos & Transaksi Pembayaran')

@section('content')
@php
    $billsByPost = $student->paymentBills->groupBy(function($b) {
        return $b->paymentBill->paymentPost->id ?? 0;
    });
    $defaultPostId = request('pos_id') ? (int)request('pos_id') : ($billsByPost->keys()->first() ?? 0);
@endphp

<div class="space-y-4" x-data="{ 
        selectedPostId: {{ $defaultPostId }},
        payModalOpen: false,
        payModalData: {},
        payFreeModalOpen: false,
        payFreeModalData: {},
        payFreeAmountRaw: 0,
        payFreeAmountDisplay: '',
        openPayFreeModal(detailId, billName, remaining) {
            this.payFreeModalData = { detailId: detailId, billName: billName, remaining: remaining };
            this.setPayFreeAmount(remaining);
            this.payFreeModalOpen = true;
        },
        setPayFreeAmount(val) {
            let clean = String(val).replace(/[^0-9]/g, '');
            let num = parseInt(clean, 10) || 0;
            this.payFreeAmountRaw = num;
            this.payFreeAmountDisplay = num > 0 ? new Intl.NumberFormat('id-ID').format(num) : '';
        },
        handlePayFreeInput(e) {
            let clean = e.target.value.replace(/[^0-9]/g, '');
            let num = parseInt(clean, 10) || 0;
            this.payFreeAmountRaw = num;
            this.payFreeAmountDisplay = num > 0 ? new Intl.NumberFormat('id-ID').format(num) : '';
        },
        terbilang(n) {
            if (isNaN(n) || n <= 0) return '';
            const angka = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
            let num = Math.floor(n);
            if (num < 12) return angka[num];
            if (num < 20) return this.terbilang(num - 10) + ' Belas';
            if (num < 100) return this.terbilang(Math.floor(num / 10)) + ' Puluh ' + this.terbilang(num % 10);
            if (num < 200) return 'Seratus ' + this.terbilang(num - 100);
            if (num < 1000) return this.terbilang(Math.floor(num / 100)) + ' Ratus ' + this.terbilang(num % 100);
            if (num < 2000) return 'Seribu ' + this.terbilang(num - 1000);
            if (num < 1000000) return this.terbilang(Math.floor(num / 1000)) + ' Ribu ' + this.terbilang(num % 1000);
            if (num < 1000000000) return this.terbilang(Math.floor(num / 1000000)) + ' Juta ' + this.terbilang(num % 1000000);
            if (num < 1000000000000) return this.terbilang(Math.floor(num / 1000000000)) + ' Miliar ' + this.terbilang(num % 1000000000);
            return '';
        },
        deleteModalOpen: false,
        deleteModalData: {},
        selectedMonthIds: [],
        init() {
            this.$watch('selectedPostId', () => {
                if (window.innerWidth < 1024) {
                    this.$nextTick(() => {
                        const el = this.$refs.detailPanel;
                        if (el) {
                            el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    });
                }
            });
        },
        openBulkPayModal(detailsJson, billName) {
            let details = JSON.parse(detailsJson);
            let selectedDetails = details.filter(d => this.selectedMonthIds.map(String).includes(String(d.id)));
            if (selectedDetails.length === 0) return;
            
            let totalAmount = selectedDetails.reduce((sum, d) => sum + parseFloat(d.amount), 0);
            let monthNames = selectedDetails.map(d => d.month_name).join(', ');
            
            this.payModalData = {
                detailIds: selectedDetails.map(d => d.id),
                billName: billName,
                monthName: monthNames,
                amount: totalAmount
            };
            this.payModalOpen = true;
        },
        confirmDeleteTx(id, number, description, amount) {
            this.deleteModalData = {
                id: id,
                number: number,
                description: description,
                amount: amount,
                actionUrl: '{{ url('admin/student-payments/transactions') }}/' + id
            };
            this.deleteModalOpen = true;
        }
    }">

    <!-- Top Compact Header Banner -->
    <div class="shrink-0 bg-white dark:bg-slate-900 rounded-2xl px-4 py-3 border border-slate-200 dark:border-slate-800 shadow-2xs space-y-2.5">
        <div class="flex items-center justify-between gap-2">
            <!-- Kiri: Tombol back + Info Siswa -->
            <div class="flex items-center gap-2.5 min-w-0">
                <a href="{{ route('admin.student-payments.index') }}" class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-200 transition-all shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-1.5">
                        <h1 class="text-sm font-black text-slate-900 dark:text-white tracking-tight truncate">{{ $student->name }}</h1>
                        <span class="px-2 py-0.5 rounded-md bg-indigo-100 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300 font-extrabold text-[10px] shrink-0">
                            {{ $student->schoolClass ? $student->schoolClass->name : '-' }}
                        </span>
                    </div>
                    <p class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 mt-0.5">
                        NISN: <span class="font-mono font-bold text-slate-700 dark:text-slate-200">{{ $student->nisn }}</span>
                        <span class="hidden sm:inline">&bull; Tabungan: <span class="font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($student->savings_balance, 0, ',', '.') }}</span></span>
                    </p>
                </div>
            </div>

            <!-- Kanan: Tombol Tracking -->
            <a href="{{ route('admin.student-payments.tracking', ['search' => $student->nisn]) }}" 
               class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-200 font-bold text-[10px] rounded-xl border border-slate-200 dark:border-slate-700 transition shrink-0">
                <svg class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="hidden sm:inline">Tracking Tunggakan</span>
                <span class="sm:hidden">Tracking</span>
            </a>
        </div>

        <!-- Stats Pills: 3 kolom di sm+, 1 kolom di mobile agar tidak overflow -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-2 border-t border-slate-100 dark:border-slate-800 text-xs">
            <div class="px-3 py-2 bg-slate-50 dark:bg-slate-800/60 rounded-lg border border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Tagihan</span>
                <span class="font-black text-slate-900 dark:text-white text-xs">
                    Rp {{ number_format($student->paymentBills->sum('total_amount'), 0, ',', '.') }}
                </span>
            </div>

            <div class="px-3 py-2 bg-emerald-50/50 dark:bg-emerald-950/30 rounded-lg border border-emerald-100 dark:border-emerald-900/40 flex items-center justify-between">
                <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Terbayar</span>
                <span class="font-black text-emerald-600 dark:text-emerald-400 text-xs">
                    Rp {{ number_format($student->paymentBills->sum('paid_amount'), 0, ',', '.') }}
                </span>
            </div>

            @php
                $totalTunggakan = $student->paymentBills->sum('total_amount') - $student->paymentBills->sum('paid_amount');
            @endphp
            <div class="px-3 py-2 {{ $totalTunggakan > 0 ? 'bg-rose-50/50 dark:bg-rose-950/30 border-rose-100 dark:border-rose-900/40' : 'bg-emerald-50/50 dark:bg-emerald-950/30 border-emerald-100 dark:border-emerald-900/40' }} rounded-lg border flex items-center justify-between">
                <span class="text-[10px] font-bold {{ $totalTunggakan > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }} uppercase tracking-wider">Sisa Tunggakan</span>
                <span class="font-black text-xs {{ $totalTunggakan > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                    Rp {{ number_format($totalTunggakan, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    <!-- PENDING MANUAL PAYMENTS VERIFICATION CARD -->
    @if(isset($pendingManualPayments) && $pendingManualPayments->count() > 0)
        @foreach($pendingManualPayments as $pendingPay)
            <div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-slate-900 dark:to-slate-850 rounded-2xl p-5 border-2 border-amber-400 dark:border-amber-500/30 shadow-md space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-amber-200 dark:border-slate-800">
                    <div class="flex items-center space-x-2.5">
                        <span class="p-1.5 rounded-lg bg-amber-500 text-white shrink-0 shadow-xs">
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </span>
                        <div>
                            <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider">Verifikasi Pembayaran Manual Transfer</h3>
                            <p class="text-[9px] text-slate-500 font-bold mt-0.5">Invoice: {{ $pendingPay->invoice_number }} &bull; Tanggal: {{ $pendingPay->created_at->format('d-m-Y H:i') }} WIB</p>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 bg-amber-100 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300 font-mono text-[9px] font-black rounded-md">PENDING VERIFIKASI</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">
                    <!-- Left: Details -->
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-400">Jumlah Transfer:</span>
                            <strong class="text-slate-800 dark:text-white font-mono text-sm">Rp {{ number_format($pendingPay->amount, 0, ',', '.') }}</strong>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-400">Metode Pembayaran:</span>
                            <span class="text-slate-700 dark:text-slate-300 font-bold">Manual Transfer Bank</span>
                        </div>
                        @if($pendingPay->notes)
                            <div class="py-1">
                                <span class="text-slate-400 block mb-0.5">Catatan Siswa:</span>
                                <p class="text-slate-700 dark:text-slate-300 p-2 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 italic">{{ $pendingPay->notes }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Right: Proof & Actions -->
                    <div class="space-y-3">
                        <div>
                            <span class="text-slate-400 text-xs block mb-1">Bukti Transfer:</span>
                            @if($pendingPay->payment_proof)
                                <a href="{{ get_public_file_url($pendingPay->payment_proof, 'img/spp/proofs') }}" target="_blank" class="block group relative rounded-xl overflow-hidden border border-slate-200 dark:border-slate-800 bg-white p-1">
                                    <img src="{{ get_public_file_url($pendingPay->payment_proof, 'img/spp/proofs') }}" alt="Bukti Transfer" class="w-full h-32 object-cover rounded-lg group-hover:scale-[1.02] transition-transform">
                                    <div class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                        <span class="px-2.5 py-1 bg-white text-slate-900 text-[10px] font-black rounded-lg shadow-sm">Buka Gambar Penuh ↗</span>
                                    </div>
                                </a>
                            @else
                                <span class="text-xs text-rose-500 italic block p-3 bg-rose-50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/40 rounded-xl">Bukti transfer belum diunggah oleh siswa.</span>
                            @endif
                        </div>

                        @if($pendingPay->payment_proof)
                            <div class="flex items-center gap-2 pt-1">
                                <form action="{{ route('admin.student-payments.approve-payment', ['student' => $student->id, 'transaction' => $pendingPay->id]) }}" method="POST" class="flex-1" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui pembayaran ini? Tagihan siswa terkait akan otomatis terbayar.')">
                                    @csrf
                                    <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] text-white font-extrabold text-[10px] uppercase tracking-wider rounded-xl transition-all shadow-xs text-center">
                                        Setujui & Tandai Lunas
                                    </button>
                                </form>
                                <form action="{{ route('admin.student-payments.reject-payment', ['student' => $student->id, 'transaction' => $pendingPay->id]) }}" method="POST" class="flex-1" onsubmit="return confirm('Apakah Anda yakin ingin menolak bukti pembayaran ini?')">
                                    @csrf
                                    <button type="submit" class="w-full py-2 bg-rose-600 hover:bg-rose-700 active:scale-[0.98] text-white font-extrabold text-[10px] uppercase tracking-wider rounded-xl transition-all shadow-xs text-center">
                                        Tolak Bukti
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <!-- 2-COLUMN SPLIT BODY (Sticky Sidebar Layout) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

        <!-- LEFT COLUMN (col-span-4): Sticky Pos List -->
        <div class="lg:col-span-4 lg:sticky lg:top-4 space-y-4">
            
            <!-- Riwayat Pembayaran Tab (At the top) -->
            <div class="space-y-2">
                <div class="flex items-center justify-between px-1 py-0.5">
                    <h2 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Menu Utama
                    </h2>
                </div>
                <div @click="selectedPostId = 'history'; selectedMonthIds = []" 
                     :class="selectedPostId === 'history' ? 'bg-slate-900 text-white shadow-md border-slate-900' : 'bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 border-slate-200 dark:border-slate-800 hover:border-indigo-500 dark:hover:border-indigo-400 shadow-2xs'"
                     class="rounded-xl p-3 border transition-all cursor-pointer relative group flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-2.5 min-w-0">
                        <div :class="selectedPostId === 'history' ? 'bg-white/20 text-white' : 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400'"
                             class="w-9 h-9 rounded-lg flex items-center justify-center font-black text-xs shrink-0 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-xs font-extrabold truncate leading-tight">Riwayat Pembayaran</h3>
                            <p class="text-[10px] text-slate-400 mt-0.5">Cetak Laporan / PDF</p>
                        </div>
                    </div>
                    <svg class="w-3.5 h-3.5" :class="selectedPostId === 'history' ? 'text-white' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </div>

            <!-- Pos Pembayaran (Below) -->
            <div class="space-y-2 pt-1">
                <div class="flex items-center justify-between px-1 py-0.5">
                    <h2 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Daftar Tagihan Siswa
                    </h2>
                    <span class="text-[10px] font-semibold text-indigo-600 dark:text-indigo-400">Pilih Pos</span>
                </div>

                @if($billsByPost->count() > 0)
                    <div class="space-y-2">
                        @foreach($billsByPost as $postId => $studentBills)
                            @php
                                $firstBill = $studentBills->first();
                                $post = $firstBill->paymentBill->paymentPost;
                                $postName = $post->name ?? 'Pos Pembayaran';
                                $postCode = $post->code ?? 'POS';
                                $postTotal = $studentBills->sum('total_amount');
                                $postPaid = $studentBills->sum('paid_amount');
                                $postRemaining = $postTotal - $postPaid;
                                $isPaid = $postRemaining <= 0;
                            @endphp

                            <div @click="selectedPostId = {{ $postId }}; selectedMonthIds = []" 
                                 :class="selectedPostId === {{ $postId }} ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30 border-indigo-600' : 'bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 border-slate-200 dark:border-slate-800 hover:border-indigo-400 dark:hover:border-indigo-700 shadow-2xs'"
                                 class="rounded-xl p-3 border transition-all cursor-pointer relative group flex items-center justify-between gap-3">
                                
                                <!-- Left: Icon & Info -->
                                <div class="flex items-center space-x-2.5 min-w-0">
                                    <div :class="selectedPostId === {{ $postId }} ? 'bg-white/20 text-white' : 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400'"
                                         class="w-9 h-9 rounded-lg flex items-center justify-center font-black text-xs shrink-0 transition-colors">
                                        {{ $postCode }}
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="text-xs font-extrabold truncate leading-tight"
                                            :class="selectedPostId === {{ $postId }} ? 'text-white' : 'text-slate-900 dark:text-white'">
                                            {{ $postName }}
                                        </h3>
                                        <p class="text-[11px] font-medium mt-0.5 truncate"
                                           :class="selectedPostId === {{ $postId }} ? 'text-indigo-100' : 'text-slate-500 dark:text-slate-400'">
                                            Sisa: <span class="font-black" :class="selectedPostId === {{ $postId }} ? 'text-white' : '{{ $postRemaining > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}'">Rp {{ number_format($postRemaining, 0, ',', '.') }}</span>
                                        </p>
                                    </div>
                                </div>

                                <!-- Right: Badge & Arrow -->
                                <div class="flex items-center space-x-1.5 shrink-0">
                                    <span :class="selectedPostId === {{ $postId }} ? 'bg-white/20 text-white' : '{{ $isPaid ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : ($postPaid > 0 ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300') }}'"
                                          class="px-2 py-0.5 text-[9px] font-extrabold rounded-md uppercase tracking-wider">
                                        {{ $isPaid ? 'LUNAS' : ($postPaid > 0 ? 'SEBAGIAN' : 'BELUM BAYAR') }}
                                    </span>
                                    <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-0.5" :class="selectedPostId === {{ $postId }} ? 'text-white' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 text-center border border-slate-200 dark:border-slate-800">
                        <p class="text-xs text-slate-500 dark:text-slate-400">Belum ada pos pembayaran.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Hint scroll mobile: hanya tampil di HP -->
        <div class="lg:hidden flex items-center justify-center gap-2 py-1 text-[10px] text-slate-400 font-semibold" x-show="selectedPostId !== null">
            <svg class="w-3 h-3 animate-bounce" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
            <span>Detail pos di bawah</span>
            <svg class="w-3 h-3 animate-bounce" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>

        <!-- RIGHT COLUMN (col-span-8): Pos Detail -->
        <div class="lg:col-span-8 space-y-4" x-ref="detailPanel">
            @foreach($billsByPost as $postId => $studentBills)
                @php
                    $firstBill = $studentBills->first();
                    $post = $firstBill->paymentBill->paymentPost;
                    $postName = $post->name ?? 'Pos Pembayaran';
                    $postCode = $post->code ?? 'POS';
                @endphp

                <div x-show="selectedPostId === {{ $postId }}" x-cloak class="space-y-4">
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
                        
                        <!-- Pos Header Detail -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3.5 border-b border-slate-100 dark:border-slate-800 gap-2">
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-black text-xs shadow-xs">
                                    {{ $postCode }}
                                </div>
                                <div>
                                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white">
                                        Detail Pos: {{ $postName }}
                                    </h3>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Rincian tagihan bulanan & cicilan bebas untuk pos pembayaran ini.</p>
                                </div>
                            </div>

                            <div class="text-left sm:text-right">
                                <span class="text-[10px] font-bold text-slate-400 uppercase block">Total Sisa Tunggakan Pos Ini</span>
                                <span class="text-sm font-black text-rose-600 dark:text-rose-400">
                                    Rp {{ number_format($studentBills->sum('total_amount') - $studentBills->sum('paid_amount'), 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- Bills Under This Pos -->
                        <div class="space-y-3.5">
                            @foreach($studentBills as $bill)
                                @php
                                    $paymentBill = $bill->paymentBill;
                                    $billType = $paymentBill->type;
                                @endphp

                                <div class="border border-slate-200 dark:border-slate-800 rounded-2xl p-4 bg-slate-50/50 dark:bg-slate-800/40 space-y-3.5">
                                    
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2.5 border-b border-slate-200 dark:border-slate-800">
                                        <div>
                                            <div class="flex items-center space-x-2">
                                                <h4 class="font-extrabold text-slate-900 dark:text-white text-xs sm:text-sm">{{ $paymentBill->name }}</h4>
                                                <span class="px-2 py-0.5 rounded-md text-[9px] font-extrabold uppercase {{ $billType === 'bulanan' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' : 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300' }}">
                                                    {{ $billType }}
                                                </span>
                                            </div>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                                Tahun Akademik: {{ $paymentBill->academicYear->name ?? '-' }}
                                            </p>
                                        </div>

                                        <div class="text-left sm:text-right text-xs">
                                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Total Nominal</span>
                                            <span class="font-black text-indigo-600 dark:text-indigo-400">
                                                Rp {{ number_format($bill->total_amount, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- IF BULANAN: Grid 12 Bulan -->
                                    @if($billType === 'bulanan')
                                        <div>
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Matriks Bulan Tagihan:</span>
                                                    @if($bill->details->where('status', '!=', 'paid')->count() > 0)
                                                        <button type="button" 
                                                                data-unpaid="{{ $bill->details->where('status', '!=', 'paid')->pluck('id')->toJson() }}"
                                                                @click="let unpaid = JSON.parse($el.dataset.unpaid); selectedMonthIds = (selectedMonthIds.filter(id => unpaid.includes(id)).length === unpaid.length) ? selectedMonthIds.filter(id => !unpaid.includes(id)) : [...new Set([...selectedMonthIds, ...unpaid])]"
                                                                class="text-[10px] text-indigo-600 dark:text-indigo-400 font-extrabold hover:underline select-none cursor-pointer">
                                                            (Pilih Semua)
                                                        </button>
                                                    @endif
                                                </div>
                                                <button type="button" 
                                                        x-show="selectedMonthIds.length > 0" 
                                                        x-cloak
                                                        data-details="{{ $bill->details->toJson() }}"
                                                        @click="openBulkPayModal($el.dataset.details, '{{ addslashes($paymentBill->name) }}')"
                                                        class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-[10px] shadow-xs transition-all flex items-center space-x-1 cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    <span>Bayar Terpilih (<span x-text="selectedMonthIds.length"></span>)</span>
                                                </button>
                                            </div>
                                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5">
                                                @foreach($bill->details->sortBy('month_no') as $detail)
                                                    <div class="p-2.5 rounded-xl border text-center transition relative flex flex-col justify-between space-y-2 {{ $detail->status === 'paid' ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800' }}">
                                                        <div>
                                                            <div class="flex items-center justify-between mb-1">
                                                                @if($detail->status !== 'paid')
                                                                    <input type="checkbox" :value="{{ $detail->id }}" x-model="selectedMonthIds" class="w-3.5 h-3.5 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500 shrink-0 cursor-pointer">
                                                                @else
                                                                    <div class="w-3.5 h-3.5"></div>
                                                                @endif
                                                                <p class="font-bold text-xs text-slate-900 dark:text-white flex-1 text-center pr-3.5">{{ $detail->month_name }}</p>
                                                            </div>
                                                            <p class="text-[11px] font-black mt-0.5 {{ $detail->status === 'paid' ? 'text-emerald-600 dark:text-emerald-400' : 'text-indigo-600 dark:text-indigo-400' }}">
                                                                Rp {{ number_format($detail->amount, 0, ',', '.') }}
                                                            </p>
                                                        </div>

                                                        <div>
                                                            @if($detail->status === 'paid')
                                                                <span class="inline-block px-2 py-0.5 rounded-full bg-emerald-200 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200 font-extrabold text-[9px]">LUNAS</span>
                                                            @else
                                                                <button type="button" 
                                                                        @click="payModalOpen = true; payModalData = { detailId: {{ $detail->id }}, monthName: '{{ $detail->month_name }}', billName: '{{ addslashes($paymentBill->name) }}', amount: {{ $detail->amount }} }" 
                                                                        class="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-bold text-[10px] shadow-xs transition-all w-full">
                                                                    Bayar
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                    <!-- IF BEBAS / CICILAN: Progress & Tombol Bayar Cicilan -->
                                    @else
                                        @php
                                            $detail = $bill->details->first();
                                            $sisa = $bill->total_amount - $bill->paid_amount;
                                        @endphp
                                        <div class="space-y-3">
                                            <div class="flex items-center justify-between text-xs font-bold">
                                                <span class="text-slate-600 dark:text-slate-400">Terbayar: <strong class="text-emerald-600 dark:text-emerald-400">Rp {{ number_format($bill->paid_amount, 0, ',', '.') }}</strong></span>
                                                <span class="text-rose-600 dark:text-rose-400">Sisa Tunggakan: <strong>Rp {{ number_format($sisa, 0, ',', '.') }}</strong></span>
                                            </div>

                                            <!-- Progress Bar -->
                                            @php
                                                $percent = $bill->total_amount > 0 ? min(100, round(($bill->paid_amount / $bill->total_amount) * 100)) : 0;
                                            @endphp
                                            <div class="w-full bg-slate-200 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                                                <div class="bg-emerald-500 h-full transition-all duration-300" style="width: {{ $percent }}%"></div>
                                            </div>

                                            <div class="pt-1 flex justify-end">
                                                @if($bill->status === 'paid' || $sisa <= 0)
                                                    <span class="px-3.5 py-1.5 rounded-xl bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200 font-extrabold text-xs">LUNAS</span>
                                                @else
                                                    <button type="button" 
                                                            @click="openPayFreeModal({{ $detail ? $detail->id : 0 }}, '{{ addslashes($paymentBill->name) }}', {{ $sisa }})" 
                                                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-xs transition-all">
                                                        Bayar Cicilan Pos Ini
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Riwayat Pembayaran Pane -->
            <div x-show="selectedPostId === 'history'" x-cloak class="space-y-4">
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
                    <!-- Header -->
                    <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Riwayat Transaksi & Cetak Laporan</h3>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">Filter transaksi pembayaran siswa berdasarkan rentang tanggal.</p>
                        </div>
                    </div>

                    <!-- Filter Form & Print -->
                    <form method="GET" target="_blank" class="space-y-4 text-xs">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-bold text-slate-600 dark:text-slate-400 mb-1 uppercase text-[10px]">Tanggal Mulai</label>
                                <input type="date" name="start_date" required value="{{ date('Y-m-01') }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-600 dark:text-slate-400 mb-1 uppercase text-[10px]">Tanggal Selesai</label>
                                <input type="date" name="end_date" required value="{{ date('Y-m-d') }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200">
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 pt-2">
                            <button type="button" 
                                    @click="let form = $el.closest('form'); form.action = '{{ route('admin.student-payments.print-history', $student->id) }}'; form.submit();"
                                    class="flex-1 py-2.5 bg-slate-700 hover:bg-slate-800 text-white rounded-xl font-bold text-xs shadow-xs transition flex items-center justify-center space-x-1.5 cursor-pointer">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span>Cetak Riwayat (Laporan)</span>
                            </button>
                            
                            <button type="button" 
                                    @click="let form = $el.closest('form'); form.action = '{{ route('admin.student-payments.print-history-receipt', $student->id) }}'; form.submit();"
                                    class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-xs shadow-xs transition flex items-center justify-center space-x-1.5 cursor-pointer">
                                <svg class="w-4 h-4 text-indigo-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                <span>Cetak Kuitansi Gabungan</span>
                            </button>
                        </div>
                    </form>

                    <!-- List of Recent Transactions -->
                    <div class="space-y-2.5 pt-2">
                        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Daftar Transaksi Terbaru</h4>
                        {{-- Tabel: tampil di sm+ --}}
                        <div class="hidden sm:block border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-2xs">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800 font-bold text-slate-700 dark:text-slate-300 uppercase">
                                    <tr>
                                        <th class="p-3">Tanggal</th>
                                        <th class="p-3">No. Transaksi</th>
                                        <th class="p-3">Keterangan</th>
                                        <th class="p-3 text-right">Jumlah</th>
                                        <th class="p-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-slate-800 dark:text-slate-200 font-medium">
                                    @forelse($transactions as $tx)
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40">
                                            <td class="p-3 whitespace-nowrap">{{ $tx->transaction_date->format('d/m/Y') }}</td>
                                            <td class="p-3 font-mono text-indigo-600 dark:text-indigo-400 font-bold whitespace-nowrap">{{ $tx->transaction_number }}</td>
                                            <td class="p-3 truncate max-w-[160px]" title="{{ $tx->description }}">{{ $tx->description }}</td>
                                            <td class="p-3 text-right font-black text-slate-900 dark:text-white whitespace-nowrap">Rp {{ number_format($tx->amount, 0, ',', '.') }}</td>
                                            <td class="p-3 text-center">
                                                <div class="flex items-center justify-center space-x-1.5">
                                                    <a href="{{ route('admin.student-payments.print', $tx->id) }}" target="_blank" class="p-1.5 bg-indigo-50 dark:bg-indigo-950 hover:bg-indigo-100 dark:hover:bg-indigo-900 text-indigo-600 dark:text-indigo-400 rounded-lg transition" title="Cetak Kuitansi">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                                        </svg>
                                                    </a>
                                                    <button type="button" @click="confirmDeleteTx('{{ $tx->id }}', '{{ addslashes($tx->transaction_number) }}', '{{ addslashes($tx->description) }}', '{{ number_format($tx->amount, 0, ',', '.') }}')" class="p-1.5 bg-rose-50 dark:bg-rose-950/60 hover:bg-rose-100 dark:hover:bg-rose-900 text-rose-600 dark:text-rose-400 rounded-lg transition" title="Hapus Transaksi">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="p-4 text-center text-slate-400">Belum ada transaksi pembayaran.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Card list: tampil di mobile (< sm) --}}
                        <div class="sm:hidden space-y-2">
                            @forelse($transactions as $tx)
                                <div class="bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl p-3 space-y-1.5">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <p class="font-mono text-[10px] font-bold text-indigo-600 dark:text-indigo-400 truncate">{{ $tx->transaction_number }}</p>
                                            <p class="text-[11px] text-slate-600 dark:text-slate-300 truncate mt-0.5" title="{{ $tx->description }}">{{ $tx->description }}</p>
                                        </div>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <a href="{{ route('admin.student-payments.print', $tx->id) }}" target="_blank" class="p-1.5 bg-indigo-50 dark:bg-indigo-950 hover:bg-indigo-100 dark:hover:bg-indigo-900 text-indigo-600 dark:text-indigo-400 rounded-lg transition" title="Cetak Kuitansi">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                                </svg>
                                            </a>
                                            <button type="button" @click="confirmDeleteTx('{{ $tx->id }}', '{{ addslashes($tx->transaction_number) }}', '{{ addslashes($tx->description) }}', '{{ number_format($tx->amount, 0, ',', '.') }}')" class="p-1.5 bg-rose-50 dark:bg-rose-950/60 hover:bg-rose-100 dark:hover:bg-rose-900 text-rose-600 dark:text-rose-400 rounded-lg transition" title="Hapus Transaksi">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] text-slate-400 font-medium">{{ $tx->transaction_date->format('d/m/Y') }}</span>
                                        <span class="text-xs font-black text-slate-900 dark:text-white">Rp {{ number_format($tx->amount, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-center text-slate-400 text-xs">Belum ada transaksi pembayaran.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal Pay Monthly -->
    <div x-show="payModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="payModalOpen = false"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-900 p-6 text-left align-middle shadow-xl transition-all w-full max-w-md border border-slate-200 dark:border-slate-800 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Konfirmasi Pembayaran Bulanan</h3>
                    <button type="button" @click="payModalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form action="{{ route('admin.student-payments.process', $student->id) }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <input type="hidden" name="payment_type" value="bulanan">
                    
                    <!-- Dynamic details IDs for single or bulk selection -->
                    <template x-if="payModalData.detailIds && payModalData.detailIds.length > 0">
                        <div>
                            <template x-for="id in payModalData.detailIds" :key="id">
                                <input type="hidden" name="detail_ids[]" :value="id">
                            </template>
                        </div>
                    </template>
                    <template x-if="!payModalData.detailIds || payModalData.detailIds.length === 0">
                        <input type="hidden" name="detail_ids[]" :value="payModalData.detailId">
                    </template>

                    <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                        <span class="text-[10px] text-slate-400 font-bold block uppercase" x-text="payModalData.billName + ' (' + payModalData.monthName + ')'"></span>
                        <span class="text-lg font-black text-emerald-600 dark:text-emerald-400 mt-1 block" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(payModalData.amount || 0)"></span>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1 uppercase text-[10px]">Rekening / Kas Penerima</label>
                        <select name="bank_account_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 text-xs font-semibold">
                            <option value="savings" class="font-bold text-emerald-600">[Tabungan Siswa] Saldo Tabungan (Saldo: Rp {{ number_format($student->savings_balance, 0, ',', '.') }})</option>
                            <optgroup label="Rekening / Kas Sekolah">
                                @foreach($bankAccounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->bank_name }} - {{ $acc->account_name }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1 uppercase text-[10px]">Catatan (Opsional)</label>
                        <input type="text" name="notes" placeholder="Catatan transaksi..." class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 text-xs">
                    </div>

                    <div class="pt-2 flex justify-end space-x-2">
                        <button type="button" @click="payModalOpen = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl font-bold">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-xs">Proses Pembayaran</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Pay Free / Installment -->
    <div x-show="payFreeModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="payFreeModalOpen = false"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-900 p-6 text-left align-middle shadow-xl transition-all w-full max-w-md border border-slate-200 dark:border-slate-800 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Bayar Cicilan Pos Pembayaran</h3>
                    <button type="button" @click="payFreeModalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form action="{{ route('admin.student-payments.process', $student->id) }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <input type="hidden" name="payment_type" value="bebas">
                    <input type="hidden" name="detail_id" :value="payFreeModalData.detailId">
                    <input type="hidden" name="amount" :value="payFreeAmountRaw">

                    <div class="p-3 bg-rose-50 dark:bg-rose-950/40 rounded-xl border border-rose-200 dark:border-rose-900">
                        <span class="text-[10px] font-bold text-rose-600 dark:text-rose-400 uppercase block" x-text="payFreeModalData.billName"></span>
                        <span class="text-base font-black text-rose-700 dark:text-rose-300 mt-0.5 block" x-text="'Sisa Tunggakan: Rp ' + new Intl.NumberFormat('id-ID').format(payFreeModalData.remaining || 0)"></span>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase text-[10px]">Nominal Cicilan Dibayar (Rp)</label>
                            <button type="button" @click="setPayFreeAmount(payFreeModalData.remaining)" class="text-[10px] font-bold text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 underline">
                                Bayar Lunas (Rp <span x-text="new Intl.NumberFormat('id-ID').format(payFreeModalData.remaining || 0)"></span>)
                            </button>
                        </div>
                        <div class="relative rounded-xl shadow-2xs">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <span class="text-slate-400 dark:text-slate-500 font-extrabold text-xs">Rp</span>
                            </div>
                            <input type="text" 
                                   :value="payFreeAmountDisplay" 
                                   @input="handlePayFreeInput($event)"
                                   placeholder="0" 
                                   required 
                                   class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 text-sm font-black text-emerald-600 dark:text-emerald-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                        <template x-if="payFreeAmountRaw > payFreeModalData.remaining">
                            <p class="mt-1 text-[11px] font-semibold text-rose-500">
                                ⚠️ Nominal melebihi sisa tunggakan (Sisa: Rp <span x-text="new Intl.NumberFormat('id-ID').format(payFreeModalData.remaining)"></span>)
                            </p>
                        </template>
                        <template x-if="payFreeAmountRaw > 0 && payFreeAmountRaw <= payFreeModalData.remaining">
                            <p class="mt-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">
                                Terbilang: <span class="font-bold text-slate-700 dark:text-slate-200" x-text="terbilang(payFreeAmountRaw) + ' Rupiah'"></span>
                            </p>
                        </template>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1 uppercase text-[10px]">Rekening / Kas Penerima</label>
                        <select name="bank_account_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 text-xs font-semibold">
                            <option value="savings" class="font-bold text-emerald-600">[Tabungan Siswa] Saldo Tabungan (Saldo: Rp {{ number_format($student->savings_balance, 0, ',', '.') }})</option>
                            <optgroup label="Rekening / Kas Sekolah">
                                @foreach($bankAccounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->bank_name }} - {{ $acc->account_name }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1 uppercase text-[10px]">Catatan (Opsional)</label>
                        <input type="text" name="notes" placeholder="Catatan cicilan..." class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 text-xs">
                    </div>

                    <div class="pt-2 flex justify-end space-x-2">
                        <button type="button" @click="payFreeModalOpen = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl font-bold">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-xs">Proses Pembayaran</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Transaksi -->
    <div x-show="deleteModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="deleteModalOpen = false"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-900 p-6 text-left align-middle shadow-xl transition-all w-full max-w-md border border-slate-200 dark:border-slate-800 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-9 h-9 rounded-xl bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Konfirmasi Hapus Transaksi</h3>
                            <p class="text-[10px] text-slate-500 font-semibold">Status tagihan siswa akan dikembalikan</p>
                        </div>
                    </div>
                    <button type="button" @click="deleteModalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <div class="p-3 bg-rose-50/60 dark:bg-rose-950/30 rounded-xl border border-rose-100 dark:border-rose-900/40 text-xs space-y-1.5">
                    <div class="flex justify-between">
                        <span class="text-slate-500">No. Transaksi:</span>
                        <strong class="font-mono text-rose-700 dark:text-rose-400 font-bold" x-text="deleteModalData.number"></strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Jumlah Pembayaran:</span>
                        <strong class="text-slate-800 dark:text-slate-200 font-black" x-text="'Rp ' + deleteModalData.amount"></strong>
                    </div>
                    <div class="pt-1 text-[11px] text-slate-600 dark:text-slate-400 border-t border-rose-200/50 dark:border-rose-900/30 mt-1">
                        <span class="font-semibold block mb-0.5">Keterangan:</span>
                        <p class="italic text-slate-700 dark:text-slate-300" x-text="deleteModalData.description"></p>
                    </div>
                </div>

                <p class="text-xs text-slate-600 dark:text-slate-400">
                    Apakah Anda yakin ingin menghapus transaksi ini? Data transaksi di buku kas akan dihapus, saldo tabungan akan dikembalikan (jika menggunakan tabungan), dan status rincian tagihan akan di-reset.
                </p>

                <form :action="deleteModalData.actionUrl" method="POST" class="pt-2 flex items-center justify-end space-x-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="deleteModalOpen = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl font-bold text-xs hover:bg-slate-200 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-bold text-xs shadow-xs transition active:scale-[0.98]">
                        Ya, Hapus Transaksi
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
