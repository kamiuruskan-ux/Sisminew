@extends('layouts.admin')

@section('title', 'Laporan Keuangan Sekolah')
@section('page_title', 'Laporan Keuangan & Kas')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Laporan Keuangan & Buku Kas Umum</h1>
            <p class="text-sm font-medium text-slate-500">Laporan standar akuntansi sekolah: Saldo Awal, Mutasi Pemasukan/Pengeluaran, Saldo Akumulasi, dan Rekapitulasi Tagihan.</p>
        </div>
        <div>
            <a href="{{ route('admin.financial-reports.print', request()->all()) }}" target="_blank" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Cetak / Print Laporan Resmi</span>
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs">
        <form method="GET" action="{{ route('admin.financial-reports.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Jenis Laporan</label>
                    <select name="report_type" onchange="this.form.submit()" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 font-bold text-xs bg-slate-50 focus:bg-white">
                        <option value="cashbook" {{ $reportType === 'cashbook' ? 'selected' : '' }}>Laporan Buku Kas Umum (BKM)</option>
                        <option value="income" {{ $reportType === 'income' ? 'selected' : '' }}>Laporan Pemasukan (Kas Masuk)</option>
                        <option value="expense" {{ $reportType === 'expense' ? 'selected' : '' }}>Laporan Pengeluaran (Kas Keluar)</option>
                        <option value="student_bills" {{ $reportType === 'student_bills' ? 'selected' : '' }}>Laporan Rekap Tagihan Siswa</option>
                    </select>
                </div>

                @if($reportType !== 'student_bills')
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Rekening / Kas</label>
                        <select name="bank_account_id" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs bg-slate-50 focus:bg-white">
                            <option value="">Semua Kas & Rekening Bank</option>
                            @foreach($bankAccounts as $acc)
                                <option value="{{ $acc->id }}" {{ $bankAccountId == $acc->id ? 'selected' : '' }}>{{ $acc->bank_name }} - {{ $acc->account_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Kategori Keuangan</label>
                        <select name="financial_category_id" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs bg-slate-50 focus:bg-white">
                            <option value="">Semua Kategori Keuangan</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }} ({{ ucfirst($cat->type) }})</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <x-major-class-select :selected-major="request('major_id')" :selected-class="$classId" :is-filter="true" layout="inline" major-label="Filter Jurusan" class-label="Filter Kelas" select-class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs bg-slate-50 focus:bg-white" label-class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1" />
                @endif

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-slate-50 focus:bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-slate-50 focus:bg-white">
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-1">
                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-xs transition">
                    Tampilkan Laporan
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Box (Accurate Financial Metrics) -->
    @if($reportType !== 'student_bills')
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl p-5 border border-slate-200 border-l-4 border-l-slate-600 shadow-xs">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Saldo Awal Periode</p>
                <p class="text-xl font-bold text-slate-900 mt-1">Rp {{ number_format($openingBalance, 0, ',', '.') }}</p>
                <p class="text-[11px] text-slate-400 mt-1">Saldo sebelum {{ date('d M Y', strtotime($startDate)) }}</p>
            </div>

            <div class="bg-white rounded-xl p-5 border border-slate-200 border-l-4 border-l-emerald-500 shadow-xs">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Pemasukan Periode</p>
                <p class="text-xl font-bold text-emerald-600 mt-1">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
                <p class="text-[11px] text-slate-400 mt-1">Kas Masuk dalam periode</p>
            </div>

            <div class="bg-white rounded-xl p-5 border border-slate-200 border-l-4 border-l-rose-500 shadow-xs">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Pengeluaran Periode</p>
                <p class="text-xl font-bold text-rose-600 mt-1">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
                <p class="text-[11px] text-slate-400 mt-1">Kas Keluar dalam periode</p>
            </div>

            <div class="bg-white rounded-xl p-5 border border-slate-200 border-l-4 border-l-indigo-600 shadow-xs">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Saldo Akhir Periode</p>
                <p class="text-xl font-bold text-indigo-700 mt-1">Rp {{ number_format($endingBalance, 0, ',', '.') }}</p>
                <p class="text-[11px] text-slate-400 mt-1">Saldo akumulasi s/d {{ date('d M Y', strtotime($endDate)) }}</p>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl p-5 border border-slate-200 border-l-4 border-l-indigo-500 shadow-xs">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Tagihan Siswa</p>
                <p class="text-xl font-bold text-indigo-600 mt-1">Rp {{ number_format($studentBills->sum('total_amount'), 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-xl p-5 border border-slate-200 border-l-4 border-l-emerald-500 shadow-xs">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Pembayaran Terbayar</p>
                <p class="text-xl font-bold text-emerald-600 mt-1">Rp {{ number_format($studentBills->sum('paid_amount'), 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-xl p-5 border border-slate-200 border-l-4 border-l-rose-500 shadow-xs">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Tunggakan Siswa</p>
                <p class="text-xl font-bold text-rose-600 mt-1">Rp {{ number_format($studentBills->sum('total_amount') - $studentBills->sum('paid_amount'), 0, ',', '.') }}</p>
            </div>
        </div>
    @endif

    <!-- Main Ledger Table -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">
                    @if($reportType === 'cashbook') Laporan Mutasi Buku Kas Umum
                    @elseif($reportType === 'income') Laporan Kas Masuk (Pemasukan)
                    @elseif($reportType === 'expense') Laporan Kas Keluar (Pengeluaran)
                    @else Rekapitulasi Tagihan & Tunggakan Siswa
                    @endif
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Periode: {{ date('d M Y', strtotime($startDate)) }} s/d {{ date('d M Y', strtotime($endDate)) }}</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            @if($reportType !== 'student_bills')
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-900 text-slate-200 uppercase tracking-wider font-semibold text-[10px]">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">No. Transaksi</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">Kas / Rekening</th>
                            <th class="px-4 py-3">Uraian Transaksi</th>
                            <th class="px-4 py-3 text-right">Pemasukan (Rp)</th>
                            <th class="px-4 py-3 text-right">Pengeluaran (Rp)</th>
                            <th class="px-4 py-3 text-right">Saldo (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <!-- Row 0: Opening Balance -->
                        <tr class="bg-slate-50 font-semibold border-b border-slate-200">
                            <td class="px-4 py-3 text-slate-400">-</td>
                            <td class="px-4 py-3 font-mono text-slate-500">SALDO-AWAL</td>
                            <td class="px-4 py-3 text-slate-600">{{ date('d/m/Y', strtotime($startDate)) }}</td>
                            <td class="px-4 py-3 text-slate-600">Saldo Awal Periode</td>
                            <td class="px-4 py-3 text-slate-600">-</td>
                            <td class="px-4 py-3 text-slate-800 font-bold">Saldo Awal Kas & Rekening Bank sebelum {{ date('d/m/Y', strtotime($startDate)) }}</td>
                            <td class="px-4 py-3 text-right text-slate-400">-</td>
                            <td class="px-4 py-3 text-right text-slate-400">-</td>
                            <td class="px-4 py-3 text-right font-bold text-slate-900">Rp {{ number_format($openingBalance, 0, ',', '.') }}</td>
                        </tr>

                        @forelse($transactions as $index => $tx)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3 text-slate-500">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 font-mono font-semibold text-slate-900">{{ $tx->transaction_number }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $tx->transaction_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $tx->financialCategory->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $tx->bankAccount->bank_name ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $tx->description }}</td>
                                <td class="px-4 py-3 text-right font-bold text-emerald-600">
                                    {{ $tx->type === 'pemasukan' ? 'Rp ' . number_format($tx->amount, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-rose-600">
                                    {{ $tx->type === 'pengeluaran' ? 'Rp ' . number_format($tx->amount, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-slate-900 bg-slate-50/40">
                                    Rp {{ number_format($tx->running_balance, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-slate-400">Tidak ada transaksi ditemukan pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-900 text-white font-bold text-xs">
                        <tr>
                            <td colspan="6" class="px-4 py-3 text-right uppercase tracking-wider">Total Periode Ini:</td>
                            <td class="px-4 py-3 text-right text-emerald-400">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-rose-400">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-indigo-300">Rp {{ number_format($endingBalance, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            @else
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-900 text-slate-200 uppercase tracking-wider font-semibold text-[10px]">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Siswa</th>
                            <th class="px-4 py-3">Kelas</th>
                            <th class="px-4 py-3">Tarif Pembayaran</th>
                            <th class="px-4 py-3">Tipe</th>
                            <th class="px-4 py-3 text-right">Total Tagihan</th>
                            <th class="px-4 py-3 text-right">Sudah Dibayar</th>
                            <th class="px-4 py-3 text-right">Sisa Tunggakan</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($studentBills as $index => $sb)
                            @php $sisa = $sb->total_amount - $sb->paid_amount; @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3 text-slate-500">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-900">{{ $sb->student->name }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $sb->student->schoolClass ? $sb->student->schoolClass->name : '-' }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $sb->paymentBill->name }}</td>
                                <td class="px-4 py-3 uppercase text-[10px] font-bold text-indigo-600">{{ $sb->paymentBill->type }}</td>
                                <td class="px-4 py-3 text-right font-bold text-slate-900">Rp {{ number_format($sb->total_amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-bold text-emerald-600">Rp {{ number_format($sb->paid_amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-bold text-rose-600">Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($sb->status === 'paid')
                                        <span class="px-2.5 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold uppercase">LUNAS</span>
                                    @elseif($sb->status === 'partial')
                                        <span class="px-2.5 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold uppercase">DICICIL</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-bold uppercase">BELUM BAYAR</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-slate-400">Tidak ada rekapitulasi tagihan siswa.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <!-- Financial Category Breakdown (If Not Student Bills) -->
    @if($reportType !== 'student_bills' && isset($categoryBreakdown) && $categoryBreakdown->count() > 0)
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs">
            <h3 class="font-bold text-slate-900 text-sm mb-3">Rekapitulasi Kategori Keuangan Periode Ini</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($categoryBreakdown as $cat)
                    <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div>
                            <p class="font-bold text-xs text-slate-800">{{ $cat['name'] }}</p>
                            <p class="text-[10px] text-slate-500 mt-0.5">
                                Masuk: <span class="text-emerald-600 font-semibold">Rp {{ number_format($cat['income'], 0, ',', '.') }}</span>
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold {{ $cat['net'] >= 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                                {{ $cat['net'] >= 0 ? '+' : '' }}Rp {{ number_format($cat['net'], 0, ',', '.') }}
                            </p>
                            <p class="text-[10px] text-slate-500 mt-0.5">
                                Keluar: <span class="text-rose-600 font-semibold">Rp {{ number_format($cat['expense'], 0, ',', '.') }}</span>
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
