@extends('layouts.admin')

@section('title', 'Kajian Pekanan Pegawai')

@section('content')
<div class="space-y-6" x-data="{ showCreateModal: false }">

    <!-- Top Header Banner -->
    <div class="p-6 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl md:text-2xl font-black text-slate-900 dark:text-white uppercase tracking-wide">
                    KAJIAN PEKANAN PEGAWAI
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Laporan kegiatan kajian rutin guru &amp; tendik, materi pembinaan spiritual, lokasi, dan rekapitulasi presensi kehadiran.
                </p>
            </div>
        </div>

        <button type="button" @click="showCreateModal = true"
                class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition shadow-xs flex items-center gap-2 shrink-0 self-start md:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Jadwalkan Kajian Baru</span>
        </button>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-xs">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Kegiatan Kajian</span>
            <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $totalSessions }} <span class="text-sm font-normal text-slate-400">Pertemuan</span></div>
        </div>

        <div class="p-5 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-xs">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Kehadiran Pegawai</span>
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $totalAttendancesRecorded }} <span class="text-sm font-normal text-slate-400">Presensi Hadir</span></div>
        </div>

        <div class="p-5 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-xs">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Pegawai &amp; Guru</span>
            <div class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ $totalEmployees }} <span class="text-sm font-normal text-slate-400">Orang</span></div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.kajian-pekanan.index') }}" class="flex flex-wrap items-center gap-3 w-full">
            <div class="relative flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari tema, pemateri, atau lokasi..." 
                       class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white pl-9 pr-4 py-2.5">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <div class="w-48">
                <input type="month" name="month" value="{{ request('month') }}"
                       class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white px-3 py-2.5">
            </div>

            <button type="submit" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 dark:bg-indigo-600 dark:hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition">
                Filter
            </button>

            @if(request('search') || request('month'))
                <a href="{{ route('admin.kajian-pekanan.index') }}" class="px-3 py-2.5 text-xs text-slate-500 hover:text-slate-800 font-bold transition">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Sessions Table / List -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 font-bold uppercase tracking-wider border-b border-slate-100 dark:border-slate-700/60">
                    <tr>
                        <th class="p-4">Tanggal &amp; Waktu</th>
                        <th class="p-4">Tema Kajian &amp; Pemateri</th>
                        <th class="p-4">Lokasi</th>
                        <th class="p-4 text-center">Rekap Kehadiran</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-slate-700 dark:text-slate-300">
                    @forelse($sessions as $session)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-700/30 transition">
                            <td class="p-4 whitespace-nowrap">
                                <span class="font-bold block text-slate-900 dark:text-white">
                                    {{ $session->date->translatedFormat('d F Y') }}
                                </span>
                                <span class="text-[11px] text-slate-400">
                                    {{ $session->time_start ?: '08:00' }} - {{ $session->time_end ?: 'Selesai' }} WITA
                                </span>
                            </td>
                            <td class="p-4">
                                <a href="{{ route('admin.kajian-pekanan.show', $session->id) }}" class="font-bold text-indigo-600 hover:underline block text-sm">
                                    {{ $session->title }}
                                </a>
                                <span class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1.5 mt-0.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Pemateri: <strong>{{ $session->speaker }}</strong>
                                </span>
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                                    📍 {{ $session->location }}
                                </span>
                            </td>
                            <td class="p-4 text-center whitespace-nowrap">
                                @php
                                    $hadir = $session->hadir_count;
                                    $total = $session->total_participants;
                                    $percent = $total > 0 ? round(($hadir / $total) * 100) : 0;
                                @endphp
                                <div class="inline-flex flex-col items-center">
                                    <div class="flex items-center gap-1.5 font-bold text-xs">
                                        <span class="text-emerald-600 dark:text-emerald-400">{{ $hadir }} Hadir</span>
                                        <span class="text-slate-400">/ {{ $total }} Pegawai</span>
                                    </div>
                                    <div class="w-24 bg-slate-200 dark:bg-slate-700 rounded-full h-1.5 mt-1.5 overflow-hidden">
                                        <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $percent }}%"></div>
                                    </div>
                                    <span class="text-[10px] text-slate-400 mt-0.5">{{ $percent }}% Kehadiran</span>
                                </div>
                            </td>
                            <td class="p-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.kajian-pekanan.show', $session->id) }}" 
                                       class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 rounded-lg font-bold text-xs transition flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                        <span>Rekap Presensi</span>
                                    </a>

                                    <form action="{{ route('admin.kajian-pekanan.destroy', $session->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kegiatan kajian ini beserta seluruh data kehadirannya?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 transition" title="Hapus Kajian">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                <p class="text-sm font-bold">Belum ada agenda kajian pekanan pegawai.</p>
                                <p class="text-xs mt-1">Klik tombol <strong>"Jadwalkan Kajian Baru"</strong> untuk memulai pencatatan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sessions->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-700">
                {{ $sessions->links() }}
            </div>
        @endif
    </div>

    <!-- Modal: Jadwalkan Kajian Baru -->
    <div x-show="showCreateModal" 
         style="display: none;" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.away="showCreateModal = false" class="bg-white dark:bg-slate-800 rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-slate-100 dark:border-slate-700/60 space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-700">
                <h3 class="text-base font-black text-slate-900 dark:text-white uppercase tracking-wider">
                    Jadwalkan Kajian Pekanan Pegawai
                </h3>
                <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('admin.kajian-pekanan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                        Judul / Tema Kajian <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="title" required placeholder="Contoh: Meneladani Akhlak Rasulullah dalam Mendidik Generasi Rabbani"
                           class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white p-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            Nama Pemateri / Ustadz <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="speaker" required placeholder="Contoh: Ustadz Ahmad Fauzi, Lc."
                               class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white p-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            Tanggal Pelaksanaan <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                               class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white p-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            Waktu Mulai
                        </label>
                        <input type="time" name="time_start" value="08:30"
                               class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white p-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            Waktu Selesai
                        </label>
                        <input type="time" name="time_end" value="10:00"
                               class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white p-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            Tempat / Lokasi <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="location" required value="Masjid SDIT Al-Fahmi Palu"
                               class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white p-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                        Notulensi / Ringkasan Materi Kajian
                    </label>
                    <textarea name="material_summary" rows="4" placeholder="Tuliskan pokok-pokok bahasan, dalil yang disampaikan, atau ringkasan ceramah..."
                              class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white p-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                        Lampiran File / Foto Dokumentasi (Opsional)
                    </label>
                    <input type="file" name="attachment" accept=".pdf,.png,.jpg,.jpeg,.docx"
                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-700">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition shadow-xs">
                        Simpan &amp; Lanjutkan Presensi
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
