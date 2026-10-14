@extends('layouts.admin')

@section('title', 'Pembelajaran & Nilai Ekstrakurikuler')

@section('content')
<div class="space-y-6" x-data="{
    tab: '{{ $currentTab }}',
    setAllAttendance(status) {
        document.querySelectorAll('.attendance-radio-' + status).forEach(el => el.checked = true);
    }
}">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div>
            <div class="flex items-center gap-3">
                <div class="p-3 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">Pembelajaran & Absensi Ekskul</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Jurnal pertemuan latihan, absensi santri, serta input nilai capaian & predikat ekstrakurikuler</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                T.A: {{ $activeYear?->name ?? '2024/2025' }} ({{ ucfirst($activeYear?->semester ?? 'Ganjil') }})
            </span>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-xl text-emerald-800 dark:text-emerald-200 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800 rounded-xl text-rose-800 dark:text-rose-200 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Tab Bar -->
    <div class="flex border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-6 rounded-t-2xl">
        <button type="button" @click="tab = 'agenda'" :class="tab === 'agenda' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'" class="py-4 px-4 border-b-2 font-medium text-sm transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Input Pertemuan & Absensi
        </button>
        <button type="button" @click="tab = 'history'" :class="tab === 'history' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'" class="py-4 px-4 border-b-2 font-medium text-sm transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Riwayat Pertemuan Ekskul
        </button>
        <button type="button" @click="tab = 'evaluation'" :class="tab === 'evaluation' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'" class="py-4 px-4 border-b-2 font-medium text-sm transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
            </svg>
            Penilaian Capaian Rapor Ekskul
        </button>
    </div>

    <!-- TAB 1: INPUT AGENDA & ABSENSI -->
    <div x-show="tab === 'agenda'" class="space-y-6">
        <!-- Form Filter Ekskul & Kelas -->
        <form method="GET" action="{{ route('admin.extracurriculars.learning.index') }}" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <input type="hidden" name="tab" value="agenda">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider mb-2">Pilih Ekstrakurikuler</label>
                    <select name="extracurricular_id" onchange="this.form.submit()" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500">
                        @foreach($extracurriculars as $ex)
                            <option value="{{ $ex->id }}" {{ $selectedExtraId == $ex->id ? 'selected' : '' }}>{{ $ex->name }} ({{ ucfirst($ex->category ?? 'Wajib') }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider mb-2">Filter Santri Berdasarkan Kelas</label>
                    <select name="class_id" onchange="this.form.submit()" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>{{ $c->name }} (Tingkat {{ $c->level }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl text-sm transition-colors shadow-sm">
                        Muat Daftar Santri
                    </button>
                </div>
            </div>
        </form>

        <!-- Form Simpan Pertemuan & Absensi -->
        <form action="{{ route('admin.extracurriculars.learning.store-agenda') }}" method="POST" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 space-y-6">
            @csrf
            <input type="hidden" name="extracurricular_id" value="{{ $selectedExtraId }}">
            <input type="hidden" name="class_id" value="{{ $selectedClassId }}">

            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Jurnal & Materi Pembelajaran Ekskul</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Isi detail materi latihan dan pertemuan hari ini</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Kegiatan</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Pertemuan Ke-</label>
                    <input type="number" name="meeting_number" value="1" min="1" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Judul / Tema Latihan</label>
                    <input type="text" name="activity_title" placeholder="Contoh: Pengenalan Tali-Temali & Sandi Kotak" required class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Lokasi Latihan / Lapangan</label>
                    <input type="text" name="location" value="Lapangan Utama Kampus" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Materi & Rincian Kegiatan</label>
                    <textarea name="material_taught" rows="2" placeholder="Tuliskan uraian materi, praktek fisik, atau teknik yang diajarkan..." required class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></textarea>
                </div>
            </div>

            <!-- Tabel Absensi & Nilai Santri -->
            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                    <div>
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">Presensi Kehadiran & Nilai Praktik Santri</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Total: {{ $students->count() }} santri terdaftar</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500">Set Semua:</span>
                        <button type="button" @click="setAllAttendance('hadir')" class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-emerald-100 text-emerald-800 hover:bg-emerald-200 transition-colors">Hadir</button>
                        <button type="button" @click="setAllAttendance('sakit')" class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-amber-100 text-amber-800 hover:bg-amber-200 transition-colors">Sakit</button>
                        <button type="button" @click="setAllAttendance('izin')" class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-blue-100 text-blue-800 hover:bg-blue-200 transition-colors">Izin</button>
                        <button type="button" @click="setAllAttendance('alpa')" class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-rose-100 text-rose-800 hover:bg-rose-200 transition-colors">Alpa</button>
                    </div>
                </div>

                <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-xl">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                        <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase font-semibold text-gray-600 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="px-4 py-3 w-12 text-center">No</th>
                                <th class="px-4 py-3">Nama Santri</th>
                                <th class="px-4 py-3">NISN</th>
                                <th class="px-4 py-3 text-center">Presensi (H / S / I / A)</th>
                                <th class="px-4 py-3 text-center w-28">Skor Praktik</th>
                                <th class="px-4 py-3 text-center w-28">Skor Sikap</th>
                                <th class="px-4 py-3">Catatan Pembina</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($students as $idx => $st)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                                    <td class="px-4 py-3 text-center font-medium">{{ $idx + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-gray-900 dark:text-white">{{ $st->user?->name ?? 'Tanpa Nama' }}</div>
                                        <div class="text-xs text-gray-400">Kelas: {{ $st->class?->name ?? '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-xs">{{ $st->nisn ?? $st->nis ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="inline-flex items-center gap-3">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="radio" name="students[{{ $st->id }}][attendance]" value="hadir" checked class="attendance-radio-hadir text-emerald-600 focus:ring-emerald-500">
                                                <span class="ml-1 text-xs text-emerald-600 font-bold">H</span>
                                            </label>
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="radio" name="students[{{ $st->id }}][attendance]" value="sakit" class="attendance-radio-sakit text-amber-600 focus:ring-amber-500">
                                                <span class="ml-1 text-xs text-amber-600 font-bold">S</span>
                                            </label>
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="radio" name="students[{{ $st->id }}][attendance]" value="izin" class="attendance-radio-izin text-blue-600 focus:ring-blue-500">
                                                <span class="ml-1 text-xs text-blue-600 font-bold">I</span>
                                            </label>
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="radio" name="students[{{ $st->id }}][attendance]" value="alpa" class="attendance-radio-alpa text-rose-600 focus:ring-rose-500">
                                                <span class="ml-1 text-xs text-rose-600 font-bold">A</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <input type="number" name="students[{{ $st->id }}][skill]" value="85" min="0" max="100" class="w-20 text-center text-xs py-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <input type="number" name="students[{{ $st->id }}][attitude]" value="85" min="0" max="100" class="w-20 text-center text-xs py-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="students[{{ $st->id }}][notes]" placeholder="Catatan keaktifan..." class="w-full text-xs py-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                        Belum ada santri terdaftar pada kelas ini. Silakan pilih kelas lain.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm transition-all shadow-md flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Agenda & Absensi Ekskul
                </button>
            </div>
        </form>
    </div>

    <!-- TAB 2: RIWAYAT AGENDA EKSKUL -->
    <div x-show="tab === 'history'" class="space-y-4">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">Catatan Riwayat Pertemuan Ekskul</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase font-semibold text-gray-600 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-4 py-3">Tanggal & Pertemuan</th>
                            <th class="px-4 py-3">Ekstrakurikuler</th>
                            <th class="px-4 py-3">Judul & Materi</th>
                            <th class="px-4 py-3">Pembina</th>
                            <th class="px-4 py-3 text-center">Rekap Kehadiran</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($agendas as $ag)
                            @php $counts = $ag->attendance_count; @endphp
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                                <td class="px-4 py-3">
                                    <div class="font-bold text-gray-900 dark:text-white">{{ $ag->date->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500">Pertemuan #{{ $ag->meeting_number }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                        {{ $ag->extracurricular?->name }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-gray-800 dark:text-gray-200">{{ $ag->activity_title }}</div>
                                    <div class="text-xs text-gray-400 line-clamp-1">{{ $ag->material_taught }}</div>
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    {{ $ag->coach?->name ?? 'Pembina' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1.5 text-xs">
                                        <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 font-bold" title="Hadir">H: {{ $counts['hadir'] }}</span>
                                        <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-700 font-bold" title="Sakit">S: {{ $counts['sakit'] }}</span>
                                        <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-700 font-bold" title="Izin">I: {{ $counts['izin'] }}</span>
                                        <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-700 font-bold" title="Alpa">A: {{ $counts['alpa'] }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <form action="{{ route('admin.extracurriculars.learning.destroy-agenda', $ag->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus agenda ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded-lg transition-colors" title="Hapus Agenda">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    Belum ada catatan riwayat agenda ekstrakurikuler.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $agendas->links() }}
            </div>
        </div>
    </div>

    <!-- TAB 3: PENILAIAN CAPAIAN RAPOR EKSKUL -->
    <div x-show="tab === 'evaluation'" class="space-y-6">
        <form method="GET" action="{{ route('admin.extracurriculars.learning.index') }}" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <input type="hidden" name="tab" value="evaluation">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider mb-2">Pilih Ekstrakurikuler</label>
                    <select name="extracurricular_id" onchange="this.form.submit()" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        @foreach($extracurriculars as $ex)
                            <option value="{{ $ex->id }}" {{ $selectedExtraId == $ex->id ? 'selected' : '' }}>{{ $ex->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider mb-2">Pilih Kelas Santri</label>
                    <select name="class_id" onchange="this.form.submit()" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl text-sm transition-colors">
                        Tampilkan Santri
                    </button>
                </div>
            </div>
        </form>

        <form action="{{ route('admin.extracurriculars.learning.save-evaluations') }}" method="POST" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 space-y-6">
            @csrf
            <input type="hidden" name="extracurricular_id" value="{{ $selectedExtraId }}">
            <input type="hidden" name="class_id" value="{{ $selectedClassId }}">

            <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-4">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Penilaian Akhir & Predikat Rapor Ekstrakurikuler</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Predikat dan deskripsi ini akan tercetak pada lembar E-Rapor Santri</p>
                </div>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm shadow-sm transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Nilai Rapor Ekskul
                </button>
            </div>

            <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-xl">
                <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase font-semibold text-gray-600 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-4 py-3 w-12 text-center">No</th>
                            <th class="px-4 py-3">Nama Santri</th>
                            <th class="px-4 py-3 text-center w-28">Predikat</th>
                            <th class="px-4 py-3 text-center w-24">Skor Angka</th>
                            <th class="px-4 py-3">Deskripsi Capaian / Catatan Kemampuan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($students as $idx => $st)
                            @php $ev = $evaluations[$st->id] ?? null; @endphp
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                                <td class="px-4 py-3 text-center font-medium">{{ $idx + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-gray-900 dark:text-white">{{ $st->user?->name ?? 'Tanpa Nama' }}</div>
                                    <div class="text-xs text-gray-400">NISN: {{ $st->nisn ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <select name="evaluations[{{ $st->id }}][predicate]" class="text-xs font-bold py-1 px-2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                        <option value="A" {{ ($ev['predicate'] ?? 'A') === 'A' ? 'selected' : '' }}>A (Sangat Baik)</option>
                                        <option value="B" {{ ($ev['predicate'] ?? '') === 'B' ? 'selected' : '' }}>B (Baik)</option>
                                        <option value="C" {{ ($ev['predicate'] ?? '') === 'C' ? 'selected' : '' }}>C (Cukup)</option>
                                        <option value="D" {{ ($ev['predicate'] ?? '') === 'D' ? 'selected' : '' }}>D (Kurang)</option>
                                    </select>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <input type="number" name="evaluations[{{ $st->id }}][score]" value="{{ $ev['score'] ?? 85 }}" min="0" max="100" class="w-16 text-center text-xs py-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white font-bold">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" name="evaluations[{{ $st->id }}][description]" value="{{ $ev['description'] ?? 'Aktif mengikuti kegiatan dengan disiplin, kemandirian, dan antusiasme yang tinggi.' }}" required class="w-full text-xs py-1.5 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    Tidak ada santri pada kelas ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm shadow-md transition-all">
                    Simpan Nilai Rapor Ekskul
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
