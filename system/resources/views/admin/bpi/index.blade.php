@extends('layouts.admin')

@section('title', 'Pembelajaran Bina Pribadi Islam (BPI)')

@section('content')
<div class="space-y-6" x-data="{
    tab: '{{ $currentTab }}',
    showGroupModal: false,
    setAllBpiAttendance(status) {
        document.querySelectorAll('.bpi-att-radio-' + status).forEach(el => el.checked = true);
    }
}">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div>
            <div class="flex items-center gap-3">
                <div class="p-3 bg-teal-50 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">Pembelajaran Bina Pribadi Islam (BPI)</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Mentoring karakter islami, mutaba'ah ibadah yaumiyah, absensi halaqah, dan penilaian raport BPI</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.bpi.raport-list') }}" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-xl text-sm transition-colors shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak Raport Khusus BPI
            </a>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="p-4 bg-teal-50 dark:bg-teal-900/30 border border-teal-200 dark:border-teal-800 rounded-xl text-teal-800 dark:text-teal-200 text-sm flex items-center gap-3">
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
        <button type="button" @click="tab = 'agenda'" :class="tab === 'agenda' ? 'border-teal-500 text-teal-600 dark:text-teal-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'" class="py-4 px-4 border-b-2 font-medium text-sm transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Input Agenda & Absen BPI
        </button>
        <button type="button" @click="tab = 'history'" :class="tab === 'history' ? 'border-teal-500 text-teal-600 dark:text-teal-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'" class="py-4 px-4 border-b-2 font-medium text-sm transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Riwayat Pembinaan BPI
        </button>
        <button type="button" @click="tab = 'groups'" :class="tab === 'groups' ? 'border-teal-500 text-teal-600 dark:text-teal-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'" class="py-4 px-4 border-b-2 font-medium text-sm transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            Kelompok Halaqah BPI
        </button>
        <button type="button" @click="tab = 'report_input'" :class="tab === 'report_input' ? 'border-teal-500 text-teal-600 dark:text-teal-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'" class="py-4 px-4 border-b-2 font-medium text-sm transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Input Nilai Raport BPI
        </button>
    </div>

    <!-- TAB 1: INPUT AGENDA & ABSENSI BPI -->
    <div x-show="tab === 'agenda'" class="space-y-6">
        <!-- Filter Kelompok BPI -->
        <form method="GET" action="{{ route('admin.bpi.index') }}" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <input type="hidden" name="tab" value="agenda">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider mb-2">Pilih Kelompok Bina Pribadi Islam</label>
                    <select name="bpi_group_id" onchange="this.form.submit()" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500">
                        @forelse($groups as $grp)
                            <option value="{{ $grp->id }}" {{ $selectedGroupId == $grp->id ? 'selected' : '' }}>
                                {{ $grp->name }} (Murabbi: {{ $grp->mentor?->name ?? '-' }}) - {{ $grp->members->count() }} Santri [{{ ucfirst($grp->gender) }}]
                            </option>
                        @empty
                            <option value="">Belum ada kelompok BPI dibuat</option>
                        @endforelse
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="button" @click="showGroupModal = true" class="w-full px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-medium rounded-xl text-sm transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Buat Kelompok Baru
                    </button>
                </div>
            </div>
        </form>

        @if($selectedGroup)
            <!-- Form Simpan Agenda BPI -->
            <form action="{{ route('admin.bpi.store-agenda') }}" method="POST" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 space-y-6">
                @csrf
                <input type="hidden" name="bpi_group_id" value="{{ $selectedGroup->id }}">

                <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Jurnal Halaqah Pembinaan BPI</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Kelompok: <span class="font-bold text-teal-600 dark:text-teal-400">{{ $selectedGroup->name }}</span> | Pembina: {{ $selectedGroup->mentor?->name }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Pembinaan</label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Pertemuan Pekan Ke-</label>
                        <input type="number" name="meeting_number" value="1" min="1" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Materi / Tema Pembinaan BPI</label>
                        <input type="text" name="topic" placeholder="Contoh: Pentingnya Menjaga Sholat Berjamaah & Birrul Walidain" required class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Tilawah Al-Qur'an Bersama (Surah / Ayat)</label>
                        <input type="text" name="tilawah_surah" placeholder="Contoh: QS. Al-Hujurat 10-13" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Ringkasan Materi & Arahan Pembina</label>
                        <textarea name="material_summary" rows="2" placeholder="Uraian pesan-pesan tarbiyah yang disampaikan..." required class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></textarea>
                    </div>
                </div>

                <!-- Presensi & Mutaba'ah Santri -->
                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                        <div>
                            <h4 class="text-sm font-bold text-gray-900 dark:text-white">Presensi & Mutaba'ah Amal Santri Binaan</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total: {{ $groupStudents->count() }} santri dalam kelompok ini</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500">Set Semua:</span>
                            <button type="button" @click="setAllBpiAttendance('hadir')" class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-teal-100 text-teal-800 hover:bg-teal-200">Hadir</button>
                            <button type="button" @click="setAllBpiAttendance('sakit')" class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-amber-100 text-amber-800 hover:bg-amber-200">Sakit</button>
                            <button type="button" @click="setAllBpiAttendance('izin')" class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-blue-100 text-blue-800 hover:bg-blue-200">Izin</button>
                            <button type="button" @click="setAllBpiAttendance('alpa')" class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-rose-100 text-rose-800 hover:bg-rose-200">Alpa</button>
                        </div>
                    </div>

                    <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-xl">
                        <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                            <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase font-semibold text-gray-600 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                                <tr>
                                    <th class="px-4 py-3 w-12 text-center">No</th>
                                    <th class="px-4 py-3">Nama Santri</th>
                                    <th class="px-4 py-3 text-center">Presensi (H / S / I / A)</th>
                                    <th class="px-4 py-3 text-center w-28">Tilawah Pekan Ini (Hal)</th>
                                    <th class="px-4 py-3 text-center w-28">Skor Karakter</th>
                                    <th class="px-4 py-3">Catatan Pembina / Mutaba'ah</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($groupStudents as $idx => $st)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                                        <td class="px-4 py-3 text-center font-medium">{{ $idx + 1 }}</td>
                                        <td class="px-4 py-3">
                                            <div class="font-bold text-gray-900 dark:text-white">{{ $st->user?->name ?? 'Tanpa Nama' }}</div>
                                            <div class="text-xs text-gray-400">Kelas: {{ $st->class?->name ?? '-' }} | NISN: {{ $st->nisn ?? '-' }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="inline-flex items-center gap-3">
                                                <label class="inline-flex items-center cursor-pointer">
                                                    <input type="radio" name="students[{{ $st->id }}][attendance]" value="hadir" checked class="bpi-att-radio-hadir text-teal-600 focus:ring-teal-500">
                                                    <span class="ml-1 text-xs text-teal-600 font-bold">H</span>
                                                </label>
                                                <label class="inline-flex items-center cursor-pointer">
                                                    <input type="radio" name="students[{{ $st->id }}][attendance]" value="sakit" class="bpi-att-radio-sakit text-amber-600 focus:ring-amber-500">
                                                    <span class="ml-1 text-xs text-amber-600 font-bold">S</span>
                                                </label>
                                                <label class="inline-flex items-center cursor-pointer">
                                                    <input type="radio" name="students[{{ $st->id }}][attendance]" value="izin" class="bpi-att-radio-izin text-blue-600 focus:ring-blue-500">
                                                    <span class="ml-1 text-xs text-blue-600 font-bold">I</span>
                                                </label>
                                                <label class="inline-flex items-center cursor-pointer">
                                                    <input type="radio" name="students[{{ $st->id }}][attendance]" value="alpa" class="bpi-att-radio-alpa text-rose-600 focus:ring-rose-500">
                                                    <span class="ml-1 text-xs text-rose-600 font-bold">A</span>
                                                </label>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <input type="number" name="students[{{ $st->id }}][tilawah_pages]" value="10" min="0" class="w-20 text-center text-xs py-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <input type="number" name="students[{{ $st->id }}][attitude]" value="88" min="0" max="100" class="w-20 text-center text-xs py-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text" name="students[{{ $st->id }}][notes]" placeholder="Mutaba'ah sholat & adab santri..." class="w-full text-xs py-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                            Kelompok ini belum memiliki santri anggota. Silakan atur pada tab "Kelompok Halaqah BPI".
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl text-sm shadow-md transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Agenda & Absensi BPI
                    </button>
                </div>
            </form>
        @else
            <div class="bg-white dark:bg-gray-800 p-12 text-center rounded-2xl border border-gray-100 dark:border-gray-700">
                <p class="text-gray-500">Belum ada kelompok BPI yang aktif. Buat kelompok terlebih dahulu.</p>
                <button type="button" @click="showGroupModal = true" class="mt-4 px-4 py-2 bg-teal-600 text-white rounded-xl text-sm font-semibold">
                    + Tambah Kelompok BPI
                </button>
            </div>
        @endif
    </div>

    <!-- TAB 2: RIWAYAT PEMBINAAN BPI -->
    <div x-show="tab === 'history'" class="space-y-4">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">Log Pertemuan Halaqah BPI</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase font-semibold text-gray-600 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-4 py-3">Tanggal & Pertemuan</th>
                            <th class="px-4 py-3">Kelompok & Murabbi</th>
                            <th class="px-4 py-3">Materi & Tilawah</th>
                            <th class="px-4 py-3 text-center">Rekap Kehadiran</th>
                            <th class="px-4 py-3 text-center">Status Approval Kepsek</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($agendas as $ag)
                            @php $counts = $ag->attendance_count; @endphp
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                                <td class="px-4 py-3">
                                    <div class="font-bold text-gray-900 dark:text-white">{{ $ag->date->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500">Pekan #{{ $ag->meeting_number }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-teal-600 dark:text-teal-400">{{ $ag->group?->name }}</div>
                                    <div class="text-xs text-gray-500">Murabbi: {{ $ag->mentor?->name ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-gray-800 dark:text-gray-200">{{ $ag->topic }}</div>
                                    <div class="text-xs text-teal-500 font-medium">Tilawah: {{ $ag->tilawah_surah ?: 'Tadarus Al-Qur\'an' }}</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1.5 text-xs">
                                        <span class="px-2 py-0.5 rounded bg-teal-100 text-teal-700 font-bold" title="Hadir">H: {{ $counts['hadir'] }}</span>
                                        <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-700 font-bold" title="Sakit">S: {{ $counts['sakit'] }}</span>
                                        <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-700 font-bold" title="Izin">I: {{ $counts['izin'] }}</span>
                                        <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-700 font-bold" title="Alpa">A: {{ $counts['alpa'] }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if(($ag->status ?? 'submitted') === 'approved')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            Disetujui Kepsek
                                        </span>
                                        @if($ag->approver)
                                            <div class="text-[10px] text-gray-400 mt-0.5">Oleh: {{ $ag->approver->name }}</div>
                                        @endif
                                    @elseif(($ag->status ?? '') === 'rejected')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Perlu Revisi
                                        </span>
                                        @if($ag->approval_notes)
                                            <div class="text-[10px] text-rose-500 mt-0.5 italic">"{{ $ag->approval_notes }}"</div>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                                            <svg class="w-3.5 h-3.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            Menunggu Approval
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        @if(auth()->user()->hasRole(['super-admin', 'admin', 'kepala-sekolah']))
                                            @if(($ag->status ?? 'submitted') !== 'approved')
                                                <form action="{{ route('admin.bpi.approve-agenda', $ag->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-2 py-1 text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors flex items-center gap-1" title="Setujui Laporan">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                        Approve
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.bpi.reject-agenda', $ag->id) }}" method="POST" class="inline" onsubmit="var note = prompt('Catatan perbaikan / alasan penolakan:'); if(note === null) return false; this.querySelector('input[name=approval_notes]').value = note; return true;">
                                                    @csrf
                                                    <input type="hidden" name="approval_notes" value="">
                                                    <button type="submit" class="px-2 py-1 text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white rounded-lg transition-colors" title="Minta Revisi">
                                                        Revisi
                                                    </button>
                                                </form>
                                            @endif
                                        @endif

                                        <form action="{{ route('admin.bpi.destroy-agenda', $ag->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus agenda BPI ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded-lg transition-colors" title="Hapus Agenda">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    Belum ada catatan riwayat pertemuan BPI.
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

    <!-- TAB 3: KELOMPOK HALAQAH BPI -->
    <div x-show="tab === 'groups'" class="space-y-4">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Daftar Kelompok Halaqah BPI</h3>
                <button type="button" @click="showGroupModal = true" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-xl text-sm transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Kelompok BPI
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($groups as $grp)
                    <div class="p-5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-bold text-gray-900 dark:text-white">{{ $grp->name }}</h4>
                                <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $grp->gender === 'ikhwan' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                                    {{ ucfirst($grp->gender) }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mb-3">Murabbi / Pembina: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $grp->mentor?->name ?? '-' }}</span></p>
                            <div class="text-xs text-gray-400 mb-2">Santri Terdaftar: {{ $grp->members->count() }} orang</div>
                            <div class="flex flex-wrap gap-1 max-h-24 overflow-y-auto">
                                @foreach($grp->members as $m)
                                    <span class="inline-block px-2 py-0.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded text-xs text-gray-600 dark:text-gray-300">
                                        {{ $m->student?->user?->name ?? 'Santri' }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-600 flex justify-end">
                            <a href="{{ route('admin.bpi.index', ['tab' => 'agenda', 'bpi_group_id' => $grp->id]) }}" class="text-xs font-semibold text-teal-600 dark:text-teal-400 hover:underline">
                                Buka Agenda Halaqah &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-8 text-gray-500">
                        Belum ada kelompok BPI. Buat kelompok baru sekarang.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- TAB 4: INPUT NILAI RAPORT BPI -->
    <div x-show="tab === 'report_input'" class="space-y-6">
        <form method="GET" action="{{ route('admin.bpi.index') }}" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <input type="hidden" name="tab" value="report_input">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider mb-2">Pilih Kelompok BPI</label>
                    <select name="bpi_group_id" onchange="this.form.submit()" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        @foreach($groups as $grp)
                            <option value="{{ $grp->id }}" {{ $selectedGroupId == $grp->id ? 'selected' : '' }}>
                                {{ $grp->name }} (Murabbi: {{ $grp->mentor?->name ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-xl text-sm transition-colors">
                        Tampilkan Santri
                    </button>
                </div>
            </div>
        </form>

        @if($selectedGroup)
            <form action="{{ route('admin.bpi.save-report') }}" method="POST" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 space-y-6">
                @csrf
                <input type="hidden" name="bpi_group_id" value="{{ $selectedGroup->id }}">

                <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-4">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Penilaian Mutaba'ah & Karakter Raport BPI</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Nilai indikator ibadah yaumiyah dan adab kepribadian santri untuk rapor khusus BPI</p>
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl text-sm shadow-md transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Nilai Raport BPI
                    </button>
                </div>

                <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-xl">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                        <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase font-semibold text-gray-600 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="px-3 py-3 w-10 text-center">No</th>
                                <th class="px-3 py-3 min-w-[140px]">Santri</th>
                                <th class="px-2 py-3 text-center">Sholat Wajib</th>
                                <th class="px-2 py-3 text-center">Sholat Sunnah</th>
                                <th class="px-2 py-3 text-center">Tilawah</th>
                                <th class="px-2 py-3 text-center">Dzikir & Doa</th>
                                <th class="px-2 py-3 text-center">Puasa</th>
                                <th class="px-2 py-3 text-center">Adab & Sopan</th>
                                <th class="px-2 py-3 text-center">Disiplin</th>
                                <th class="px-2 py-3 text-center">Ukhuwah</th>
                                <th class="px-2 py-3 text-center">Keaktifan</th>
                                <th class="px-2 py-3 text-center w-20">Predikat</th>
                                <th class="px-3 py-3 min-w-[200px]">Catatan Pembina / Murabbi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($groupStudents as $idx => $st)
                                @php $rep = $reports[$st->id] ?? null; @endphp
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 text-xs">
                                    <td class="px-3 py-3 text-center font-medium">{{ $idx + 1 }}</td>
                                    <td class="px-3 py-3">
                                        <div class="font-bold text-gray-900 dark:text-white">{{ $st->user?->name ?? 'Tanpa Nama' }}</div>
                                        <div class="text-[11px] text-gray-400">NISN: {{ $st->nisn ?? '-' }}</div>
                                    </td>
                                    @foreach(['sholat_fardhu', 'sholat_sunnah', 'tilawah_quran', 'dzikir_doa', 'puasa_sunnah', 'adab_guru_ortu', 'kedisiplinan_kebersihan', 'ukhuwah_sosial', 'keaktifan_halaqah'] as $field)
                                        <td class="px-1 py-3 text-center">
                                            <select name="reports[{{ $st->id }}][{{ $field }}]" class="text-xs font-bold py-1 px-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                                <option value="A" {{ ($rep[$field] ?? 'A') === 'A' ? 'selected' : '' }}>A</option>
                                                <option value="B" {{ ($rep[$field] ?? '') === 'B' ? 'selected' : '' }}>B</option>
                                                <option value="C" {{ ($rep[$field] ?? '') === 'C' ? 'selected' : '' }}>C</option>
                                            </select>
                                        </td>
                                    @endforeach
                                    <td class="px-2 py-3 text-center">
                                        <select name="reports[{{ $st->id }}][final_predicate]" class="text-xs font-bold py-1 px-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                            <option value="A" {{ ($rep['final_predicate'] ?? 'A') === 'A' ? 'selected' : '' }}>A (Mumtaz)</option>
                                            <option value="B" {{ ($rep['final_predicate'] ?? '') === 'B' ? 'selected' : '' }}>B (Jayyid Jiddan)</option>
                                            <option value="C" {{ ($rep['final_predicate'] ?? '') === 'C' ? 'selected' : '' }}>C (Jayyid)</option>
                                            <option value="D" {{ ($rep['final_predicate'] ?? '') === 'D' ? 'selected' : '' }}>D (Maqbul)</option>
                                        </select>
                                    </td>
                                    <td class="px-3 py-3">
                                        <input type="text" name="reports[{{ $st->id }}][mentor_notes]" value="{{ $rep['mentor_notes'] ?? 'Ananda aktif dan istiqomah dalam mutabaah yaumiyah serta beradab mulia.' }}" class="w-full text-xs py-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="px-4 py-8 text-center text-gray-500">
                                        Tidak ada santri dalam kelompok ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl text-sm shadow-md transition-all">
                        Simpan Nilai Raport BPI
                    </button>
                </div>
            </form>
        @endif
    </div>

    <!-- MODAL TAMBAH KELOMPOK BPI -->
    <div x-show="showGroupModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" style="display: none;">
        <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-2xl w-full p-6 space-y-4 shadow-2xl border border-gray-100 dark:border-gray-700 max-h-[92vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-2">
                    <div class="p-2 bg-teal-50 dark:bg-teal-900/40 text-teal-600 dark:text-teal-400 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Tambah Kelompok Halaqah BPI</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Tentukan murabbi dan pilih santri binaan</p>
                    </div>
                </div>
                <button type="button" @click="showGroupModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
            </div>

            <form action="{{ route('admin.bpi.store-group') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Nama Kelompok BPI</label>
                        <input type="text" name="name" placeholder="Contoh: Kelompok Umar bin Khattab 1" required class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Guru Pembina / Murabbi</label>
                        <select name="mentor_id" required class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            @foreach($mentors as $m)
                                <option value="{{ $m->id }}">{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Kategori Kelompok</label>
                        <select name="gender" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="ikhwan">Ikhwan (Laki-laki)</option>
                            <option value="akhwat">Akhwat (Perempuan)</option>
                            <option value="all">Campuran / Umum</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Tingkat / Jenjang</label>
                        <input type="text" name="level" placeholder="Contoh: Kelas 7 atau Semua Tingkat" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>

                <!-- Pemilihan Anggota dengan Filter Kelas, Tingkat, & Pencarian Nama -->
                <div x-data="{
                    searchStudent: '',
                    selectedLevel: '',
                    selectedClass: '',
                    matchStudent(name, level, classId) {
                        if (this.searchStudent && !name.toLowerCase().includes(this.searchStudent.toLowerCase())) return false;
                        if (this.selectedLevel && String(level) !== String(this.selectedLevel)) return false;
                        if (this.selectedClass && String(classId) !== String(this.selectedClass)) return false;
                        return true;
                    },
                    selectAllVisible() {
                        const checkboxes = this.$el.querySelectorAll('.student-checkbox');
                        checkboxes.forEach(cb => {
                            const row = cb.closest('.student-row');
                            if (row && row.style.display !== 'none') {
                                cb.checked = true;
                            }
                        });
                    },
                    deselectAllVisible() {
                        const checkboxes = this.$el.querySelectorAll('.student-checkbox');
                        checkboxes.forEach(cb => {
                            const row = cb.closest('.student-row');
                            if (row && row.style.display !== 'none') {
                                cb.checked = false;
                            }
                        });
                    }
                }" class="space-y-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-gray-800 dark:text-gray-200">
                            Pilih Anggota Santri Bina Pribadi Islam
                        </label>
                        <div class="flex items-center gap-2 text-xs">
                            <button type="button" @click="selectAllVisible()" class="text-teal-600 dark:text-teal-400 font-semibold hover:underline flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Pilih Semua (Sesuai Filter)
                            </button>
                            <span class="text-gray-300">|</span>
                            <button type="button" @click="deselectAllVisible()" class="text-rose-500 font-semibold hover:underline">
                                Batal Pilih
                            </button>
                        </div>
                    </div>

                    <!-- Filter Bar: Tingkat, Kelas, & Search Nama -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Filter Tingkat</label>
                            <select x-model="selectedLevel" class="w-full text-xs py-1.5 px-2.5 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500">
                                <option value="">Semua Tingkat</option>
                                @foreach($classes->pluck('level')->filter()->unique()->sort() as $lvl)
                                    <option value="{{ $lvl }}">Tingkat {{ $lvl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Filter Kelas</label>
                            <select x-model="selectedClass" class="w-full text-xs py-1.5 px-2.5 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500">
                                <option value="">Semua Kelas</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}" x-show="!selectedLevel || '{{ $c->level }}' === selectedLevel">{{ $c->name }} (Tingkat {{ $c->level }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Cari Nama Santri</label>
                            <div class="relative">
                                <input type="text" x-model="searchStudent" placeholder="Ketik nama santri..." class="w-full text-xs py-1.5 pl-8 pr-2.5 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-teal-500 focus:border-teal-500">
                                <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Checklist Santri -->
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-2.5 max-h-56 overflow-y-auto space-y-1 divide-y divide-gray-100 dark:divide-gray-700/50">
                        @foreach($allStudents as $st)
                            @php
                                $stName = addslashes($st->user?->name ?? 'Santri');
                                $stLevel = $st->class?->level ?? '';
                                $stClassId = $st->class_id ?? '';
                            @endphp
                            <label x-show="matchStudent('{{ $stName }}', '{{ $stLevel }}', '{{ $stClassId }}')" class="student-row flex items-center justify-between text-xs text-gray-700 dark:text-gray-300 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 p-2 rounded-lg transition-colors">
                                <div class="flex items-center gap-2.5">
                                    <input type="checkbox" name="student_ids[]" value="{{ $st->id }}" class="student-checkbox rounded text-teal-600 focus:ring-teal-500">
                                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ $st->user?->name ?? 'Santri' }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="px-2 py-0.5 text-[11px] font-semibold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-md">
                                        {{ $st->class?->name ?? 'Tanpa Kelas' }}
                                    </span>
                                    @if($st->class?->level)
                                        <span class="px-1.5 py-0.5 text-[10px] font-bold bg-teal-50 text-teal-700 dark:bg-teal-900/40 dark:text-teal-300 rounded">
                                            Tingkat {{ $st->class->level }}
                                        </span>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" @click="showGroupModal = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-xl text-sm font-medium transition-colors">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl text-sm shadow-sm transition-all flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Kelompok BPI
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
