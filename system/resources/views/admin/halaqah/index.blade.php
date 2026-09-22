@extends('layouts.admin')

@section('title', "Halaqah Al-Qur'an - Tahsin & Tahfidz")
@section('page_title', "Halaqah Al-Qur'an")

@section('content')
<div class="space-y-6" x-data="{
    activeTab: '{{ $tab }}',
    evalMode: '{{ $mode }}',
    programType: 'tahsin',
    tahsinType: 'jilid',
    scoreCognitive: 90,
    scoreAdab: 85,
    selectedJuz: 30,
    onSurahChange(event) {
        const sel = event.target;
        const opt = sel.options[sel.selectedIndex];
        if (opt && opt.dataset.juz) {
            this.selectedJuz = parseInt(opt.dataset.juz);
        }
    },
    get calculatedPredicate() {
        let sc = parseFloat(this.scoreCognitive) || 0;
        if (sc >= 90) return 'Mumtaz (90-100)';
        if (sc >= 80) return 'Jayyid Jiddan (80-89)';
        if (sc >= 70) return 'Jayyid (70-79)';
        return 'Maqbul (< 70)';
    },

    // Template massal default
    massProgram: 'tahsin',
    massTahsinType: 'jilid',
    massJilid: 'Jilid 1',
    massStart: 1,
    massEnd: 10,
    massCognitive: 90,
    massAdab: 85,
    massNotes: 'Makhraj & kelancaran tajwid baik',
    applyTemplateToAll() {
        document.querySelectorAll('.mass-program-select').forEach(el => el.value = this.massProgram);
        document.querySelectorAll('.mass-jilid-select').forEach(el => el.value = this.massJilid);
        document.querySelectorAll('.mass-start-input').forEach(el => el.value = this.massStart);
        document.querySelectorAll('.mass-end-input').forEach(el => el.value = this.massEnd);
        document.querySelectorAll('.mass-cognitive-input').forEach(el => el.value = this.massCognitive);
        document.querySelectorAll('.mass-adab-input').forEach(el => el.value = this.massAdab);
        document.querySelectorAll('.mass-notes-input').forEach(el => el.value = this.massNotes);
    },

    // ── Manajemen Kelompok Halaqah (Modal Interaktif) ──
    isGroupModalOpen: false,
    modalGrade: {{ $selectedGrade }},
    modalTeacherId: {{ $activeTeacherId }},
    modalTeacherName: '{{ addslashes($activeTeacher->name ?? "Guru Al-Qur\'an") }}',
    allGroupStudents: [],
    modalClasses: [],
    selectedStudentIds: [],
    loadingGroupStudents: false,
    savingGroup: false,
    searchQuery: '',
    filterClassId: '',
    filterStatus: 'all', // 'all', 'my_group', 'unassigned', 'other_group'

    openGroupModal(grade) {
        this.modalGrade = grade || this.modalGrade;
        this.isGroupModalOpen = true;
        this.fetchGroupStudents();
    },
    closeGroupModal() {
        this.isGroupModalOpen = false;
    },
    switchModalGrade(g) {
        this.modalGrade = g;
        this.fetchGroupStudents();
    },
    switchModalTeacher(tid, tname) {
        this.modalTeacherId = tid;
        this.modalTeacherName = tname;
        this.fetchGroupStudents();
    },
    async fetchGroupStudents() {
        this.loadingGroupStudents = true;
        try {
            const url = `{{ route('admin.halaqah.group-students') }}?grade=${this.modalGrade}&teacher_id=${this.modalTeacherId}`;
            const res = await fetch(url);
            const data = await res.json();
            this.modalClasses = data.classes || [];
            this.allGroupStudents = data.students || [];
            this.selectedStudentIds = this.allGroupStudents
                .filter(s => s.is_in_my_group)
                .map(s => s.id);
        } catch (err) {
            console.error('Gagal memuat data kelompok santri:', err);
        } finally {
            this.loadingGroupStudents = false;
        }
    },
    get filteredGroupStudents() {
        let list = this.allGroupStudents;
        if (this.searchQuery.trim() !== '') {
            const q = this.searchQuery.toLowerCase();
            list = list.filter(s => s.name.toLowerCase().includes(q) || s.nisn.toLowerCase().includes(q));
        }
        if (this.filterClassId !== '') {
            list = list.filter(s => String(s.class_id) === String(this.filterClassId));
        }
        if (this.filterStatus === 'my_group') {
            list = list.filter(s => this.selectedStudentIds.includes(s.id));
        } else if (this.filterStatus === 'unassigned') {
            list = list.filter(s => !this.selectedStudentIds.includes(s.id) && !s.assigned_teacher_id);
        } else if (this.filterStatus === 'other_group') {
            list = list.filter(s => !this.selectedStudentIds.includes(s.id) && s.assigned_teacher_id && s.assigned_teacher_id !== this.modalTeacherId);
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
            const res = await fetch('{{ route('admin.halaqah.save-group') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    grade: this.modalGrade,
                    teacher_id: this.modalTeacherId,
                    student_ids: this.selectedStudentIds
                })
            });
            const result = await res.json();
            if (result.success) {
                // Refresh halaman untuk memuat santri kelompok yang baru disimpan
                window.location.href = `{{ route('admin.halaqah.index') }}?tab={{ $tab }}&mode={{ $mode }}&grade=${this.modalGrade}&teacher_id=${this.modalTeacherId}`;
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

    {{-- Top Bar Tabs (Input & Evaluasi | Laporan & Grafik | Rekap Kehadiran) --}}
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-white dark:bg-slate-900 p-2.5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="flex items-center gap-1.5 w-full sm:w-auto overflow-x-auto">
            <a href="{{ route('admin.halaqah.index', ['tab' => 'input', 'grade' => $selectedGrade, 'mode' => $mode, 'teacher_id' => $activeTeacherId]) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 whitespace-nowrap {{ $tab === 'input' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Input &amp; Catatan Evaluasi</span>
            </a>

            <a href="{{ route('admin.halaqah.index', ['tab' => 'reports', 'grade' => $selectedGrade, 'teacher_id' => $activeTeacherId]) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 whitespace-nowrap {{ $tab === 'reports' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>Laporan Harian &amp; Bulanan + Grafik</span>
            </a>

            <a href="{{ route('admin.halaqah.index', ['tab' => 'attendance', 'grade' => $selectedGrade, 'teacher_id' => $activeTeacherId]) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 whitespace-nowrap {{ $tab === 'attendance' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Rekap Kehadiran</span>
            </a>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            {{-- Tombol Atur Kelompok Santri --}}
            <button type="button" @click="openGroupModal({{ $selectedGrade }})"
                    class="px-3.5 py-1.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-xl text-xs font-black shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>Atur Kelompok Halaqah</span>
            </button>

            <a href="{{ route('admin.halaqah.export-excel', ['grade' => $selectedGrade, 'teacher_id' => $activeTeacherId]) }}"
               class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 text-xs font-bold rounded-xl border border-emerald-200 dark:border-emerald-800 transition flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Export Excel</span>
            </a>
            <a href="{{ route('admin.quran-raport.index') }}"
               class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>E-Raport Al-Qur'an</span>
            </a>
        </div>
    </div>

    {{-- ALERT NOTIFIKASI --}}
    @if(session('success'))
    <div class="p-4 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-2xs">
        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="p-4 bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-2xs">
        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- ========================================================================= --}}
    {{-- TAB 1: INPUT & CATATAN EVALUASI --}}
    {{-- ========================================================================= --}}
    @if($tab === 'input')
    <div class="space-y-5">
        
        {{-- Sub-Toggle: Individu (Satu Santri) vs Massal Satu Kelompok Halaqah --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <div>
                <h3 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider">METODE PENILAIAN EVALUASI</h3>
                <p class="text-[11px] text-slate-400">Pilih input individu untuk santri tunggal atau input massal untuk satu kelompok halaqah sekaligus</p>
            </div>
            <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl">
                <a href="{{ route('admin.halaqah.index', ['tab' => 'input', 'mode' => 'individual', 'grade' => $selectedGrade, 'teacher_id' => $activeTeacherId]) }}"
                   class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ $mode === 'individual' ? 'bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 shadow-xs' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white' }}">
                    <span>👤 Individu (Satu Santri)</span>
                </a>
                <a href="{{ route('admin.halaqah.index', ['tab' => 'input', 'mode' => 'mass', 'grade' => $selectedGrade, 'teacher_id' => $activeTeacherId]) }}"
                   class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ $mode === 'mass' ? 'bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 shadow-xs' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white' }}">
                    <span>👥 Massal Satu Kelompok</span>
                </a>
            </div>
        </div>

        {{-- MODE 1: INDIVIDU (SATU SANTRI) --}}
        @if($mode === 'individual')
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            {{-- Kolom Kiri: Form Input Individu --}}
            <div class="lg:col-span-5 bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="flex items-center gap-2 text-amber-500 font-extrabold text-xs tracking-wider uppercase">
                        <span>⚡ INPUT NILAI BARU HARIAN</span>
                    </div>
                    <span class="text-[11px] font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-2.5 py-0.5 rounded-full border border-emerald-200 dark:border-emerald-800">
                        {{ $activeTeacher->name }}
                    </span>
                </div>

                <form action="{{ route('admin.halaqah.store-individual') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="grade" value="{{ $selectedGrade }}">

                    @if($isAdmin)
                    {{-- Filter Guru Pembimbing (Khusus Admin) --}}
                    <div>
                        <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Guru Pembimbing (Mode Admin)</label>
                        <select onchange="location.href = '{{ route('admin.halaqah.index') }}?tab=input&mode=individual&grade={{ $selectedGrade }}&teacher_id=' + this.value"
                                class="w-full px-3.5 py-2.5 border border-indigo-200 dark:border-indigo-800 bg-indigo-50/50 dark:bg-indigo-950/30 text-indigo-900 dark:text-indigo-200 rounded-xl text-xs font-bold">
                            @foreach($quranTeachers as $t)
                                <option value="{{ $t->id }}" {{ $activeTeacherId == $t->id ? 'selected' : '' }}>
                                    Ust. {{ $t->name }} (Guru Al-Qur'an)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    
                    {{-- Tanggal Penilaian --}}
                    <div>
                        <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Tanggal Penilaian</label>
                        <input type="date" name="assessment_date" value="{{ $selectedDate }}" required
                               class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white rounded-xl text-xs font-bold focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                    </div>

                    {{-- PILIH TINGKAT KELAS YANG DIAJAR (LANGSUNG TINGKAT, BUKAN PILIHAN KELAS SPESIFIK) --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-[11px] font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                Pilih Tingkat Kelas
                            </label>
                            <button type="button" @click="openGroupModal({{ $selectedGrade }})" class="text-[11px] font-bold text-emerald-600 hover:text-emerald-700 underline flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                <span>Atur Anggota Kelompok</span>
                            </button>
                        </div>
                        <select onchange="location.href = '{{ route('admin.halaqah.index') }}?tab=input&mode=individual&grade=' + this.value + '{{ $isAdmin ? '&teacher_id=' . $activeTeacherId : '' }}'"
                                class="w-full px-3.5 py-2.5 border-2 border-emerald-500/30 dark:border-emerald-600/40 bg-white dark:bg-slate-800 text-slate-900 dark:text-white rounded-xl text-xs font-extrabold focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                            @foreach($availableGrades as $gradeItem)
                                <option value="{{ $gradeItem }}" {{ $selectedGrade == $gradeItem ? 'selected' : '' }}>
                                    Tingkat Kelas {{ $gradeItem }} ({{ $gradeCounts[$gradeItem] ?? 0 }} Santri Kelompok Anda)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- PILIH NAMA SANTRI (HANYA SANTRI KELOMPOK GURU BERSANGKUTAN) --}}
                    <div>
                        <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">
                            Pilih Nama Santri (Kelompok Anda)
                        </label>
                        @if(count($students) > 0)
                            <select name="student_id" required
                                    class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white rounded-xl text-xs font-bold focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                                <option value="">-- Pilih Santri ({{ count($students) }} dalam Kelompok Anda) --</option>
                                @foreach($students as $st)
                                    <option value="{{ $st->id }}">
                                        {{ $st->user?->name ?? 'Santri' }} (Kelas: {{ $st->class?->name ?? 'Tingkat ' . $selectedGrade }} • NISN: {{ $st->nisn ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        @else
                            {{-- Empty State Ramah: Belum ada santri di kelompok tingkat ini --}}
                            <div class="p-4 bg-amber-50/80 dark:bg-amber-950/40 rounded-2xl border border-dashed border-amber-300 dark:border-amber-800 text-center space-y-2">
                                <p class="text-xs font-bold text-amber-800 dark:text-amber-300">
                                    Belum ada santri di kelompok Tingkat Kelas {{ $selectedGrade }} Anda.
                                </p>
                                <p class="text-[11px] text-amber-600 dark:text-amber-400">
                                    Pembelajaran Al-Qur'an bersifat eksklusif. Atur santri bimbingan Anda sekali saja agar muncul di sini.
                                </p>
                                <button type="button" @click="openGroupModal({{ $selectedGrade }})"
                                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black shadow transition inline-flex items-center gap-1.5 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    <span>Pilih Santri Kelompok Tingkat {{ $selectedGrade }}</span>
                                </button>
                            </div>
                        @endif
                    </div>

                    {{-- Jenis Program Materi --}}
                    <div>
                        <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Jenis Program Materi</label>
                        <select name="program_type" x-model="programType"
                                class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white rounded-xl text-xs font-bold focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                            <option value="tahsin">TAHSIN (Bimbingan)</option>
                            <option value="tahfidz">TAHFIDZ (Hafalan)</option>
                        </select>
                    </div>

                    {{-- Detail Lembar Jika Tahsin --}}
                    <div x-show="programType === 'tahsin'" class="space-y-3 p-3.5 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 mb-1">Pilihan Jilid / Level</label>
                                <select name="jilid_level" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white rounded-xl text-xs font-bold">
                                    <option value="Jilid 1">Jilid 1</option>
                                    <option value="Jilid 2">Jilid 2</option>
                                    <option value="Jilid 3">Jilid 3</option>
                                    <option value="Jilid 4">Jilid 4</option>
                                    <option value="Tilawah">Tilawah</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 mb-1">Range Halaman</label>
                                <div class="flex items-center gap-1.5">
                                    <input type="number" name="page_start" value="1" placeholder="Hal" class="w-1/2 px-2.5 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-bold text-center">
                                    <span class="text-slate-400">-</span>
                                    <input type="number" name="page_end" value="10" placeholder="Hal" class="w-1/2 px-2.5 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-bold text-center">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Detail Surah & Ayat Jika Tahfidz --}}
                    <div x-show="programType === 'tahfidz'" class="space-y-3 p-3.5 bg-emerald-50/50 dark:bg-emerald-950/30 rounded-2xl border border-emerald-100 dark:border-emerald-800/40">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1">Pilihan Nama Surah</label>
                            <select name="surah_name" @change="onSurahChange($event)" class="w-full px-3 py-2 border border-emerald-300 dark:border-emerald-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white rounded-xl text-xs font-bold">
                                <option value="">-- Pilih Surah --</option>
                                @foreach($surahOptions ?? [] as $surah)
                                    <option value="{{ $surah['name'] }}" data-juz="{{ $surah['juz'] ?? 30 }}">
                                        {{ $surah['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 mb-1">Juz</label>
                                <input type="number" name="juz_number" x-model="selectedJuz" class="w-full px-2 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-bold text-center">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 mb-1">Ayat Mulai</label>
                                <input type="number" name="ayat_start" value="1" class="w-full px-2 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-bold text-center">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 mb-1">Ayat Selesai</label>
                                <input type="number" name="ayat_end" value="10" class="w-full px-2 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-bold text-center">
                            </div>
                        </div>
                    </div>

                    {{-- Penilaian Nilai Kognitif & Nilai Adab --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Nilai Kognitif (0 - 100)</label>
                            <input type="number" name="score_cognitive" x-model="scoreCognitive" min="0" max="100" required
                                   class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white rounded-xl text-xs font-bold text-center focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Nilai Adab (0 - 100)</label>
                            <input type="number" name="score_adab" x-model="scoreAdab" min="0" max="100" required
                                   class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white rounded-xl text-xs font-bold text-center focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    {{-- Predikat Terkalkulasi Otomatis --}}
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl border border-emerald-200 dark:border-emerald-800 flex items-center justify-between">
                        <span class="text-xs font-bold text-emerald-800 dark:text-emerald-300">PREDIKAT TERKALKULASI OTOMATIS:</span>
                        <span class="px-2.5 py-1 bg-emerald-600 text-white rounded-lg text-xs font-extrabold" x-text="calculatedPredicate"></span>
                    </div>

                    {{-- Kehadiran Santri --}}
                    <div>
                        <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Status Kehadiran</label>
                        <select name="attendance_status" class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white rounded-xl text-xs font-bold">
                            <option value="hadir">Hadir (Mengikuti Majelis)</option>
                            <option value="sakit">Sakit</option>
                            <option value="izin">Izin</option>
                            <option value="alpa">Alpa</option>
                        </select>
                    </div>

                    {{-- Catatan Musyrif --}}
                    <div>
                        <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Catatan Khusus Musyrif</label>
                        <textarea name="teacher_notes" rows="2" placeholder="Tuliskan catatan kelancaran, makhraj, tajwid atau motivasi santri..."
                                  class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white rounded-xl text-xs font-medium focus:ring-2 focus:ring-emerald-500"></textarea>
                    </div>

                    <button type="submit" @if(count($students) === 0) disabled @endif
                            class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-600/20 transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>SIMPAN HASIL EVALUASI HALAQAH</span>
                    </button>
                </form>
            </div>

            {{-- Kolom Kanan: Catatan Riwayat Pembelajaran --}}
            <div class="lg:col-span-7 bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="flex items-center gap-2 font-black text-xs text-slate-800 dark:text-white uppercase tracking-wider">
                        <span>📜 CATATAN RIWAYAT PEMBELAJARAN (TINGKAT {{ $selectedGrade }})</span>
                    </div>
                    <span class="text-[11px] font-bold text-slate-400">Total {{ $totalRecordsCount }} Data</span>
                </div>

                {{-- Tabel Riwayat Pembelajaran --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="text-[10px] font-extrabold text-slate-400 uppercase border-b border-slate-200 dark:border-slate-800">
                                <th class="py-2.5 px-3">Tanggal</th>
                                <th class="py-2.5 px-3">Santri</th>
                                <th class="py-2.5 px-3">Materi</th>
                                <th class="py-2.5 px-3">Predikat</th>
                                <th class="py-2.5 px-3">Nilai</th>
                                <th class="py-2.5 px-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($records as $rec)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40">
                                <td class="py-2.5 px-3 whitespace-nowrap font-bold text-slate-500">
                                    {{ $rec->assessment_date->format('d/m/Y') }}
                                </td>
                                <td class="py-2.5 px-3 whitespace-nowrap">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $rec->student->user?->name ?? 'Santri' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $rec->class->name ?? '-' }}</div>
                                </td>
                                <td class="py-2.5 px-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold {{ $rec->program_type === 'tahsin' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                        {{ strtoupper($rec->program_type) }}
                                    </span>
                                    <div class="text-[11px] font-medium text-slate-600 dark:text-slate-300 mt-0.5">
                                        {{ $rec->material_summary }}
                                    </div>
                                </td>
                                <td class="py-2.5 px-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                                        {{ $rec->predicate }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 font-black text-slate-900 dark:text-white">
                                    {{ $rec->score_cognitive }}
                                </td>
                                <td class="py-2.5 px-3 text-right whitespace-nowrap">
                                    <form action="{{ route('admin.halaqah.destroy', $rec->id) }}" method="POST" onsubmit="return confirm('Hapus catatan riwayat halaqah ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-rose-500 hover:text-rose-700 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    Belum ada data evaluasi pembelajaran Al-Qur'an pada Tingkat Kelas {{ $selectedGrade }}.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($records->hasPages())
                <div class="pt-2">
                    {{ $records->links() }}
                </div>
                @endif
            </div>

        </div>

        {{-- MODE 2: MASSAL SATU KELOMPOK HALAQAH --}}
        @elseif($mode === 'mass')
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-5">
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-4">
                <div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <span>👥 PENILAIAN MASSAL KELOMPOK HALAQAH AL-QUR'AN</span>
                    </h3>
                    <p class="text-xs text-slate-400">Penilaian cepat sekaligus untuk seluruh santri yang masuk dalam kelompok halaqah bimbingan Anda</p>
                </div>

                {{-- Filter Tingkat Kelas untuk Pengisian Massal --}}
                <div class="flex flex-wrap items-center gap-3">
                    @if($isAdmin)
                    <div>
                        <select onchange="location.href = '{{ route('admin.halaqah.index') }}?tab=input&mode=mass&grade={{ $selectedGrade }}&teacher_id=' + this.value"
                                class="px-3.5 py-2 border border-indigo-200 dark:border-indigo-800 bg-indigo-50/50 dark:bg-indigo-950/30 text-indigo-900 dark:text-indigo-200 rounded-xl text-xs font-bold">
                            @foreach($quranTeachers as $t)
                                <option value="{{ $t->id }}" {{ $activeTeacherId == $t->id ? 'selected' : '' }}>
                                    Ust. {{ $t->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div>
                        <select onchange="location.href = '{{ route('admin.halaqah.index') }}?tab=input&mode=mass&grade=' + this.value + '{{ $isAdmin ? '&teacher_id=' . $activeTeacherId : '' }}'"
                                class="px-3.5 py-2 border-2 border-emerald-500/40 bg-white dark:bg-slate-800 text-slate-900 dark:text-white rounded-xl text-xs font-extrabold">
                            @foreach($availableGrades as $gradeItem)
                                <option value="{{ $gradeItem }}" {{ $selectedGrade == $gradeItem ? 'selected' : '' }}>
                                    Tingkat Kelas {{ $gradeItem }} ({{ $gradeCounts[$gradeItem] ?? 0 }} Santri)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="button" @click="openGroupModal({{ $selectedGrade }})"
                            class="px-3 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 rounded-xl text-xs font-bold border border-emerald-200 dark:border-emerald-800 flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        <span>Atur Santri Kelompok</span>
                    </button>
                </div>
            </div>

            @if(count($students) > 0)
            <form action="{{ route('admin.halaqah.store-mass') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="grade" value="{{ $selectedGrade }}">
                
                {{-- Fast-Grading Template Toolbar (Salin Template ke Semua Santri) --}}
                <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="text-amber-500 font-bold text-xs">⚡ TEMPLATE PENGISIAN MASSAL (CEPAT)</span>
                            <span class="text-[11px] text-slate-400">({{ count($students) }} Santri dalam Kelompok Tingkat {{ $selectedGrade }})</span>
                        </div>
                        <button type="button" @click="applyTemplateToAll()"
                                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs shadow-xs transition flex items-center gap-1.5 self-start sm:self-auto cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            <span>SALIN TEMPLATE KE SEMUA SANTRI</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2 text-xs">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1">Tanggal</label>
                            <input type="date" name="assessment_date" value="{{ $selectedDate }}" required
                                   class="w-full px-2.5 py-1.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg font-bold">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1">Jenis Program</label>
                            <select x-model="massProgram" class="w-full px-2.5 py-1.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg font-bold">
                                <option value="tahsin">TAHSIN</option>
                                <option value="tahfidz">TAHFIDZ</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1">Jilid / Level</label>
                            <select x-model="massJilid" class="w-full px-2.5 py-1.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg font-bold">
                                <option value="Jilid 1">Jilid 1</option>
                                <option value="Jilid 2">Jilid 2</option>
                                <option value="Jilid 3">Jilid 3</option>
                                <option value="Jilid 4">Jilid 4</option>
                                <option value="Tilawah">Tilawah</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1">Range Halaman</label>
                            <div class="flex items-center gap-1">
                                <input type="number" x-model="massStart" class="w-1/2 px-2 py-1.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg font-bold text-center">
                                <span>-</span>
                                <input type="number" x-model="massEnd" class="w-1/2 px-2 py-1.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg font-bold text-center">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1">Nilai Kognitif</label>
                            <input type="number" x-model="massCognitive" min="0" max="100" class="w-full px-2.5 py-1.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg font-bold text-center">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1">Nilai Adab</label>
                            <input type="number" x-model="massAdab" min="0" max="100" class="w-full px-2.5 py-1.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg font-bold text-center">
                        </div>
                    </div>
                </div>

                {{-- Matriks Santri dalam Kelompok Halaqah --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="text-[11px] font-extrabold text-slate-500 uppercase border-b border-slate-200 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-800/50">
                                <th class="py-3 px-3">Santri (Siswa)</th>
                                <th class="py-3 px-3">Kelas Asal</th>
                                <th class="py-3 px-3">Kehadiran</th>
                                <th class="py-3 px-3">Program &amp; Materi</th>
                                <th class="py-3 px-3 text-center">Halaman / Range</th>
                                <th class="py-3 px-3 text-center">Nilai Angka</th>
                                <th class="py-3 px-3">Catatan Khusus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($students as $st)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                <td class="py-3 px-3 whitespace-nowrap">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $st->user?->name ?? 'Santri' }}</div>
                                    <div class="text-[10px] text-slate-400">NISN: {{ $st->nisn ?? '-' }}</div>
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap">
                                    <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 rounded text-[10px] font-bold text-slate-600 dark:text-slate-300">
                                        {{ $st->class?->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-3 px-3">
                                    <select name="items[{{ $st->id }}][attendance_status]" class="px-2 py-1.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg text-xs font-bold text-emerald-600">
                                        <option value="hadir">Hadir</option>
                                        <option value="sakit">Sakit</option>
                                        <option value="izin">Izin</option>
                                        <option value="alpa">Alpa</option>
                                    </select>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="flex flex-col gap-1.5">
                                        <div class="flex items-center gap-1.5">
                                            <select name="items[{{ $st->id }}][program_type]" class="mass-program-select px-2 py-1.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg text-xs font-bold">
                                                <option value="tahsin">Tahsin</option>
                                                <option value="tahfidz">Tahfidz</option>
                                            </select>
                                            <select name="items[{{ $st->id }}][jilid_level]" class="mass-jilid-select px-2 py-1.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg text-xs font-bold">
                                                <option value="Jilid 1">Jilid 1</option>
                                                <option value="Jilid 2">Jilid 2</option>
                                                <option value="Jilid 3">Jilid 3</option>
                                                <option value="Jilid 4">Jilid 4</option>
                                                <option value="Tilawah">Tilawah</option>
                                            </select>
                                        </div>
                                        <select name="items[{{ $st->id }}][surah_name]" class="mass-surah-select w-full px-2 py-1 border border-emerald-300 dark:border-emerald-700 bg-white dark:bg-slate-800 rounded-lg text-[10px] font-bold">
                                            <option value="">-- Pilihan Surah (Khusus Tahfidz / Tilawah) --</option>
                                            @foreach($surahOptions ?? [] as $surah)
                                                <option value="{{ $surah['name'] }}">{{ $surah['label'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <div class="inline-flex items-center gap-1">
                                        <span class="text-slate-400">hl.</span>
                                        <input type="number" name="items[{{ $st->id }}][page_start]" value="1" class="mass-start-input w-12 px-1.5 py-1 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg text-center font-bold">
                                        <span>-</span>
                                        <input type="number" name="items[{{ $st->id }}][page_end]" value="10" class="mass-end-input w-12 px-1.5 py-1 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg text-center font-bold">
                                    </div>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <div class="inline-flex items-center gap-1.5">
                                        <input type="number" name="items[{{ $st->id }}][score_cognitive]" value="90" min="0" max="100" class="mass-cognitive-input w-14 px-2 py-1 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg font-black text-center text-emerald-600">
                                        <input type="number" name="items[{{ $st->id }}][score_adab]" value="85" min="0" max="100" placeholder="Adab" class="mass-adab-input w-12 px-1 py-1 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg text-[10px] text-center">
                                    </div>
                                </td>
                                <td class="py-3 px-3">
                                    <input type="text" name="items[{{ $st->id }}][teacher_notes]" placeholder="Catatan khusus..." class="mass-notes-input w-full px-2 py-1 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg text-xs font-medium">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end pt-3">
                    <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-600/20 transition flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>SIMPAN SELURUH NILAI KELOMPOK SEKALIGUS</span>
                    </button>
                </div>
            </form>
            @else
            {{-- Empty State Massal --}}
            <div class="p-12 text-center bg-slate-50 dark:bg-slate-800/40 rounded-3xl border border-dashed border-slate-200 dark:border-slate-700 space-y-3">
                <div class="w-14 h-14 rounded-2xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 mx-auto flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <h4 class="text-sm font-black text-slate-800 dark:text-white uppercase">Kelompok Tingkat Kelas {{ $selectedGrade }} Masih Kosong</h4>
                <p class="text-xs text-slate-400 max-w-md mx-auto">
                    Belum ada santri yang dimasukkan ke dalam kelompok halaqah {{ $activeTeacher->name }} pada Tingkat Kelas {{ $selectedGrade }}. Pembelajaran Al-Qur'an bersifat eksklusif per guru pembimbing.
                </p>
                <div class="pt-2">
                    <button type="button" @click="openGroupModal({{ $selectedGrade }})"
                            class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black shadow-md shadow-emerald-600/20 transition inline-flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        <span>Pilih Santri Kelompok Anda Sekarang</span>
                    </button>
                </div>
            </div>
            @endif
        </div>
        @endif

    </div>
    @endif

    {{-- ========================================================================= --}}
    {{-- TAB 2: LAPORAN HARIAN & BULANAN + GRAFIK STATISTIK --}}
    {{-- ========================================================================= --}}
    @if($tab === 'reports')
    <div class="space-y-6">
        
        {{-- KPI Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            {{-- KPI 1: Total Keaktifan Selesai --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-2">
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">TOTAL KEAKTIFAN SELESAI</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-black text-slate-900 dark:text-white">{{ $totalSetoran }}</span>
                    <span class="text-xs text-slate-400 font-bold">Kali Setoran</span>
                </div>
                <div class="pt-2 flex items-center justify-between text-xs font-bold border-t border-slate-100 dark:border-slate-800">
                    <span class="text-amber-600">Materi Tahsin: {{ $tahsinCount }}</span>
                    <span class="text-emerald-600">Materi Tahfidz: {{ $tahfidzCount }}</span>
                </div>
            </div>

            {{-- KPI 2: Nilai Evaluasi Rata-rata --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-2">
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">NILAI EVALUASI RATA-RATA</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-black text-emerald-600 dark:text-emerald-400">{{ $avgScore }}</span>
                    <span class="text-xs text-slate-400 font-bold">/ 100</span>
                </div>
                <div class="pt-2 flex items-center justify-between text-xs font-bold border-t border-slate-100 dark:border-slate-800">
                    <span class="text-slate-500">Predikat Capaian:</span>
                    <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 rounded text-[10px] font-extrabold">
                        {{ \App\Models\HalaqahRecord::calculatePredicate($avgScore) }}
                    </span>
                </div>
            </div>

            {{-- KPI 3: Persentase Kelancaran Excellent --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-2">
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">PERSENTASE KELANCARAN EXCELLENT</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-black text-indigo-600 dark:text-indigo-400">{{ $mumtazPercentage }}%</span>
                    <span class="text-xs text-slate-400 font-bold">Lancar (Mumtaz)</span>
                </div>
                <div class="pt-2 flex items-center justify-between text-xs font-bold border-t border-slate-100 dark:border-slate-800">
                    <span class="text-slate-500">Batas Standar Mumtaz:</span>
                    <span class="text-slate-700 dark:text-slate-300 font-extrabold">&gt;= 90 Skala Angka</span>
                </div>
            </div>
        </div>

        {{-- Baris Grafik 1: Sebaran Predikat Santri & Pembagian Fokus Program --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            {{-- Card Grafik Sebaran Predikat --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                <div>
                    <h4 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <span>📊 GRAFIK SEBARAN PREDIKAT SANTRI</span>
                    </h4>
                    <p class="text-[11px] text-slate-400">Tingkatan hasil evaluasi berdasarkan nilai standardisasi sekolah</p>
                </div>

                <div class="space-y-3 pt-2 text-xs">
                    @foreach($predicateDistribution as $pName => $pCount)
                    @php
                        $pPct = $totalSetoran > 0 ? round(($pCount / $totalSetoran) * 100) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between font-bold mb-1">
                            <span class="text-slate-700 dark:text-slate-300">{{ $pName }}</span>
                            <span class="text-slate-500">{{ $pCount }} Santri ({{ $pPct }}%)</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 h-2.5 rounded-full overflow-hidden">
                            <div class="bg-emerald-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $pPct }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Card Pembagian Fokus Program (Tahsin vs Tahfidz) --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                <div>
                    <h4 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <span>🎯 PEMBAGIAN FOKUS PROGRAM</span>
                    </h4>
                    <p class="text-[11px] text-slate-400">Komparasi keaktifan bimbingan membaca Al-Qur'an (Tahsin) dengan setoran hafalan (Tahfidz)</p>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4">
                    <div class="p-4 bg-amber-50 dark:bg-amber-950/30 rounded-2xl border border-amber-200 dark:border-amber-900/50 text-center space-y-1">
                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-amber-100 text-amber-800">TAHSIN</span>
                        <div class="text-3xl font-black text-amber-600">{{ $tahsinCount }}</div>
                        <p class="text-[11px] text-slate-500 font-bold">Pelajaran</p>
                    </div>

                    <div class="p-4 bg-emerald-50 dark:bg-emerald-950/30 rounded-2xl border border-emerald-200 dark:border-emerald-900/50 text-center space-y-1">
                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 text-emerald-800">TAHFIDZ</span>
                        <div class="text-3xl font-black text-emerald-600">{{ $tahfidzCount }}</div>
                        <p class="text-[11px] text-slate-500 font-bold">Hafalan</p>
                    </div>
                </div>

                <div class="p-3 bg-slate-50 dark:bg-slate-800/40 rounded-xl text-[11px] text-slate-500 italic">
                    💡 <strong>Insight Halaqah:</strong> Program Al-Qur'an disarankan berjalan seimbang antara bimbingan makhorijul huruf tilawah dan penguatan hafalan mutqin santri.
                </div>
            </div>

        </div>

        {{-- Baris Grafik 2: Sebaran Jilid & Juz Siswa --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
            <div>
                <h4 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <span>📈 GRAFIK SEBARAN JILID &amp; JUZ SISWA</span>
                </h4>
                <p class="text-[11px] text-slate-400">Distribusi pencapaian materi belajar (Tahsin) &amp; hafalan (Tahfidz)</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-2">
                {{-- Kolom Tahsin Jilid --}}
                <div class="space-y-3">
                    <p class="text-xs font-extrabold text-amber-600 uppercase">SEBARAN JILID TAHSIN</p>
                    @foreach($jilidStats as $jName => $jCount)
                    @php
                        $jPct = $tahsinCount > 0 ? round(($jCount / $tahsinCount) * 100) : 0;
                    @endphp
                    <div class="text-xs">
                        <div class="flex justify-between font-bold mb-1">
                            <span class="text-slate-600 dark:text-slate-400">{{ $jName }}</span>
                            <span class="text-slate-500">{{ $jCount }} Santri ({{ $jPct }}%)</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                            <div class="bg-amber-500 h-2 rounded-full" style="width: {{ $jPct }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Kolom Tahfidz Juz --}}
                <div class="space-y-3">
                    <p class="text-xs font-extrabold text-emerald-600 uppercase">SEBARAN JUZ TAHFIDZ</p>
                    @foreach($juzStats as $juzName => $jzCount)
                    @php
                        $jzPct = $tahfidzCount > 0 ? round(($jzCount / $tahfidzCount) * 100) : 0;
                    @endphp
                    <div class="text-xs">
                        <div class="flex justify-between font-bold mb-1">
                            <span class="text-slate-600 dark:text-slate-400">{{ $juzName }}</span>
                            <span class="text-slate-500">{{ $jzCount }} Santri ({{ $jzPct }}%)</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                            <div class="bg-emerald-600 h-2 rounded-full" style="width: {{ $jzPct }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
    @endif

    {{-- ========================================================================= --}}
    {{-- TAB 3: REKAP KEHADIRAN HALAQAH --}}
    {{-- ========================================================================= --}}
    @if($tab === 'attendance')
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-6">
        <div>
            <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                <span>🗓️ REKAPITULASI KEHADIRAN HALAQAH SANTRI (TINGKAT {{ $selectedGrade }})</span>
            </h3>
            <p class="text-xs text-slate-400">Tingkat kehadiran santri dalam mengikuti majelis Al-Qur'an harian</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-center">
                <span class="text-[11px] font-extrabold text-emerald-700 dark:text-emerald-300 uppercase">HADIR</span>
                <p class="text-3xl font-black text-emerald-600 mt-1">{{ $attendanceStats['hadir'] }}</p>
            </div>
            <div class="p-4 rounded-2xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 text-center">
                <span class="text-[11px] font-extrabold text-blue-700 dark:text-blue-300 uppercase">SAKIT</span>
                <p class="text-3xl font-black text-blue-600 mt-1">{{ $attendanceStats['sakit'] }}</p>
            </div>
            <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-center">
                <span class="text-[11px] font-extrabold text-amber-700 dark:text-amber-300 uppercase">IZIN</span>
                <p class="text-3xl font-black text-amber-600 mt-1">{{ $attendanceStats['izin'] }}</p>
            </div>
            <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-center">
                <span class="text-[11px] font-extrabold text-rose-700 dark:text-rose-300 uppercase">ALPA</span>
                <p class="text-3xl font-black text-rose-600 mt-1">{{ $attendanceStats['alpa'] }}</p>
            </div>
        </div>
    </div>
    @endif

    {{-- ========================================================================= --}}
    {{-- MODAL INTUITIF: ATUR / KELOLA KELOMPOK HALAQAH AL-QUR'AN --}}
    {{-- ====================================    <div x-show="isGroupModalOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-hidden bg-slate-900/60 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4"
         x-cloak>
        
        <div @click.away="closeGroupModal()"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
             class="bg-white dark:bg-slate-900 rounded-t-3xl sm:rounded-3xl shadow-2xl border border-slate-200/80 dark:border-slate-800 w-full max-w-4xl h-[92vh] sm:h-[88vh] flex flex-col overflow-hidden">
            
            {{-- Modal Header (Sticky at top) --}}
            <div class="px-4 py-3 sm:px-6 sm:py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3 bg-slate-50/80 dark:bg-slate-800/40 shrink-0 z-10">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2 py-0.5 bg-emerald-600 text-white rounded-md text-[9px] sm:text-[10px] font-black uppercase tracking-wider">
                            Eksklusif Halaqah
                        </span>
                        <h3 class="text-sm sm:text-base font-black text-slate-900 dark:text-white truncate">
                            Atur Kelompok Santri Al-Qur'an
                        </h3>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-0.5 hidden sm:block">
                        Pilih santri untuk kelompok halaqah bersama guru pembimbing. Diatur sekali saja dan dapat diedit kapan saja.
                    </p>
                </div>
                <button type="button" @click="closeGroupModal()"
                        class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-white flex items-center justify-center transition shrink-0 cursor-pointer"
                        title="Tutup Modal">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Modal Scrollable Body: Controls + Student Cards Scroll Together Smoothly --}}
            <div class="flex-1 min-h-0 overflow-y-auto overscroll-contain bg-slate-50/50 dark:bg-slate-900/50 divide-y divide-slate-100 dark:divide-slate-800/60">

                {{-- Section 1: Modal Controls (Tingkat, Guru, Search, Filter) --}}
                <div class="p-3.5 sm:p-5 bg-white dark:bg-slate-900 space-y-3 sm:space-y-4">
                    
                    {{-- Baris 1: Selector Tingkat Kelas (Pill Buttons) --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[10px] sm:text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">
                                PILIH TINGKAT KELAS:
                            </span>
                            <span class="text-[11px] sm:text-xs font-black text-emerald-700 dark:text-emerald-400">
                                Sedang Mengatur: Tingkat Kelas <span x-text="modalGrade"></span>
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-1.5 sm:gap-2">
                            @foreach($availableGrades as $g)
                            <button type="button" @click="switchModalGrade({{ $g }})"
                                    :class="modalGrade === {{ $g }} ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30 ring-2 ring-emerald-500 font-black' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 font-bold'"
                                    class="px-3 py-1.5 sm:px-4 sm:py-2 rounded-xl text-xs transition cursor-pointer">
                                <span>Tingkat {{ $g }}</span>
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Baris 2: Guru Pembimbing (Jika Admin) / Info Guru --}}
                    @if($isAdmin)
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 sm:gap-3 items-center pt-0.5">
                        <div class="sm:col-span-4">
                            <label class="block text-[10px] sm:text-[11px] font-extrabold text-slate-500 uppercase">Guru Pembimbing Halaqah:</label>
                        </div>
                        <div class="sm:col-span-8">
                            <select x-model="modalTeacherId" @change="fetchGroupStudents()"
                                    class="w-full px-3 py-2 border border-indigo-200 dark:border-indigo-800 bg-indigo-50/50 dark:bg-indigo-950/30 text-indigo-950 dark:text-indigo-200 rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-500">
                                @foreach($quranTeachers as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }} (Guru Al-Qur'an)</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @else
                    <div class="flex items-center gap-2 p-2 sm:p-2.5 bg-emerald-50/60 dark:bg-emerald-950/30 rounded-xl border border-emerald-100 dark:border-emerald-800/40 text-xs">
                        <span class="font-extrabold text-emerald-800 dark:text-emerald-300 text-[11px] sm:text-xs">Guru Pembimbing:</span>
                        <span class="font-bold text-slate-700 dark:text-slate-200 text-[11px] sm:text-xs">{{ $activeTeacher->name }}</span>
                    </div>
                    @endif

                    {{-- Baris 3: Live Search, Filter Rombel Asal, & Bulk Actions --}}
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 sm:gap-2.5 pt-0.5">
                        {{-- Search box --}}
                        <div class="sm:col-span-5 relative">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" x-model="searchQuery" placeholder="Cari nama santri atau NISN..."
                                   class="w-full pl-9 pr-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-medium focus:ring-2 focus:ring-emerald-500 outline-none transition">
                        </div>

                        {{-- Filter Rombel Asal Kelas --}}
                        <div class="sm:col-span-4">
                            <select x-model="filterClassId"
                                    class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="">Semua Rombel (Tingkat <span x-text="modalGrade"></span>)</option>
                                <template x-for="c in modalClasses" :key="c.id">
                                    <option :value="c.id" x-text="c.name"></option>
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

                    {{-- Baris 4: Quick Filter Status Pills & Selected Counter --}}
                    <div class="flex flex-wrap items-center justify-between gap-2 pt-0.5">
                        <div class="flex flex-wrap items-center gap-1.5">
                            <button type="button" @click="filterStatus = 'all'"
                                    :class="filterStatus === 'all' ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300'"
                                    class="px-2.5 py-1 rounded-lg text-[10px] sm:text-[11px] font-bold transition cursor-pointer">
                                Semua (<span x-text="allGroupStudents.length"></span>)
                            </button>
                            <button type="button" @click="filterStatus = 'my_group'"
                                    :class="filterStatus === 'my_group' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300'"
                                    class="px-2.5 py-1 rounded-lg text-[10px] sm:text-[11px] font-bold transition cursor-pointer">
                                ✓ Kelompok Ini (<span x-text="selectedStudentIds.length"></span>)
                            </button>
                            <button type="button" @click="filterStatus = 'unassigned'"
                                    :class="filterStatus === 'unassigned' ? 'bg-amber-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300'"
                                    class="px-2.5 py-1 rounded-lg text-[10px] sm:text-[11px] font-bold transition cursor-pointer">
                                Belum Berkelompok
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
                        <p class="text-xs font-bold">Memuat daftar santri Tingkat Kelas <span x-text="modalGrade"></span>...</p>
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
                                    ? 'bg-emerald-50/80 dark:bg-emerald-950/40 border-2 border-emerald-500 shadow-xs'
                                    : 'bg-white dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 hover:border-slate-300 dark:hover:border-slate-600'"
                                 class="p-3 rounded-2xl transition cursor-pointer flex items-center justify-between gap-2.5 select-none">
                                
                                <div class="flex items-center gap-2.5 min-w-0">
                                    {{-- Checkbox Visual --}}
                                    <div :class="isStudentSelected(st.id) ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white dark:bg-slate-700 border-slate-300 dark:border-slate-600 text-transparent'"
                                         class="w-5 h-5 rounded-lg border flex items-center justify-center shrink-0 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </div>

                                    {{-- Avatar / Initials --}}
                                    <div class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 text-emerald-800 dark:text-emerald-200 flex items-center justify-center font-black text-xs shrink-0 uppercase">
                                        <span x-text="st.name.substring(0, 2)"></span>
                                    </div>

                                    {{-- Data Santri --}}
                                    <div class="min-w-0">
                                        <h4 class="text-xs font-black text-slate-900 dark:text-white truncate" x-text="st.name"></h4>
                                        <div class="flex items-center gap-1.5 text-[10px] text-slate-400 mt-0.5">
                                            <span class="font-bold text-slate-600 dark:text-slate-300" x-text="st.class_name"></span>
                                            <span>•</span>
                                            <span>NISN: <span x-text="st.nisn"></span></span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Status Badges --}}
                                <div class="shrink-0 text-right">
                                    <template x-if="isStudentSelected(st.id)">
                                        <span class="px-2 py-0.5 bg-emerald-600 text-white text-[9px] sm:text-[10px] font-black rounded-lg shadow-2xs">
                                            ✓ Di Kelompok
                                        </span>
                                    </template>
                                    <template x-if="!isStudentSelected(st.id) && st.assigned_teacher_name">
                                        <span class="px-2 py-0.5 bg-amber-50 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 text-[9px] sm:text-[10px] font-bold rounded-lg border border-amber-200 dark:border-amber-800"
                                              :title="'Saat ini di kelompok ' + st.assigned_teacher_name + '. Klik untuk pindahkan ke sini.'">
                                            Kelompok: <span x-text="st.assigned_teacher_name"></span>
                                        </span>
                                    </template>
                                    <template x-if="!isStudentSelected(st.id) && !st.assigned_teacher_name">
                                        <span class="px-2 py-0.5 bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400 text-[9px] sm:text-[10px] font-bold rounded-lg">
                                            Belum Ada
                                        </span>
                                    </template>
                                </div>

                            </div>
                        </template>
                    </div>

                </div>

            </div>

            {{-- Modal Footer: Save & Cancel (PERMANENTLY STICKY AT BOTTOM) --}}
            <div class="p-3 sm:p-4 border-t border-slate-200/90 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md flex items-center justify-between gap-2 shrink-0 z-20 shadow-lg">
                <div class="flex items-center gap-1.5 min-w-0">
                    <span class="text-xs font-black text-emerald-700 dark:text-emerald-400 truncate">
                        ✨ <span x-text="selectedStudentIds.length"></span> Santri
                    </span>
                    <span class="text-[10px] text-slate-400 hidden sm:inline">(Tingkat <span x-text="modalGrade"></span>)</span>
                </div>
                
                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" @click="closeGroupModal()"
                            class="px-3.5 py-2 sm:px-4 sm:py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-xs font-bold hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click="saveGroupArrangement()" :disabled="savingGroup"
                            class="px-4 py-2 sm:px-5 sm:py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white rounded-xl text-xs font-black shadow-md shadow-emerald-600/30 transition flex items-center gap-1.5 cursor-pointer">
                        <template x-if="savingGroup">
                            <svg class="animate-spin w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </template>
                        <template x-if="!savingGroup">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </template>
                        <span>Simpan Kelompok (<span x-text="selectedStudentIds.length"></span>)</span>
                    </button>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
