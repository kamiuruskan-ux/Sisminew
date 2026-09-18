<!-- ═════════════════════════════════════════════════════════════════════ -->
<!-- DASHBOARD VIEW: BENDAHARA & KEUANGAN SEKOLAH -->
<!-- ═════════════════════════════════════════════════════════════════════ -->
<div class="space-y-6">
    <!-- Header Section Keuangan -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-3xl bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white shadow-xs">
        <div class="flex items-center space-x-3.5">
            <div class="w-11 h-11 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-100 dark:border-emerald-900/50 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-extrabold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Ringkasan Keuangan &amp; Kas Sekolah</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Monitoring penerimaan SPP, saldo kas, serta rekapitulasi transaksi bulan {{ now()->translatedFormat('F Y') }}</p>
            </div>
        </div>
        <div class="flex items-center space-x-2 shrink-0">
            <a href="{{ route('admin.student-payments.index') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-2xl shadow-xs transition-all flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>Terima Bayar SPP</span>
            </a>
            <a href="{{ route('admin.financial-transactions.create', ['type' => 'pemasukan']) }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs rounded-2xl shadow-xs transition-all flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                <span>Catat Transaksi Kas</span>
            </a>
        </div>
    </div>

    <!-- 4 KPI Metrics Grid Keuangan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- 1. Total Pemasukan Bulan Ini -->
        <div class="tailadmin-card p-5 bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">Pemasukan Bulan Ini</p>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-100 dark:border-emerald-900/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight">Rp {{ number_format($financialStats['total_income_month'] ?? 0, 0, ',', '.') }}</h3>
                <div class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 mt-1 flex items-center justify-between">
                    <span>SPP: Rp {{ number_format($financialStats['spp_income_month'] ?? 0, 0, ',', '.') }}</span>
                    <span>Kas: Rp {{ number_format($financialStats['tx_income_month'] ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- 2. Total Pengeluaran Bulan Ini -->
        <div class="tailadmin-card p-5 bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">Pengeluaran Bulan Ini</p>
                <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center border border-rose-100 dark:border-rose-900/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-black text-rose-600 dark:text-rose-400 tracking-tight">Rp {{ number_format($financialStats['total_expense_month'] ?? 0, 0, ',', '.') }}</h3>
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 mt-1">
                    Total pengeluaran kas sekolah
                </p>
            </div>
        </div>

        <!-- 3. Tunggakan Tagihan Siswa -->
        <div class="tailadmin-card p-5 bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">Tunggakan SPP Siswa</p>
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-100 dark:border-amber-900/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-black text-amber-600 dark:text-amber-400 tracking-tight">Rp {{ number_format($financialStats['total_unpaid_bills'] ?? 0, 0, ',', '.') }}</h3>
                <a href="{{ route('admin.payment-bills.index') }}" class="text-[11px] font-bold text-amber-600 hover:text-amber-700 dark:text-amber-400 mt-1 inline-flex items-center gap-1">
                    <span>Lihat Rincian Tagihan</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        <!-- 4. Saldo Kas & Bank -->
        <div class="tailadmin-card p-5 bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">Saldo Kas &amp; Rekening</p>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-100 dark:border-indigo-900/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-black text-indigo-600 dark:text-indigo-400 tracking-tight">Rp {{ number_format($financialStats['total_bank_balance'] ?? 0, 0, ',', '.') }}</h3>
                <a href="{{ route('admin.bank-accounts.index') }}" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 mt-1 inline-flex items-center gap-1">
                    <span>Kelola {{ isset($financialAccounts) ? $financialAccounts->count() : 0 }} Rekening Bank</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </div>

    <!-- 2 Kolom Ringkasan Transaksi SPP & Transaksi Kas Terbaru -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Transaksi SPP Terbaru -->
        <div class="tailadmin-card p-6 bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <h4 class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Penerimaan SPP Siswa Terbaru
                </h4>
                <a href="{{ route('admin.student-payments.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">Lihat Semua &rarr;</a>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($recentStudentPayments ?? [] as $pym)
                <div class="py-3 flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-xs shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="space-y-0.5">
                            <p class="text-xs font-bold text-slate-900 dark:text-white">
                                {{ $pym->studentPaymentBill?->student?->user?->name ?? 'Siswa' }}
                            </p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                                {{ $pym->studentPaymentBill?->paymentBill?->title ?? 'Pembayaran SPP' }} &bull; {{ $pym->created_at->translatedFormat('d M Y') }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-xs font-black text-emerald-600 dark:text-emerald-400">+Rp {{ number_format($pym->paid_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
                @empty
                <div class="py-6 text-center text-xs font-medium text-slate-400">Belum ada catatan pembayaran SPP.</div>
                @endforelse
            </div>
        </div>

        <!-- Transaksi Kas Umum Terbaru -->
        <div class="tailadmin-card p-6 bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <h4 class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Transaksi Kas &amp; Bank Terbaru
                </h4>
                <a href="{{ route('admin.financial-transactions.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700">Lihat Semua &rarr;</a>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($financialTransactions ?? [] as $tx)
                <div class="py-3 flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-xl {{ $tx->type === 'pemasukan' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400' : 'bg-rose-50 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400' }} flex items-center justify-center font-extrabold text-xs shrink-0">
                            {{ $tx->type === 'pemasukan' ? '↓' : '↑' }}
                        </div>
                        <div class="space-y-0.5">
                            <p class="text-xs font-bold text-slate-900 dark:text-white">
                                {{ $tx->transaction_number }} &bull; {{ $tx->financialCategory?->name ?? 'Kas' }}
                            </p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                                {{ $tx->bankAccount?->account_name ?? 'Kas Utama' }} &bull; {{ $tx->transaction_date?->translatedFormat('d M Y') }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-xs font-black {{ $tx->type === 'pemasukan' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                            {{ $tx->type === 'pemasukan' ? '+' : '-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="py-6 text-center text-xs font-medium text-slate-400">Belum ada transaksi kas terbaru.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
