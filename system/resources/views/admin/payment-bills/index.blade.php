@extends('layouts.admin')

@section('title', 'Tarif Tagihan Sekolah')
@section('page_title', 'Setting Tarif Tagihan Sekolah')

@section('content')
<div class="space-y-6" x-data="{ 
    createModalOpen: false,
    showDeleteModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    targetType: 'all',
    billType: 'bulanan',
    confirmDelete(id, name) {
        this.deleteTarget = { id: id, name: name };
        this.deleteFormAction = '{{ url('admin/payment-bills') }}/' + id;
        this.showDeleteModal = true;
    }
}">
    @include('components.delete-modal', ['title' => 'Hapus Tarif Tagihan', 'message' => 'Apakah Anda yakin ingin menghapus tarif tagihan :name ini? Tindakan ini tidak dapat dibatalkan.'])

    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Setting Tarif Tagihan Pembayaran</h1>
            <p class="text-xs text-slate-500 mt-1">Atur tarif pembayaran (Bulanan atau Bebas/Cicilan) per Siswa, Kelas, atau Kejuruan.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('admin.payment-posts.index') }}" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-xl transition-colors border border-slate-300 dark:border-slate-700">
                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <span>Kelola Pos Pembayaran</span>
            </a>
            <button type="button" @click="createModalOpen = true" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-indigo-500/20 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Buat Tarif Pembayaran</span>
            </button>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs">
        <form method="GET" action="{{ route('admin.payment-bills.index') }}" class="flex flex-wrap items-center gap-4">
            <div class="w-full sm:w-auto flex-1 max-w-xs">
                <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Tahun Akademik</label>
                <select name="academic_year_id" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-lg p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500">
                    @foreach($academicYears as $ay)
                        <option value="{{ $ay->id }}" {{ $selectedYearId == $ay->id ? 'selected' : '' }}>
                            {{ $ay->name }} {{ $ay->is_active ? '(Aktif Utama)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="font-bold text-slate-900 text-sm">Daftar Setting Tarif Tagihan</h3>
            <span class="text-xs text-slate-500">Total {{ $paymentBills->count() }} tarif dikonfigurasi</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">Pos Pembayaran</th>
                        <th class="px-5 py-3">Nama Tarif</th>
                        <th class="px-5 py-3">Tahun Akademik</th>
                        <th class="px-5 py-3">Sasaran Tagihan</th>
                        <th class="px-5 py-3 text-center">Tipe Billing</th>
                        <th class="px-5 py-3 text-right">Nominal Tarif</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($paymentBills as $bill)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- Pos -->
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-200 font-bold text-xs">
                                    {{ $bill->paymentPost->name ?? '-' }}
                                </span>
                            </td>

                            <!-- Name -->
                            <td class="px-5 py-3.5 font-bold text-slate-900">
                                {{ $bill->name }}
                            </td>

                            <!-- Year -->
                            <td class="px-5 py-3.5 text-slate-600 font-medium">
                                {{ $bill->academicYear->name ?? '-' }}
                            </td>

                            <!-- Sasaran Tagihan -->
                            <td class="px-5 py-3.5">
                                @if(($bill->target_type === 'student' || $bill->student_id) && $bill->student)
                                    <span class="px-2.5 py-0.5 rounded bg-purple-50 text-purple-700 border border-purple-200 text-xs font-semibold">
                                        Siswa: {{ $bill->student->name }} ({{ $bill->student->nisn ?? '-' }})
                                    </span>
                                @elseif(($bill->target_type === 'major' || $bill->major_id) && $bill->major)
                                    <span class="px-2.5 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200 text-xs font-semibold">
                                        Kejuruan: {{ $bill->major->name }}
                                    </span>
                                @elseif(($bill->target_type === 'class' || $bill->schoolClass) && $bill->schoolClass)
                                    <span class="px-2.5 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200 text-xs font-semibold">
                                        Kelas {{ $bill->schoolClass->name }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-semibold">
                                        Semua Siswa
                                    </span>
                                @endif
                            </td>

                            <!-- Type -->
                            <td class="px-5 py-3.5 text-center">
                                @if($bill->type === 'bulanan')
                                    <span class="px-2.5 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold uppercase">
                                        Bulanan
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-200 text-[10px] font-bold uppercase">
                                        Bebas / Cicilan
                                    </span>
                                @endif
                            </td>

                            <!-- Amount -->
                            <td class="px-5 py-3.5 text-right font-bold text-emerald-700 text-xs">
                                Rp {{ number_format($bill->amount, 0, ',', '.') }}
                                @if($bill->type === 'bulanan')
                                    <span class="text-[10px] text-slate-400 font-normal">/bulan</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.payment-bills.show', $bill->id) }}" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs font-bold transition border border-indigo-200">
                                        Kelola Tagihan
                                    </a>
                                    <button type="button" @click="confirmDelete({{ $bill->id }}, '{{ addslashes($bill->name) }}')" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer" title="Hapus Tarif">
                                         <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                             <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                         </svg>
                                     </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-slate-400 text-xs font-medium">
                                Belum ada tarif tagihan pembayaran pada tahun akademik ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Modal (Proportional & Modern SVG Design) -->
    <div x-show="createModalOpen" x-cloak class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs z-50 overflow-y-auto flex items-center justify-center p-4" @click="createModalOpen = false">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl max-w-lg w-full border border-slate-200 dark:border-slate-800 overflow-hidden transition-all transform" @click.stop>
            
            <!-- Modal Header -->
            <div class="flex justify-between items-center px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/80">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-sm">Buat Tarif Tagihan Pembayaran</h3>
                        <p class="text-[11px] text-slate-500">Konfigurasi tarif dan sasaran siswa</p>
                    </div>
                </div>
                <button type="button" @click="createModalOpen = false" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-white rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Form -->
            <form action="{{ route('admin.payment-bills.store') }}" method="POST" class="p-5 space-y-4 text-xs">
                @csrf
                
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Pos Pembayaran <span class="text-rose-500">*</span></label>
                        <select name="payment_post_id" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500 font-medium">
                            <option value="">Pilih Pos</option>
                            @foreach($paymentPosts as $post)
                                <option value="{{ $post->id }}">{{ $post->name }} ({{ $post->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Tahun Akademik <span class="text-rose-500">*</span></label>
                        <select name="academic_year_id" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500 font-medium">
                            @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}" {{ $selectedYearId == $ay->id ? 'selected' : '' }}>{{ $ay->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Nama Tarif <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: SPP Bulanan Kelas 10, Uang Pangkal TKJ 2026" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500 font-semibold">
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
                    <select name="class_id" :required="targetType === 'class'" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl p-2.5 focus:ring-1 focus:ring-indigo-500 font-semibold">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}">Kelas {{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                @if($isVocational)
                    <div x-show="targetType === 'major'" x-cloak class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700">
                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Pilih Kejuruan / Jurusan Target <span class="text-rose-500">*</span></label>
                        <select name="major_id" :required="targetType === 'major'" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl p-2.5 focus:ring-1 focus:ring-indigo-500 font-semibold">
                            <option value="">-- Pilih Jurusan --</option>
                            @foreach($majors as $m)
                                <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->code }})</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Searchable Select Student Component Helper -->
                <div x-show="targetType === 'student'" x-cloak>
                    <x-student-select-search :students="$students" name="student_id" label="Pilih Siswa Target" placeholder="-- Cari & Pilih Siswa (Nama / NISN / Kelas) --" />
                </div>

                <div>
                    <x-rupiah-input name="amount" label="Nominal Tarif (Rp)" required show-terbilang />
                    <p class="text-[11px] text-slate-400 mt-1">Jika tipe Bulanan: nominal per bulan. Jika tipe Bebas: total nominal tagihan.</p>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Keterangan / Catatan</label>
                    <textarea name="description" rows="2" placeholder="Catatan tambahan (opsional)" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-indigo-500"></textarea>
                </div>

                <div class="flex items-center space-x-2.5 p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    <input type="checkbox" name="auto_generate" value="1" id="auto_generate" class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                    <label for="auto_generate" class="text-xs font-semibold text-slate-700 dark:text-slate-300 cursor-pointer">Otomatis generate tagihan ke seluruh siswa aktif yang menjadi sasaran</label>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="createModalOpen = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs rounded-xl border border-slate-200 dark:border-slate-700 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                        Simpan Tarif
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
