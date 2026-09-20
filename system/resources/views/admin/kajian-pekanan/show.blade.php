@extends('layouts.admin')

@section('title', 'Rekap Presensi & Detail Kajian Pekanan')

@section('content')
<div class="space-y-6" x-data="{
    showEditModal: false,
    searchEmployee: '',
    markAll(status) {
        document.querySelectorAll(`input[type='radio'][value='${status}']`).forEach(el => {
            el.checked = true;
        });
    }
}">

    <!-- Top Bar with Back Button & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.kajian-pekanan.index') }}" 
               class="p-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 hover:text-slate-900 dark:text-slate-300 transition shadow-2xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">DETAIL KAJIAN &amp; PRESENSI PEGAWAI</span>
                <h1 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">
                    {{ $session->title }}
                </h1>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" @click="showEditModal = true"
                    class="px-3.5 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl transition shadow-2xs flex items-center gap-1.5">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Edit Info Kajian</span>
            </button>

            <a href="{{ route('admin.kajian-pekanan.print', $session->id) }}" target="_blank"
               class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 dark:bg-indigo-600 dark:hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition shadow-2xs flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Rekap Presensi</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Session Detail Card -->
    <div class="p-6 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-xs">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 pb-4 border-b border-slate-100 dark:border-slate-700/60">
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Pemateri / Penceramah</span>
                <span class="text-sm font-black text-slate-900 dark:text-white mt-0.5 block">
                    {{ $session->speaker }}
                </span>
            </div>

            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Tanggal Pelaksanaan</span>
                <span class="text-sm font-black text-slate-900 dark:text-white mt-0.5 block">
                    {{ $session->date->translatedFormat('l, d F Y') }}
                </span>
            </div>

            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Waktu Pelaksanaan</span>
                <span class="text-sm font-black text-slate-900 dark:text-white mt-0.5 block">
                    {{ $session->time_start ?: '08:30' }} - {{ $session->time_end ?: 'Selesai' }} WITA
                </span>
            </div>

            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Lokasi Kegiatan</span>
                <span class="text-sm font-black text-slate-900 dark:text-white mt-0.5 block">
                    📍 {{ $session->location }}
                </span>
            </div>
        </div>

        <!-- Material Summary & Attachments -->
        <div class="pt-4 space-y-2">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Ringkasan Materi / Pembahasan:</span>
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/50 text-xs text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line border border-slate-100 dark:border-slate-800">
                {{ $session->material_summary ?: 'Belum ada catatan ringkasan materi yang dimasukkan.' }}
            </div>

            @if($session->attachment_path)
                <div class="pt-2">
                    <a href="{{ asset($session->attachment_path) }}" target="_blank" 
                       class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/40 rounded-lg hover:underline">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        <span>Lihat Dokumen / Foto Materi Lampiran</span>
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Attendance Counters Stats Bar -->
    <div class="grid grid-cols-2 sm:grid-cols-6 gap-3">
        <div class="p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700/60 shadow-xs text-center">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Pegawai</span>
            <span class="text-xl font-black text-slate-900 dark:text-white mt-0.5 block">{{ $totalCount }}</span>
        </div>

        <div class="p-4 bg-emerald-50/70 dark:bg-emerald-950/30 rounded-xl border border-emerald-200/60 dark:border-emerald-800/60 shadow-xs text-center">
            <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider block">Hadir</span>
            <span class="text-xl font-black text-emerald-700 dark:text-emerald-300 mt-0.5 block">{{ $hadirCount }}</span>
        </div>

        <div class="p-4 bg-indigo-50/70 dark:bg-indigo-950/30 rounded-xl border border-indigo-200/60 dark:border-indigo-800/60 shadow-xs text-center">
            <span class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider block">Izin</span>
            <span class="text-xl font-black text-indigo-700 dark:text-indigo-300 mt-0.5 block">{{ $izinCount }}</span>
        </div>

        <div class="p-4 bg-amber-50/70 dark:bg-amber-950/30 rounded-xl border border-amber-200/60 dark:border-amber-800/60 shadow-xs text-center">
            <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider block">Sakit</span>
            <span class="text-xl font-black text-amber-700 dark:text-amber-300 mt-0.5 block">{{ $sakitCount }}</span>
        </div>

        <div class="p-4 bg-rose-50/70 dark:bg-rose-950/30 rounded-xl border border-rose-200/60 dark:border-rose-800/60 shadow-xs text-center">
            <span class="text-[10px] font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wider block">Alpa / Tanpa Ket.</span>
            <span class="text-xl font-black text-rose-700 dark:text-rose-300 mt-0.5 block">{{ $alpaCount }}</span>
        </div>

        <div class="p-4 bg-sky-50/70 dark:bg-sky-950/30 rounded-xl border border-sky-200/60 dark:border-sky-800/60 shadow-xs text-center">
            <span class="text-[10px] font-bold text-sky-600 dark:text-sky-400 uppercase tracking-wider block">% Kehadiran</span>
            <span class="text-xl font-black text-sky-700 dark:text-sky-300 mt-0.5 block">{{ $persentase }}%</span>
        </div>
    </div>

    <!-- Attendance Form & Table -->
    <form action="{{ route('admin.kajian-pekanan.attendance', $session->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700/60 shadow-xs overflow-hidden">
            
            <!-- Table Action Header Bar -->
            <div class="p-4 border-b border-slate-100 dark:border-slate-700/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50 dark:bg-slate-900/40">
                <div class="flex items-center gap-2">
                    <div class="relative w-64">
                        <input type="text" x-model="searchEmployee" placeholder="Cari nama pegawai..." 
                               class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white pl-8 pr-3 py-2">
                        <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>

                    <button type="button" @click="markAll('hadir')" 
                            class="px-3 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 rounded-xl font-bold text-xs transition flex items-center gap-1.5 shrink-0">
                        <span>✨ Tandai Semua Hadir</span>
                    </button>
                </div>

                <button type="submit" 
                        class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs uppercase tracking-wider rounded-xl transition shadow-xs flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>SIMPAN REKAP PRESENSI</span>
                </button>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 font-bold uppercase tracking-wider border-b border-slate-100 dark:border-slate-700/60">
                        <tr>
                            <th class="p-3.5 w-12 text-center">No</th>
                            <th class="p-3.5">Nama Pegawai &amp; NIP</th>
                            <th class="p-3.5">Jabatan / Role</th>
                            <th class="p-3.5 text-center w-72">Status Kehadiran</th>
                            <th class="p-3.5">Keterangan / Alasan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-slate-700 dark:text-slate-300">
                        @foreach($employees as $index => $emp)
                            @php
                                $att = $existingAttendances->get($emp->id);
                                $currentStatus = $att ? $att->status : 'hadir';
                                $currentNotes = $att ? $att->notes : '';
                            @endphp
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-700/30 transition"
                                x-show="!searchEmployee || '{{ strtolower(addslashes($emp->name)) }}'.includes(searchEmployee.toLowerCase())">
                                <td class="p-3.5 text-center text-slate-400 font-mono">{{ $index + 1 }}</td>
                                <td class="p-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold flex items-center justify-center text-xs shrink-0">
                                            {{ strtoupper(substr($emp->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <span class="font-bold block text-slate-900 dark:text-white">{{ $emp->name }}</span>
                                            <span class="text-[11px] text-slate-400">NIP: {{ $emp->nip ?: '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3.5 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                        {{ $emp->roles->first()?->name ?? 'Pegawai' }}
                                    </span>
                                </td>
                                <td class="p-3.5 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5 bg-slate-100 dark:bg-slate-900 p-1 rounded-xl">
                                        <label class="cursor-pointer">
                                            <input type="radio" name="attendance[{{ $emp->id }}][status]" value="hadir" 
                                                   {{ $currentStatus === 'hadir' ? 'checked' : '' }}
                                                   class="peer sr-only">
                                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold block transition text-slate-600 peer-checked:bg-emerald-600 peer-checked:text-white dark:text-slate-400">
                                                Hadir
                                            </span>
                                        </label>

                                        <label class="cursor-pointer">
                                            <input type="radio" name="attendance[{{ $emp->id }}][status]" value="izin" 
                                                   {{ $currentStatus === 'izin' ? 'checked' : '' }}
                                                   class="peer sr-only">
                                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold block transition text-slate-600 peer-checked:bg-indigo-600 peer-checked:text-white dark:text-slate-400">
                                                Izin
                                            </span>
                                        </label>

                                        <label class="cursor-pointer">
                                            <input type="radio" name="attendance[{{ $emp->id }}][status]" value="sakit" 
                                                   {{ $currentStatus === 'sakit' ? 'checked' : '' }}
                                                   class="peer sr-only">
                                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold block transition text-slate-600 peer-checked:bg-amber-600 peer-checked:text-white dark:text-slate-400">
                                                Sakit
                                            </span>
                                        </label>

                                        <label class="cursor-pointer">
                                            <input type="radio" name="attendance[{{ $emp->id }}][status]" value="alpa" 
                                                   {{ $currentStatus === 'alpa' ? 'checked' : '' }}
                                                   class="peer sr-only">
                                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold block transition text-slate-600 peer-checked:bg-rose-600 peer-checked:text-white dark:text-slate-400">
                                                Alpa
                                            </span>
                                        </label>
                                    </div>
                                </td>
                                <td class="p-3.5">
                                    <input type="text" name="attendance[{{ $emp->id }}][notes]" value="{{ $currentNotes }}" 
                                           placeholder="Keterangan / alasan izin jika ada..."
                                           class="w-full text-xs rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white px-2.5 py-1.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Bottom Save Action Bar -->
            <div class="p-4 border-t border-slate-100 dark:border-slate-700/60 bg-slate-50/50 dark:bg-slate-900/40 flex items-center justify-between">
                <span class="text-xs text-slate-500">
                    Pastikan seluruh data kehadiran sudah benar sebelum menyimpan.
                </span>
                <button type="submit" 
                        class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs uppercase tracking-wider rounded-xl transition shadow-xs flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>SIMPAN REKAP PRESENSI</span>
                </button>
            </div>
        </div>
    </form>

    <!-- Modal: Edit Informasi Kajian -->
    <div x-show="showEditModal" 
         style="display: none;" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.away="showEditModal = false" class="bg-white dark:bg-slate-800 rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-slate-100 dark:border-slate-700/60 space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-700">
                <h3 class="text-base font-black text-slate-900 dark:text-white uppercase tracking-wider">
                    Edit Informasi Kegiatan Kajian
                </h3>
                <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('admin.kajian-pekanan.update', $session->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                        Judul / Tema Kajian <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="title" required value="{{ $session->title }}"
                           class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white p-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            Nama Pemateri / Ustadz <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="speaker" required value="{{ $session->speaker }}"
                               class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white p-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            Tanggal Pelaksanaan <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="date" required value="{{ $session->date->format('Y-m-d') }}"
                               class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white p-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            Waktu Mulai
                        </label>
                        <input type="time" name="time_start" value="{{ $session->time_start }}"
                               class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white p-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            Waktu Selesai
                        </label>
                        <input type="time" name="time_end" value="{{ $session->time_end }}"
                               class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white p-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            Tempat / Lokasi <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="location" required value="{{ $session->location }}"
                               class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white p-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                        Notulensi / Ringkasan Materi Kajian
                    </label>
                    <textarea name="material_summary" rows="4"
                              class="w-full text-xs rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white p-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">{{ $session->material_summary }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                        Ganti Lampiran File / Foto Dokumentasi (Opsional)
                    </label>
                    <input type="file" name="attachment" accept=".pdf,.png,.jpg,.jpeg,.docx"
                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-700">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition shadow-xs">
                        Perbarui Informasi
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
