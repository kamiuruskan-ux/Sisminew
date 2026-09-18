@extends('layouts.admin')

@section('title', 'Tracking Tunggakan Tagihan Siswa')
@section('page_title', 'Tracking Tunggakan Tagihan')

@section('content')
<div class="space-y-6">

    <!-- Top Bar Navigation & Header -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-2xs">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight flex items-center gap-2.5">
                    <span class="p-2 rounded-xl bg-indigo-600/10 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </span>
                    Tracking Tunggakan Tagihan Siswa
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Matriks pemantauan status kelunasan dan penelusuran seluruh tunggakan tagihan siswa (SPP bulanan & tagihan bebas/cicilan) lintas tahun akademik.
                </p>
            </div>

            <!-- Switch View Tabs -->
            <div class="inline-flex p-1.5 bg-slate-200/70 dark:bg-slate-800/80 rounded-2xl border border-slate-300 dark:border-slate-700 text-xs font-bold shadow-xs self-start md:self-auto">
                <a href="{{ route('admin.student-payments.index') }}" 
                   class="px-4 py-2 rounded-xl transition-all {{ request()->routeIs('admin.student-payments.index') ? 'bg-indigo-600 text-white shadow-md font-extrabold' : 'text-slate-700 dark:text-slate-200 hover:text-slate-900 dark:hover:text-white hover:bg-slate-300/50 dark:hover:bg-slate-700/50' }}">
                    Daftar Siswa & Bayar
                </a>
                <a href="{{ route('admin.student-payments.manual-confirm') }}" 
                   class="px-4 py-2 rounded-xl transition-all flex items-center gap-1.5 {{ request()->routeIs('admin.student-payments.manual-confirm') ? 'bg-indigo-600 text-white shadow-md font-extrabold' : 'text-slate-700 dark:text-slate-200 hover:text-slate-900 dark:hover:text-white hover:bg-slate-300/50 dark:hover:bg-slate-700/50' }}">
                    <span>Konfirmasi Transfer Manual</span>
                    @if(isset($pendingCount) && $pendingCount > 0)
                        <span class="px-1.5 py-0.5 rounded-full {{ request()->routeIs('admin.student-payments.manual-confirm') ? 'bg-white text-indigo-600' : 'bg-rose-500 text-white' }} text-[9px] font-black leading-none">{{ $pendingCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.student-payments.tracking') }}" 
                   class="px-4 py-2 rounded-xl transition-all {{ request()->routeIs('admin.student-payments.tracking') ? 'bg-indigo-600 text-white shadow-md font-extrabold' : 'text-slate-700 dark:text-slate-200 hover:text-slate-900 dark:hover:text-white hover:bg-slate-300/50 dark:hover:bg-slate-700/50' }}">
                    Matriks Tracking Multi-Tahun
                </a>
            </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('admin.student-payments.tracking') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 mt-6 pt-5 border-t border-slate-100 dark:border-slate-800">
            <!-- Academic Year Filter -->
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tahun Akademik</label>
                <select name="academic_year_id" @change="$el.closest('form').submit()" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white text-xs rounded-xl p-2.5 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-indigo-500 font-semibold">
                    <option value="">Semua T.A</option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                            {{ $year->name }} {{ $year->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <x-major-class-select :selected-major="request('major_id')" :selected-class="$selectedClassId" :is-filter="true" layout="inline" major-label="Jurusan" class-label="Kelas" select-class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white text-xs rounded-xl p-2.5 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-indigo-500 font-semibold" label-class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1" :on-major-change="'$el.closest(\'form\').submit()'" :on-class-change="'$el.closest(\'form\').submit()'" />

            <!-- Bill Type Filter -->
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Jenis Tagihan</label>
                <select name="bill_type" @change="$el.closest('form').submit()" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white text-xs rounded-xl p-2.5 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-indigo-500 font-semibold">
                    <option value="all" {{ ($selectedType ?? 'all') == 'all' ? 'selected' : '' }}>Semua (SPP & Bebas)</option>
                    <option value="bulanan" {{ ($selectedType ?? '') == 'bulanan' ? 'selected' : '' }}>Tagihan Bulanan (SPP)</option>
                    <option value="bebas" {{ ($selectedType ?? '') == 'bebas' ? 'selected' : '' }}>Tagihan Bebas / Non-Bulanan</option>
                </select>
            </div>

            <!-- Payment Post Filter -->
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Pos Pembayaran</label>
                <select name="payment_post_id" @change="$el.closest('form').submit()" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white text-xs rounded-xl p-2.5 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-indigo-500 font-semibold">
                    <option value="">Semua Pos</option>
                    @foreach($paymentPosts as $post)
                        <option value="{{ $post->id }}" {{ $selectedPostId == $post->id ? 'selected' : '' }}>
                            {{ $post->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Search -->
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Cari Siswa / NISN</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}" 
                        @input.debounce.400ms="$el.closest('form').submit()"
                        x-init="if ('{{ $search }}') { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length); }"
                        placeholder="Nama atau NISN..." class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white text-xs rounded-xl p-2.5 pr-8 focus:bg-white dark:focus:bg-slate-800 focus:ring-2 focus:ring-indigo-500 font-semibold">
                    @if($search)
                        <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Summary Statistics Ribbon -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-100 dark:border-indigo-900/40 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 7a4 4 0 100-8 4 4 0 000 8z"/>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Siswa Terfilter</p>
                <p class="text-xl font-black text-slate-800 dark:text-white mt-0.5">{{ number_format($students->total()) }} Siswa</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-100 dark:border-amber-900/40 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Item Tunggakan</p>
                <p class="text-xl font-black text-amber-600 dark:text-amber-400 mt-0.5">{{ number_format($totalUnpaidCount ?? 0) }} Item</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-2xs flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center border border-rose-100 dark:border-rose-900/40 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nominal Belum Dibayar</p>
                <p class="text-xl font-black text-rose-600 dark:text-rose-400 mt-0.5">Rp {{ number_format($totalUnpaidAmount, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Multi-Year Matrix Table Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-2xs">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 bg-slate-50/50 dark:bg-slate-800/30">
            <div>
                <h3 class="font-extrabold text-slate-900 dark:text-white text-sm">Matriks Penelusuran Tunggakan Tagihan</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Detail status kelunasan per tagihan bulanan & bebas untuk tiap siswa</p>
            </div>
            <div class="flex items-center space-x-4 text-xs font-semibold text-slate-600 dark:text-slate-300">
                <div class="flex items-center space-x-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <span>Lunas</span>
                </div>
                <div class="flex items-center space-x-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    <span>Dicicil</span>
                </div>
                <div class="flex items-center space-x-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                    <span>Belum Dibayar</span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-900 dark:bg-slate-950 text-slate-200 uppercase tracking-wider font-extrabold text-[10px]">
                    <tr>
                        <th class="px-4 py-3.5 border-b border-slate-800 min-w-[180px]">Nama Siswa & NISN</th>
                        <th class="px-3 py-3.5 border-b border-slate-800 min-w-[100px]">Kelas</th>
                        <th class="px-3 py-3.5 border-b border-slate-800 min-w-[160px]">Tahun Akademik / Pos</th>
                        <th class="px-2 py-3.5 border-b border-slate-800 text-center w-11">Jul</th>
                        <th class="px-2 py-3.5 border-b border-slate-800 text-center w-11">Ags</th>
                        <th class="px-2 py-3.5 border-b border-slate-800 text-center w-11">Sep</th>
                        <th class="px-2 py-3.5 border-b border-slate-800 text-center w-11">Okt</th>
                        <th class="px-2 py-3.5 border-b border-slate-800 text-center w-11">Nov</th>
                        <th class="px-2 py-3.5 border-b border-slate-800 text-center w-11">Des</th>
                        <th class="px-2 py-3.5 border-b border-slate-800 text-center w-11">Jan</th>
                        <th class="px-2 py-3.5 border-b border-slate-800 text-center w-11">Feb</th>
                        <th class="px-2 py-3.5 border-b border-slate-800 text-center w-11">Mar</th>
                        <th class="px-2 py-3.5 border-b border-slate-800 text-center w-11">Apr</th>
                        <th class="px-2 py-3.5 border-b border-slate-800 text-center w-11">Mei</th>
                        <th class="px-2 py-3.5 border-b border-slate-800 text-center w-11">Jun</th>
                        <th class="px-3 py-3.5 border-b border-slate-800 text-center min-w-[110px]">Tunggakan</th>
                        <th class="px-4 py-3.5 border-b border-slate-800 text-right min-w-[100px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse($students as $student)
                        @php
                            $bills = $student->paymentBills;
                        @endphp

                        @if($bills->count() > 0)
                            @foreach($bills as $billIndex => $bill)
                                @php
                                    $isBulanan = ($bill->paymentBill->type === 'bulanan');
                                    $detailsByMonth = $bill->details->keyBy('month_no');
                                    $unpaidDetails = $bill->details->where('status', '!=', 'paid');
                                    $unpaidCount = $unpaidDetails->count();
                                    $unpaidSum = $bill->total_amount - $bill->paid_amount;
                                @endphp
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors {{ $billIndex === 0 ? 'border-t-2 border-slate-200 dark:border-slate-700' : '' }}">
                                    <!-- Student Name (Rowspan for multiple bills of same student) -->
                                    @if($billIndex === 0)
                                        <td class="px-4 py-3.5 align-top font-semibold bg-white dark:bg-slate-900" rowspan="{{ $bills->count() }}">
                                            <div class="flex items-center space-x-2.5">
                                                <div class="w-7 h-7 rounded-full bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 font-black text-xs flex items-center justify-center shrink-0 border border-indigo-200 dark:border-indigo-800">
                                                    {{ strtoupper(substr($student->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <p class="font-extrabold text-slate-900 dark:text-white text-xs">{{ $student->name }}</p>
                                                    <p class="text-[10px] text-slate-400 font-mono mt-0.5">NISN: {{ $student->nisn }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3.5 align-top font-medium bg-white dark:bg-slate-900" rowspan="{{ $bills->count() }}">
                                            <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded text-[11px] font-semibold border border-slate-200 dark:border-slate-700">
                                                {{ $student->schoolClass->name ?? '-' }}
                                            </span>
                                        </td>
                                    @endif

                                    <!-- Bill Academic Year & Post -->
                                    <td class="px-3 py-3.5">
                                        <div class="flex items-center space-x-1.5 mb-0.5">
                                            <span class="px-1.5 py-0.5 text-[9px] font-extrabold uppercase rounded border {{ $isBulanan ? 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800' : 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/60 dark:text-purple-300 dark:border-purple-800' }}">
                                                {{ $isBulanan ? 'Bulanan' : 'Bebas' }}
                                            </span>
                                            <p class="font-bold text-slate-900 dark:text-white text-xs truncate">{{ $bill->paymentBill->name }}</p>
                                        </div>
                                        <p class="text-[10px] text-slate-400">
                                            T.A {{ $bill->paymentBill->academicYear->name ?? '-' }} &bull; {{ $bill->paymentBill->paymentPost->name ?? '-' }}
                                        </p>
                                    </td>

                                    <!-- Matrix Content (Bulanan vs Bebas) -->
                                    @if($isBulanan)
                                        <!-- 12 Months Columns (1 to 12) -->
                                        @for($m = 1; $m <= 12; $m++)
                                            @php
                                                $detail = $detailsByMonth->get($m);
                                            @endphp
                                            <td class="px-1 py-3.5 text-center">
                                                @if($detail)
                                                    @if($detail->status === 'paid')
                                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 font-bold text-[10px] border border-emerald-200 dark:border-emerald-800" title="{{ $detail->month_name }}: Lunas (Rp {{ number_format($detail->paid_amount, 0, ',', '.') }})">
                                                            ✓
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300 font-bold text-[10px] border border-rose-200 dark:border-rose-800" title="{{ $detail->month_name }}: Belum Dibayar (Rp {{ number_format($detail->amount, 0, ',', '.') }})">
                                                            ✕
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="text-slate-300 dark:text-slate-600 font-bold text-xs">-</span>
                                                @endif
                                            </td>
                                        @endfor
                                    @else
                                        <!-- Non-Bulanan (Bebas) Colspan Bar -->
                                        <td colspan="12" class="px-3 py-3.5 text-center">
                                            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-lg border text-xs font-semibold {{ $bill->status === 'paid' ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800' : ($bill->status === 'partial' ? 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800' : 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800') }}">
                                                <span>Tagihan Bebas / Cicilan:</span>
                                                <span class="font-black font-mono">
                                                    Rp {{ number_format($bill->paid_amount, 0, ',', '.') }} / Rp {{ number_format($bill->total_amount, 0, ',', '.') }}
                                                </span>
                                                <span class="uppercase text-[9px] font-extrabold px-1.5 py-0.5 rounded border {{ $bill->status === 'paid' ? 'bg-emerald-200 text-emerald-900 border-emerald-300 dark:bg-emerald-900 dark:text-emerald-100 dark:border-emerald-700' : ($bill->status === 'partial' ? 'bg-amber-200 text-amber-900 border-amber-300 dark:bg-amber-900 dark:text-amber-100 dark:border-amber-700' : 'bg-rose-200 text-rose-900 border-rose-300 dark:bg-rose-900 dark:text-rose-100 dark:border-rose-700') }}">
                                                    {{ $bill->status === 'paid' ? 'Lunas' : ($bill->status === 'partial' ? 'Dicicil' : 'Belum Dibayar') }}
                                                </span>
                                            </div>
                                        </td>
                                    @endif

                                    <!-- Tunggakan Column -->
                                    <td class="px-3 py-3.5 text-center">
                                        @if($unpaidSum > 0)
                                            <span class="inline-flex flex-col items-center px-2 py-1 bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 rounded-lg border border-rose-200 dark:border-rose-800 text-[10px] font-bold">
                                                <span>{{ $isBulanan ? $unpaidCount . ' Bulan' : 'Tagihan Bebas' }}</span>
                                                <span class="text-[9px] font-bold font-mono mt-0.5">Rp {{ number_format($unpaidSum, 0, ',', '.') }}</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 rounded-md text-[10px] font-extrabold border border-emerald-200 dark:border-emerald-800">
                                                Lunas
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Action Button -->
                                    <td class="px-4 py-3.5 text-right">
                                        <a href="{{ route('admin.student-payments.pay', $student->id) }}" class="inline-flex items-center space-x-1 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 active:scale-[0.98] text-white rounded-xl text-xs font-bold transition-colors shadow-xs">
                                            <span>Bayar</span>
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors border-t border-slate-200 dark:border-slate-700">
                                <td class="px-4 py-3 font-semibold text-slate-900 dark:text-white">
                                    <p class="font-extrabold text-slate-900 dark:text-white text-xs">{{ $student->name }}</p>
                                    <p class="text-[11px] text-slate-400 font-mono mt-0.5">NISN: {{ $student->nisn }}</p>
                                </td>
                                <td class="px-3 py-3 font-medium text-slate-600 dark:text-slate-300">
                                    <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded text-[11px] font-semibold border border-slate-200 dark:border-slate-700">
                                        {{ $student->schoolClass->name ?? '-' }}
                                    </span>
                                </td>
                                <td colspan="14" class="px-3 py-3 text-slate-400 text-xs italic">
                                    Belum ada tagihan yang dibuat untuk siswa ini.
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.student-payments.pay', $student->id) }}" class="inline-flex items-center space-x-1 px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-colors border border-slate-200 dark:border-slate-700">
                                        <span>Lihat</span>
                                    </a>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="17" class="px-5 py-12 text-center text-slate-400 text-xs font-medium">
                                Tidak ada data tunggakan tagihan siswa yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($students->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                {{ $students->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

