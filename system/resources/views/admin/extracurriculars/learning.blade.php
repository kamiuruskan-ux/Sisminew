@extends('layouts.admin')

@section('title', 'Pembelajaran & Nilai Ekstrakurikuler')

@section('content')
<div class="space-y-6" x-data="{
    tab: '{{ $currentTab }}',
    setAllAttendance(status) {
        document.querySelectorAll('.attendance-radio-' + status).forEach(el => el.checked = true);
    },

    // ── Manajemen Kelompok Ekskul (Modal Interaktif) ──
    isGroupModalOpen: false,
    modalExtraId: {{ $selectedExtraId ?: 0 }},
    modalExtraName: '{{ addslashes($selectedExtra?->name ?? "Ekstrakurikuler") }}',
    modalGrade: 'all',
    modalClassId: '',
    searchQuery: '',
    filterStatus: 'all', // 'all', 'in_group', 'not_in_group'
    allGroupStudents: [],
    modalClasses: [],
    selectedStudentIds: [],
    loadingGroupStudents: false,
    savingGroup: false,

    openGroupModal(extraId, extraName, grade) {
        this.modalExtraId = extraId || this.modalExtraId;
        this.modalExtraName = extraName || this.modalExtraName;
        this.modalGrade = grade || 'all';
        this.modalClassId = '';
        this.searchQuery = '';
        this.filterStatus = 'all';
        this.isGroupModalOpen = true;
        document.body.style.overflow = 'hidden';
        this.fetchGroupStudents();
    },
    closeGroupModal() {
        this.isGroupModalOpen = false;
        document.body.style.overflow = '';
    },
    switchModalGrade(g) {
        this.modalGrade = g;
        this.modalClassId = '';
        this.fetchGroupStudents();
    },
    async fetchGroupStudents() {
        this.loadingGroupStudents = true;
        try {
            const url = `{{ route('admin.extracurriculars.learning.group-students') }}?extracurricular_id=${this.modalExtraId}&grade=${this.modalGrade}&class_id=${this.modalClassId}`;
            const res = await fetch(url);
            const data = await res.json();
            this.modalClasses = data.classes || [];
            this.allGroupStudents = data.students || [];
            this.selectedStudentIds = this.allGroupStudents
                .filter(s => s.is_in_group)
                .map(s => s.id);
        } catch (err) {
            console.error('Gagal memuat data kelompok santri ekskul:', err);
        } finally {
            this.loadingGroupStudents = false;
        }
    },
    get filteredGroupStudents() {
        let list = this.allGroupStudents;
        if (this.searchQuery && this.searchQuery.trim() !== '') {
            const q = this.searchQuery.toLowerCase();
            list = list.filter(s => (s.name && s.name.toLowerCase().includes(q)) || (s.nisn && s.nisn.toLowerCase().includes(q)));
        }
        if (this.modalClassId && this.modalClassId !== '') {
            list = list.filter(s => String(s.class_id) === String(this.modalClassId));
        }
        if (this.filterStatus === 'in_group') {
            list = list.filter(s => this.selectedStudentIds.includes(s.id));
        } else if (this.filterStatus === 'not_in_group') {
            list = list.filter(s => !this.selectedStudentIds.includes(s.id));
        }
        return list;
    },
    toggleStudent(id) {
        const idx = this.selectedStudentIds.indexOf(id);
        if (idx > -1) {
            this.selectedStudentIds.splice(idx, 1);
        } else {
            this.selectedStudentIds.push(id);
        }
    },
    isStudentSelected(id) {
        return this.selectedStudentIds.includes(id);
    },
    selectAllFiltered() {
        this.filteredGroupStudents.forEach(s => {
            if (!this.selectedStudentIds.includes(s.id)) {
                this.selectedStudentIds.push(s.id);
            }
        });
    },
    deselectAllFiltered() {
        const filteredIds = this.filteredGroupStudents.map(s => s.id);
        this.selectedStudentIds = this.selectedStudentIds.filter(id => !filteredIds.includes(id));
    },
    async saveGroupArrangement() {
        this.savingGroup = true;
        try {
            const res = await fetch('{{ route('admin.extracurriculars.learning.save-group') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    extracurricular_id: this.modalExtraId,
                    student_ids: this.selectedStudentIds,
                    scope_grade: this.modalGrade,
                    scope_class_id: this.modalClassId
                })
            });
            const result = await res.json();
            if (result.success) {
                window.location.href = `{{ route('admin.extracurriculars.learning.index') }}?tab=${this.tab}&extracurricular_id=${this.modalExtraId}&grade=${this.modalGrade}&class_id=${this.modalClassId}`;
            } else {
                alert('Gagal menyimpan susunan kelompok: ' + (result.message || 'Terjadi kesalahan'));
            }
        } catch (err) {
            console.error('Error saat menyimpan kelompok:', err);
            alert('Terjadi kesalahan jaringan.');
        } finally {
            this.savingGroup = false;
        }
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
    <div class="flex border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-6 rounded-t-2xl overflow-x-auto">
        <button type="button" @click="tab = 'agenda'" :class="tab === 'agenda' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'" class="py-4 px-4 border-b-2 font-medium text-sm transition-colors flex items-center gap-2 whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Input Pertemuan & Absensi
        </button>
        <button type="button" @click="tab = 'history'" :class="tab === 'history' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'" class="py-4 px-4 border-b-2 font-medium text-sm transition-colors flex items-center gap-2 whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Riwayat Pertemuan Ekskul
        </button>
        <button type="button" @click="tab = 'evaluation'" :class="tab === 'evaluation' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'" class="py-4 px-4 border-b-2 font-medium text-sm transition-colors flex items-center gap-2 whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
            </svg>
            Penilaian Capaian Rapor Ekskul
        </button>
        <button type="button" @click="tab = 'summary'" :class="tab === 'summary' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'" class="py-4 px-4 border-b-2 font-medium text-sm transition-colors flex items-center gap-2 whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            Rekap Mengajar Pembina
        </button>
    </div>

    <!-- TAB 1: INPUT AGENDA & ABSENSI -->
    <div x-show="tab === 'agenda'" class="space-y-6">
        <!-- Banner Info Kelompok & Tombol Atur Kelompok -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-emerald-50 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $selectedExtra?->name ?? 'Pilih Ekstrakurikuler' }}
                        </h2>
                        @if($selectedExtra)
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                {{ $totalEnrolledMembers }} Santri di Kelompok Ini
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Atur anggota santri ekskul lintas tingkat &amp; kelas sebagaimana di Halaqah Al-Qur'an.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="openGroupModal({{ $selectedExtraId }}, '{{ addslashes($selectedExtra?->name ?? 'Ekstrakurikuler') }}', 'all')" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-sm transition-all flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Atur Kelompok Santri</span>
                </button>
            </div>
        </div>

        <!-- Form Filter Ekskul, Tingkat & Kelas -->
        <form method="GET" action="{{ route('admin.extracurriculars.learning.index') }}" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <input type="hidden" name="tab" value="agenda">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider mb-2">Pilih Ekstrakurikuler</label>
                    <select name="extracurricular_id" onchange="this.form.submit()" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500 font-medium">
                        @foreach($extracurriculars as $ex)
                            <option value="{{ $ex->id }}" {{ $selectedExtraId == $ex->id ? 'selected' : '' }}>{{ $ex->name }} ({{ ucfirst($ex->category ?? 'Wajib') }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider mb-2">Filter Tingkat</label>
                    <select name="grade" onchange="this.form.submit()" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500 font-medium">
                        <option value="all">Semua Tingkat ({{ $totalEnrolledMembers }} Santri)</option>
                        @foreach($availableGrades as $lvl)
                            <option value="{{ $lvl }}" {{ (string)$selectedGrade === (string)$lvl ? 'selected' : '' }}>Tingkat {{ $lvl }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider mb-2">Filter Berdasarkan Kelas</label>
                    <select name="class_id" onchange="this.form.submit()" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500 font-medium">
                        <option value="all">Semua Kelas / Rombel</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ (string)$selectedClassId === (string)$c->id ? 'selected' : '' }}>{{ $c->name }} (Tingkat {{ $c->level }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl text-sm transition-colors shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Muat Santri
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
            <div class="pt-4 border-t border-gray-200 dark:border-gray-700" x-data="{ searchStudent: '' }">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                    <div>
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">Presensi Kehadiran & Nilai Praktik Santri</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Total: {{ $students->count() }} santri terdaftar</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <!-- Quick Search Input -->
                        <div class="relative w-48 sm:w-56">
                            <input type="text" x-model="searchStudent" placeholder="Cari nama santri..." class="w-full text-xs py-1.5 pl-7 pr-2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-emerald-500 focus:border-emerald-500">
                            <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="text-xs text-gray-500">Set Semua:</span>
                            <button type="button" @click="setAllAttendance('hadir')" class="px-2 py-1 text-xs font-semibold rounded-lg bg-emerald-100 text-emerald-800 hover:bg-emerald-200 transition-colors">Hadir</button>
                            <button type="button" @click="setAllAttendance('sakit')" class="px-2 py-1 text-xs font-semibold rounded-lg bg-amber-100 text-amber-800 hover:bg-amber-200 transition-colors">Sakit</button>
                            <button type="button" @click="setAllAttendance('izin')" class="px-2 py-1 text-xs font-semibold rounded-lg bg-blue-100 text-blue-800 hover:bg-blue-200 transition-colors">Izin</button>
                            <button type="button" @click="setAllAttendance('alpa')" class="px-2 py-1 text-xs font-semibold rounded-lg bg-rose-100 text-rose-800 hover:bg-rose-200 transition-colors">Alpa</button>
                        </div>
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
                                @php
                                    $stName = strtolower(addslashes($st->user?->name ?? ''));
                                @endphp
                                <tr x-show="!searchStudent || '{{ $stName }}'.includes(searchStudent.toLowerCase())" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
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
                                    <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                                        @if($totalEnrolledMembers === 0)
                                            <div class="max-w-md mx-auto space-y-3">
                                                <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-400 mx-auto flex items-center justify-center">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                                </div>
                                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Kelompok Ekstrakurikuler {{ $selectedExtra?->name }} Belum Diatur</h4>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Belum ada santri yang dimasukkan ke kelompok ekstrakurikuler ini. Silakan atur santri dengan filter tingkat, rombel kelas, dan pencarian nama.</p>
                                                <button type="button" @click="openGroupModal({{ $selectedExtraId }}, '{{ addslashes($selectedExtra?->name ?? 'Ekstrakurikuler') }}', 'all')" class="mt-2 inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition cursor-pointer">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                                    Atur Kelompok Santri Sekarang
                                                </button>
                                            </div>
                                        @else
                                            <div class="space-y-1">
                                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Tidak ada santri pada filter tingkat / kelas ini.</p>
                                                <p class="text-xs text-gray-400">Silakan pilih 'Semua Tingkat' &amp; 'Semua Kelas', atau tambahkan santri melalui menu <button type="button" @click="openGroupModal({{ $selectedExtraId }}, '{{ addslashes($selectedExtra?->name ?? 'Ekstrakurikuler') }}', 'all')" class="text-emerald-600 font-bold hover:underline cursor-pointer">Atur Kelompok Santri</button>.</p>
                                            </div>
                                        @endif
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
                    Simpan & Ajukan Agenda Ekskul
                </button>
            </div>
        </form>
    </div>

    <!-- TAB 2: RIWAYAT AGENDA EKSKUL -->
    <div x-show="tab === 'history'" class="space-y-4">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Catatan Riwayat Pertemuan Ekskul</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Daftar agenda latihan dan status pengesahan dari Kepala Sekolah</p>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase font-semibold text-gray-600 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-4 py-3">Tanggal & Pertemuan</th>
                            <th class="px-4 py-3">Ekstrakurikuler</th>
                            <th class="px-4 py-3">Judul & Materi</th>
                            <th class="px-4 py-3">Pembina</th>
                            <th class="px-4 py-3 text-center">Rekap Kehadiran</th>
                            <th class="px-4 py-3 text-center">Status Approval</th>
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
                                    @if(($ag->status ?? 'submitted') === 'approved')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            Disetujui Kepsek
                                        </span>
                                        @if($ag->approver)
                                            <div class="text-[10px] text-gray-400 mt-0.5">oleh {{ $ag->approver->name }}</div>
                                        @endif
                                    @elseif(($ag->status ?? '') === 'rejected')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
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
                                    <div class="flex items-center justify-center gap-1.5">
                                        @if(auth()->user()->hasRole(['super-admin', 'admin', 'kepala-sekolah']))
                                            @if(($ag->status ?? 'submitted') !== 'approved')
                                                <form action="{{ route('admin.extracurriculars.learning.approve-agenda', $ag->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-2 py-1 text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors flex items-center gap-1" title="Setujui Laporan">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                        Approve
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.extracurriculars.learning.reject-agenda', $ag->id) }}" method="POST" class="inline" onsubmit="var note = prompt('Catatan perbaikan / alasan penolakan:'); if(note === null) return false; this.querySelector('input[name=approval_notes]').value = note; return true;">
                                                    @csrf
                                                    <input type="hidden" name="approval_notes" value="">
                                                    <button type="submit" class="px-2 py-1 text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white rounded-lg transition-colors" title="Minta Revisi">
                                                        Revisi
                                                    </button>
                                                </form>
                                            @endif
                                        @endif

                                        <form action="{{ route('admin.extracurriculars.learning.destroy-agenda', $ag->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus agenda ini?')">
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
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
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
        <form method="GET" action="{{ route('admin.extracurriculars.learning.index') }}" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700"
            x-data="{
                selectedLevel: '{{ $classes->firstWhere('id', $selectedClassId)?->level ?? '' }}'
            }">
            <input type="hidden" name="tab" value="evaluation">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider mb-2">Pilih Ekstrakurikuler</label>
                    <select name="extracurricular_id" onchange="this.form.submit()" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        @foreach($extracurriculars as $ex)
                            <option value="{{ $ex->id }}" {{ $selectedExtraId == $ex->id ? 'selected' : '' }}>{{ $ex->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider mb-2">Filter Tingkat</label>
                    <select x-model="selectedLevel" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <option value="">Semua Tingkat</option>
                        @foreach($classes->pluck('level')->filter()->unique()->sort() as $lvl)
                            <option value="{{ $lvl }}">Tingkat {{ $lvl }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider mb-2">Pilih Kelas Santri</label>
                    <select name="class_id" onchange="this.form.submit()" class="w-full text-sm rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" x-show="!selectedLevel || '{{ $c->level }}' === selectedLevel" {{ $selectedClassId == $c->id ? 'selected' : '' }}>{{ $c->name }} (Tingkat {{ $c->level }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl text-sm transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Tampilkan Santri
                    </button>
                </div>
            </div>
        </form>

        <form action="{{ route('admin.extracurriculars.learning.save-evaluations') }}" method="POST" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 space-y-6" x-data="{ searchEval: '' }">
            @csrf
            <input type="hidden" name="extracurricular_id" value="{{ $selectedExtraId }}">
            <input type="hidden" name="class_id" value="{{ $selectedClassId }}">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-gray-200 dark:border-gray-700 pb-4">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Penilaian Akhir & Predikat Rapor Ekstrakurikuler</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Predikat dan deskripsi ini akan tercetak pada lembar E-Rapor Santri</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="relative w-48 sm:w-56">
                        <input type="text" x-model="searchEval" placeholder="Cari nama santri..." class="w-full text-xs py-1.5 pl-7 pr-2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm shadow-sm transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Nilai Rapor Ekskul
                    </button>
                </div>
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
                            @php
                                $ev = $evaluations[$st->id] ?? null;
                                $stEvalName = strtolower(addslashes($st->user?->name ?? ''));
                            @endphp
                            <tr x-show="!searchEval || '{{ $stEvalName }}'.includes(searchEval.toLowerCase())" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
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

    <!-- TAB 4: REKAP MENGAJAR PEMBINA EKSKUL (KHUSUS ADMIN & KEPALA SEKOLAH) -->
    <div x-show="tab === 'summary'" class="space-y-6">
        <!-- Overview Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-5 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Pembina Aktif</div>
                        <div class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ $coachSummaries->count() }}</div>
                        <div class="text-xs text-gray-400 mt-1">Guru & Pelatih Eksternal</div>
                    </div>
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                </div>
            </div>

            <div class="p-5 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Pertemuan Mengajar</div>
                        <div class="text-2xl font-black text-teal-600 dark:text-teal-400 mt-1">{{ $coachSummaries->sum('total_meetings') }}</div>
                        <div class="text-xs text-gray-400 mt-1">Sesi latihan tercatat</div>
                    </div>
                    <div class="p-3 bg-teal-50 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                </div>
            </div>

            <div class="p-5 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status Pengesahan</div>
                        <div class="text-xl font-bold text-gray-900 dark:text-white mt-1">
                            <span class="text-emerald-600">{{ $coachSummaries->sum('approved_meetings') }} Disetujui</span>
                        </div>
                        <div class="text-xs text-amber-500 mt-1">{{ $coachSummaries->sum('pending_meetings') }} Menunggu Approval</div>
                    </div>
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>

            <div class="p-5 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Partisipasi Santri</div>
                        <div class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ $coachSummaries->sum('attended_count') }}</div>
                        <div class="text-xs text-gray-400 mt-1">Presensi hadir santri</div>
                    </div>
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Rekap Frekuensi Mengajar Guru Pembina -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Laporan Rekapan Mengajar Pembina Ekstrakurikuler</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Monitoring kehadiran mengajar guru pembina dan pengesahan laporan oleh Kepala Sekolah</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase font-semibold text-gray-600 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-4 py-3 w-12 text-center">No</th>
                            <th class="px-4 py-3">Nama Pembina</th>
                            <th class="px-4 py-3">Cabang Ekskul</th>
                            <th class="px-4 py-3 text-center">Frekuensi Mengajar</th>
                            <th class="px-4 py-3 text-center">Status Laporan</th>
                            <th class="px-4 py-3 text-center">Santri Hadir</th>
                            <th class="px-4 py-3 text-center">Rata-rata Skor</th>
                            <th class="px-4 py-3 text-center">Terakhir Mengajar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($coachSummaries as $idx => $cs)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                                <td class="px-4 py-3 text-center font-medium">{{ $idx + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300 font-bold flex items-center justify-center text-xs">
                                            {{ strtoupper(substr($cs->coach->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white">{{ $cs->coach->name }}</div>
                                            <div class="text-[11px] text-gray-400">{{ $cs->coach->email ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                        {{ $cs->extra_names }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                        {{ $cs->total_meetings }}x Mengajar
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1.5 text-xs">
                                        <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 font-bold" title="Laporan Disetujui">
                                            ✓ {{ $cs->approved_meetings }} Disetujui
                                        </span>
                                        @if($cs->pending_meetings > 0)
                                            <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-700 font-bold" title="Menunggu Approval">
                                                ⏳ {{ $cs->pending_meetings }} Menunggu
                                            </span>
                                        @endif
                                        @if($cs->rejected_meetings > 0)
                                            <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-700 font-bold" title="Perlu Revisi">
                                                ✕ {{ $cs->rejected_meetings }} Revisi
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ $cs->attended_count }} santri
                                </td>
                                <td class="px-4 py-3 text-center text-xs">
                                    <span class="font-bold text-gray-800 dark:text-gray-200">{{ $cs->avg_skill }}</span>
                                    <span class="text-gray-400 text-[10px]">(Praktik)</span> /
                                    <span class="font-bold text-gray-800 dark:text-gray-200">{{ $cs->avg_attitude }}</span>
                                    <span class="text-gray-400 text-[10px]">(Sikap)</span>
                                </td>
                                <td class="px-4 py-3 text-center text-xs text-gray-600 dark:text-gray-400">
                                    {{ $cs->latest_teaching ? \Carbon\Carbon::parse($cs->latest_teaching)->format('d M Y') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                    Belum ada data rekapan mengajar guru pembina ekstrakurikuler.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- MODAL INTUITIF: ATUR / KELOLA KELOMPOK SANTRI EKSTRAKURIKULER             --}}
    {{-- ========================================================================= --}}
    <div x-show="isGroupModalOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[9999] overflow-hidden bg-slate-900/60 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4"
         style="display: none;"
         x-cloak>
        
        <div @click.away="closeGroupModal()"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
             class="bg-white dark:bg-slate-900 rounded-t-3xl sm:rounded-3xl shadow-2xl border border-slate-200/80 dark:border-slate-800 w-full max-w-4xl h-[88dvh] sm:h-[85vh] max-h-[88dvh] flex flex-col overflow-hidden">
            
            {{-- Modal Header (Sticky at top) --}}
            <div class="px-4 py-3 sm:px-6 sm:py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3 bg-slate-50/80 dark:bg-slate-800/40 shrink-0 z-10">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2 py-0.5 bg-emerald-600 text-white rounded-md text-[9px] sm:text-[10px] font-black uppercase tracking-wider">
                            Kelompok Ekstrakurikuler
                        </span>
                        <h3 class="text-sm sm:text-base font-black text-slate-900 dark:text-white truncate">
                            Atur Anggota: <span x-text="modalExtraName"></span>
                        </h3>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-0.5 hidden sm:block">
                        Pilih santri untuk kelompok ekstrakurikuler ini. Tersedia filter tingkat, kelas, dan pencarian santri.
                    </p>
                </div>
                <button type="button" @click="closeGroupModal()"
                        class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-white flex items-center justify-center transition shrink-0 cursor-pointer"
                        title="Tutup Modal">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Modal Scrollable Body: Controls + Student Cards --}}
            <div class="flex-1 min-h-0 overflow-y-auto overscroll-contain bg-slate-50/50 dark:bg-slate-900/50 divide-y divide-slate-100 dark:divide-slate-800/60"
                 style="-webkit-overflow-scrolling: touch; overscroll-behavior: contain; touch-action: pan-y;">

                {{-- Section 1: Modal Controls (Tingkat, Search, Filter Rombel, Status) --}}
                <div class="p-3.5 sm:p-5 bg-white dark:bg-slate-900 space-y-3 sm:space-y-4">
                    
                    {{-- Baris 1: Selector Tingkat Kelas (Pill Buttons) --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[10px] sm:text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">
                                PILIH TINGKAT KELAS:
                            </span>
                            <span class="text-[11px] sm:text-xs font-black text-emerald-700 dark:text-emerald-400">
                                Sedang Mengatur: <span x-text="modalGrade === 'all' ? 'Semua Tingkat' : 'Tingkat ' + modalGrade"></span>
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-1.5 sm:gap-2">
                            <button type="button" @click="switchModalGrade('all')"
                                    :class="modalGrade === 'all' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30 ring-2 ring-emerald-500 font-black' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 font-bold'"
                                    class="px-3 py-1.5 sm:px-4 sm:py-2 rounded-xl text-xs transition cursor-pointer">
                                <span>Semua Tingkat</span>
                            </button>
                            @foreach($availableGrades as $g)
                            <button type="button" @click="switchModalGrade({{ $g }})"
                                    :class="modalGrade === {{ $g }} ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30 ring-2 ring-emerald-500 font-black' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 font-bold'"
                                    class="px-3 py-1.5 sm:px-4 sm:py-2 rounded-xl text-xs transition cursor-pointer">
                                <span>Tingkat {{ $g }}</span>
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Baris 2: Live Search, Filter Rombel Asal, & Bulk Actions --}}
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 sm:gap-2.5 pt-0.5">
                        {{-- Search box --}}
                        <div class="sm:col-span-5 relative">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" x-model="searchQuery" placeholder="Cari nama santri atau NISN..."
                                   class="w-full pl-9 pr-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-medium focus:ring-2 focus:ring-emerald-500 outline-none transition">
                        </div>

                        {{-- Filter Rombel Asal Kelas --}}
                        <div class="sm:col-span-4">
                            <select x-model="modalClassId" @change="fetchGroupStudents()"
                                    class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="">Semua Rombel Kelas</option>
                                <template x-for="c in modalClasses" :key="c.id">
                                    <option :value="c.id" x-text="c.name + ' (Tingkat ' + (c.grade || '-') + ')'"></option>
                                </template>
                            </select>
                        </div>

                        {{-- Bulk Actions --}}
                        <div class="sm:col-span-3 flex items-center gap-1.5">
                            <button type="button" @click="selectAllFiltered()"
                                    class="flex-1 py-2 px-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-xl text-[11px] font-extrabold text-slate-700 dark:text-slate-300 transition text-center cursor-pointer">
                                Pilih Semua
                            </button>
                            <button type="button" @click="deselectAllFiltered()"
                                    class="flex-1 py-2 px-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-xl text-[11px] font-extrabold text-slate-700 dark:text-slate-300 transition text-center cursor-pointer">
                                Batal Pilih
                            </button>
                        </div>
                    </div>

                    {{-- Baris 3: Quick Filter Status Pills & Selected Counter --}}
                    <div class="flex flex-wrap items-center justify-between gap-2 pt-0.5">
                        <div class="flex flex-wrap items-center gap-1.5">
                            <button type="button" @click="filterStatus = 'all'"
                                    :class="filterStatus === 'all' ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300'"
                                    class="px-2.5 py-1 rounded-lg text-[10px] sm:text-[11px] font-bold transition cursor-pointer">
                                Semua (<span x-text="allGroupStudents.length"></span>)
                            </button>
                            <button type="button" @click="filterStatus = 'in_group'"
                                    :class="filterStatus === 'in_group' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300'"
                                    class="px-2.5 py-1 rounded-lg text-[10px] sm:text-[11px] font-bold transition cursor-pointer">
                                ✓ Kelompok Ini (<span x-text="selectedStudentIds.length"></span>)
                            </button>
                            <button type="button" @click="filterStatus = 'not_in_group'"
                                    :class="filterStatus === 'not_in_group' ? 'bg-amber-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300'"
                                    class="px-2.5 py-1 rounded-lg text-[10px] sm:text-[11px] font-bold transition cursor-pointer">
                                Belum Terdaftar
                            </button>
                        </div>

                        <div class="text-[11px] sm:text-xs font-black text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 px-2.5 py-0.5 rounded-lg border border-emerald-200 dark:border-emerald-800">
                            ✨ <span x-text="selectedStudentIds.length"></span> Santri Terpilih
                        </div>
                    </div>

                </div>

                {{-- Section 2: Daftar Kartu Santri --}}
                <div class="p-3.5 sm:p-5">
                    
                    {{-- Loading Spinner --}}
                    <div x-show="loadingGroupStudents" class="py-12 text-center text-slate-400 space-y-3">
                        <svg class="animate-spin w-8 h-8 mx-auto text-emerald-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <p class="text-xs font-bold">Memuat daftar santri...</p>
                    </div>

                    {{-- Empty List State --}}
                    <div x-show="!loadingGroupStudents && filteredGroupStudents.length === 0" class="py-12 text-center text-slate-400">
                        <p class="text-xs font-bold">Tidak ada santri yang sesuai dengan filter pencarian.</p>
                    </div>

                    {{-- Grid Kartu Santri --}}
                    <div x-show="!loadingGroupStudents && filteredGroupStudents.length > 0"
                         class="grid grid-cols-1 md:grid-cols-2 gap-2 sm:gap-3">
                        <template x-for="st in filteredGroupStudents" :key="st.id">
                            <div @click="toggleStudent(st.id)"
                                 :class="isStudentSelected(st.id) 
                                    ? 'bg-emerald-50/80 dark:bg-emerald-950/40 border-emerald-500 shadow-sm shadow-emerald-500/10 ring-1 ring-emerald-500' 
                                    : 'bg-white dark:bg-slate-800/80 border-slate-200 dark:border-slate-700/80 hover:border-slate-300 dark:hover:border-slate-600'"
                                 class="p-3 rounded-2xl border transition-all cursor-pointer flex items-center justify-between gap-3 select-none">
                                
                                <div class="flex items-center gap-2.5 min-w-0">
                                    {{-- Avatar Initial / Gender --}}
                                    <div :class="st.gender === 'female' ? 'bg-pink-100 text-pink-700 dark:bg-pink-950 dark:text-pink-300' : 'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300'"
                                         class="w-9 h-9 rounded-xl flex items-center justify-center font-black text-xs shrink-0 uppercase">
                                        <span x-text="st.name.substring(0, 2)"></span>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-xs font-bold text-slate-900 dark:text-white truncate" x-text="st.name"></div>
                                        <div class="flex items-center gap-1.5 text-[10px] text-slate-500 mt-0.5 flex-wrap">
                                            <span class="font-semibold text-slate-700 dark:text-slate-300" x-text="st.class_name"></span>
                                            <span>•</span>
                                            <span class="font-mono" x-text="'NISN: ' + st.nisn"></span>
                                            <span x-show="st.grade" class="px-1.5 py-0.2 bg-slate-100 dark:bg-slate-700 rounded text-[9px] font-bold" x-text="'Tingkat ' + st.grade"></span>
                                        </div>
                                        {{-- Ekskul lain yang diikuti --}}
                                        <template x-if="st.other_extras && st.other_extras.length > 0">
                                            <div class="text-[9px] text-indigo-600 dark:text-indigo-400 font-semibold mt-1 truncate">
                                                Ikut: <span x-text="st.other_extras.join(', ')"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                {{-- Checkbox Indicator --}}
                                <div class="shrink-0 flex items-center">
                                    <div :class="isStudentSelected(st.id) ? 'bg-emerald-600 border-emerald-600 text-white' : 'border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700'"
                                         class="w-6 h-6 rounded-lg border-2 flex items-center justify-center transition">
                                        <svg x-show="isStudentSelected(st.id)" class="w-4 h-4 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                </div>

                            </div>
                        </template>
                    </div>

                </div>

            </div>

            {{-- Modal Footer (Sticky at bottom) --}}
            <div class="px-4 py-3 sm:px-6 sm:py-3.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3 bg-white dark:bg-slate-900 shrink-0">
                <button type="button" @click="closeGroupModal()"
                        class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800 transition cursor-pointer">
                    Batal
                </button>
                <button type="button" @click="saveGroupArrangement()" :disabled="savingGroup"
                        class="px-5 py-2.5 rounded-xl text-xs font-black text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/30 transition flex items-center gap-2 cursor-pointer disabled:opacity-50">
                    <svg x-show="savingGroup" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span>Simpan Kelompok (<span x-text="selectedStudentIds.length"></span> Santri)</span>
                </button>
            </div>

        </div>
    </div>
</div>
@endsection
