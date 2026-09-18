@extends('layouts.admin')

@section('title', 'Nilai Siswa')
@section('page_title', 'Nilai Siswa')

@section('content')
<div class="space-y-5" x-data="{
    showDeleteModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    confirmDelete(id, name) {
        this.deleteTarget = { id: id, name: name };
        this.deleteFormAction = '{{ url('admin/grades') }}/' + id;
        this.showDeleteModal = true;
    },
    // Searchable Student Picker State
    studentId: '{{ request('student_id', '') }}',
    selectedClassId: '{{ request('class_id', '') }}',
    searchOpen: false,
    studentSearch: '',
    studentsList: {{ json_encode($students->map(fn($s) => [
        'id' => $s->id,
        'name' => $s->user->name ?? $s->nisn ?? 'Siswa',
        'nisn' => $s->nisn ?? '-',
        'class_id' => $s->class_id ?? null,
        'class_name' => $s->class->name ?? 'Tanpa Kelas'
    ])) }},
    get selectedStudentName() {
        if (!this.studentId) return 'Semua Siswa';
        const found = this.studentsList.find(s => s.id == this.studentId);
        return found ? found.name + ' (NISN: ' + (found.nisn || '-') + ' • Kelas ' + found.class_name + ')' : 'Semua Siswa';
    },
    get filteredStudents() {
        let list = this.studentsList;
        if (this.selectedClassId) {
            list = list.filter(s => s.class_id == this.selectedClassId);
        }
        if (!this.studentSearch) return list;
        const q = this.studentSearch.toLowerCase();
        return list.filter(s => 
            s.name.toLowerCase().includes(q) || 
            (s.nisn && s.nisn.toLowerCase().includes(q)) ||
            (s.class_name && s.class_name.toLowerCase().includes(q))
        );
    }
}">
    @include('components.delete-modal', ['title' => 'Hapus Nilai Siswa', 'message' => 'Apakah Anda yakin ingin menghapus nilai :name ini? Tindakan ini tidak dapat dibatalkan.'])


    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Nilai Siswa</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola nilai dan hasil belajar siswa</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.raport.index', request()->all()) }}"
               class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-xs shadow-sm transition">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Raport</span>
            </a>
            <button onclick="openModal('importExcelModal')"
                    class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 rounded-xl font-bold text-xs hover:bg-emerald-100 transition cursor-pointer">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Import Excel</span>
            </button>
            <a href="{{ route('admin.grades.export.excel', request()->all()) }}"
               class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 rounded-xl font-bold text-xs hover:bg-blue-100 transition">
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <span>Export Excel</span>
            </a>
            <a href="{{ route('admin.grades.bulk.create') }}"
               class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 rounded-xl font-semibold text-xs hover:bg-slate-50 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                Input Massal
            </a>
            <button onclick="openModal('createModal')"
                    class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-gradient-to-r from-primary to-secondary text-white rounded-xl font-semibold text-xs hover:brightness-110 shadow-sm transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Nilai
            </button>
        </div>
    </div>

    {{-- Filters (Real-Time Auto Submit) --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 sm:p-5 shadow-xs border border-slate-200 dark:border-slate-800">
        <form action="{{ route('admin.grades.index') }}" method="GET"
              class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 items-end">
            <x-major-class-select 
                :selected-major="request('major_id')" 
                :selected-class="request('class_id')" 
                :is-filter="true" 
                layout="inline" 
                major-label="Jurusan" 
                class-label="Kelas" 
                select-class="w-full h-10 px-3.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none transition shadow-2xs" 
                label-class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5" 
                :on-major-change="'$el.closest(\'form\').submit()'"
                :on-class-change="'$el.closest(\'form\').submit()'"
            />
            <!-- Searchable Student Picker (Matches BK Violation Create) -->
            <div class="relative">
                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Siswa</label>
                <input type="hidden" name="student_id" x-model="studentId">

                <button type="button" @click="searchOpen = !searchOpen" 
                        class="w-full h-10 px-3.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold flex items-center justify-between focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none transition shadow-2xs text-left">
                    <span x-text="selectedStudentName" class="truncate"></span>
                    <svg class="w-4 h-4 text-slate-400 shrink-0 ml-2 transition-transform duration-200" :class="{ 'rotate-180': searchOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <!-- Dropdown Search Panel -->
                <div x-show="searchOpen" @click.outside="searchOpen = false" x-cloak
                     class="absolute z-50 mt-1 w-full min-w-[280px] bg-white dark:bg-[#24303F] border border-slate-200 dark:border-[#2E3A47] rounded-2xl shadow-2xl p-2.5 space-y-2 max-h-72 flex flex-col">
                    <div class="relative">
                        <input type="text" x-model="studentSearch" placeholder="Cari nama siswa, NISN, atau kelas..." 
                               class="w-full bg-slate-50 dark:bg-[#1A222C] border border-slate-200 dark:border-[#2E3A47] text-xs rounded-xl p-2.5 pl-9 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    <div class="overflow-y-auto space-y-1 flex-1 pr-1">
                        <button type="button" @click="studentId = ''; searchOpen = false; $nextTick(() => $el.closest('form').submit())" 
                                :class="!studentId ? 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-bold border border-indigo-500/30' : 'hover:bg-slate-100 dark:hover:bg-[#1A222C] text-slate-800 dark:text-white font-medium'"
                                class="w-full text-left p-2.5 rounded-xl text-xs flex items-center justify-between transition">
                            <span>Semua Siswa</span>
                        </button>
                        <template x-for="s in filteredStudents" :key="s.id">
                            <button type="button" @click="studentId = s.id; searchOpen = false; $nextTick(() => $el.closest('form').submit())" 
                                    :class="studentId == s.id ? 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-bold border border-indigo-500/30' : 'hover:bg-slate-100 dark:hover:bg-[#1A222C] text-slate-800 dark:text-white font-medium'"
                                    class="w-full text-left p-2.5 rounded-xl text-xs flex items-center justify-between transition">
                                <div>
                                    <p class="font-bold" x-text="s.name"></p>
                                    <p class="text-[10px] text-slate-400" x-text="'NISN: ' + (s.nisn || '-')"></p>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 shrink-0" x-text="s.class_name"></span>
                            </button>
                        </template>
                        <div x-show="filteredStudents.length === 0" class="p-4 text-center text-xs text-slate-400 font-semibold">
                            Tidak ada siswa yang sesuai kata kunci pencarian.
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Mata Pelajaran</label>
                @php
                    $subjList = (isset($subjects) && count($subjects) > 0) ? $subjects : ($globalSubjects ?? []);
                @endphp
                <select name="subject" @change="$el.closest('form').submit()" 
                        class="w-full h-10 px-3.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none transition shadow-2xs">
                    <option value="">Semua Mapel</option>
                    @foreach($subjList as $subject)
                        @php $sName = is_object($subject) ? $subject->name : $subject; @endphp
                        <option value="{{ $sName }}" {{ request('subject') == $sName ? 'selected' : '' }}>{{ $sName }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Tipe Penilaian</label>
                <select name="type" @change="$el.closest('form').submit()" 
                        class="w-full h-10 px-3.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none transition shadow-2xs">
                    <option value="">Semua Tipe</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                            @if($type === 'daily') Harian
                            @elseif($type === 'mid_term') UTS
                            @elseif($type === 'final_term') UAS
                            @else Ujian @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    {{-- Tabel: desktop --}}
    <div class="hidden sm:block bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3 text-left font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Siswa</th>
                        <th class="px-4 py-3 text-left font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kelas</th>
                        <th class="px-4 py-3 text-left font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Mapel</th>
                        <th class="px-4 py-3 text-left font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tipe</th>
                        <th class="px-4 py-3 text-left font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nilai</th>
                        <th class="px-4 py-3 text-right font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($grades as $grade)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400 whitespace-nowrap">{{ $grade->created_at->translatedFormat('d M Y') }}</td>
                            <td class="px-4 py-3">
                                <p class="font-bold text-slate-900 dark:text-white">{{ $grade->student->user->name ?? '-' }}</p>
                                <p class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $grade->student->nisn ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-950 text-blue-800 dark:text-blue-300 text-[10px] font-extrabold rounded-md">{{ $grade->class->name ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-700 dark:text-slate-300 font-medium">{{ $grade->subject }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $typeBadge = match($grade->type) {
                                        'daily'      => 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300',
                                        'mid_term'   => 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300',
                                        'final_term' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300',
                                        default      => 'bg-orange-100 text-orange-800 dark:bg-orange-950 dark:text-orange-300',
                                    };
                                    $typeLabel = match($grade->type) {
                                        'daily'      => 'Harian',
                                        'mid_term'   => 'UTS',
                                        'final_term' => 'UAS',
                                        default      => 'Ujian',
                                    };
                                @endphp
                                <span class="px-2 py-0.5 text-[10px] font-extrabold rounded-md {{ $typeBadge }}">{{ $typeLabel }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-base font-black {{ $grade->score >= 75 ? 'text-emerald-600 dark:text-emerald-400' : ($grade->score >= 60 ? 'text-amber-600 dark:text-amber-400' : 'text-rose-600 dark:text-rose-400') }}">
                                    {{ $grade->score }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button onclick="openEditModal({{ json_encode($grade) }})"
                                            class="p-1.5 text-slate-400 hover:bg-indigo-600 hover:text-white rounded-lg transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button type="button" @click="confirmDelete({{ $grade->id }}, '{{ addslashes(($grade->student->user->name ?? 'Siswa') . ' - ' . $grade->subject) }}')" class="p-1.5 text-slate-400 hover:bg-rose-600 hover:text-white rounded-lg transition cursor-pointer" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg class="w-12 h-12 mx-auto text-slate-200 dark:text-slate-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                <p class="text-slate-400 text-sm font-medium">Belum ada data nilai</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Card list: mobile --}}
    <div class="sm:hidden space-y-2.5">
        @forelse($grades as $grade)
            @php
                $scoreColor = $grade->score >= 75 ? 'text-emerald-600 dark:text-emerald-400' : ($grade->score >= 60 ? 'text-amber-600 dark:text-amber-400' : 'text-rose-600 dark:text-rose-400');
                $typeLabel  = match($grade->type) { 'daily' => 'Harian', 'mid_term' => 'UTS', 'final_term' => 'UAS', default => 'Ujian' };
                $typeBadge  = match($grade->type) {
                    'daily'      => 'bg-blue-100 text-blue-800',
                    'mid_term'   => 'bg-purple-100 text-purple-800',
                    'final_term' => 'bg-indigo-100 text-indigo-800',
                    default      => 'bg-orange-100 text-orange-800',
                };
            @endphp
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 space-y-3 shadow-xs">
                {{-- Top row: nama + nilai --}}
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-black text-slate-900 dark:text-white text-sm truncate">{{ $grade->student->user->name ?? '-' }}</p>
                        <p class="text-[10px] font-mono text-slate-400 mt-0.5">{{ $grade->student->nisn ?? '-' }}</p>
                    </div>
                    <span class="text-2xl font-black {{ $scoreColor }} shrink-0">{{ $grade->score }}</span>
                </div>
                {{-- Badges row --}}
                <div class="flex flex-wrap items-center gap-1.5">
                    <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-950 text-blue-800 dark:text-blue-300 text-[10px] font-extrabold rounded-md">{{ $grade->class->name ?? '-' }}</span>
                    <span class="px-2 py-0.5 {{ $typeBadge }} text-[10px] font-extrabold rounded-md">{{ $typeLabel }}</span>
                    <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[10px] font-semibold rounded-md">{{ $grade->subject }}</span>
                </div>
                {{-- Bottom row: tanggal + aksi --}}
                <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800">
                    <span class="text-[10px] text-slate-400">{{ $grade->created_at->translatedFormat('d M Y') }}</span>
                    <div class="flex items-center gap-1.5">
                        <button onclick="openEditModal({{ json_encode($grade) }})"
                                class="p-1.5 text-slate-400 hover:bg-indigo-600 hover:text-white rounded-lg transition" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <button type="button" @click="confirmDelete({{ $grade->id }}, '{{ addslashes(($grade->student->user->name ?? 'Siswa') . ' - ' . $grade->subject) }}')" class="p-1.5 text-slate-400 hover:bg-rose-600 hover:text-white rounded-lg transition cursor-pointer" title="Hapus">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>

                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-8 text-center">
                <svg class="w-12 h-12 mx-auto text-slate-200 dark:text-slate-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <p class="text-slate-400 text-sm font-medium">Belum ada data nilai</p>
            </div>
        @endforelse
    </div>

    {{ $grades->links() }}
</div>

{{-- Create Modal --}}
<div id="createModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 overflow-y-auto" onclick="closeModal('createModal')">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-lg border border-slate-200 dark:border-slate-800" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center px-5 py-4 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-sm font-black text-slate-900 dark:text-white">Tambah Nilai</h3>
                <button onclick="closeModal('createModal')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('admin.grades.store') }}" method="POST" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Siswa *</label>
                    <select name="student_id" id="create_student_id" required class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-medium">
                        <option value="">Pilih Siswa</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->user->name }} - {{ $student->class->name ?? '-' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                        <x-major-class-select 
                            :majors="$majors" 
                            :classes="$classes" 
                            required-class="true" 
                            layout="contents" 
                            select-class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-medium" 
                            label-class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" 
                        />
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Mapel *</label>
                        @php
                            $subjList = (isset($subjects) && count($subjects) > 0) ? $subjects : ($globalSubjects ?? []);
                        @endphp
                        <select name="subject" required class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-medium">
                            <option value="">Pilih Mapel</option>
                            @foreach($subjList as $subject)
                                @php $sName = is_object($subject) ? $subject->name : $subject; @endphp
                                <option value="{{ $sName }}">{{ $sName }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tipe *</label>
                        <select name="type" required class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-medium">
                            <option value="">Pilih Tipe</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}">
                                    @if($type === 'daily') Harian @elseif($type === 'mid_term') UTS @elseif($type === 'final_term') UAS @else Ujian @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nilai (0–100) *</label>
                        <input type="number" name="score" min="0" max="100" step="0.01" required
                               class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-medium">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Catatan</label>
                    <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-medium resize-none"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="closeModal('createModal')" class="px-4 py-2 text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 rounded-xl text-xs font-bold hover:bg-slate-200 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-primary to-secondary text-white rounded-xl text-xs font-bold shadow-sm hover:brightness-110 transition">Simpan Nilai</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div id="editModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 overflow-y-auto" onclick="closeModal('editModal')">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-lg border border-slate-200 dark:border-slate-800" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center px-5 py-4 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-sm font-black text-slate-900 dark:text-white">Edit Nilai</h3>
                <button onclick="closeModal('editModal')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="editForm" method="POST" class="p-5 space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Siswa *</label>
                    <select name="student_id" id="edit_student_id" required class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-medium">
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kelas *</label>
                        <select name="class_id" id="edit_class_id" required class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-medium"></select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Mapel *</label>
                        <select name="subject" id="edit_subject" required class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-medium"></select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tipe *</label>
                        <select name="type" id="edit_type" required class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-medium">
                            @foreach($types as $type)
                                <option value="{{ $type }}">
                                    @if($type === 'daily') Harian @elseif($type === 'mid_term') UTS @elseif($type === 'final_term') UAS @else Ujian @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nilai *</label>
                        <input type="number" name="score" id="edit_score" min="0" max="100" step="0.01" required
                               class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-medium">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Catatan</label>
                    <textarea name="notes" id="edit_notes" rows="2" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-medium resize-none"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="closeModal('editModal')" class="px-4 py-2 text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 rounded-xl text-xs font-bold hover:bg-slate-200 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-primary to-secondary text-white rounded-xl text-xs font-bold shadow-sm hover:brightness-110 transition">Update Nilai</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Import Excel Modal --}}
<div id="importExcelModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 overflow-y-auto"
     x-data="{
         isUploading: false,
         statusText: 'Membaca file Excel...',
         statusIndex: 0,
         statusList: [
             'Membaca berkas Excel...',
             'Memvalidasi data NISN & Nama Siswa...',
             'Mencocokkan Kelas & Mata Pelajaran...',
             'Menyimpan record nilai ke basis data...',
             'Hampir selesai, menyinkronkan data...'
         ],
         startImportLoading() {
             this.isUploading = true;
             setInterval(() => {
                 this.statusIndex = (this.statusIndex + 1) % this.statusList.length;
                 this.statusText = this.statusList[this.statusIndex];
             }, 1200);
         }
     }"
     onclick="closeModal('importExcelModal')">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-md border border-slate-200 dark:border-slate-800 overflow-hidden" onclick="event.stopPropagation()">
            
            {{-- Header --}}
            <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900 dark:text-white">Import Nilai Siswa (Excel)</h3>
                        <p class="text-[10px] text-slate-400 font-medium">Unggah berkas spreadsheet format .xlsx</p>
                    </div>
                </div>
                <button x-show="!isUploading" onclick="closeModal('importExcelModal')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Form Upload (Hidden during upload) --}}
            <form x-show="!isUploading" action="{{ route('admin.grades.import.excel') }}" method="POST" enctype="multipart/form-data" @submit="startImportLoading()" class="p-6 space-y-4">
                @csrf
                <div class="p-3.5 bg-emerald-50/80 dark:bg-emerald-950/40 rounded-2xl border border-emerald-200/80 dark:border-emerald-800/80 text-xs space-y-2 text-emerald-900 dark:text-emerald-300">
                    <p class="font-bold flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Petunjuk Format File Excel (.xlsx / .xls):</span>
                    </p>
                    <p class="text-[11px] text-slate-600 dark:text-slate-300 leading-relaxed">
                        File Excel wajib memiliki kolom di baris 1: <strong>NISN, Nama Siswa, Kelas, Mata Pelajaran, Tipe Nilai, Nilai, Catatan</strong>.
                    </p>
                    <div class="pt-1 border-t border-emerald-200/60 dark:border-emerald-800/60">
                        <a href="{{ route('admin.grades.download.template') }}" class="inline-flex items-center gap-1.5 text-[11px] font-extrabold text-indigo-600 dark:text-indigo-400 hover:underline">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Download Template Excel (.xlsx)</span>
                        </a>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Pilih Berkas Excel (.xlsx / .xls)</label>
                    <x-file-upload name="file" accept=".xlsx,.xls,.csv" required="true" label="Upload Berkas Excel Nilai" help="Seret & lepas berkas Excel (.xlsx / .xls) di sini" />
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="closeModal('importExcelModal')" class="px-4 py-2.5 text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 rounded-xl text-xs font-bold hover:bg-slate-200 transition cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black shadow-md transition flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Proses Import Excel</span>
                    </button>
                </div>
            </form>

            {{-- ANIMASI LOADING & RUNNING TEXT --}}
            <div x-show="isUploading" x-cloak class="p-8 text-center space-y-6">
                {{-- Spinner Ring & Icon Animation --}}
                <div class="relative w-20 h-20 mx-auto flex items-center justify-center">
                    <div class="absolute inset-0 rounded-full border-4 border-emerald-100 dark:border-emerald-950 border-t-emerald-600 animate-spin"></div>
                    <div class="w-12 h-12 rounded-full bg-emerald-50 dark:bg-emerald-950/80 text-emerald-600 flex items-center justify-center animate-pulse">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </div>
                </div>

                {{-- Text Running / Ticker Animation --}}
                <div class="space-y-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 text-[10px] font-black uppercase tracking-wider rounded-full border border-emerald-200 dark:border-emerald-800">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        Sedang Memproses Excel...
                    </span>
                    <h4 class="text-sm font-black text-slate-900 dark:text-white h-6 transition-all duration-300" x-text="statusText"></h4>
                    <p class="text-xs text-slate-400 font-medium">Mohon tidak menutup halaman ini selama proses impor berlangsung.</p>
                </div>

                {{-- Animated Progress Bar --}}
                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 overflow-hidden relative">
                    <div class="bg-gradient-to-r from-emerald-500 via-teal-500 to-indigo-600 h-2 rounded-full w-full animate-pulse"></div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); document.body.style.overflow = ''; }
function openEditModal(data) {
    document.getElementById('editForm').action = '{{ url('admin/grades') }}/' + data.id;
    document.getElementById('edit_student_id').value = data.student_id;

    const classSelect = document.getElementById('edit_class_id');
    classSelect.innerHTML = '';
    @foreach($classes as $class)
    classSelect.add(new Option('{{ $class->name }}', '{{ $class->id }}'));
    @endforeach
    classSelect.value = data.class_id;

    const subjectSelect = document.getElementById('edit_subject');
    subjectSelect.innerHTML = '';
    @php
        $subjList = (isset($subjects) && count($subjects) > 0) ? $subjects : ($globalSubjects ?? []);
    @endphp
    @foreach($subjList as $subject)
    @php $sName = is_object($subject) ? $subject->name : $subject; @endphp
    subjectSelect.add(new Option('{{ $sName }}', '{{ $sName }}'));
    @endforeach
    subjectSelect.value = data.subject;

    document.getElementById('edit_type').value = data.type;
    document.getElementById('edit_score').value = data.score;
    document.getElementById('edit_notes').value = data.notes || '';
    openModal('editModal');
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { closeModal('createModal'); closeModal('editModal'); closeModal('importExcelModal'); }
});

document.addEventListener('DOMContentLoaded', function() {
    if (typeof TomSelect !== 'undefined') {
        new TomSelect('#filter_student_id', { placeholder: 'Cari siswa...', allowEmptyOption: true, create: false });
        new TomSelect('#filter_class_id',   { placeholder: 'Cari kelas...', allowEmptyOption: true, create: false });
        new TomSelect('#create_student_id', { placeholder: 'Pilih siswa...', create: false });
        new TomSelect('#edit_student_id',   { placeholder: 'Pilih siswa...', create: false });
        new TomSelect('#edit_class_id',     { placeholder: 'Pilih kelas...', create: false });
        new TomSelect('#edit_subject',      { placeholder: 'Pilih mapel...', create: false });
    }
});
</script>
@endsection
