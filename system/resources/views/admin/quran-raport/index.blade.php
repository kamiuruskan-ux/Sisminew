@extends('layouts.admin')

@section('title', "E-Raport Khusus Pembelajaran Al-Qur'an")
@section('page_title', "Cetak E-Raport Al-Qur'an")

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
    {{-- Header Banner --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gradient-to-r from-emerald-900 via-teal-950 to-slate-900 p-6 rounded-3xl text-white shadow-xl">
        <div class="space-y-1">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 rounded-full text-xs font-semibold text-emerald-200 backdrop-blur-md">
                <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>Modul E-Raport Al-Qur'an Mandiri &amp; Resmi</span>
            </div>
            <h2 class="text-2xl font-black tracking-tight">Cetak Lembar Raport Al-Qur'an Santri</h2>
            <p class="text-xs text-emerald-100 max-w-xl">Cetak lembar Raport Khusus Capaian Tahsin &amp; Tahfidz Al-Qur'an santri secara individu atau massal per kelas dalam format A4 siap cetak &amp; simpan PDF.</p>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            @if($selectedClass)
            <a href="{{ route('admin.quran-raport.print', ['class_id' => encrypt_id($selectedClass->id), 'academic_year_id' => request('academic_year_id'), 'semester' => request('semester', 'Ganjil')]) }}"
               target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white rounded-2xl font-bold text-xs shadow-lg shadow-teal-500/20 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak 1 Kelas ({{ $selectedClass->name }})</span>
            </a>
            @endif
        </div>
    </div>

    {{-- Filter Panel --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 shadow-xs border border-slate-200 dark:border-slate-800 space-y-4">
        <form action="{{ route('admin.quran-raport.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
            <div>
                <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Pilih Kelas</label>
                <select name="class_id" onchange="this.form.submit()"
                        class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white rounded-xl text-xs font-semibold">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->students_count }} Santri)
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Tahun Ajaran</label>
                <select name="academic_year_id" onchange="this.form.submit()"
                        class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white rounded-xl text-xs font-semibold">
                    @foreach($academicYears as $ay)
                        <option value="{{ $ay->id }}" {{ (request('academic_year_id') == $ay->id || (!request('academic_year_id') && $activeAcademicYear && $activeAcademicYear->id == $ay->id)) ? 'selected' : '' }}>
                            {{ $ay->name ?? ($ay->start_year . '/' . $ay->end_year) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Semester</label>
                <select name="semester" onchange="this.form.submit()"
                        class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white rounded-xl text-xs font-semibold">
                    <option value="Ganjil" {{ request('semester') == 'Ganjil' ? 'selected' : '' }}>Semester Ganjil</option>
                    <option value="Genap" {{ request('semester') == 'Genap' ? 'selected' : '' }}>Semester Genap</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Cari Santri</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama atau NISN..."
                       class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white rounded-xl text-xs font-semibold">
            </div>
        </form>
    </div>

    {{-- Tabel Santri untuk Cetak Raport --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <span class="text-xs font-extrabold text-slate-500 uppercase">Daftar Santri Siap Cetak Raport Al-Qur'an</span>
            <span class="text-xs text-slate-400">Total {{ $students->total() }} Santri</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40">
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">NISN / NIS</th>
                        <th class="py-3 px-4">Nama Santri</th>
                        <th class="py-3 px-4">Kelas</th>
                        <th class="py-3 px-4 text-center">Riwayat Setoran</th>
                        <th class="py-3 px-4 text-right">Aksi Lembar Cetak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($students as $idx => $st)
                    @php
                        $setoranCount = $st->halaqahRecords->count();
                    @endphp
                    <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4 text-slate-400 font-bold">{{ $students->firstItem() + $idx }}</td>
                        <td class="py-3 px-4 font-bold text-slate-600 dark:text-slate-300">{{ $st->nisn ?? $st->nis ?? '-' }}</td>
                        <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">{{ $st->user?->name ?? 'Santri' }}</td>
                        <td class="py-3 px-4">{{ $st->class->name ?? '-' }}</td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold {{ $setoranCount > 0 ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-400' }}">
                                {{ $setoranCount }} Riwayat
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right">
                            <a href="{{ route('admin.quran-raport.print', ['student_id' => encrypt_id($st->id), 'academic_year_id' => request('academic_year_id'), 'semester' => request('semester', 'Ganjil')]) }}"
                               target="_blank"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-xs transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                <span>Cetak Raport</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400">
                            Tidak ada data santri yang cocok dengan filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $students->links() }}
        </div>
    </div>
</div>
@endsection
