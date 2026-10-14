@extends('layouts.admin')

@section('title', 'Penempatan Mengajar Guru Bidang Studi')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-1">
                <span>Akademik & Pembelajaran</span>
                <span>/</span>
                <span>Bidang Studi</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Penempatan Guru Bidang Studi</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Atur penugasan mengajar guru berdasarkan kelas dan mata pelajaran pada tahun ajaran aktif. Guru hanya dapat menginput nilai siswa sesuai penugasan ini.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.grades.index') }}" class="btn-secondary px-4 py-2.5 text-xs rounded-xl flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Agenda & Nilai
            </a>
            <button type="button" onclick="document.getElementById('modalTambahPenempatan').classList.remove('hidden')" class="btn-primary px-4 py-2.5 text-xs rounded-xl flex items-center gap-2 shadow-sm shadow-blue-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Penempatan Guru
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-xs">
        <form method="GET" action="{{ route('admin.grades.assignments') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider mb-1.5">Tahun Ajaran</label>
                <select name="academic_year_id" onchange="this.form.submit()" class="w-full text-xs rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 p-2.5">
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                            {{ $year->name }} {{ $year->semester ? '(Semester ' . $year->semester . ')' : '' }} {{ $year->is_active ? '★ (Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider mb-1.5">Pilih Guru</label>
                <select name="teacher_id" onchange="this.form.submit()" class="w-full text-xs rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 p-2.5">
                    <option value="">-- Semua Guru --</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider mb-1.5">Pilih Kelas</label>
                <select name="class_id" onchange="this.form.submit()" class="w-full text-xs rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 p-2.5">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider mb-1.5">Mata Pelajaran</label>
                <select name="subject" onchange="this.form.submit()" class="w-full text-xs rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500 p-2.5">
                    <option value="">-- Semua Mata Pelajaran --</option>
                    @foreach($subjects as $sub)
                        <option value="{{ $sub }}" {{ request('subject') == $sub ? 'selected' : '' }}>
                            {{ $sub }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Table Assignments -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Daftar Penempatan Mengajar (Total: {{ $assignments->total() }})
            </h2>
            <div class="text-xs text-gray-500 dark:text-gray-400">
                Tahun Ajaran: <span class="font-bold text-blue-600 dark:text-blue-400">{{ $activeYear?->name ?? 'Default' }}</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700 text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wider">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Guru</th>
                        <th class="py-3 px-4">Mata Pelajaran</th>
                        <th class="py-3 px-4">Kelas yang Diajar</th>
                        <th class="py-3 px-4">Tahun Ajaran</th>
                        <th class="py-3 px-4">Catatan</th>
                        <th class="py-3 px-4 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                    @forelse($assignments as $idx => $item)
                        <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-750/50 transition-colors">
                            <td class="py-3 px-4 text-center text-gray-400 font-medium">
                                {{ $assignments->firstItem() + $idx }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 font-bold flex items-center justify-center shrink-0">
                                        {{ substr($item->teacher?->name ?? 'G', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white">
                                            {{ $item->teacher?->name ?? 'Guru Tidak Ditemukan' }}
                                        </div>
                                        <div class="text-[10px] text-gray-500 dark:text-gray-400">
                                            {{ $item->teacher?->email ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-1 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 font-semibold border border-blue-100 dark:border-blue-900/50">
                                    {{ $item->subject }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-bold text-gray-900 dark:text-gray-100">
                                <span class="px-2 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold border border-emerald-100 dark:border-emerald-900/50">
                                    {{ $item->classModel?->name ?? 'Kelas #' . $item->class_id }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-gray-600 dark:text-gray-300">
                                {{ $item->academicYear?->name ?? 'Semua Tahun' }}
                            </td>
                            <td class="py-3 px-4 text-gray-500 dark:text-gray-400 italic">
                                {{ $item->notes ?: '-' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <form action="{{ route('admin.grades.assignments.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus penempatan mengajar ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Hapus Penempatan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400 mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Belum ada penempatan mengajar yang dicatat</p>
                                    <p class="text-xs text-gray-400 mt-1">Klik tombol "Tambah Penempatan Guru" di atas untuk menambahkan penugasan mengajar.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($assignments->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                {{ $assignments->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Penempatan -->
<div id="modalTambahPenempatan" class="fixed inset-0 z-50 overflow-y-auto hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-xl w-full p-6 shadow-2xl border border-gray-100 dark:border-gray-700 animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-700">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Form Penempatan Mengajar Guru</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Tugaskan guru untuk mengajar mata pelajaran tertentu di satu atau banyak kelas.</p>
            </div>
            <button type="button" onclick="document.getElementById('modalTambahPenempatan').classList.add('hidden')" class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('admin.grades.assignments.store') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">Tahun Ajaran <span class="text-red-500">*</span></label>
                <select name="academic_year_id" class="w-full text-xs rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 p-2.5" required>
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" {{ ($selectedYearId == $year->id || $year->is_active) ? 'selected' : '' }}>
                            {{ $year->name }} {{ $year->is_active ? '★ (Aktif Saat Ini)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">Pilih Guru Pengampu <span class="text-red-500">*</span></label>
                <select name="teacher_id" class="w-full text-xs rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 p-2.5" required>
                    <option value="">-- Pilih Guru --</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->name }} ({{ $teacher->email }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">Mata Pelajaran <span class="text-red-500">*</span></label>
                <select name="subject" class="w-full text-xs rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 p-2.5" required>
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    @foreach($subjects as $sub)
                        <option value="{{ $sub }}">{{ $sub }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">Pilih Kelas Penempatan (Bisa Pilih Banyak) <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 p-3 bg-gray-50 dark:bg-gray-900/60 rounded-xl border border-gray-100 dark:border-gray-700 max-h-48 overflow-y-auto">
                    @foreach($classes as $c)
                        <label class="flex items-center gap-2 text-xs text-gray-700 dark:text-gray-300 cursor-pointer p-1.5 hover:bg-white dark:hover:bg-gray-800 rounded-lg">
                            <input type="checkbox" name="class_ids[]" value="{{ $c->id }}" class="rounded text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span>{{ $c->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">Catatan (Opsional)</label>
                <input type="text" name="notes" placeholder="Contoh: Mengajar kurikulum Merdeka / pengganti sementara" class="w-full text-xs rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 p-2.5">
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                <button type="button" onclick="document.getElementById('modalTambahPenempatan').classList.add('hidden')" class="btn-secondary px-4 py-2 text-xs rounded-xl">Batal</button>
                <button type="submit" class="btn-primary px-5 py-2 text-xs rounded-xl">Simpan Penempatan</button>
            </div>
        </form>
    </div>
</div>
@endsection
