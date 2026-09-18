@extends('layouts.admin')

@section('title', 'Kuitansi Bukti Pembayaran')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Action Bar -->
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('admin.student-payments.index') }}" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold text-xs rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition border border-slate-200 dark:border-slate-700 flex items-center space-x-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Pos Pembayaran</span>
        </a>
        <a href="{{ route('admin.student-payments.print', $transaction->id) }}" target="_blank" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition shadow-md flex items-center space-x-1.5 active:scale-[0.98]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            <span>Cetak Kuitansi</span>
        </a>
    </div>

    <!-- Printable Receipt Card -->
    <div class="bg-white dark:bg-slate-900 p-8 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xl print-area space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between pb-6 border-b-2 border-slate-800 dark:border-slate-700 gap-4">
            <div class="flex items-center space-x-4">
                <img src="{{ Setting::getLogoUrl() }}" alt="Logo" class="w-14 h-14 object-contain">
                <div>
                    <h2 class="text-xl font-black uppercase text-slate-900 dark:text-white tracking-tight">{{ Setting::get('school_name', 'SEKOLAH INDONESIA') }}</h2>
                    <p class="text-xs font-semibold text-slate-600 dark:text-slate-400">{{ Setting::get('school_address', 'Jl. Pendidikan No. 1') }}</p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Telp: {{ Setting::get('school_phone', '-') }} &bull; Email: {{ Setting::get('school_email', '-') }}</p>
                </div>
            </div>
            <div class="text-right">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block">KUITANSI PEMBAYARAN</span>
                <span class="text-sm font-black font-mono text-indigo-600 dark:text-indigo-400">{{ $transaction->transaction_number }}</span>
                <span class="text-xs text-slate-500 dark:text-slate-400 block mt-1">{{ $transaction->transaction_date->format('d/m/Y') }}</span>
            </div>
        </div>

        <!-- Student Info -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 py-2 text-xs">
            <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700">
                <span class="text-slate-400 font-bold block uppercase text-[10px]">Telah Diterima Dari</span>
                <span class="text-base font-extrabold text-slate-900 dark:text-white block mt-0.5">{{ $student ? $student->name : $transaction->recipient_or_payee }}</span>
                @if($student)
                    <span class="text-slate-600 dark:text-slate-300 block mt-0.5 font-medium">NISN: <strong class="font-mono text-slate-800 dark:text-white">{{ $student->nisn }}</strong> &bull; Kelas: <strong class="text-slate-800 dark:text-white">{{ $student->schoolClass ? $student->schoolClass->name : '-' }}</strong></span>
                @endif
            </div>
            <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700 text-left sm:text-right">
                <span class="text-slate-400 font-bold block uppercase text-[10px]">Metode / Rekening</span>
                <span class="text-sm font-extrabold text-slate-800 dark:text-white block mt-0.5">{{ $transaction->bankAccount->bank_name }} - {{ $transaction->bankAccount->account_name }}</span>
                <span class="text-slate-500 dark:text-slate-400 block mt-0.5 font-medium">Petugas: <strong class="text-slate-800 dark:text-white">{{ $transaction->creator ? $transaction->creator->name : 'Kasir Bendahara' }}</strong></span>
            </div>
        </div>

        <!-- Items Table -->
        <div class="border rounded-2xl overflow-hidden border-slate-200 dark:border-slate-800">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100 dark:bg-slate-800 font-extrabold text-slate-700 dark:text-slate-300 uppercase text-[10px]">
                    <tr>
                        <th class="p-3 w-12 text-center">No</th>
                        <th class="p-3">Uraian Pembayaran</th>
                        <th class="p-3 text-right">Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-slate-800 dark:text-slate-200 font-medium">
                    @forelse($paidDetails as $index => $detail)
                        <tr>
                            <td class="p-3 text-center font-bold text-slate-500">{{ $index + 1 }}</td>
                            <td class="p-3 font-extrabold text-slate-900 dark:text-white">
                                {{ $detail->studentPaymentBill->paymentBill->name }}
                                @if($detail->month_name)
                                    <span class="text-indigo-600 dark:text-indigo-400 font-bold">({{ $detail->month_name }})</span>
                                @endif
                            </td>
                            <td class="p-3 text-right font-black text-slate-900 dark:text-white font-mono">
                                Rp {{ number_format($detail->paid_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="p-3 text-center font-bold text-slate-500">1</td>
                            <td class="p-3 font-extrabold text-slate-900 dark:text-white">{{ $transaction->description }}</td>
                            <td class="p-3 text-right font-black text-slate-900 dark:text-white font-mono">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-slate-50 dark:bg-slate-800/80 font-black text-slate-900 dark:text-white border-t border-slate-200 dark:border-slate-700">
                    <tr>
                        <td colspan="2" class="p-3 text-right text-xs uppercase tracking-wider">TOTAL DIBAYAR:</td>
                        <td class="p-3 text-right text-sm text-emerald-600 dark:text-emerald-400 font-mono">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Terbilang & Signature -->
        <div class="mt-6 flex flex-col md:flex-row justify-between items-start text-xs pt-4 border-t border-slate-200 dark:border-slate-800 gap-6">
            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-medium italic max-w-md">
                Catatan: Kuitansi ini adalah bukti pembayaran yang sah dan dicetak secara otomatis oleh sistem Keuangan Sekolah.
            </div>

            <div class="text-center min-w-[200px] ml-auto">
                <p class="text-slate-500 dark:text-slate-400 font-semibold mb-12">Bendahara Sekolah,</p>
                <p class="font-black text-slate-900 dark:text-white uppercase border-b border-slate-900 dark:border-slate-100 pb-0.5 inline-block">{{ $transaction->creator ? $transaction->creator->name : 'BENDAHARA' }}</p>
                <p class="text-[10px] text-slate-400 mt-1">NIP / NPY: -</p>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white !important; color: black !important; }
    .print-area { border: none !important; box-shadow: none !important; padding: 0 !important; background: white !important; }
    .print-area * { color: black !important; background: transparent !important; }
}
</style>
@endsection

