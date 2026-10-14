@extends('layouts.admin')

@section('title', 'Arsip Riwayat Kenaikan Kelas')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.class-promotions.index') }}" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1">
                    &larr; Kembali ke Form Kenaikan
                </a>
            </div>
            <h1 class="text-2xl font-black text-slate-800 dark:text-white">Arsip Riwayat Kenaikan & Mutasi Kelas</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Rekaman historis perjalanan akademik santri dari tahun ke tahun. Memastikan data kelas lama tetap valid saat mencetak ulang rapor masa lalu.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.class-promotions.index') }}" 
               class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Proses Kenaikan Baru
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-5">
        <form method="GET" action="{{ route('admin.class-promotions.history') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Filter Tahun Ajaran -->
            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Tahun Akademik
                </label>
                <select name="academic_year_id" onchange="this.form.submit()" 
                        class="w-full text-xs font-semibold rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                    <option value="all">Semua Tahun Akademik</option>
                    @foreach($academicYears as $ay)
                        <option value="{{ $ay->id }}" {{ $yearId == $ay->id ? 'selected' : '' }}>
                            {{ $ay->name }} {{ $ay->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Kelas -->
            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Kelas / Rombel
                </label>
                <select name="class_id" onchange="this.form.submit()" 
                        class="w-full text-xs font-semibold rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                    <option value="all">Semua Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} (Tk. {{ $c->grade ?? $c->level }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status -->
            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Status Riwayat
                </label>
                <select name="status" onchange="this.form.submit()" 
                        class="w-full text-xs font-semibold rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                    <option value="all">Semua Status</option>
                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Aktif Berjalan</option>
                    <option value="promoted" {{ $status === 'promoted' ? 'selected' : '' }}>Naik Kelas</option>
                    <option value="retained" {{ $status === 'retained' ? 'selected' : '' }}>Tinggal Kelas</option>
                    <option value="graduated" {{ $status === 'graduated' ? 'selected' : '' }}>Lulus</option>
                    <option value="transferred" {{ $status === 'transferred' ? 'selected' : '' }}>Mutasi / Pindah</option>
                </select>
            </div>

            <!-- Pencarian Nama / NISN -->
            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Cari Santri
                </label>
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Nama santri / NISN..." 
                           class="w-full text-xs rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white pr-9">
                    <button type="submit" class="absolute right-2.5 top-2.5 text-slate-400 hover:text-emerald-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Tabel Riwayat -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        @if($histories->isEmpty())
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-700 text-slate-400 mx-auto flex items-center justify-center mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 class="text-base font-bold text-slate-800 dark:text-white">Belum Ada Catatan Riwayat</h3>
                <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1">
                    Gunakan filter di atas atau lakukan proses kenaikan kelas santri untuk melihat catatan riwayat di sini.
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="py-3 px-4 w-12 text-center">No</th>
                            <th class="py-3 px-4">Santri</th>
                            <th class="py-3 px-4">Tahun Akademik</th>
                            <th class="py-3 px-4">Kelas / Rombel</th>
                            <th class="py-3 px-3 text-center">Status</th>
                            <th class="py-3 px-4">Keterangan</th>
                            <th class="py-3 px-4">Waktu Diproses</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                        @foreach($histories as $idx => $h)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/40 transition">
                                <td class="py-3 px-4 text-center font-bold text-slate-400">
                                    {{ $histories->firstItem() + $idx }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-500 text-white font-bold flex items-center justify-center text-xs shrink-0">
                                            {{ strtoupper(substr($h->student?->user?->name ?? 'S', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800 dark:text-white">{{ $h->student?->user?->name ?? 'Santri' }}</p>
                                            <p class="text-[11px] text-slate-400 font-mono">NISN: {{ $h->student?->nisn ?? $h->student?->nis ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 font-semibold text-slate-700 dark:text-slate-300">
                                    {{ $h->academicYear?->name ?? '-' }}
                                    @if($h->academicYear?->is_active)
                                        <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 ml-1">Aktif</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <span class="font-bold text-slate-800 dark:text-white">{{ $h->class?->name ?? '-' }}</span>
                                    <span class="text-[11px] text-slate-400 block">Tingkat {{ $h->class?->grade ?? $h->class?->level ?? '-' }}</span>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <span class="inline-block px-2.5 py-1 rounded-full text-[11px] font-bold {{ $h->status_badge }}">
                                        {{ $h->status_label }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-slate-600 dark:text-slate-300 text-xs">
                                    {{ $h->notes ?: '-' }}
                                </td>
                                <td class="py-3 px-4 text-slate-400 text-[11px]">
                                    @if($h->promoted_at)
                                        {{ $h->promoted_at->format('d/m/Y H:i') }}
                                        @if($h->promoter)
                                            <span class="block text-slate-500">oleh {{ $h->promoter->name }}</span>
                                        @endif
                                    @else
                                        {{ $h->created_at?->format('d/m/Y') ?? '-' }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-200 dark:border-slate-700">
                {{ $histories->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
