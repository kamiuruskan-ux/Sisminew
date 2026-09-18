@extends('layouts.admin')

@section('title', 'Cetak Raport Siswa')
@section('page_title', 'Cetak Raport Siswa')

@section('content')
<div class="space-y-6" x-data="{
    selectAll: false,
    selectedStudents: [],
    toggleAll() {
        if (this.selectAll) {
            this.selectedStudents = Array.from(document.querySelectorAll('.student-checkbox')).map(cb => cb.value);
        } else {
            this.selectedStudents = [];
        }
    }
}">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-6 rounded-3xl text-white shadow-xl">
        <div class="space-y-1">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 rounded-full text-xs font-semibold text-indigo-200 backdrop-blur-md">
                <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>Modul Akademik & Raport Resmi</span>
            </div>
            <h2 class="text-2xl font-black tracking-tight">Cetak Raport Siswa</h2>
            <p class="text-xs text-slate-300 max-w-xl">Cetak lembar Raport Hasil Belajar Siswa secara individu maupun massal per kelas dalam format A4 siap cetak &amp; simpan sebagai PDF.</p>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            @if($selectedClass)
            <a href="{{ route('admin.raport.print', ['class_id' => encrypt_id($selectedClass->id), 'academic_year_id' => request('academic_year_id'), 'semester' => request('semester', 'Ganjil')]) }}"
               target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white rounded-2xl font-bold text-xs shadow-lg shadow-teal-500/20 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak 1 Kelas ({{ $selectedClass->name }})</span>
            </a>
            @endif
        </div>
    </div>

    {{-- Filter Panel (Real-Time Auto Submit) --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 shadow-sm border border-slate-200 dark:border-slate-800 space-y-4">
        <form action="{{ route('admin.raport.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
            <x-major-class-select 
                :selected-major="request('major_id')" 
                :selected-class="request('class_id')" 
                :is-filter="true" 
                layout="inline" 
                major-label="Jurusan" 
                class-label="Kelas" 
                select-class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition" 
                label-class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5" 
                :on-major-change="'$el.closest(\'form\').submit()'" 
                :on-class-change="'$el.closest(\'form\').submit()'" 
            />

            <div>
                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Tahun Ajaran</label>
                <select name="academic_year_id" @change="$el.closest('form').submit()"
                        class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition">
                    @foreach($academicYears as $ay)
                        <option value="{{ $ay->id }}" {{ (request('academic_year_id') == $ay->id || (!request()->filled('academic_year_id') && $ay->is_active)) ? 'selected' : '' }}>
                            {{ $ay->name }} {{ $ay->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Semester</label>
                <select name="semester" @change="$el.closest('form').submit()"
                        class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition">
                    <option value="Ganjil" {{ request('semester', 'Ganjil') == 'Ganjil' ? 'selected' : '' }}>Semester Ganjil</option>
                    <option value="Genap" {{ request('semester') == 'Genap' ? 'selected' : '' }}>Semester Genap</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Pencarian Siswa</label>
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari NISN / Nama Siswa..."
                           @input.debounce.400ms="$el.closest('form').submit()"
                           x-init="if ('{{ request('search') }}') { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length); }"
                           class="w-full px-3.5 py-2.5 pr-8 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition">
                    @if(request('search'))
                        <a href="{{ route('admin.raport.index', request()->except('search')) }}" class="absolute right-3 top-2.5 text-slate-400 hover:text-rose-500 font-bold text-sm" title="Hapus Pencarian">&times;</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Table Area --}}
    <form action="{{ route('admin.raport.print-bulk') }}" method="POST" target="_blank">
        @csrf
        <input type="hidden" name="academic_year_id" value="{{ request('academic_year_id') }}">
        <input type="hidden" name="semester" value="{{ request('semester', 'Ganjil') }}">

        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <input type="checkbox" x-model="selectAll" @change="toggleAll()" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-200">
                        Pilih Semua (<span x-text="selectedStudents.length"></span> Terpilih)
                    </span>
                </div>

                <div>
                    <button type="submit" :disabled="selectedStudents.length === 0"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-xl font-bold text-xs shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Terpilih (<span x-text="selectedStudents.length"></span>)</span>
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100/70 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 font-extrabold uppercase tracking-wider text-[10px] border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-5 py-3.5 w-10 text-center">#</th>
                            <th class="px-5 py-3.5">NISN / Siswa</th>
                            <th class="px-5 py-3.5">{{ \App\Models\Setting::get('is_vocational', '1') == '1' ? 'Kelas & Jurusan' : 'Kelas' }}</th>
                            <th class="px-5 py-3.5 text-center">Tercatat Mapel</th>
                            <th class="px-5 py-3.5 text-center">Rata-rata Nilai</th>
                            <th class="px-5 py-3.5 text-right">Aksi Raport</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800 font-medium text-slate-700 dark:text-slate-300">
                        @forelse($students as $index => $student)
                        @php
                            $studentGradesCount = $student->grades->groupBy('subject')->count();
                            $avgScore = round($student->grades->avg('score') ?? 0, 1);
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                            <td class="px-5 py-4 text-center">
                                <input type="checkbox" name="student_ids[]" value="{{ encrypt_id($student->id) }}" x-model="selectedStudents"
                                       class="student-checkbox w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-indigo-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center font-bold text-indigo-600 dark:text-indigo-400 overflow-hidden shrink-0">
                                        @if($student->photo_url)
                                            <img src="{{ $student->photo_url }}" class="w-full h-full object-cover" alt="Foto">
                                        @else
                                            {{ strtoupper(substr($student->user->name ?? 'S', 0, 1)) }}
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-slate-900 dark:text-white">{{ $student->user->name ?? $student->name }}</p>
                                        <p class="text-[11px] text-slate-400 font-medium">
                                            @if($student->nisn && $student->nis)
                                                NISN: {{ $student->nisn }} | NIS: {{ $student->nis }}
                                            @elseif($student->nisn)
                                                NISN: {{ $student->nisn }}
                                            @elseif($student->nis)
                                                NIS: {{ $student->nis }}
                                            @else
                                                NISN: -
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 font-extrabold text-[11px]">
                                    {{ $student->class->name ?? '-' }}
                                </span>
                                @if($student->major && \App\Models\Setting::get('is_vocational', '1') == '1')
                                <p class="text-[11px] text-slate-400 mt-0.5">{{ $student->major->name }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $studentGradesCount }} Mapel</span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-lg font-black text-xs {{ $avgScore >= 80 ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' : ($avgScore >= 70 ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300') }}">
                                    {{ $avgScore }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right space-x-1.5">
                                <a href="{{ route('admin.raport.print', ['student_id' => encrypt_id($student->id), 'academic_year_id' => request('academic_year_id'), 'semester' => request('semester', 'Ganjil')]) }}"
                                   target="_blank"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/60 dark:hover:bg-indigo-900 text-indigo-700 dark:text-indigo-300 rounded-xl font-bold text-xs transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Cetak Raport</span>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto text-slate-300 dark:text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="font-extrabold text-slate-600 dark:text-slate-400 text-sm">Tidak ada data siswa ditemukan.</p>
                                <p class="text-xs text-slate-400 mt-1">Silakan sesuaikan filter kelas atau kata kunci pencarian Anda.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($students->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800">
                {{ $students->links() }}
            </div>
            @endif
        </div>
    </form>
</div>
@endsection
