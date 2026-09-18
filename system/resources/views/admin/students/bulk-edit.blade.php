@extends('layouts.admin')

@php
    use Illuminate\Support\Str;
    $isVocational = \App\Models\Setting::get('is_vocational', '1') == '1';
    $pageTitleText = $isVocational ? 'Edit Masal Kelas & Jurusan' : 'Edit Masal Kelas';

    $classesArray = $classes->map(fn($c) => [
        'id' => (string) $c->id,
        'name' => $c->name,
        'major_id' => $c->major_id ? (string) $c->major_id : '',
    ])->values()->toArray();
@endphp

@section('title', $pageTitleText)
@section('page_title', $pageTitleText)

@section('content')
<div class="space-y-6" x-data="{
    selected: [{{ implode(',', $students->pluck('id')->toArray()) }}],
    selectAll: true,
    targetMajor: '',
    targetClass: '',
    toastMessage: '',
    showToast: false,
    allClasses: {{ \Illuminate\Support\Js::from($classesArray) }},

    get filteredTargetClasses() {
        if (!{{ $isVocational ? 'true' : 'false' }}) {
            return this.allClasses;
        }
        if (!this.targetMajor) {
            return [];
        }
        return this.allClasses.filter(c => String(c.major_id) === String(this.targetMajor));
    },

    onTargetMajorChange() {
        if (this.targetClass) {
            const valid = this.filteredTargetClasses.some(c => String(c.id) === String(this.targetClass));
            if (!valid) {
                this.targetClass = '';
            }
        }
    },

    onTargetClassChange() {
        if (this.targetClass) {
            const cls = this.allClasses.find(c => String(c.id) === String(this.targetClass));
            if (cls && cls.major_id) {
                this.targetMajor = cls.major_id;
            }
        }
    },

    toggleSelectAll() {
        if (this.selectAll) {
            this.selected = [{{ implode(',', $students->pluck('id')->toArray()) }}];
        } else {
            this.selected = [];
        }
    },
    
    updateSelectAll() {
        const allIds = [{{ implode(',', $students->pluck('id')->toArray()) }}];
        this.selectAll = allIds.length > 0 && allIds.every(id => this.selected.includes(id));
    },

    applyBatch() {
        if (this.selected.length === 0) {
            alert('Pilih minimal satu siswa untuk menerapkan perubahan masal.');
            return;
        }
        if (!this.targetClass && !this.targetMajor) {
            alert('Pilih Jurusan Tujuan atau Kelas Tujuan yang ingin diterapkan.');
            return;
        }

        let updatedCount = 0;
        let majorToApply = this.targetMajor;
        if (this.targetClass) {
            const cls = this.allClasses.find(c => String(c.id) === String(this.targetClass));
            if (cls && cls.major_id) {
                majorToApply = cls.major_id;
            }
        }

        this.selected.forEach(id => {
            if (this.targetClass) {
                const selectClassElem = document.getElementById('class_select_' + id);
                if (selectClassElem) {
                    selectClassElem.value = this.targetClass;
                    updatedCount++;
                }
            }
            if (majorToApply) {
                const selectMajorElem = document.getElementById('major_select_' + id);
                if (selectMajorElem) {
                    selectMajorElem.value = majorToApply;
                }
            }
        });

        this.toastMessage = `Berhasil menerapkan ke ${this.selected.length} siswa terpilih. Klik 'Simpan Perubahan Masal' di bawah untuk menyimpan.`;
        this.showToast = true;
        setTimeout(() => { this.showToast = false; }, 4000);
    }
}">

    <!-- Notification Toast -->
    <div x-show="showToast" x-cloak x-transition
         class="fixed bottom-6 right-6 z-50 bg-emerald-600 text-white px-5 py-3.5 rounded-2xl shadow-2xl flex items-center space-x-3 border border-emerald-400">
        <svg class="w-5 h-5 text-emerald-200 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        <span class="text-xs font-bold" x-text="toastMessage"></span>
    </div>

    <!-- Header Action Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.students.index') }}" class="text-[#64748B] hover:text-[#3C50E0] dark:text-[#8A99AD] transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <h1 class="text-2xl font-extrabold text-[#1C2434] dark:text-white tracking-tight">{{ $pageTitleText }}</h1>
            </div>
            <p class="text-xs text-[#64748B] dark:text-[#8A99AD] mt-1">
                Ubah data {{ $isVocational ? 'Kelas dan/atau Jurusan' : 'Kelas' }} untuk beberapa siswa sekaligus atau seluruh rombel kelas secara cepat.
            </p>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.students.index') }}" class="px-4 py-2.5 bg-[#F1F5F9] dark:bg-[#1A222C] hover:bg-slate-200 dark:hover:bg-[#2E3A47] text-[#1C2434] dark:text-white text-xs font-bold rounded-xl transition border border-[#E2E8F0] dark:border-[#2E3A47]">
                Batal
            </a>
            <button type="button" @click="document.getElementById('bulkUpdateForm').submit()" class="px-5 py-2.5 bg-[#3C50E0] hover:bg-[#3C50E0]/90 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-[#3C50E0]/30 flex items-center space-x-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <span>Simpan Perubahan Masal</span>
            </button>
        </div>
    </div>

    <!-- Filter & Data Scope Selection -->
    <div class="tailadmin-card p-6">
        <form method="GET" action="{{ route('admin.students.bulk-edit') }}" class="grid grid-cols-1 sm:grid-cols-2 {{ $isVocational ? 'lg:grid-cols-4' : 'lg:grid-cols-3' }} gap-4">
            <x-major-class-select :majors="$majors" :classes="$classes" :selected-major="request('major_id')" :selected-class="request('class_id')" :is-filter="true" layout="inline" major-label="Filter Jurusan Asal" class-label="Filter Kelas Asal" select-class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-[#1C2434] dark:text-white text-xs rounded-xl p-2.5 focus:outline-none focus:border-[#3C50E0]" label-class="block text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider mb-1.5" />

            <!-- Search Keyword -->
            <div>
                <label class="block text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider mb-1.5">Cari Nama / NISN</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NISN..." class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-[#1C2434] dark:text-white text-xs rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-[#3C50E0]">
            </div>

            <!-- Action Filter Buttons -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition shadow-xs">
                    Muat Siswa
                </button>
                <a href="{{ route('admin.students.bulk-edit') }}" class="px-4 py-2.5 bg-[#F1F5F9] dark:bg-[#1A222C] hover:bg-slate-200 dark:hover:bg-[#2E3A47] text-[#1C2434] dark:text-white text-xs font-bold rounded-xl transition border border-[#E2E8F0] dark:border-[#2E3A47]">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Quick Batch Setter Banner (Terapkan Masal Cepat) -->
    <div class="p-5 rounded-2xl bg-indigo-50/80 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 space-y-4">
        <div class="flex items-center space-x-2 text-indigo-900 dark:text-indigo-200 font-extrabold text-xs uppercase tracking-wider">
            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <span>Aksi Pengaturan Masal Cepat (Batch Quick Set)</span>
        </div>
        <div class="grid grid-cols-1 {{ $isVocational ? 'sm:grid-cols-3' : 'sm:grid-cols-2' }} gap-4 items-end">
            @if($isVocational)
            <!-- Input 1: Jurusan Tujuan (Tampil Pertama) -->
            <div>
                <label class="block text-[11px] font-bold text-indigo-900 dark:text-indigo-200 mb-1">1. Set Jurusan Tujuan</label>
                <select x-model="targetMajor" @change="onTargetMajorChange()" class="w-full bg-white dark:bg-[#1A222C] border border-indigo-200 dark:border-indigo-800 text-[#1C2434] dark:text-white text-xs rounded-xl p-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-semibold">
                    <option value="">-- Pilih Jurusan Tujuan --</option>
                    @foreach($majors as $m)
                        <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->code }})</option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Input 2: Kelas Tujuan (Tampil Kedua, Filtered by Jurusan) -->
            <div>
                <label class="block text-[11px] font-bold text-indigo-900 dark:text-indigo-200 mb-1">
                    {{ $isVocational ? '2. Set Kelas Tujuan' : 'Set Kelas Tujuan' }}
                </label>
                <select x-model="targetClass" 
                        @change="onTargetClassChange()"
                        :disabled="{{ $isVocational ? 'true' : 'false' }} && !targetMajor"
                        class="w-full bg-white dark:bg-[#1A222C] border border-indigo-200 dark:border-indigo-800 text-[#1C2434] dark:text-white text-xs rounded-xl p-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-semibold disabled:opacity-60 disabled:bg-slate-100 dark:disabled:bg-slate-800">
                    <template x-if="{{ $isVocational ? 'true' : 'false' }} && !targetMajor">
                        <option value="">-- Pilih Jurusan Terlebih Dahulu --</option>
                    </template>
                    <template x-if="!{{ $isVocational ? 'true' : 'false' }} || targetMajor">
                        <option value="">-- Pilih Kelas Tujuan --</option>
                    </template>
                    <template x-for="c in filteredTargetClasses" :key="c.id">
                        <option :value="c.id" x-text="c.name" :selected="c.id == targetClass"></option>
                    </template>
                </select>
            </div>

            <div>
                <button type="button" @click="applyBatch()" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-md shadow-indigo-600/30 flex items-center justify-center space-x-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Terapkan ke Siswa Terpilih</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Mass Edit Form Table -->
    <form id="bulkUpdateForm" method="POST" action="{{ route('admin.students.bulk-update') }}">
        @csrf
        
        <div class="tailadmin-card overflow-hidden">
            <div class="px-6 py-4 border-b border-[#E2E8F0] dark:border-[#2E3A47] flex items-center justify-between bg-slate-50/50 dark:bg-[#1A222C]/40">
                <div class="flex items-center space-x-3">
                    <h3 class="font-extrabold text-[#1C2434] dark:text-white text-base tracking-tight">Daftar Siswa untuk Diedit</h3>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300">
                        {{ $students->total() }} Total Data (Menampilkan {{ $students->firstItem() ?? 0 }} - {{ $students->lastItem() ?? 0 }})
                    </span>
                </div>
                <div class="text-xs text-[#64748B] dark:text-[#8A99AD] font-semibold" x-text="selected.length + ' dari ' + {{ $students->count() }} + ' siswa dicentang'"></div>
            </div>

            @if($students->isEmpty())
                <div class="p-12 text-center space-y-3">
                    <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <h4 class="text-base font-extrabold text-[#1C2434] dark:text-white">Tidak Ada Data Siswa Ditemukan</h4>
                    <p class="text-xs text-[#64748B] dark:text-[#8A99AD]">Silakan sesuaikan kriteria filter di atas untuk memuat data siswa.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-[#F1F5F9] dark:bg-[#1A222C] text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider font-bold border-b border-[#E2E8F0] dark:border-[#2E3A47]">
                            <tr>
                                <th class="px-4 py-3.5 w-10 text-center">
                                    <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()" class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 text-[#3C50E0] focus:ring-[#3C50E0] cursor-pointer" title="Pilih Semua">
                                </th>
                                <th class="px-6 py-3.5">No</th>
                                <th class="px-6 py-3.5">Profil Siswa</th>
                                <th class="px-6 py-3.5">NISN / NIK</th>
                                <th class="px-6 py-3.5 min-w-[200px]">Kelas (Ubah Masal)</th>
                                @if($isVocational)
                                    <th class="px-6 py-3.5 min-w-[200px]">Jurusan (Ubah Masal)</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#2E3A47]">
                            @foreach($students as $index => $student)
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-[#1A222C]/60 transition-colors">
                                    <td class="px-4 py-4 text-center">
                                        <input type="checkbox" name="selected_ids[]" value="{{ $student->id }}" x-model="selected" @change="updateSelectAll()" class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 text-[#3C50E0] focus:ring-[#3C50E0] cursor-pointer">
                                        <input type="hidden" name="students[{{ $index }}][id]" value="{{ $student->id }}">
                                    </td>
                                    <td class="px-6 py-4 font-bold text-[#64748B] dark:text-[#8A99AD]">
                                        {{ $students->firstItem() + $index }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-3">
                                            @if($student->photo)
                                                <img src="{{ \Illuminate\Support\Str::startsWith($student->photo, 'img/') ? asset($student->photo) : asset('img/students/' . $student->photo) }}" alt="{{ $student->name }}" class="w-9 h-9 rounded-full object-cover border border-slate-200 dark:border-slate-700">
                                            @else
                                                <div class="w-9 h-9 rounded-full bg-[#3C50E0]/10 text-[#3C50E0] flex items-center justify-center font-bold text-xs border border-[#3C50E0]/20">
                                                    {{ strtoupper(substr($student->name, 0, 2)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <p class="font-extrabold text-[#1C2434] dark:text-white">{{ $student->name }}</p>
                                                <p class="text-[11px] text-[#64748B] dark:text-[#8A99AD] mt-0.5">{{ $student->user?->email ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-mono text-[#1C2434] dark:text-white font-semibold">{{ $student->nisn ?? '-' }}</p>
                                        <p class="text-[11px] text-[#64748B] dark:text-[#8A99AD] font-mono">{{ $student->nik ?? '-' }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <select name="students[{{ $index }}][class_id]" 
                                                id="class_select_{{ $student->id }}" 
                                                onchange="
                                                    const selectedOpt = this.options[this.selectedIndex];
                                                    const majorId = selectedOpt.getAttribute('data-major-id');
                                                    if (majorId) {
                                                        const majorSelect = document.getElementById('major_select_{{ $student->id }}');
                                                        if (majorSelect) majorSelect.value = majorId;
                                                    }
                                                "
                                                class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-[#1C2434] dark:text-white text-xs rounded-xl p-2.5 focus:outline-none focus:border-[#3C50E0]">
                                            <option value="">-- Tanpa Kelas --</option>
                                            @foreach($classes as $c)
                                                <option value="{{ $c->id }}" data-major-id="{{ $c->major_id }}" {{ $student->class_id == $c->id ? 'selected' : '' }}>
                                                    {{ $c->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    @if($isVocational)
                                    <td class="px-6 py-4">
                                        <select name="students[{{ $index }}][major_id]" id="major_select_{{ $student->id }}" class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-[#1C2434] dark:text-white text-xs rounded-xl p-2.5 focus:outline-none focus:border-[#3C50E0]">
                                            <option value="">-- Tanpa Jurusan --</option>
                                            @foreach($majors as $m)
                                                <option value="{{ $m->id }}" {{ $student->major_id == $m->id ? 'selected' : '' }}>
                                                    {{ $m->name }} ({{ $m->code }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($students->hasPages())
                    <div class="px-6 py-4 border-t border-[#E2E8F0] dark:border-[#2E3A47]">
                        {{ $students->links() }}
                    </div>
                @endif

                <!-- Bottom Action Footer -->
                <div class="px-6 py-4 border-t border-[#E2E8F0] dark:border-[#2E3A47] bg-slate-50/50 dark:bg-[#1A222C]/40 flex items-center justify-between">
                    <a href="{{ route('admin.students.index') }}" class="px-4 py-2.5 bg-[#F1F5F9] dark:bg-[#1A222C] hover:bg-slate-200 dark:hover:bg-[#2E3A47] text-[#1C2434] dark:text-white text-xs font-bold rounded-xl transition border border-[#E2E8F0] dark:border-[#2E3A47]">
                        Kembali ke Direktori
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-[#3C50E0] hover:bg-[#3C50E0]/90 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-[#3C50E0]/30 flex items-center space-x-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Perubahan Masal</span>
                    </button>
                </div>
            @endif
        </div>
    </form>
</div>
@endsection

