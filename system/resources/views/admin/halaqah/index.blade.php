@extends('layouts.admin')

@section('title', "Halaqah Al-Qur'an - Tahsin & Tahfidz")
@section('page_title', "Halaqah Al-Qur'an")

@section('content')
<div class="space-y-6" x-data="{
    activeTab: '{{ $tab }}',
    evalMode: '{{ $mode }}',
    programType: 'tahsin',
    recordCategory: 'ziyadah',
    tahsinType: 'jilid',
    scoreCognitive: 90,
    scoreAdab: 85,
    selectedJuz: 30,
    lastRecord: null,
    isLoadingLastRecord: false,
    fetchLastRecord(studentId) {
        if (!studentId) {
            this.lastRecord = null;
            return;
        }
        this.isLoadingLastRecord = true;
        fetch(`/admin/halaqah/last-record/${studentId}`)
            .then(res => res.json())
            .then(data => {
                this.lastRecord = data;
                this.isLoadingLastRecord = false;
            })
            .catch(() => {
                this.isLoadingLastRecord = false;
            });
    },
    applySmartAssist() {
        if (!this.lastRecord) return;
        const sug = this.lastRecord.suggested;
        this.programType = sug.program_type || 'tahfidz';
        this.recordCategory = sug.record_category || 'ziyadah';
        if (this.programType === 'tahsin') {
            const jilidSelect = document.querySelector('select[name=\'jilid_level\']');
            if (jilidSelect && sug.jilid_level) jilidSelect.value = sug.jilid_level;
            const pStart = document.querySelector('input[name=\'page_start\']');
            const pEnd = document.querySelector('input[name=\'page_end\']');
            if (pStart && sug.page_start) pStart.value = sug.page_start;
            if (pEnd && sug.page_end) pEnd.value = sug.page_end;
        } else {
            const surahSelect = document.querySelector('select[name=\'surah_name\']');
            if (surahSelect && sug.surah_name) {
                surahSelect.value = sug.surah_name;
                this.selectedJuz = sug.juz_number || 30;
            }
            const aStart = document.querySelector('input[name=\'ayat_start\']');
            const aEnd = document.querySelector('input[name=\'ayat_end\']');
            if (aStart && sug.ayat_start) aStart.value = sug.ayat_start;
            if (aEnd && sug.ayat_end) aEnd.value = sug.ayat_end;
        }
    },
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
    massCategory: 'ziyadah',
    massTahsinType: 'jilid',
    massJilid: 'Jilid 1',
    massStart: 1,
    massEnd: 10,
    massCognitive: 90,
    massAdab: 85,
    massNotes: 'Makhraj & kelancaran tajwid baik',
    applyTemplateToAll() {
        document.querySelectorAll('.mass-program-select').forEach(el => el.value = this.massProgram);
        document.querySelectorAll('.mass-category-select').forEach(el => el.value = this.massCategory);
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
        document.body.style.overflow = 'hidden';
        this.fetchGroupStudents();
    },
    closeGroupModal() {
        this.isGroupModalOpen = false;
        document.body.style.overflow = '';
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
        <div class="flex items-center gap-1.5 w-full sm:w-auto overflow-x-auto pb-1 sm:pb-0">
            <a href="{{ route('admin.halaqah.index', ['tab' => 'input', 'grade' => is_numeric($selectedGrade) ? $selectedGrade : 1, 'mode' => $mode, 'teacher_id' => $activeTeacherId]) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 whitespace-nowrap {{ $tab === 'input' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Input Evaluasi</span>
            </a>

            <a href="{{ route('admin.halaqah.index', array_filter(['tab' => 'history', 'grade' => $filterGrade !== 'all' ? $filterGrade : null, 'teacher_id' => ($isAdmin && $filterTeacherId !== 'all') ? $filterTeacherId : null])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 whitespace-nowrap {{ $tab === 'history' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>History Mengajar</span>
            </a>

            <a href="{{ route('admin.halaqah.index', array_filter(['tab' => 'reports', 'grade' => $filterGrade !== 'all' ? $filterGrade : null, 'teacher_id' => ($isAdmin && $filterTeacherId !== 'all') ? $filterTeacherId : null])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 whitespace-nowrap {{ $tab === 'reports' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>Laporan &amp; Grafik</span>
            </a>

            <a href="{{ route('admin.halaqah.index', array_filter(['tab' => 'attendance', 'grade' => $filterGrade !== 'all' ? $filterGrade : null, 'teacher_id' => ($isAdmin && $filterTeacherId !== 'all') ? $filterTeacherId : null])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 whitespace-nowrap {{ $tab === 'attendance' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Presensi</span>
            </a>

            <a href="{{ route('admin.halaqah.index', ['tab' => 'target', 'grade' => is_numeric($selectedGrade) ? $selectedGrade : 1]) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 whitespace-nowrap {{ $tab === 'target' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span>Target Hafalan</span>
            </a>

            <a href="{{ route('admin.halaqah.index', ['tab' => 'tasmi']) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 whitespace-nowrap {{ $tab === 'tasmi' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                <span>Ujian Tasmi' 1 Juz</span>
            </a>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            {{-- Tombol Atur Kelompok Santri --}}
            <button type="button" @click="openGroupModal({{ is_numeric($selectedGrade) ? $selectedGrade : 1 }})"
                    class="px-3.5 py-1.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-xl text-xs font-black shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>Atur Kelompok Halaqah</span>
            </button>

            <a href="{{ route('admin.halaqah.export-excel', array_filter([
                'grade' => $filterGrade !== 'all' ? $filterGrade : null,
                'class_id' => $filterClassId !== 'all' ? $filterClassId : null,
                'teacher_id' => ($isAdmin && $filterTeacherId !== 'all') ? $filterTeacherId : ($isAdmin ? null : $activeTeacherId),
                'program' => $filterProgram !== 'all' ? $filterProgram : null,
                'jilid' => $filterJilid !== 'all' ? $filterJilid : null,
                'juz' => $filterJuz !== 'all' ? $filterJuz : null,
                'time_filter' => $timeFilter !== 'all' ? $timeFilter : null,
                'date' => $timeFilter === 'daily' ? $selectedDate : null,
                'month' => $timeFilter === 'monthly' ? $selectedMonth : null,
                'year' => $timeFilter === 'monthly' ? $selectedYear : null,
                'date_from' => $timeFilter === 'range' ? $dateFrom : null,
                'date_to' => $timeFilter === 'range' ? $dateTo : null,
                'search_student' => !empty($searchStudent) ? $searchStudent : null,
               ])) }}"
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
                            <select name="student_id" required @change="fetchLastRecord($event.target.value)"
                                    class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white rounded-xl text-xs font-bold focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                                <option value="">-- Pilih Santri ({{ count($students) }} dalam Kelompok Anda) --</option>
                                @foreach($students as $st)
                                    <option value="{{ $st->id }}">
                                        {{ $st->user?->name ?? 'Santri' }} (Kelas: {{ $st->class?->name ?? 'Tingkat ' . $selectedGrade }} • NISN: {{ $st->nisn ?? '-' }})
                                    </option>
                                @endforeach
                            </select>

                            {{-- SMART ASSIST: Capaian Terakhir Santri & Auto-Fill --}}
                            <div x-show="lastRecord" x-transition class="mt-2.5 p-3.5 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-950/40 dark:to-teal-950/40 rounded-2xl border border-emerald-200 dark:border-emerald-800/60 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-black uppercase tracking-wider text-emerald-800 dark:text-emerald-300 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        Smart Assist: Capaian Terakhir Santri
                                    </span>
                                    <template x-if="lastRecord && lastRecord.record">
                                        <span class="text-[10px] font-bold text-slate-500" x-text="lastRecord.record.date"></span>
                                    </template>
                                </div>
                                <template x-if="lastRecord && lastRecord.record">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                        <div class="space-y-0.5">
                                            <p class="text-xs font-black text-slate-800 dark:text-white" x-text="lastRecord.record.material_summary"></p>
                                            <p class="text-[10px] text-slate-500">
                                                Nilai: <strong class="text-emerald-600" x-text="lastRecord.record.score_cognitive"></strong> (<span x-text="lastRecord.record.predicate"></span>) &bull;
                                                Kategori: <span class="capitalize font-bold text-teal-700" x-text="lastRecord.record.record_category"></span>
                                            </p>
                                        </div>
                                        <button type="button" @click="applySmartAssist()"
                                                class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-[11px] font-extrabold shadow-xs transition flex items-center gap-1 cursor-pointer shrink-0 self-start sm:self-center">
                                            <span>⚡ Lanjutkan Otomatis</span>
                                        </button>
                                    </div>
                                </template>
                                <template x-if="lastRecord && !lastRecord.record">
                                    <p class="text-xs text-slate-500 italic">Belum ada riwayat setoran sebelumnya untuk santri ini.</p>
                                </template>
                            </div>
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

                    {{-- Jenis Program Materi (Tahsin, Tahfidz, Tilawah) --}}
                    <div>
                        <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Jenis Program Materi</label>
                        <select name="program_type" x-model="programType"
                                class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white rounded-xl text-xs font-bold focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                            <option value="tahsin">TAHSIN (Metode/Jilid)</option>
                            <option value="tahfidz">TAHFIDZ (Hafalan)</option>
                            <option value="tilawah">TILAWAH (Al-Qur'an)</option>
                        </select>
                    </div>

                    {{-- Detail Lembar Jika Tahsin (HANYA JILID 1 - 6, PILIHAN TILAWAH DIHAPUS) --}}
                    <div x-show="programType === 'tahsin'" class="space-y-3 p-3.5 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 mb-1">Pilihan Jilid / Level</label>
                                <select name="jilid_level" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white rounded-xl text-xs font-bold">
                                    <option value="Jilid 1">Jilid 1</option>
                                    <option value="Jilid 2">Jilid 2</option>
                                    <option value="Jilid 3">Jilid 3</option>
                                    <option value="Jilid 4">Jilid 4</option>
                                    <option value="Jilid 5">Jilid 5</option>
                                    <option value="Jilid 6">Jilid 6</option>
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

                    {{-- Detail Surah & Ayat Jika Tahfidz ATAU Tilawah (Mengikuti Acuan Penilaian Tahfidz) --}}
                    <div x-show="programType === 'tahfidz' || programType === 'tilawah'" class="space-y-3 p-3.5 bg-emerald-50/50 dark:bg-emerald-950/30 rounded-2xl border border-emerald-100 dark:border-emerald-800/40">
                        {{-- Kategori Ziyadah vs Muroja'ah --}}
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1">Kategori Capaian</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="flex items-center justify-center p-2 rounded-xl border cursor-pointer text-xs font-bold transition"
                                       :class="recordCategory === 'ziyadah' ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700'">
                                    <input type="radio" name="record_category" value="ziyadah" x-model="recordCategory" class="sr-only">
                                    <span>🌱 Ziyadah (Hafalan Baru)</span>
                                </label>
                                <label class="flex items-center justify-center p-2 rounded-xl border cursor-pointer text-xs font-bold transition"
                                       :class="recordCategory === 'murojaah' ? 'bg-teal-600 text-white border-teal-600 shadow-xs' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700'">
                                    <input type="radio" name="record_category" value="murojaah" x-model="recordCategory" class="sr-only">
                                    <span>🔁 Muroja'ah (Pengulangan)</span>
                                </label>
                            </div>
                        </div>

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
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold {{ $rec->program_type === 'tahsin' ? 'bg-amber-100 text-amber-800' : ($rec->program_type === 'tilawah' ? 'bg-teal-100 text-teal-800' : 'bg-emerald-100 text-emerald-800') }}">
                                        {{ strtoupper($rec->program_type) }}
                                    </span>
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                        {{ $rec->record_category === 'murojaah' ? "Mur." : "Ziy." }}
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
                                    <a href="{{ route('admin.halaqah.send-wa', $rec->id) }}" target="_blank"
                                       class="p-1 text-emerald-600 hover:text-emerald-800 transition inline-block mr-1" title="Kirim Resume WA ke Orang Tua">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    </a>
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
                                <option value="tilawah">TILAWAH</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1">Jilid / Level</label>
                            <select x-model="massJilid" class="w-full px-2.5 py-1.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg font-bold">
                                <option value="Jilid 1">Jilid 1</option>
                                <option value="Jilid 2">Jilid 2</option>
                                <option value="Jilid 3">Jilid 3</option>
                                <option value="Jilid 4">Jilid 4</option>
                                <option value="Jilid 5">Jilid 5</option>
                                <option value="Jilid 6">Jilid 6</option>
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
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <select name="items[{{ $st->id }}][program_type]" class="mass-program-select px-2 py-1.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg text-xs font-bold">
                                                <option value="tahsin">Tahsin</option>
                                                <option value="tahfidz">Tahfidz</option>
                                                <option value="tilawah">Tilawah</option>
                                            </select>
                                            <select name="items[{{ $st->id }}][record_category]" class="mass-category-select px-2 py-1.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg text-xs font-bold">
                                                <option value="ziyadah">Ziyadah</option>
                                                <option value="murojaah">Muroja'ah</option>
                                            </select>
                                            <select name="items[{{ $st->id }}][jilid_level]" class="mass-jilid-select px-2 py-1.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg text-xs font-bold">
                                                <option value="Jilid 1">Jilid 1</option>
                                                <option value="Jilid 2">Jilid 2</option>
                                                <option value="Jilid 3">Jilid 3</option>
                                                <option value="Jilid 4">Jilid 4</option>
                                                <option value="Jilid 5">Jilid 5</option>
                                                <option value="Jilid 6">Jilid 6</option>
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
    {{-- TAB BARU: RIWAYAT & HISTORY MENGAJAR HALAQAH --}}
    {{-- ========================================================================= --}}
    @if($tab === 'history')
    <div class="space-y-6">
        
        {{-- Header Tab History Mengajar --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 bg-emerald-600 text-white rounded-lg text-[10px] font-black uppercase tracking-wider">
                        Logbook Mengajar
                    </span>
                    <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">
                        History &amp; Catatan Mengajar Halaqah
                    </h2>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Rekapitulasi lengkap riwayat pembelajaran, setoran ayat/jilid, kehadiran, dan penilaian santri oleh Guru Pembimbing.
                </p>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('admin.halaqah.export-excel', ['grade' => $selectedGrade, 'teacher_id' => $activeTeacherId]) }}"
                   class="px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 rounded-xl text-xs font-bold border border-emerald-200 dark:border-emerald-800 transition inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Export Rekap Excel</span>
                </a>
                <a href="{{ route('admin.halaqah.index', ['tab' => 'input', 'grade' => $selectedGrade, 'teacher_id' => $activeTeacherId]) }}"
                   class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black shadow-xs transition inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Input Nilai Baru</span>
                </a>
            </div>
        </div>

        {{-- 4 Kartu Metrik Ringkasan History Mengajar --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800 shadow-xs">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Total Bimbingan</span>
                <p class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">{{ $totalTeachingCount }}</p>
                <span class="text-[11px] font-bold text-emerald-600">Kali Setoran Terdata</span>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800 shadow-xs">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Santri Terbimbing</span>
                <p class="text-2xl sm:text-3xl font-black text-emerald-600 mt-1">{{ $historyUniqueStudentsCount }}</p>
                <span class="text-[11px] font-bold text-slate-500">Santri Unik Berbeda</span>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800 shadow-xs">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Rata-Rata Nilai</span>
                <p class="text-2xl sm:text-3xl font-black text-indigo-600 mt-1">{{ $historyAvgScore }}</p>
                <span class="text-[11px] font-bold text-indigo-500">Skala 0 - 100</span>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800 shadow-xs">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Kelancaran Unggul</span>
                <p class="text-2xl sm:text-3xl font-black text-teal-600 mt-1">{{ $historyExcellentPct }}%</p>
                <span class="text-[11px] font-bold text-teal-600">Mumtaz &amp; Jayyid Jiddan</span>
            </div>
        </div>

        {{-- Filter & Search Form --}}
        @include('admin.halaqah.partials.filter-bar')

        {{-- Tabel & List History Mengajar --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
            
            {{-- Header Card --}}
            <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs sm:text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">
                        Daftar Riwayat Mengajar
                    </span>
                    <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-md text-[10px] font-bold">
                        {{ $totalTeachingCount }} Catatan
                    </span>
                </div>
                @if($isAdmin)
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 hidden sm:inline">
                    @if($filterTeacherId === 'all')
                        👑 Mode Admin: Menampilkan riwayat seluruh guru pembimbing
                    @else
                        Mode Admin: Menampilkan catatan Ust. {{ $quranTeachers->firstWhere('id', $filterTeacherId)?->name ?? $activeTeacher->name }}
                    @endif
                </span>
                @endif
            </div>

            @if($historyTeachingRecords->count() > 0)
                {{-- Tampilan Desktop (Table) --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-wider bg-slate-50/80 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                                <th class="py-3 px-4">Tanggal</th>
                                <th class="py-3 px-4">Santri</th>
                                <th class="py-3 px-4">Tingkat/Kelas</th>
                                @if($isAdmin)
                                <th class="py-3 px-4">Guru Pembimbing</th>
                                @endif
                                <th class="py-3 px-4">Program &amp; Materi</th>
                                <th class="py-3 px-4 text-center">Kehadiran</th>
                                <th class="py-3 px-4 text-center">Nilai</th>
                                <th class="py-3 px-4 text-center">Predikat</th>
                                <th class="py-3 px-4">Catatan Musyrif</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($historyTeachingRecords as $item)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="font-extrabold text-slate-800 dark:text-white">{{ $item->assessment_date->format('d/m/Y') }}</span>
                                    <p class="text-[10px] text-slate-400 font-medium">{{ $item->assessment_date->isoFormat('dddd') }}</p>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 text-emerald-800 dark:text-emerald-200 flex items-center justify-center font-black text-[10px] uppercase shrink-0">
                                            {{ substr($item->student?->user?->name ?? 'S', 0, 2) }}
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="font-black text-slate-900 dark:text-white truncate">{{ $item->student?->user?->name ?? 'Santri' }}</h4>
                                            <span class="text-[10px] text-slate-400">NISN: {{ $item->student?->nisn ?? '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="font-bold text-slate-700 dark:text-slate-300">
                                        Tingkat {{ $item->grade ?: ($item->class?->grade ?: 'Kelas') }}
                                    </span>
                                    @if($item->class)
                                        <p class="text-[10px] text-slate-400">{{ $item->class->name }}</p>
                                    @endif
                                </td>
                                @if($isAdmin)
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-extrabold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                        Ust. {{ $item->teacher?->name ?? 'Guru' }}
                                    </span>
                                </td>
                                @endif
                                <td class="py-3 px-4">
                                    @if($item->program_type === 'tahsin')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 text-[10px] font-black uppercase">
                                            Tahsin
                                        </span>
                                        <p class="text-xs font-bold text-slate-800 dark:text-white mt-0.5">
                                            {{ $item->jilid_level ?? 'Jilid' }}
                                            @if($item->page_start && $item->page_end)
                                                <span class="text-slate-400 font-normal">• hl. {{ $item->page_start }} - {{ $item->page_end }}</span>
                                            @elseif($item->page_start)
                                                <span class="text-slate-400 font-normal">• hl. {{ $item->page_start }}</span>
                                            @endif
                                        </p>
                                    @elseif($item->program_type === 'tilawah')
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-sky-50 text-sky-800 dark:bg-sky-950/60 dark:text-sky-300 text-[10px] font-black uppercase">
                                                Tilawah
                                            </span>
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold {{ $item->record_category === 'murojaah' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300' }}">
                                                {{ $item->record_category === 'murojaah' ? 'Muroja\'ah' : 'Ziyadah' }}
                                            </span>
                                        </div>
                                        <p class="text-xs font-bold text-slate-800 dark:text-white mt-0.5">
                                            Surah {{ $item->surah_name ?? '-' }}
                                            @if($item->ayat_start && $item->ayat_end)
                                                <span class="text-slate-400 font-normal">({{ $item->ayat_start }}-{{ $item->ayat_end }})</span>
                                            @endif
                                            @if($item->juz_number)
                                                <span class="px-1.5 py-0.2 bg-sky-100 dark:bg-sky-900/60 text-sky-800 dark:text-sky-200 text-[9px] rounded font-extrabold ml-1">Juz {{ $item->juz_number }}</span>
                                            @endif
                                        </p>
                                    @else
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-teal-50 text-teal-800 dark:bg-teal-950/60 dark:text-teal-300 text-[10px] font-black uppercase">
                                                Tahfidz
                                            </span>
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold {{ $item->record_category === 'murojaah' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300' }}">
                                                {{ $item->record_category === 'murojaah' ? 'Muroja\'ah' : 'Ziyadah' }}
                                            </span>
                                        </div>
                                        <p class="text-xs font-bold text-slate-800 dark:text-white mt-0.5">
                                            Surah {{ $item->surah_name ?? '-' }}
                                            @if($item->ayat_start && $item->ayat_end)
                                                <span class="text-slate-400 font-normal">({{ $item->ayat_start }}-{{ $item->ayat_end }})</span>
                                            @endif
                                            @if($item->juz_number)
                                                <span class="px-1.5 py-0.2 bg-teal-100 dark:bg-teal-900/60 text-teal-800 dark:text-teal-200 text-[9px] rounded font-extrabold ml-1">Juz {{ $item->juz_number }}</span>
                                            @endif
                                        </p>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    @if($item->attendance_status === 'hadir')
                                        <span class="px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 text-[10px] font-extrabold border border-emerald-200 dark:border-emerald-800">
                                            Hadir
                                        </span>
                                    @elseif($item->attendance_status === 'sakit')
                                        <span class="px-2 py-0.5 rounded-lg bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 text-[10px] font-extrabold border border-blue-200 dark:border-blue-800">
                                            Sakit
                                        </span>
                                    @elseif($item->attendance_status === 'izin')
                                        <span class="px-2 py-0.5 rounded-lg bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 text-[10px] font-extrabold border border-amber-200 dark:border-amber-800">
                                            Izin
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-lg bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 text-[10px] font-extrabold border border-rose-200 dark:border-rose-800">
                                            Alpa
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1 text-xs">
                                        <span class="font-black text-emerald-600">{{ number_format($item->score_cognitive, 0) }}</span>
                                        <span class="text-slate-300">/</span>
                                        <span class="font-bold text-slate-400 text-[11px]">{{ number_format($item->score_adab, 0) }}</span>
                                    </div>
                                    <p class="text-[9px] text-slate-400">Kog / Adab</p>
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    @php
                                        $predClass = match($item->predicate) {
                                            'Mumtaz' => 'bg-emerald-600 text-white',
                                            'Jayyid Jiddan' => 'bg-teal-600 text-white',
                                            'Jayyid' => 'bg-blue-600 text-white',
                                            default => 'bg-amber-500 text-white',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-black {{ $predClass }}">
                                        {{ $item->predicate }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 max-w-xs truncate text-[11px] text-slate-500 dark:text-slate-400">
                                    {{ $item->teacher_notes ?: '-' }}
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1">
                                        <a href="{{ route('admin.halaqah.send-wa', $item->id) }}" target="_blank"
                                           class="p-1.5 text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 rounded-lg transition"
                                           title="Kirim Resume via WhatsApp">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2zm.01 16.74c-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31c-.82-1.31-1.26-2.83-1.26-4.38 0-4.54 3.7-8.24 8.24-8.24 2.2 0 4.27.86 5.82 2.42a8.18 8.18 0 012.41 5.83c.02 4.54-3.68 8.23-8.22 8.23zm4.52-6.17c-.25-.12-1.47-.72-1.7-.81-.23-.08-.39-.12-.56.12-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.12-1.05-.39-1.99-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.38.11-.5.11-.11.25-.29.37-.43.12-.14.17-.25.25-.41.08-.17.04-.31-.02-.43s-.56-1.34-.76-1.84c-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.43.06-.66.31-.22.25-.86.84-.86 2.05s.88 2.38 1 2.55c.12.17 1.73 2.65 4.2 3.71.59.25 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.15-1.18-.06-.12-.22-.19-.47-.31z"/></svg>
                                        </a>
                                        <form action="{{ route('admin.halaqah.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus riwayat penilaian halaqah ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/40 transition" title="Hapus Catatan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Tampilan Mobile (Cards Responsive) --}}
                <div class="md:hidden divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($historyTeachingRecords as $item)
                    <div class="p-4 space-y-2.5">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-black text-slate-900 dark:text-white">
                                    {{ $item->assessment_date->format('d/m/Y') }}
                                </span>
                                <span class="text-[10px] text-slate-400 font-bold">({{ $item->assessment_date->isoFormat('dddd') }})</span>
                            </div>
                            <span class="px-2 py-0.5 rounded-md text-[9px] font-black {{ $item->predicate === 'Mumtaz' ? 'bg-emerald-600 text-white' : ($item->predicate === 'Jayyid Jiddan' ? 'bg-teal-600 text-white' : 'bg-blue-600 text-white') }}">
                                {{ $item->predicate }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-2">
                            <div class="min-w-0">
                                <h4 class="text-xs font-black text-slate-900 dark:text-white truncate">
                                    {{ $item->student?->user?->name ?? 'Santri' }}
                                </h4>
                                <p class="text-[10px] text-slate-400">
                                    Tingkat {{ $item->grade ?: ($item->class?->grade ?: 'Kelas') }} • {{ $item->class?->name ?? '-' }}
                                    @if($isAdmin)
                                        • <span class="text-indigo-600 dark:text-indigo-400 font-bold">Ust. {{ $item->teacher?->name ?? '-' }}</span>
                                    @endif
                                </p>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-xs font-black text-emerald-600">{{ number_format($item->score_cognitive, 0) }}</span>
                                <span class="text-[10px] text-slate-400">/ 100</span>
                            </div>
                        </div>

                        <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl flex items-center justify-between text-xs">
                            <div>
                                <span class="text-[10px] font-extrabold uppercase text-slate-400">{{ $item->program_type }} ({{ $item->record_category ?? 'ziyadah' }}):</span>
                                <span class="font-bold text-slate-700 dark:text-slate-200">
                                    @if($item->program_type === 'tahsin')
                                        {{ $item->jilid_level ?? 'Jilid' }} (hl. {{ $item->page_start ?? 1 }}-{{ $item->page_end ?? '-' }})
                                    @else
                                        Surah {{ $item->surah_name }} (ayat {{ $item->ayat_start ?? 1 }}-{{ $item->ayat_end ?? '-' }})
                                    @endif
                                </span>
                            </div>
                            <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase {{ $item->attendance_status === 'hadir' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300' }}">
                                {{ $item->attendance_status }}
                            </span>
                        </div>

                        @if($item->teacher_notes)
                        <p class="text-[11px] text-slate-500 italic bg-amber-50/50 dark:bg-amber-950/30 p-2 rounded-lg border border-amber-100 dark:border-amber-900/40">
                            "{{ $item->teacher_notes }}"
                        </p>
                        @endif

                        <div class="flex items-center justify-between pt-1">
                            <a href="{{ route('admin.halaqah.send-wa', $item->id) }}" target="_blank"
                               class="text-[11px] font-bold text-emerald-600 hover:underline flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2z"/></svg>
                                <span>Kirim Resume WA</span>
                            </a>
                            <form action="{{ route('admin.halaqah.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus riwayat penilaian ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-[11px] font-bold text-rose-600 hover:underline flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span>Hapus</span>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Pagination Links --}}
                <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                    {{ $historyTeachingRecords->links() }}
                </div>
            @else
                {{-- Empty State History --}}
                <div class="p-12 text-center space-y-3">
                    <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 mx-auto flex items-center justify-center">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <h4 class="text-sm font-black text-slate-800 dark:text-white uppercase">Belum Ada Riwayat Mengajar</h4>
                    <p class="text-xs text-slate-400 max-w-md mx-auto">
                        Belum ada catatan penilaian halaqah yang tersimpan atau sesuai dengan filter pencarian yang Anda pilih.
                    </p>
                    <div class="pt-2">
                        <a href="{{ route('admin.halaqah.index', ['tab' => 'input', 'grade' => $selectedGrade, 'teacher_id' => $activeTeacherId]) }}"
                           class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black shadow transition inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Input Penilaian Santri Sekarang</span>
                        </a>
                    </div>
                </div>
            @endif

        </div>

    </div>
    @endif

    {{-- ========================================================================= --}}
    {{-- TAB 2: LAPORAN HARIAN & BULANAN + GRAFIK STATISTIK --}}
    {{-- ========================================================================= --}}
    {{-- ========================================================================= --}}
    {{-- TAB 3: LAPORAN HARIAN & BULANAN + GRAFIK STATISTIK --}}
    {{-- ========================================================================= --}}
    @if($tab === 'reports')
    <div class="space-y-6">
        
        {{-- Header Tab Laporan --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 bg-indigo-600 text-white rounded-lg text-[10px] font-black uppercase tracking-wider">
                        Analitik &amp; Laporan
                    </span>
                    <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">
                        Laporan Harian &amp; Bulanan + Grafik Statistik
                    </h2>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Analisis komprehensif capaian bimbingan Al-Qur'an santri, distribusi jilid, juz tahfidz, dan mutu kelancaran bacaan.
                </p>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('admin.halaqah.export-excel', array_filter([
                    'grade' => $filterGrade !== 'all' ? $filterGrade : null,
                    'class_id' => $filterClassId !== 'all' ? $filterClassId : null,
                    'teacher_id' => ($isAdmin && $filterTeacherId !== 'all') ? $filterTeacherId : ($isAdmin ? null : $activeTeacherId),
                    'program' => $filterProgram !== 'all' ? $filterProgram : null,
                    'jilid' => $filterJilid !== 'all' ? $filterJilid : null,
                    'juz' => $filterJuz !== 'all' ? $filterJuz : null,
                    'time_filter' => $timeFilter !== 'all' ? $timeFilter : null,
                    'date' => $timeFilter === 'daily' ? $selectedDate : null,
                    'month' => $timeFilter === 'monthly' ? $selectedMonth : null,
                    'year' => $timeFilter === 'monthly' ? $selectedYear : null,
                    'date_from' => $timeFilter === 'range' ? $dateFrom : null,
                    'date_to' => $timeFilter === 'range' ? $dateTo : null,
                    'search_student' => !empty($searchStudent) ? $searchStudent : null,
                   ])) }}"
                   class="px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 rounded-xl text-xs font-bold border border-emerald-200 dark:border-emerald-800 transition inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Export Laporan Excel</span>
                </a>
            </div>
        </div>

        {{-- Menu Filter Komprehensif --}}
        @include('admin.halaqah.partials.filter-bar')

        {{-- Early Warning System: Santri Butuh Pendampingan / Mandek > 7 Hari / Predikat Maqbul --}}
        @if(isset($attentionStudents) && $attentionStudents->count() > 0)
        <div class="bg-gradient-to-r from-rose-50 to-amber-50 dark:from-rose-950/40 dark:to-amber-950/30 rounded-3xl p-5 sm:p-6 border border-rose-200/80 dark:border-rose-900/60 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-rose-500 text-white flex items-center justify-center shrink-0 shadow-md shadow-rose-500/20">
                        <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-rose-900 dark:text-rose-200 uppercase tracking-wide flex items-center gap-2">
                            <span>Early Warning System Halaqah</span>
                            <span class="px-2 py-0.5 rounded-full bg-rose-600 text-white text-[10px] font-extrabold">{{ $attentionStudents->count() }} Santri</span>
                        </h3>
                        <p class="text-xs text-rose-700 dark:text-rose-300">
                            Santri yang belum setor hafalan/tilawah > 7 hari atau evaluasi capaian terakhir berpredikat <strong>Maqbul</strong> (butuh bimbingan intensif).
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($attentionStudents->take(6) as $attn)
                @php
                    $stObj = is_array($attn) ? ($attn['student'] ?? null) : ($attn->student ?? $attn);
                    $lastRec = is_array($attn) ? ($attn['last_record'] ?? null) : ($attn->last_record ?? null);
                    $reason = is_array($attn) ? ($attn['reason'] ?? '') : ($attn->reason ?? '');
                    $daysSince = is_array($attn) ? ($attn['days_inactive'] ?? null) : ($attn->days_inactive ?? null);
                    $parentPhone = $stObj?->user?->phone ?? ($stObj?->parent_phone ?? null);
                @endphp
                <div class="p-3.5 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xs rounded-2xl border border-rose-200/60 dark:border-rose-900/40 flex items-center justify-between gap-3 shadow-2xs">
                    <div class="min-w-0">
                        <h4 class="text-xs font-black text-slate-900 dark:text-white truncate">
                            {{ $stObj?->user?->name ?? 'Santri' }}
                        </h4>
                        <p class="text-[10px] text-slate-400">
                            {{ $stObj?->class?->name ?? 'Kelas' }} • 
                            <span class="text-rose-600 font-bold">{{ $reason }}</span>
                        </p>
                    </div>

                    <div class="flex items-center gap-1 shrink-0">
                        <a href="{{ route('admin.halaqah.index', ['tab' => 'input', 'grade' => $stObj?->class?->grade ?? 1, 'student_id' => $stObj?->id]) }}"
                           class="px-2 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 rounded-lg text-[10px] font-black transition"
                           title="Input Nilai Baru">
                            Input
                        </a>
                        @if($parentPhone)
                        <a href="https://api.whatsapp.com/send?phone={{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $parentPhone)) }}&text={{ urlencode('Assalamu\'alaikum Wr. Wb. Kami dari tim Halaqah menginfokan perkembangan Al-Qur\'an ananda ' . ($stObj?->user?->name ?? 'Santri') . '...') }}"
                           target="_blank"
                           class="p-1.5 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 rounded-lg transition"
                           title="Hubungi Wali Murid">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2z"/></svg>
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @if($attentionStudents->count() > 6)
            <p class="text-[11px] text-rose-600 dark:text-rose-400 font-bold text-center">
                + {{ $attentionStudents->count() - 6 }} santri lainnya memerlukan perhatian intensif
            </p>
            @endif
        </div>
        @endif

        {{-- 4 Kartu KPI Ringkasan Laporan --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-1">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">TOTAL KEAKTIFAN SELESAI</span>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $totalSetoran }}</span>
                    <span class="text-xs text-slate-400 font-bold">Kali Setoran</span>
                </div>
                <div class="pt-2 flex items-center justify-between text-[10px] font-bold border-t border-slate-100 dark:border-slate-800 flex-wrap gap-1">
                    <span class="text-amber-600">Tahsin: {{ $tahsinCount }}</span>
                    <span class="text-teal-600">Tahfidz: {{ $tahfidzCount }}</span>
                    <span class="text-sky-600">Tilawah: {{ $tilawahCount ?? 0 }}</span>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-1">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">NILAI EVALUASI RATA-RATA</span>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ $avgScore }}</span>
                    <span class="text-xs text-slate-400 font-bold">/ 100</span>
                </div>
                <div class="pt-2 flex items-center justify-between text-[11px] font-bold border-t border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400">Predikat:</span>
                    <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 rounded text-[10px] font-black">
                        {{ \App\Models\HalaqahRecord::calculatePredicate($avgScore) }}
                    </span>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-1">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">KELANCARAN EXCELLENT</span>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-indigo-600 dark:text-indigo-400">{{ $mumtazPercentage }}%</span>
                    <span class="text-xs text-slate-400 font-bold">Mumtaz</span>
                </div>
                <div class="pt-2 flex items-center justify-between text-[11px] font-bold border-t border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400">Standar Mumtaz:</span>
                    <span class="text-indigo-600 dark:text-indigo-400 font-black">&gt;= 90</span>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-1">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">SANTRI TERBIMBING</span>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-teal-600 dark:text-teal-400">{{ $reportUniqueStudentsCount }}</span>
                    <span class="text-xs text-slate-400 font-bold">Santri Aktif</span>
                </div>
                <div class="pt-2 flex items-center justify-between text-[11px] font-bold border-t border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400">Tercatat:</span>
                    <span class="text-teal-600 dark:text-teal-400 font-black">Terdata Aktif</span>
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

            {{-- Card Pembagian Fokus Program (Tahsin vs Tahfidz vs Tilawah) --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                <div>
                    <h4 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <span>🎯 PEMBAGIAN FOKUS PROGRAM</span>
                    </h4>
                    <p class="text-[11px] text-slate-400">Komparasi keaktifan bimbingan membaca Al-Qur'an (Tahsin), hafalan (Tahfidz), dan Tilawah</p>
                </div>

                <div class="grid grid-cols-3 gap-3 pt-2">
                    <div class="p-3 bg-amber-50 dark:bg-amber-950/30 rounded-2xl border border-amber-200 dark:border-amber-900/50 text-center space-y-1">
                        <span class="px-2 py-0.5 rounded text-[9px] font-extrabold bg-amber-100 text-amber-800 uppercase">Tahsin</span>
                        <div class="text-2xl sm:text-3xl font-black text-amber-600">{{ $tahsinCount }}</div>
                        <p class="text-[10px] text-slate-500 font-bold">Jilid 1-6</p>
                    </div>

                    <div class="p-3 bg-teal-50 dark:bg-teal-950/30 rounded-2xl border border-teal-200 dark:border-teal-900/50 text-center space-y-1">
                        <span class="px-2 py-0.5 rounded text-[9px] font-extrabold bg-teal-100 text-teal-800 uppercase">Tahfidz</span>
                        <div class="text-2xl sm:text-3xl font-black text-teal-600">{{ $tahfidzCount }}</div>
                        <p class="text-[10px] text-slate-500 font-bold">Ziyadah &amp; Mur.</p>
                    </div>

                    <div class="p-3 bg-sky-50 dark:bg-sky-950/30 rounded-2xl border border-sky-200 dark:border-sky-900/50 text-center space-y-1">
                        <span class="px-2 py-0.5 rounded text-[9px] font-extrabold bg-sky-100 text-sky-800 uppercase">Tilawah</span>
                        <div class="text-2xl sm:text-3xl font-black text-sky-600">{{ $tilawahCount ?? 0 }}</div>
                        <p class="text-[10px] text-slate-500 font-bold">Surah &amp; Juz</p>
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

        {{-- TABEL REKAPITULASI CAPAIAN PEMBELAJARAN SANTRI (DATA TERATAS & DETAIL) --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs sm:text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">
                        Rekapitulasi Capaian &amp; Keaktifan Santri
                    </span>
                    <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 rounded-md text-[10px] font-bold">
                        {{ $studentReportList->total() }} Santri
                    </span>
                </div>
            </div>

            @if($studentReportList->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="text-[10px] font-black text-slate-400 uppercase tracking-wider bg-slate-50/80 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                            <th class="py-3 px-4 text-center">No</th>
                            <th class="py-3 px-4">Santri</th>
                            <th class="py-3 px-4">Kelas Asal</th>
                            <th class="py-3 px-4 text-center">Total Setoran</th>
                            <th class="py-3 px-4 text-center">Rata-rata Nilai</th>
                            <th class="py-3 px-4 text-center">Predikat</th>
                            <th class="py-3 px-4">Setoran Terakhir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($studentReportList as $idx => $stRep)
                        @php
                            $studentObj = $stRep->student;
                            $pred = \App\Models\HalaqahRecord::calculatePredicate($stRep->avg_score);
                            $predBadge = match($pred) {
                                'Mumtaz' => 'bg-emerald-600 text-white',
                                'Jayyid Jiddan' => 'bg-teal-600 text-white',
                                'Jayyid' => 'bg-blue-600 text-white',
                                default => 'bg-amber-500 text-white',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3 px-4 text-center font-bold text-slate-400">
                                {{ $studentReportList->firstItem() + $idx }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 text-indigo-800 dark:text-indigo-200 flex items-center justify-center font-black text-[10px] uppercase shrink-0">
                                        {{ substr($studentObj?->user?->name ?? 'S', 0, 2) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="font-black text-slate-900 dark:text-white truncate">{{ $studentObj?->user?->name ?? 'Santri' }}</h4>
                                        <span class="text-[10px] text-slate-400">NISN: {{ $studentObj?->nisn ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="font-bold text-slate-700 dark:text-slate-300">{{ $studentObj?->class?->name ?? '-' }}</span>
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 font-extrabold rounded-lg text-xs">
                                    {{ $stRep->total_setoran }}x Setoran
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap font-black text-slate-800 dark:text-white text-xs">
                                {{ $stRep->avg_score }}
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-black {{ $predBadge }}">
                                    {{ $pred }}
                                </span>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap text-slate-500 font-medium">
                                {{ $stRep->last_date ? \Carbon\Carbon::parse($stRep->last_date)->format('d/m/Y') : '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $studentReportList->links() }}
            </div>
            @else
            <div class="p-12 text-center text-slate-400">
                <p class="text-xs font-bold">Tidak ada data capaian santri yang sesuai dengan filter yang dipilih.</p>
            </div>
            @endif
        </div>

    </div>
    @endif

    {{-- ========================================================================= --}}
    {{-- TAB 4: REKAP KEHADIRAN HALAQAH --}}
    {{-- ========================================================================= --}}
    @if($tab === 'attendance')
    <div class="space-y-6">
        
        {{-- Header Tab Kehadiran --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 bg-emerald-600 text-white rounded-lg text-[10px] font-black uppercase tracking-wider">
                        Presensi Halaqah
                    </span>
                    <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">
                        Rekapitulasi Kehadiran Santri Al-Qur'an
                    </h2>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Pemantauan kedisiplinan dan absensi harian santri dalam majelis Halaqah Tahsin dan Tahfidz.
                </p>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('admin.halaqah.export-excel', array_filter([
                    'grade' => $filterGrade !== 'all' ? $filterGrade : null,
                    'class_id' => $filterClassId !== 'all' ? $filterClassId : null,
                    'teacher_id' => ($isAdmin && $filterTeacherId !== 'all') ? $filterTeacherId : ($isAdmin ? null : $activeTeacherId),
                    'program' => $filterProgram !== 'all' ? $filterProgram : null,
                    'jilid' => $filterJilid !== 'all' ? $filterJilid : null,
                    'juz' => $filterJuz !== 'all' ? $filterJuz : null,
                    'time_filter' => $timeFilter !== 'all' ? $timeFilter : null,
                    'date' => $timeFilter === 'daily' ? $selectedDate : null,
                    'month' => $timeFilter === 'monthly' ? $selectedMonth : null,
                    'year' => $timeFilter === 'monthly' ? $selectedYear : null,
                    'date_from' => $timeFilter === 'range' ? $dateFrom : null,
                    'date_to' => $timeFilter === 'range' ? $dateTo : null,
                    'search_student' => !empty($searchStudent) ? $searchStudent : null,
                   ])) }}"
                   class="px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 rounded-xl text-xs font-bold border border-emerald-200 dark:border-emerald-800 transition inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Export Rekap Excel</span>
                </a>
            </div>
        </div>

        {{-- Menu Filter Komprehensif --}}
        @include('admin.halaqah.partials.filter-bar')

        {{-- 5 Kartu Ringkasan Kehadiran --}}
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 sm:gap-4">
            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-center">
                <span class="text-[10px] font-extrabold text-emerald-700 dark:text-emerald-300 uppercase tracking-wider">HADIR</span>
                <p class="text-2xl sm:text-3xl font-black text-emerald-600 mt-1">{{ $attendanceStats['hadir'] }}</p>
                <span class="text-[10px] text-emerald-700 font-bold">{{ $attendancePercentage }}% dari total</span>
            </div>
            <div class="p-4 rounded-2xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 text-center">
                <span class="text-[10px] font-extrabold text-blue-700 dark:text-blue-300 uppercase tracking-wider">SAKIT</span>
                <p class="text-2xl sm:text-3xl font-black text-blue-600 mt-1">{{ $attendanceStats['sakit'] }}</p>
                <span class="text-[10px] text-blue-700 font-bold">Surat Dokter / Izin</span>
            </div>
            <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-center">
                <span class="text-[10px] font-extrabold text-amber-700 dark:text-amber-300 uppercase tracking-wider">IZIN</span>
                <p class="text-2xl sm:text-3xl font-black text-amber-600 mt-1">{{ $attendanceStats['izin'] }}</p>
                <span class="text-[10px] text-amber-700 font-bold">Pemberitahuan Wali</span>
            </div>
            <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-center">
                <span class="text-[10px] font-extrabold text-rose-700 dark:text-rose-300 uppercase tracking-wider">ALPA</span>
                <p class="text-2xl sm:text-3xl font-black text-rose-600 mt-1">{{ $attendanceStats['alpa'] }}</p>
                <span class="text-[10px] text-rose-700 font-bold">Tanpa Keterangan</span>
            </div>
            <div class="p-4 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 text-center col-span-2 sm:col-span-1">
                <span class="text-[10px] font-extrabold text-indigo-700 dark:text-indigo-300 uppercase tracking-wider">TOTAL PRESENSI</span>
                <p class="text-2xl sm:text-3xl font-black text-indigo-600 mt-1">{{ $totalAttendanceEntries }}</p>
                <span class="text-[10px] text-indigo-700 font-bold">Catatan Masuk</span>
            </div>
        </div>

        {{-- Tabel Rekapitulasi Presensi Per Santri --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs sm:text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">
                        Rincian Kehadiran Per Santri
                    </span>
                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 rounded-md text-[10px] font-bold">
                        {{ $studentAttendanceList->total() }} Santri
                    </span>
                </div>
            </div>

            @if($studentAttendanceList->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="text-[10px] font-black text-slate-400 uppercase tracking-wider bg-slate-50/80 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                            <th class="py-3 px-4 text-center">No</th>
                            <th class="py-3 px-4">Santri</th>
                            <th class="py-3 px-4">Kelas</th>
                            <th class="py-3 px-4 text-center">Hadir (H)</th>
                            <th class="py-3 px-4 text-center">Sakit (S)</th>
                            <th class="py-3 px-4 text-center">Izin (I)</th>
                            <th class="py-3 px-4 text-center">Alpa (A)</th>
                            <th class="py-3 px-4 text-center">Total Pertemuan</th>
                            <th class="py-3 px-4 text-center">% Kehadiran</th>
                            <th class="py-3 px-4 text-center">Status Kelayakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($studentAttendanceList as $idx => $stAtt)
                        @php
                            $stObj = $stAtt->student;
                            $pct = $stAtt->total_meetings > 0 ? round(($stAtt->count_hadir / $stAtt->total_meetings) * 100) : 0;
                            $statusBadge = $pct >= 85 
                                ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800'
                                : ($pct >= 70 ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200 dark:border-amber-800' : 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border-rose-200 dark:border-rose-800');
                            $statusLabel = $pct >= 85 ? 'Sangat Baik' : ($pct >= 70 ? 'Cukup' : 'Perlu Pembinaan');
                        @endphp
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3 px-4 text-center font-bold text-slate-400">
                                {{ $studentAttendanceList->firstItem() + $idx }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 text-emerald-800 dark:text-emerald-200 flex items-center justify-center font-black text-[10px] uppercase shrink-0">
                                        {{ substr($stObj?->user?->name ?? 'S', 0, 2) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="font-black text-slate-900 dark:text-white truncate">{{ $stObj?->user?->name ?? 'Santri' }}</h4>
                                        <span class="text-[10px] text-slate-400">NISN: {{ $stObj?->nisn ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap font-bold text-slate-700 dark:text-slate-300">
                                {{ $stObj?->class?->name ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-emerald-600">
                                {{ $stAtt->count_hadir }}
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-blue-600">
                                {{ $stAtt->count_sakit }}
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-amber-600">
                                {{ $stAtt->count_izin }}
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-rose-600">
                                {{ $stAtt->count_alpa }}
                            </td>
                            <td class="py-3 px-4 text-center font-black text-slate-800 dark:text-white">
                                {{ $stAtt->total_meetings }}
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <span class="font-black text-xs text-slate-800 dark:text-white">{{ $pct }}%</span>
                                    <div class="w-12 bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden">
                                        <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black border {{ $statusBadge }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $studentAttendanceList->links() }}
            </div>
            @else
            <div class="p-12 text-center text-slate-400">
                <p class="text-xs font-bold">Tidak ada data kehadiran yang sesuai dengan filter yang dipilih.</p>
            </div>
            @endif
        </div>

    </div>
    @endif

    {{-- ========================================================================= --}}
    {{-- TAB 5: TARGET CAPAIAN KURIKULUM (TAHFIDZ, TILAWAH, TAHSIN) --}}
    {{-- ========================================================================= --}}
    @if($tab === 'target')
    <div class="space-y-6">
        
        {{-- Header Tab Target --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 bg-emerald-600 text-white rounded-lg text-[10px] font-black uppercase tracking-wider">
                        Standar Kurikulum
                    </span>
                    <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">
                        Target Capaian Al-Qur'an (Tahfidz, Tilawah &amp; Tahsin)
                    </h2>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Tentukan target batas minimal hafalan juz, surah, jilid tahsin per tingkat kelas dan semester sebagai standar acuan rapor halaqah.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6" x-data="{ targetProg: 'tahfidz' }">
            
            {{-- Form Tambah Target --}}
            <div class="lg:col-span-4 bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <span>🎯 TAMBAH TARGET KURIKULUM</span>
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Konfigurasi standar kelulusan per semester</p>
                </div>

                <form action="{{ route('admin.halaqah.target.store') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">Tingkat Kelas</label>
                            <select name="grade" class="w-full text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2.5 px-3 focus:ring-emerald-500 text-slate-800 dark:text-white" required>
                                @foreach($grades as $g)
                                    <option value="{{ $g }}">Kelas {{ $g }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">Semester</label>
                            <select name="semester" class="w-full text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2.5 px-3 focus:ring-emerald-500 text-slate-800 dark:text-white" required>
                                <option value="1">Semester 1 (Ganjil)</option>
                                <option value="2">Semester 2 (Genap)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">Program Halaqah</label>
                        <select name="program_type" x-model="targetProg" class="w-full text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2.5 px-3 focus:ring-emerald-500 text-slate-800 dark:text-white" required>
                            <option value="tahfidz">Tahfidz (Hafalan)</option>
                            <option value="tilawah">Tilawah (Tartil &amp; Tajwid)</option>
                            <option value="tahsin">Tahsin (Jilid 1 - 6)</option>
                        </select>
                    </div>

                    {{-- Form Dinamis: Tahfidz & Tilawah (Juz & Surah) --}}
                    <div x-show="targetProg === 'tahfidz' || targetProg === 'tilawah'" class="space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">Juz Mulai</label>
                                <select name="target_juz_start" class="w-full text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2.5 px-3 focus:ring-emerald-500 text-slate-800 dark:text-white">
                                    <option value="">-- Pilih --</option>
                                    @for($i=1; $i<=30; $i++)
                                        <option value="{{ $i }}" {{ $i == 30 ? 'selected' : '' }}>Juz {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">Juz Selesai</label>
                                <select name="target_juz_end" class="w-full text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2.5 px-3 focus:ring-emerald-500 text-slate-800 dark:text-white">
                                    <option value="">-- Pilih --</option>
                                    @for($i=1; $i<=30; $i++)
                                        <option value="{{ $i }}" {{ $i == 30 ? 'selected' : '' }}>Juz {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">Target Surah (Opsional)</label>
                            <input type="text" name="target_surah" placeholder="Contoh: An-Naba s.d An-Nas" class="w-full text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2.5 px-3 focus:ring-emerald-500 text-slate-800 dark:text-white">
                        </div>
                    </div>

                    {{-- Form Dinamis: Tahsin (Jilid 1 - 6) --}}
                    <div x-show="targetProg === 'tahsin'" class="space-y-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">Target Jilid</label>
                            <select name="target_jilid" class="w-full text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2.5 px-3 focus:ring-emerald-500 text-slate-800 dark:text-white">
                                <option value="">-- Pilih Target Jilid --</option>
                                @for($j=1; $j<=6; $j++)
                                    <option value="Jilid {{ $j }}">Jilid {{ $j }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">Nilai Kelulusan Minimal</label>
                        <input type="number" name="min_score" value="75" min="0" max="100" class="w-full text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2.5 px-3 focus:ring-emerald-500 text-slate-800 dark:text-white" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">Keterangan / Catatan</label>
                        <textarea name="description" rows="2" placeholder="Catatan standar target atau fokus tajwid..." class="w-full text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2 px-3 focus:ring-emerald-500 text-slate-800 dark:text-white"></textarea>
                    </div>

                    <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black shadow-md shadow-emerald-600/30 transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Target Kurikulum</span>
                    </button>
                </form>
            </div>

            {{-- Tabel Daftar Target yang Aktif --}}
            <div class="lg:col-span-8 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xs sm:text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">
                            Daftar Target Kurikulum Al-Qur'an
                        </span>
                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 rounded-md text-[10px] font-bold">
                            {{ $targets->count() }} Target
                        </span>
                    </div>
                </div>

                @if($targets->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800 text-[11px] font-extrabold text-slate-400 uppercase">
                                <th class="py-3 px-4">Tingkat &amp; Sem.</th>
                                <th class="py-3 px-4">Program</th>
                                <th class="py-3 px-4">Target Capaian</th>
                                <th class="py-3 px-4 text-center">Standar Min</th>
                                <th class="py-3 px-4">Keterangan</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-semibold text-slate-700 dark:text-slate-200">
                            @foreach($targets as $t)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="font-black text-slate-900 dark:text-white">Kelas {{ $t->grade }}</span>
                                    <p class="text-[10px] text-slate-400">Semester {{ $t->semester }}</p>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    @if($t->program_type === 'tahsin')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase bg-amber-50 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">Tahsin</span>
                                    @elseif($t->program_type === 'tilawah')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase bg-sky-50 text-sky-800 dark:bg-sky-950/60 dark:text-sky-300">Tilawah</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase bg-teal-50 text-teal-800 dark:bg-teal-950/60 dark:text-teal-300">Tahfidz</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if($t->program_type === 'tahsin')
                                        <span class="font-black text-amber-600">{{ $t->target_jilid ?? 'Jilid' }}</span>
                                    @else
                                        <div class="font-bold text-slate-800 dark:text-white">
                                            @if($t->target_juz)
                                                Juz {{ $t->target_juz }}
                                            @elseif($t->target_juz_start)
                                                Juz {{ $t->target_juz_start }} {{ $t->target_juz_end ? '- ' . $t->target_juz_end : '' }}
                                            @endif
                                            @if($t->target_surah_start)
                                                <span class="text-slate-400 font-normal">({{ $t->target_surah_start }} {{ $t->target_surah_end ? 's.d ' . $t->target_surah_end : '' }})</span>
                                            @elseif($t->target_surah)
                                                <span class="text-slate-400 font-normal">({{ $t->target_surah }})</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap font-black text-emerald-600">
                                    {{ $t->min_score ?? 75 }}
                                </td>
                                <td class="py-3 px-4 text-[11px] text-slate-400 max-w-xs truncate">
                                    {{ $t->notes ?: ($t->description ?: ($t->title ?: '-')) }}
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <form action="{{ route('admin.halaqah.target.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Hapus target kurikulum ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/40 transition" title="Hapus Target">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="p-12 text-center text-slate-400 space-y-2">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 mx-auto flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <p class="text-xs font-bold text-slate-600 dark:text-slate-300">Belum Ada Target Kurikulum yang Ditentukan</p>
                    <p class="text-[11px] text-slate-400">Gunakan form di sebelah kiri untuk menentukan target pencapaian santri per tingkat kelas.</p>
                </div>
                @endif
            </div>

        </div>

    </div>
    @endif

    {{-- ========================================================================= --}}
    {{-- TAB 6: UJIAN TASMI' 1 JUZ SEKALI DUDUK & SYAHADAH --}}
    {{-- ========================================================================= --}}
    @if($tab === 'tasmi')
    <div class="space-y-6" x-data="{
        tajwid: 85,
        kelancaran: 85,
        fashohah: 85,
        get totalScore() {
            return Math.round((Number(this.tajwid) + Number(this.kelancaran) + Number(this.fashohah)) / 3);
        },
        get isLulus() {
            return this.totalScore >= 75;
        }
    }">
        
        {{-- Header Tab Tasmi' --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 bg-amber-600 text-white rounded-lg text-[10px] font-black uppercase tracking-wider">
                        Ujian &amp; Syahadah
                    </span>
                    <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">
                        Ujian Tasmi' 1 Juz Sekali Duduk
                    </h2>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Pencatatan pengujian hafalan 1 juz penuh dalam satu majelis, penilaian tajwid, kelancaran, fashohah, dan cetak Syahadah Tasmi'.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            {{-- Form Input Ujian Tasmi' Baru --}}
            <div class="lg:col-span-4 bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <span>📜 CATAT UJIAN TASMI' BARU</span>
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Penilaian dewan asatidz penguji ujian tasmi'</p>
                </div>

                <form action="{{ route('admin.halaqah.tasmi.store') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">Pilih Santri yang Diuji</label>
                        <select name="student_id" class="w-full text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2.5 px-3 focus:ring-emerald-500 text-slate-800 dark:text-white" required>
                            <option value="">-- Pilih Santri --</option>
                            @foreach($students as $st)
                                <option value="{{ $st->id }}">{{ $st->user?->name ?? 'Santri' }} (Kelas {{ $st->class?->name ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">Juz yang Diuji</label>
                            <select name="juz_number" class="w-full text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2.5 px-3 focus:ring-emerald-500 text-slate-800 dark:text-white" required>
                                @for($j=1; $j<=30; $j++)
                                    <option value="{{ $j }}" {{ $j == 30 ? 'selected' : '' }}>Juz {{ $j }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">Tanggal Ujian</label>
                            <input type="date" name="exam_date" value="{{ date('Y-m-d') }}" class="w-full text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2.5 px-3 focus:ring-emerald-500 text-slate-800 dark:text-white" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">Nama Dewan Penguji</label>
                        <input type="text" name="examiner_name" value="{{ $activeTeacher->name ?? '' }}" placeholder="Nama Ustadz / Ustadzah Penguji" class="w-full text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2.5 px-3 focus:ring-emerald-500 text-slate-800 dark:text-white" required>
                    </div>

                    {{-- 3 Aspek Penilaian --}}
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200/80 dark:border-slate-700 space-y-3">
                        <span class="text-[10px] font-black uppercase text-slate-400">Komponen Penilaian (0-100)</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-0.5">Tajwid</label>
                                <input type="number" name="score_tajwid" x-model="tajwid" min="0" max="100" class="w-full text-center text-xs font-black rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 py-2 text-slate-800 dark:text-white" required>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-0.5">Kelancaran</label>
                                <input type="number" name="score_kelancaran" x-model="kelancaran" min="0" max="100" class="w-full text-center text-xs font-black rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 py-2 text-slate-800 dark:text-white" required>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-0.5">Fashohah</label>
                                <input type="number" name="score_fashohah" x-model="fashohah" min="0" max="100" class="w-full text-center text-xs font-black rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 py-2 text-slate-800 dark:text-white" required>
                            </div>
                        </div>

                        {{-- Kalkulasi Otomatis --}}
                        <div class="pt-2 border-t border-slate-200/60 dark:border-slate-700 flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-500">Rata-rata: <strong class="text-slate-900 dark:text-white" x-text="totalScore"></strong></span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black" :class="isLulus ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300'" x-text="isLulus ? '✓ LULUS' : '✗ MENGULANG'"></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">Catatan / Evaluasi Penguji</label>
                        <textarea name="notes" rows="2" placeholder="Catatan makhorijul huruf atau kelancaran tasmi'..." class="w-full text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 py-2 px-3 focus:ring-emerald-500 text-slate-800 dark:text-white"></textarea>
                    </div>

                    <button type="submit" class="w-full py-3 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-black shadow-md shadow-amber-600/30 transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Simpan Hasil Ujian Tasmi'</span>
                    </button>
                </form>
            </div>

            {{-- Tabel Riwayat Ujian Tasmi' --}}
            <div class="lg:col-span-8 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xs sm:text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">
                            Riwayat Ujian Tasmi' 1 Juz
                        </span>
                        <span class="px-2 py-0.5 bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 rounded-md text-[10px] font-bold">
                            {{ $tasmiExams->count() }} Ujian
                        </span>
                    </div>
                </div>

                @if($tasmiExams->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800 text-[11px] font-extrabold text-slate-400 uppercase">
                                <th class="py-3 px-4">Santri</th>
                                <th class="py-3 px-4 text-center">Juz</th>
                                <th class="py-3 px-4 text-center">Tanggal</th>
                                <th class="py-3 px-4 text-center">Nilai (Taj/Lan/Fas)</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4">Penguji</th>
                                <th class="py-3 px-4 text-right">Syahadah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-semibold text-slate-700 dark:text-slate-200">
                            @foreach($tasmiExams as $exam)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                <td class="py-3 px-4">
                                    <div class="font-black text-slate-900 dark:text-white">{{ $exam->student?->user?->name ?? 'Santri' }}</div>
                                    <p class="text-[10px] text-slate-400">Kelas {{ $exam->student?->class?->name ?? '-' }}</p>
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 font-black text-xs">
                                        {{ $exam->juz_tested ?? ('Juz ' . ($exam->juz_number ?? '30')) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap text-slate-500">
                                    {{ $exam->exam_date->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <div class="font-black text-slate-900 dark:text-white">
                                        {{ number_format($exam->score_final ?? ($exam->score_total ?? 0), 0) }}
                                    </div>
                                    <p class="text-[9px] text-slate-400">
                                        {{ number_format($exam->score_tajwid, 0) }} / {{ number_format($exam->score_kelancaran, 0) }} / {{ number_format($exam->score_fashohah, 0) }}
                                    </p>
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    @if($exam->status === 'lulus')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                            ✓ LULUS
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                                            MENGULANG
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap text-[11px] text-slate-600 dark:text-slate-300">
                                    Ust. {{ $exam->teacher?->name ?? ($exam->examiner_name ?? 'Penguji') }}
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    @if($exam->status === 'lulus')
                                    <a href="{{ route('admin.halaqah.tasmi.certificate', $exam->id) }}" target="_blank"
                                       class="px-3 py-1.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white rounded-xl text-xs font-black shadow-xs transition inline-flex items-center gap-1.5"
                                       title="Cetak Syahadah Tasmi'">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        <span>Cetak Syahadah</span>
                                    </a>
                                    @else
                                    <span class="text-slate-400 text-[10px] italic">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="p-12 text-center text-slate-400 space-y-2">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 mx-auto flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <p class="text-xs font-bold text-slate-600 dark:text-slate-300">Belum Ada Riwayat Ujian Tasmi'</p>
                    <p class="text-[11px] text-slate-400">Gunakan form di sebelah kiri untuk mencatat hasil ujian tasmi' 1 juz santri.</p>
                </div>
                @endif
            </div>

        </div>

    </div>
    @endif

    {{-- ========================================================================= --}}
    {{-- MODAL INTUITIF: ATUR / KELOLA KELOMPOK HALAQAH AL-QUR'AN --}}
    {{-- ========================================================================= --}}
    <div x-show="isGroupModalOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[9999] overflow-hidden bg-slate-900/60 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4"
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
            <div class="flex-1 min-h-0 overflow-y-auto overscroll-contain bg-slate-50/50 dark:bg-slate-900/50 divide-y divide-slate-100 dark:divide-slate-800/60"
                 style="-webkit-overflow-scrolling: touch; overscroll-behavior: contain; touch-action: pan-y;">

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
            <div class="p-3.5 sm:p-4 border-t border-slate-200/90 dark:border-slate-800 bg-white dark:bg-slate-900 flex items-center justify-between gap-2 shrink-0 z-20 shadow-lg pb-7 sm:pb-4">
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
