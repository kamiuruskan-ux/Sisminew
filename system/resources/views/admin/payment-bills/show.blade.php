@extends('layouts.admin')

@section('title', 'Detail Tarif Tagihan - ' . $paymentBill->name)

@section('content')
<div class="space-y-6" x-data="{
    editTarifModalOpen: false,
    editStudentBillModalOpen: false,
    bulkEditModalOpen: false,
    targetType: '{{ $paymentBill->target_type ?? 'all' }}',
    billType: '{{ $paymentBill->type ?? 'bulanan' }}',
    
    // Selection state for Bulk Edit
    selectedBills: [],
    selectAll: false,
    toggleAll() {
        if (this.selectAll) {
            this.selectedBills = [
                @foreach($paymentBill->studentPaymentBills as $sb)
                    {{ $sb->id }},
                @endforeach
            ];
        } else {
            this.selectedBills = [];
        }
    },

    // Individual Edit Student Bill state
    activeStudentBill: null,
    openEditStudentBill(sb) {
        this.activeStudentBill = sb;
        this.editStudentBillModalOpen = true;
    }
}">

    <!-- Header & Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.payment-bills.index') }}" class="p-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight">{{ $paymentBill->name }}</h1>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-0.5">
                    {{ $paymentBill->paymentPost->name ?? '-' }} &bull; {{ $paymentBill->academicYear->name ?? '-' }}
                </p>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            <button type="button" @click="editTarifModalOpen = true" class="inline-flex items-center space-x-2 px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs rounded-xl border border-indigo-200 transition shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span>Edit Data Tarif</span>
            </button>
        </div>
    </div>

    <!-- Card Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tipe Tarif</p>
            <p class="text-lg font-black text-indigo-600 dark:text-indigo-400 uppercase mt-1">{{ $paymentBill->type }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Nominal Tarif</p>
            <p class="text-lg font-black text-emerald-600 dark:text-emerald-400 mt-1">Rp {{ number_format($paymentBill->amount, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sasaran Tagihan</p>
            <p class="text-sm font-black text-slate-800 dark:text-white mt-1.5">
                @if(($paymentBill->target_type === 'student' || $paymentBill->student_id) && $paymentBill->student)
                    Siswa: {{ $paymentBill->student->name }}
                @elseif(($paymentBill->target_type === 'major' || $paymentBill->major_id) && $paymentBill->major)
                    Kejuruan: {{ $paymentBill->major->name }}
                @elseif(($paymentBill->target_type === 'class' || $paymentBill->schoolClass) && $paymentBill->schoolClass)
                    Kelas: {{ $paymentBill->schoolClass->name }}
                @else
                    Semua Siswa
                @endif
            </p>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Jumlah Terdaftar</p>
            <p class="text-lg font-black text-purple-600 dark:text-purple-400 mt-1">{{ $paymentBill->studentPaymentBills->count() }} Siswa</p>
        </div>
    </div>

    <!-- Action Generate Tagihan -->
    <div class="bg-gradient-to-r from-indigo-900 to-slate-900 rounded-xl p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 text-white shadow-md">
        <div>
            <h3 class="text-base font-extrabold">Generate Tagihan Siswa Baru</h3>
            <p class="text-xs text-indigo-200 mt-1">Buat tagihan siswa yang belum terdaftar pada tarif ini sesuai filter sasaran.</p>
        </div>
        <form action="{{ route('admin.payment-bills.generate', $paymentBill->id) }}" method="POST" class="flex flex-wrap items-center gap-2.5">
            @csrf
            <x-major-class-select :selected-major="request('major_id')" :selected-class="request('class_id')" :is-filter="true" layout="inline" major-label="" class-label="" select-class="px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs font-semibold focus:outline-none" />
            <button type="submit" class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-xl text-xs font-bold transition shadow-md whitespace-nowrap">
                Generate Tagihan
            </button>
        </form>
    </div>

    <!-- Bulk Action Toolbar -->
    <div x-show="selectedBills.length > 0" x-cloak class="bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 rounded-xl p-4 flex flex-col sm:flex-row items-center justify-between gap-3 transition-all shadow-xs">
        <div class="flex items-center space-x-2 text-xs font-bold text-amber-800 dark:text-amber-300">
            <span class="w-6 h-6 rounded-full bg-amber-600 text-white flex items-center justify-center text-[11px]" x-text="selectedBills.length"></span>
            <span>Tagihan siswa terpilih</span>
        </div>
        <div class="flex items-center space-x-2">
            <button type="button" @click="bulkEditModalOpen = true" class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-bold transition shadow-2xs">
                Edit Bulk Nominal
            </button>
            <form action="{{ route('admin.payment-bills.bulk-update-student-bills', $paymentBill->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tagihan terpilih yang belum dibayar?')">
                @csrf
                <template x-for="id in selectedBills" :key="id">
                    <input type="hidden" name="student_bill_ids[]" :value="id">
                </template>
                <input type="hidden" name="action_type" value="delete">
                <button type="submit" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-bold transition shadow-2xs">
                    Hapus Selected
                </button>
            </form>
        </div>
    </div>

    <!-- Student Bills List -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-900/50">
            <h3 class="font-extrabold text-slate-800 dark:text-white text-sm">Daftar Tagihan Siswa</h3>
            <span class="text-xs text-slate-500">Total {{ $paymentBill->studentPaymentBills->count() }} records</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3 text-center w-10">
                            <input type="checkbox" x-model="selectAll" @change="toggleAll()" class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                        </th>
                        <th class="px-4 py-3">Siswa</th>
                        <th class="px-4 py-3">NISN</th>
                        <th class="px-4 py-3">Kelas / Kejuruan</th>
                        <th class="px-4 py-3 text-right">Total Tagihan</th>
                        <th class="px-4 py-3 text-right">Sudah Dibayar</th>
                        <th class="px-4 py-3 text-right">Sisa Tagihan</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse($paymentBill->studentPaymentBills as $sb)
                        @php 
                            $sisa = $sb->total_amount - $sb->paid_amount;
                            $sbData = [
                                'id' => $sb->id,
                                'student_name' => addslashes($sb->student->name ?? ''),
                                'total_amount' => $sb->total_amount,
                                'paid_amount' => $sb->paid_amount,
                                'details' => $sb->details->map(fn($d) => [
                                    'id' => $d->id,
                                    'month_name' => $d->month_name,
                                    'amount' => $d->amount,
                                    'paid_amount' => $d->paid_amount,
                                    'status' => $d->status,
                                ])
                            ];
                        @endphp
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-4 py-3.5 text-center">
                                <input type="checkbox" value="{{ $sb->id }}" x-model="selectedBills" class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                            </td>
                            <td class="px-4 py-3.5 font-bold text-slate-800 dark:text-white">{{ $sb->student->name ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-slate-500 font-mono">{{ $sb->student->nisn ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-slate-600 dark:text-slate-400 font-medium">
                                Kelas {{ $sb->student->schoolClass->name ?? '-' }}
                                @if($isVocational && $sb->student->major)
                                    <span class="text-[10px] text-indigo-600 dark:text-indigo-400 block">({{ $sb->student->major->code }})</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-right font-bold text-slate-900 dark:text-white">Rp {{ number_format($sb->total_amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3.5 text-right font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($sb->paid_amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3.5 text-right font-bold text-rose-600 dark:text-rose-400">Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                            <td class="px-4 py-3.5 text-center">
                                @if($sb->status === 'paid')
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-extrabold uppercase">Lunas</span>
                                @elseif($sb->status === 'partial')
                                    <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[10px] font-extrabold uppercase">Dicicil</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-700 text-[10px] font-extrabold uppercase">Belum Bayar</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center justify-end space-x-1.5">
                                    <button type="button" @click='openEditStudentBill({{ json_encode($sbData) }})' class="px-2.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 rounded-lg text-xs font-bold transition" title="Edit Tagihan Siswa">
                                        Edit
                                    </button>
                                    <a href="{{ route('admin.student-payments.pay', $sb->student_id) }}" class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-lg text-xs font-bold transition">
                                        Bayar
                                    </a>
                                    @if($sb->paid_amount == 0)
                                        <form action="{{ route('admin.payment-bills.student-bills.destroy', [$paymentBill->id, $sb->id]) }}" method="POST" onsubmit="return confirm('Hapus tagihan untuk {{ addslashes($sb->student->name ?? 'siswa') }}?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 rounded hover:bg-slate-100 transition" title="Hapus Tagihan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-12 text-slate-400 text-xs">
                                Belum ada tagihan siswa untuk tarif ini. Klik "Generate Tagihan" di atas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL 1: Edit Tarif PaymentBill (SVG Proportional Design) -->
    <div x-show="editTarifModalOpen" x-cloak class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs z-50 overflow-y-auto flex items-center justify-center p-4" @click="editTarifModalOpen = false">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl max-w-lg w-full border border-slate-200 dark:border-slate-800 overflow-hidden transition-all transform" @click.stop>
            
            <div class="flex justify-between items-center px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/80">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-sm">Edit Data Tarif Pembayaran</h3>
                        <p class="text-[11px] text-slate-500">Perbarui setting tarif dan nominal</p>
                    </div>
                </div>
                <button type="button" @click="editTarifModalOpen = false" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-white rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.payment-bills.update', $paymentBill->id) }}" method="POST" class="p-5 space-y-4 text-xs">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Pos Pembayaran <span class="text-rose-500">*</span></label>
                        <select name="payment_post_id" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl p-2.5 font-medium">
                            @foreach($paymentPosts as $post)
                                <option value="{{ $post->id }}" {{ $paymentBill->payment_post_id == $post->id ? 'selected' : '' }}>{{ $post->name }} ({{ $post->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Tahun Akademik <span class="text-rose-500">*</span></label>
                        <select name="academic_year_id" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl p-2.5 font-medium">
                            @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}" {{ $paymentBill->academic_year_id == $ay->id ? 'selected' : '' }}>{{ $ay->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Nama Tarif <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ $paymentBill->name }}" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl p-2.5 font-semibold">
                </div>

                <!-- Tipe Pembayaran -->
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Tipe Pembayaran <span class="text-rose-500">*</span></label>
                    <input type="hidden" name="type" :value="billType">
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" @click="billType = 'bulanan'" class="p-2.5 rounded-xl border text-left transition-all cursor-pointer flex items-center space-x-2.5" :class="billType === 'bulanan' ? 'bg-indigo-50/80 border-indigo-500 text-indigo-700 font-bold dark:bg-indigo-950/60 dark:text-indigo-300' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:border-slate-700'">
                            <div class="p-1.5 rounded-lg bg-indigo-100 dark:bg-indigo-900/60 text-indigo-600 dark:text-indigo-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="block text-xs font-bold">Bulanan</span>
                                <span class="block text-[10px] text-slate-500 font-normal">12 Bulan (SPP)</span>
                            </div>
                        </button>

                        <button type="button" @click="billType = 'bebas'" class="p-2.5 rounded-xl border text-left transition-all cursor-pointer flex items-center space-x-2.5" :class="billType === 'bebas' ? 'bg-indigo-50/80 border-indigo-500 text-indigo-700 font-bold dark:bg-indigo-950/60 dark:text-indigo-300' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:border-slate-700'">
                            <div class="p-1.5 rounded-lg bg-indigo-100 dark:bg-indigo-900/60 text-indigo-600 dark:text-indigo-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="block text-xs font-bold">Bebas / Cicilan</span>
                                <span class="block text-[10px] text-slate-500 font-normal">Total Tagihan</span>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Sasaran Tagihan SVG Selector Cards -->
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Sasaran Tagihan <span class="text-rose-500">*</span></label>
                    <input type="hidden" name="target_type" :value="targetType">
                    
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <!-- All -->
                        <button type="button" @click="targetType = 'all'" class="p-2.5 rounded-xl border text-center transition-all flex flex-col items-center justify-center cursor-pointer" :class="targetType === 'all' ? 'bg-indigo-50/80 border-indigo-500 text-indigo-700 font-bold shadow-2xs dark:bg-indigo-950/60 dark:text-indigo-300' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:border-slate-700'">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span class="text-[11px] mt-1 tracking-tight">Semua Siswa</span>
                        </button>

                        <!-- Class -->
                        <button type="button" @click="targetType = 'class'" class="p-2.5 rounded-xl border text-center transition-all flex flex-col items-center justify-center cursor-pointer" :class="targetType === 'class' ? 'bg-indigo-50/80 border-indigo-500 text-indigo-700 font-bold shadow-2xs dark:bg-indigo-950/60 dark:text-indigo-300' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:border-slate-700'">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4M10 11H6m4 0V7m0 4v10"/>
                            </svg>
                            <span class="text-[11px] mt-1 tracking-tight">Per Kelas</span>
                        </button>

                        <!-- Major (Vocational) -->
                        @if($isVocational)
                            <button type="button" @click="targetType = 'major'" class="p-2.5 rounded-xl border text-center transition-all flex flex-col items-center justify-center cursor-pointer" :class="targetType === 'major' ? 'bg-indigo-50/80 border-indigo-500 text-indigo-700 font-bold shadow-2xs dark:bg-indigo-950/60 dark:text-indigo-300' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:border-slate-700'">
                                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                </svg>
                                <span class="text-[11px] mt-1 tracking-tight">Per Kejuruan</span>
                            </button>
                        @endif

                        <!-- Student -->
                        <button type="button" @click="targetType = 'student'" class="p-2.5 rounded-xl border text-center transition-all flex flex-col items-center justify-center cursor-pointer" :class="targetType === 'student' ? 'bg-indigo-50/80 border-indigo-500 text-indigo-700 font-bold shadow-2xs dark:bg-indigo-950/60 dark:text-indigo-300' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:border-slate-700'">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="text-[11px] mt-1 tracking-tight">Per Siswa</span>
                        </button>
                    </div>
                </div>

                <!-- Conditional Target Selectors -->
                <div x-show="targetType === 'class'" x-cloak class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700">
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Pilih Kelas Target <span class="text-rose-500">*</span></label>
                    <select name="class_id" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl p-2.5 font-medium">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $paymentBill->class_id == $c->id ? 'selected' : '' }}>Kelas {{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                @if($isVocational)
                    <div x-show="targetType === 'major'" x-cloak class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700">
                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Pilih Kejuruan / Jurusan Target <span class="text-rose-500">*</span></label>
                        <select name="major_id" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl p-2.5 font-medium">
                            <option value="">-- Pilih Jurusan --</option>
                            @foreach($majors as $m)
                                <option value="{{ $m->id }}" {{ $paymentBill->major_id == $m->id ? 'selected' : '' }}>{{ $m->name }} ({{ $m->code }})</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div x-show="targetType === 'student'" x-cloak>
                    <x-student-select-search :students="$students" name="student_id" :selected="$paymentBill->student_id ?? ''" label="Pilih Siswa Target" placeholder="-- Cari & Pilih Siswa (Nama / NISN / Kelas) --" />
                </div>

                <div>
                    <x-rupiah-input name="amount" label="Nominal Tarif (Rp)" :value="$paymentBill->amount" required show-terbilang />
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Keterangan</label>
                    <textarea name="description" rows="2" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">{{ $paymentBill->description }}</textarea>
                </div>

                <div class="flex items-center space-x-2.5 p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    <input type="checkbox" name="update_unpaid_bills" value="1" id="update_unpaid_bills" class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                    <label for="update_unpaid_bills" class="text-xs font-semibold text-slate-700 dark:text-slate-300 cursor-pointer">Update nominal tagihan siswa yang belum dibayar mengikuti nominal tarif baru ini</label>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="editTarifModalOpen = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs rounded-xl border border-slate-200 dark:border-slate-700 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: Edit Individual Student Bill -->
    <div x-show="editStudentBillModalOpen" x-cloak class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs z-50 overflow-y-auto flex items-center justify-center p-4" @click="editStudentBillModalOpen = false">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl max-w-lg w-full border border-slate-200 dark:border-slate-800 overflow-hidden" @click.stop>
            <div class="flex justify-between items-center px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/80">
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-sm">Edit Nominal Tagihan Siswa</h3>
                    <p class="text-xs text-indigo-600 font-semibold mt-0.5" x-text="activeStudentBill ? activeStudentBill.student_name : ''"></p>
                </div>
                <button type="button" @click="editStudentBillModalOpen = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <template x-if="activeStudentBill">
                <form :action="'{{ url('admin/payment-bills/' . $paymentBill->id . '/student-bills') }}/' + activeStudentBill.id" method="POST" class="p-5 space-y-4 text-xs">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Total Nominal Tagihan (Rp) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-slate-400 text-xs">Rp</span>
                            <input type="number" name="total_amount" x-model="activeStudentBill.total_amount" min="0" step="1000" required class="w-full pl-9 pr-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs rounded-xl font-extrabold text-emerald-600 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Mengubah total nominal akan membagi rata ke bulan-bulan yang belum dibayar (jika tipe bulanan).</p>
                    </div>

                    @if($paymentBill->type === 'bulanan')
                        <div class="border-t border-slate-100 dark:border-slate-800 pt-3">
                            <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Rincian Per Bulan (Bulanan)</label>
                            <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto p-1">
                                <template x-for="detail in activeStudentBill.details" :key="detail.id">
                                    <div class="bg-slate-50 dark:bg-slate-800 p-2 rounded-xl border border-slate-200 dark:border-slate-700">
                                        <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300 block" x-text="detail.month_name"></span>
                                        <input type="number" :name="'monthly_amounts[' + detail.id + ']'" x-model="detail.amount" :disabled="detail.status === 'paid'" min="0" step="1000" class="w-full mt-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs rounded-lg p-1 font-semibold text-slate-900 dark:text-white">
                                    </div>
                                </template>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="editStudentBillModalOpen = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs rounded-xl border border-slate-200 dark:border-slate-700 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    <!-- MODAL 3: Bulk Edit Selected Student Bills -->
    <div x-show="bulkEditModalOpen" x-cloak class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs z-50 overflow-y-auto flex items-center justify-center p-4" @click="bulkEditModalOpen = false">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl max-w-md w-full border border-slate-200 dark:border-slate-800 overflow-hidden" @click.stop>
            <div class="flex justify-between items-center px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/80">
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-sm">Bulk Edit Tagihan Terpilih</h3>
                    <p class="text-xs text-amber-600 font-semibold" x-text="selectedBills.length + ' tagihan siswa dipilih'"></p>
                </div>
                <button type="button" @click="bulkEditModalOpen = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.payment-bills.bulk-update-student-bills', $paymentBill->id) }}" method="POST" class="p-5 space-y-4 text-xs">
                @csrf
                <template x-for="id in selectedBills" :key="id">
                    <input type="hidden" name="student_bill_ids[]" :value="id">
                </template>
                <input type="hidden" name="action_type" value="set_amount">

                <div>
                    <x-rupiah-input name="new_amount" label="Set Total Nominal Baru (Rp)" required show-terbilang />
                    <p class="text-[11px] text-slate-400 mt-1">Setiap tagihan siswa terpilih akan diubah total nominalnya ke angka baru ini.</p>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="bulkEditModalOpen = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs rounded-xl border border-slate-200 dark:border-slate-700 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                        Terapkan ke Selected
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
