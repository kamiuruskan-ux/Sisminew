@extends('layouts.admin')

@section('title', 'Agenda Kelas & Penilaian Bidang Studi')
@section('page_title', 'Agenda Kelas & Penilaian')

@section('content')
<div class="space-y-6" x-data="{
    showDeleteModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    confirmDelete(id, name) {
        this.deleteTarget = { id: id, name: name };
        this.deleteFormAction = '{{ url('admin/grades') }}/' + id;
        this.showDeleteModal = true;
    },
    // Searchable Student Picker State for Tab 3
    studentId: '{{ request('student_id', '') }}',
    selectedClassId: '{{ request('class_id', '') }}',
    searchOpen: false,
    studentSearch: '',
    studentsList: {{ json_encode($students->map(fn($s) => [
        'id' => $s->id,
        'name' => $s->user->name ?? $s->nisn ?? 'Siswa',
        'nisn' => $s->nisn ?? '-',
        'class_id' => $s->class_id ?? null,
        'class_name' => $s->class->name ?? 'Tanpa Kelas'
    ])) }},
    get selectedStudentName() {
        if (!this.studentId) return 'Semua Siswa';
        const found = this.studentsList.find(s => s.id == this.studentId);
        return found ? found.name + ' (NISN: ' + (found.nisn || '-') + ' • Kelas ' + found.class_name + ')' : 'Semua Siswa';
    },
    get filteredStudents() {
        let list = this.studentsList;
        if (this.selectedClassId) {
            list = list.filter(s => s.class_id == this.selectedClassId);
        }
        if (!this.studentSearch) return list;
        const q = this.studentSearch.toLowerCase();
        return list.filter(s => 
            s.name.toLowerCase().includes(q) || 
            (s.nisn && s.nisn.toLowerCase().includes(q)) ||
            (s.class_name && s.class_name.toLowerCase().includes(q))
        );
    }
}">
    @include('components.delete-modal', ['title' => 'Hapus Nilai Siswa', 'message' => 'Apakah Anda yakin ingin menghapus nilai :name ini? Tindakan ini tidak dapat dibatalkan.'])

    {{-- Header Banner matching Image 3 --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <span class="p-2.5 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900/50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                    <div>
                        <h1 class="text-lg md:text-xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                            AGENDA KELAS &amp; PENILAIAN BIDANG STUDI
                        </h1>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 max-w-3xl">
                            Pusat penilaian mata pelajaran sekolah terpadu yang terpisah dari Al-Qur'an. Mengkoordinasikan absensi harian mengajar guru, agenda materi, serta capaian evaluasi standar kompetensi siswa.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('admin.raport.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-xs shadow-sm transition">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Raport</span>
                </a>
            </div>
        </div>

        {{-- 3 Tab Navigation (Masukan Penilaian & Agenda, Histori Mengajar Saya, Rerata Nilai Rapor Siswa) --}}
        <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 mt-6 pt-2 overflow-x-auto">
            <a href="{{ route('admin.grades.index', ['tab' => 'agenda', 'class_id' => $selectedClassId, 'subject' => $selectedSubject]) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 font-black text-xs rounded-t-xl transition-all border-b-2 {{ ($tab === 'agenda') ? 'border-indigo-600 text-white bg-slate-900 dark:bg-slate-800 shadow-xs' : 'border-transparent text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white bg-slate-50 dark:bg-slate-800/40' }}">
                <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.316.492-.533 1.035-.688 1.587a11.96 11.96 0 00-.317 1.833 6.94 6.94 0 00-.783-.758 1 1 0 00-1.468.083c-.345.413-.564.928-.68 1.455a7.02 7.02 0 00-.174 1.54 6.47 6.47 0 00.74 3.018c.28.536.66 1.018 1.127 1.417A6.99 6.99 0 0010 16a6.99 6.99 0 004.96-2.07c.466-.4.846-.88 1.127-1.418a6.47 6.47 0 00.74-3.017c0-.527-.06-1.05-.175-1.54-.115-.527-.334-1.042-.679-1.455a1 1 0 00-1.468-.083 6.94 6.94 0 00-.783.758 11.96 11.96 0 00-.317-1.833 12.6 12.6 0 00-.688-1.587c-.208-.322-.477-.65-.822-.88zM10 14a4.98 4.98 0 01-3.535-1.464 4.54 4.54 0 01-.837-1.128 4.47 4.47 0 01-.428-1.808c.07-.36.196-.706.37-1.02.13.14.275.27.433.39a3 3 0 003.794-.04c.18-.15.34-.32.48-.51.14.19.3.36.48.51a3 3 0 003.794.04c.158-.12.303-.25.433-.39.174.314.3.66.37 1.02a4.47 4.47 0 01-.428 1.808 4.54 4.54 0 01-.837 1.128A4.98 4.98 0 0110 14z" clip-rule="evenodd"/></svg>
                <span>MASUKKAN PENILAIAN &amp; AGENDA</span>
            </a>
            <a href="{{ route('admin.grades.index', ['tab' => 'history', 'class_id' => $selectedClassId, 'subject' => $selectedSubject]) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 font-black text-xs rounded-t-xl transition-all border-b-2 {{ ($tab === 'history') ? 'border-indigo-600 text-white bg-slate-900 dark:bg-slate-800 shadow-xs' : 'border-transparent text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white bg-slate-50 dark:bg-slate-800/40' }}">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>HISTORI MENGAJAR SAYA</span>
            </a>
            <a href="{{ route('admin.grades.index', ['tab' => 'summary', 'class_id' => $selectedClassId, 'subject' => $selectedSubject]) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 font-black text-xs rounded-t-xl transition-all border-b-2 {{ ($tab === 'summary') ? 'border-indigo-600 text-white bg-slate-900 dark:bg-slate-800 shadow-xs' : 'border-transparent text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white bg-slate-50 dark:bg-slate-800/40' }}">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>RERATA NILAI RAPOR SISWA</span>
            </a>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- TAB 1: MASUKKAN PENILAIAN & AGENDA (Image 3) --}}
    {{-- ========================================== --}}
    @if($tab === 'agenda')
    <div class="space-y-5">
        {{-- Callout Info --}}
        <div class="p-3.5 bg-blue-50/80 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-900/50 rounded-2xl flex items-center gap-2.5 text-xs text-blue-700 dark:text-blue-300 font-medium shadow-2xs">
            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Silakan lengkapi Jurnal Mengajar dan Absensi Kehadiran Siswa untuk sesi kelas ini.</span>
        </div>

        <form action="{{ route('admin.grades.store-agenda') }}" method="POST" class="space-y-5">
            @csrf
            <input type="hidden" name="date" value="{{ date('Y-m-d') }}">

            {{-- Card 1: Jurnal Mengajar --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
                <div class="flex items-center gap-2 text-sm font-black text-slate-800 dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-3">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Jurnal Mengajar</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Materi Pembelajaran <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="material_taught" required rows="3" 
                              placeholder="Tuliskan materi/kompetensi yang diajarkan hari ini secara detail..." 
                              class="w-full px-4 py-3 text-xs bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none transition text-slate-800 dark:text-slate-200 leading-relaxed"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Catatan Kelas (Opsional)
                    </label>
                    <textarea name="class_notes" rows="2" 
                              placeholder="Catatan kendala kelas, siswa bermasalah, atau hal penting lainnya..." 
                              class="w-full px-4 py-3 text-xs bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none transition text-slate-800 dark:text-slate-200 leading-relaxed"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Kelas</label>
                        <select name="class_id" 
                                onchange="window.location.href='{{ route('admin.grades.index') }}?tab=agenda&class_id=' + this.value + '&subject={{ urlencode($selectedSubject ?? '') }}'" 
                                class="w-full h-11 px-3.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold rounded-xl text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none transition shadow-2xs">
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->students_count ?? 0 }} Siswa)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Mapel</label>
                        @if(empty($teacherSubjects))
                            <div class="h-11 px-3.5 flex items-center bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 rounded-xl text-rose-600 dark:text-rose-400 font-bold text-xs shadow-2xs">
                                Belum ditugaskan mapel!
                            </div>
                            <input type="hidden" name="subject" value="Umum">
                        @else
                            <select name="subject" 
                                    class="w-full h-11 px-3.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold rounded-xl text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none transition shadow-2xs">
                                @foreach($teacherSubjects as $sub)
                                    @php $subName = is_object($sub) ? $sub->name : $sub; @endphp
                                    <option value="{{ $subName }}" {{ $selectedSubject == $subName ? 'selected' : '' }}>{{ $subName }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Card 2: Absensi & Nilai Siswa --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div class="flex items-center gap-2 text-sm font-black text-slate-800 dark:text-slate-100">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span>Absensi &amp; Nilai Siswa</span>
                    </div>
                    <span class="text-xs font-bold text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-800 px-3 py-1 rounded-full">
                        {{ $studentsInClass->count() }} Santri Terdaftar
                    </span>
                </div>

                <div class="space-y-3.5">
                    @forelse($studentsInClass as $st)
                    <div class="p-4 bg-slate-50/80 dark:bg-slate-800/60 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 space-y-3 shadow-2xs hover:border-indigo-300 dark:hover:border-indigo-700 transition" 
                         x-data="{ status: 'hadir' }">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <h4 class="text-sm font-black text-slate-900 dark:text-white">{{ $st->user->name ?? $st->nisn ?? 'Siswa' }}</h4>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500 font-mono">NIS: {{ $st->nisn ?? $st->nis ?? ('STD_' . $st->id) }}</p>
                            </div>

                            {{-- Status Presensi Pills (Hadir, Sakit, Izin, Alpa) --}}
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <input type="hidden" :name="'students[' + {{ $st->id }} + '][attendance]'" :value="status">
                                <button type="button" @click="status = 'hadir'" 
                                        :class="status === 'hadir' ? 'bg-emerald-600 text-white font-black shadow-xs ring-2 ring-emerald-400/40' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100'"
                                        class="px-3.5 py-1.5 rounded-xl text-xs transition cursor-pointer">
                                    Hadir
                                </button>
                                <button type="button" @click="status = 'sakit'" 
                                        :class="status === 'sakit' ? 'bg-amber-500 text-white font-black shadow-xs ring-2 ring-amber-400/40' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100'"
                                        class="px-3.5 py-1.5 rounded-xl text-xs transition cursor-pointer">
                                    Sakit
                                </button>
                                <button type="button" @click="status = 'izin'" 
                                        :class="status === 'izin' ? 'bg-blue-500 text-white font-black shadow-xs ring-2 ring-blue-400/40' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100'"
                                        class="px-3.5 py-1.5 rounded-xl text-xs transition cursor-pointer">
                                    Izin
                                </button>
                                <button type="button" @click="status = 'alpa'" 
                                        :class="status === 'alpa' ? 'bg-rose-500 text-white font-black shadow-xs ring-2 ring-rose-400/40' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100'"
                                        class="px-3.5 py-1.5 rounded-xl text-xs transition cursor-pointer">
                                    Alpa
                                </button>
                            </div>
                        </div>

                        {{-- Skor Kognitif & Adab --}}
                        <div class="grid grid-cols-2 gap-3 pt-1 border-t border-slate-200/50 dark:border-slate-700/50">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">Skor Kognitif</label>
                                <input type="number" min="0" max="100" name="students[{{ $st->id }}][cognitive]" value="80" 
                                       class="w-full h-10 px-3 text-center font-black text-xs bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none transition shadow-2xs">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">Skor Adab</label>
                                <input type="number" min="0" max="100" name="students[{{ $st->id }}][adab]" value="80" 
                                       class="w-full h-10 px-3 text-center font-black text-xs bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none transition shadow-2xs">
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-10 text-center bg-slate-50 dark:bg-slate-800/40 rounded-3xl border border-dashed border-slate-200 dark:border-slate-800">
                        <p class="text-xs text-slate-400 font-medium">Belum ada siswa terdaftar pada kelas yang dipilih.</p>
                    </div>
                    @endforelse
                </div>

                @if($studentsInClass->isNotEmpty())
                <div class="pt-4 flex justify-end">
                    <button type="submit" 
                            class="px-7 py-3.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs rounded-2xl shadow-lg hover:shadow-xl transition flex items-center gap-2 cursor-pointer transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        <span>Simpan Agenda &amp; Nilai Kelas</span>
                    </button>
                </div>
                @endif
            </div>
        </form>
    </div>
    @endif

    {{-- ========================================== --}}
    {{-- TAB 2: HISTORI MENGAJAR SAYA               --}}
    {{-- ========================================== --}}
    @if($tab === 'history')
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
            <div>
                <h3 class="text-sm font-black text-slate-900 dark:text-white">Riwayat Agenda Mengajar</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Catatan pembelajaran kelas dan evaluasi berkala yang telah Anda terbitkan</p>
            </div>
            <a href="{{ route('admin.grades.index', ['tab' => 'agenda']) }}" class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 transition">
                + Buat Agenda Baru
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 font-extrabold uppercase text-[10px] tracking-wider border-y border-slate-200 dark:border-slate-800">
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Kelas &amp; Mapel</th>
                        <th class="py-3 px-4">Materi Pembelajaran</th>
                        <th class="py-3 px-4 text-center">Kehadiran</th>
                        <th class="py-3 px-4 text-center">Rerata Kognitif</th>
                        <th class="py-3 px-4 text-center">Rerata Adab</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($teachingAgendas as $agenda)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition">
                        <td class="py-3.5 px-4 font-bold text-slate-900 dark:text-white whitespace-nowrap">
                            {{ $agenda->date ? $agenda->date->translatedFormat('d M Y') : '-' }}
                            <p class="text-[10px] text-slate-400 font-normal">{{ $agenda->created_at->format('H:i') }} WITA</p>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $agenda->class->name ?? 'Kelas' }}</span>
                            <p class="text-[11px] text-slate-500 font-medium">{{ $agenda->subject }}</p>
                        </td>
                        <td class="py-3.5 px-4 max-w-xs">
                            <p class="font-semibold text-slate-800 dark:text-slate-200 truncate" title="{{ $agenda->material_taught }}">
                                {{ $agenda->material_taught }}
                            </p>
                            @if($agenda->class_notes)
                                <p class="text-[10px] text-amber-600 dark:text-amber-400 truncate mt-0.5">
                                    Catatan: {{ $agenda->class_notes }}
                                </p>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            @php $counts = $agenda->attendance_count; @endphp
                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-black rounded-full" title="Hadir">{{ $counts['hadir'] }}H</span>
                            @if($counts['sakit'] > 0)
                                <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-black rounded-full" title="Sakit">{{ $counts['sakit'] }}S</span>
                            @endif
                            @if($counts['izin'] > 0)
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-black rounded-full" title="Izin">{{ $counts['izin'] }}I</span>
                            @endif
                            @if($counts['alpa'] > 0)
                                <span class="px-2 py-0.5 bg-rose-100 text-rose-700 text-[10px] font-black rounded-full" title="Alpa">{{ $counts['alpa'] }}A</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center font-black text-slate-800 dark:text-slate-100">
                            {{ number_format($agenda->avg_cognitive, 1) }}
                        </td>
                        <td class="py-3.5 px-4 text-center font-black text-slate-800 dark:text-slate-100">
                            {{ number_format($agenda->avg_adab, 1) }}
                        </td>
                        <td class="py-3.5 px-4 text-right whitespace-nowrap">
                            <form action="{{ route('admin.grades.destroy-agenda', $agenda->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus agenda mengajar tanggal {{ $agenda->date->translatedFormat('d M Y') }} ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition" title="Hapus Agenda">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400 text-xs">
                            Belum ada riwayat agenda mengajar yang disimpan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-2">
            {{ $teachingAgendas->appends(['tab' => 'history'])->links() }}
        </div>
    </div>
    @endif

    {{-- ========================================== --}}
    {{-- TAB 3: RERATA NILAI RAPOR SISWA (Summary)  --}}
    {{-- ========================================== --}}
    @if($tab === 'summary')
    <div class="space-y-5">
        {{-- Quick Actions --}}
        <div class="flex flex-wrap items-center justify-between gap-3 bg-white dark:bg-slate-900 rounded-3xl p-4 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center gap-2 flex-wrap">
                <button onclick="openModal('importExcelModal')"
                        class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 rounded-xl font-bold text-xs hover:bg-emerald-100 transition cursor-pointer">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Import Excel</span>
                </button>
                <a href="{{ route('admin.grades.export.excel', request()->all()) }}"
                   class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 rounded-xl font-bold text-xs hover:bg-blue-100 transition">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span>Export Excel</span>
                </a>
                <a href="{{ route('admin.grades.bulk.create') }}"
                   class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 rounded-xl font-semibold text-xs hover:bg-slate-50 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    <span>Input Massal</span>
                </a>
            </div>

            <button onclick="openModal('createModal')"
                    class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-gradient-to-r from-primary to-secondary text-white rounded-xl font-semibold text-xs hover:brightness-110 shadow-sm transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Nilai Manual</span>
            </button>
        </div>

        {{-- Filters (Real-Time Auto Submit) --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-5 shadow-xs border border-slate-200 dark:border-slate-800">
            <form action="{{ route('admin.grades.index') }}" method="GET"
                  class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 items-end">
                <input type="hidden" name="tab" value="summary">
                <x-major-class-select 
                    :selected-major="request('major_id')" 
                    :selected-class="request('class_id')" 
                    :is-filter="true" 
                    layout="inline" 
                    major-label="Jurusan" 
                    class-label="Kelas" 
                    select-class="w-full h-10 px-3.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none transition shadow-2xs" 
                    label-class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5" 
                    :on-major-change="'$el.closest(\'form\').submit()'"
                    :on-class-change="'$el.closest(\'form\').submit()'"
                />
                <!-- Searchable Student Picker -->
                <div class="relative">
                    <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Siswa</label>
                    <input type="hidden" name="student_id" x-model="studentId">

                    <button type="button" @click="searchOpen = !searchOpen" 
                            class="w-full h-10 px-3.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold flex items-center justify-between focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none transition shadow-2xs text-left">
                        <span x-text="selectedStudentName" class="truncate"></span>
                        <svg class="w-4 h-4 text-slate-400 shrink-0 ml-2 transition-transform duration-200" :class="{ 'rotate-180': searchOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- Dropdown Search Panel -->
                    <div x-show="searchOpen" @click.outside="searchOpen = false" x-cloak
                         class="absolute z-50 mt-1 w-full min-w-[280px] bg-white dark:bg-[#24303F] border border-slate-200 dark:border-[#2E3A47] rounded-2xl shadow-2xl p-2.5 space-y-2 max-h-72 flex flex-col">
                        <div class="relative">
                            <input type="text" x-model="studentSearch" placeholder="Cari nama siswa, NISN, atau kelas..." 
                                   class="w-full bg-slate-50 dark:bg-[#1A222C] border border-slate-200 dark:border-[#2E3A47] text-xs rounded-xl p-2.5 pl-9 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>

                        <div class="overflow-y-auto space-y-1 flex-1 pr-1">
                            <button type="button" @click="studentId = ''; searchOpen = false; $nextTick(() => $el.closest('form').submit())" 
                                    :class="!studentId ? 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-bold border border-indigo-500/30' : 'hover:bg-slate-100 dark:hover:bg-[#1A222C] text-slate-800 dark:text-white font-medium'"
                                    class="w-full text-left p-2.5 rounded-xl text-xs flex items-center justify-between transition">
                                <span>Semua Siswa</span>
                            </button>
                            <template x-for="s in filteredStudents" :key="s.id">
                                <button type="button" @click="studentId = s.id; searchOpen = false; $nextTick(() => $el.closest('form').submit())" 
                                        :class="studentId == s.id ? 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-bold border border-indigo-500/30' : 'hover:bg-slate-100 dark:hover:bg-[#1A222C] text-slate-800 dark:text-white font-medium'"
                                        class="w-full text-left p-2.5 rounded-xl text-xs flex items-center justify-between transition">
                                    <div>
                                        <p class="font-bold" x-text="s.name"></p>
                                        <p class="text-[10px] text-slate-400" x-text="'NISN: ' + (s.nisn || '-')"></p>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 shrink-0" x-text="s.class_name"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Mata Pelajaran</label>
                    @php
                        $subjList = (isset($subjects) && count($subjects) > 0) ? $subjects : ($globalSubjects ?? []);
                    @endphp
                    <select name="subject" @change="$el.closest('form').submit()" 
                            class="w-full h-10 px-3.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none transition shadow-2xs">
                        <option value="">Semua Mapel</option>
                        @foreach($subjList as $subject)
                            @php $sName = is_object($subject) ? $subject->name : $subject; @endphp
                            <option value="{{ $sName }}" {{ request('subject') == $sName ? 'selected' : '' }}>{{ $sName }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Tipe Penilaian</label>
                    <select name="type" @change="$el.closest('form').submit()" 
                            class="w-full h-10 px-3.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none transition shadow-2xs">
                        <option value="">Semua Tipe</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                @if($type === 'daily') Harian
                                @elseif($type === 'mid_term') UTS
                                @elseif($type === 'final_term') UAS
                                @else Ujian @endif
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        {{-- Table Nilai Siswa --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-200 dark:border-slate-800">
                            <th class="py-3 px-4">Siswa</th>
                            <th class="py-3 px-4">Kelas</th>
                            <th class="py-3 px-4">Mata Pelajaran</th>
                            <th class="py-3 px-4">Tipe</th>
                            <th class="py-3 px-4 text-center">Nilai</th>
                            <th class="py-3 px-4">Catatan</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($grades as $grade)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition">
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-900 dark:text-white">{{ $grade->student->user->name ?? $grade->student->nisn ?? 'Siswa' }}</span>
                                <p class="text-[10px] text-slate-400 font-mono">NISN: {{ $grade->student->nisn ?? '-' }}</p>
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-slate-700 dark:text-slate-300">
                                {{ $grade->class->name ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-slate-800 dark:text-slate-200">
                                {{ $grade->subject }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if($grade->type === 'daily')
                                    <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 font-bold rounded-full text-[10px]">Harian</span>
                                @elseif($grade->type === 'mid_term')
                                    <span class="px-2 py-0.5 bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 font-bold rounded-full text-[10px]">UTS</span>
                                @elseif($grade->type === 'final_term')
                                    <span class="px-2 py-0.5 bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 font-bold rounded-full text-[10px]">UAS</span>
                                @else
                                    <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold rounded-full text-[10px]">{{ ucfirst($grade->type) }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center font-black text-sm {{ $grade->score >= 75 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500' }}">
                                {{ $grade->score }}
                            </td>
                            <td class="py-3.5 px-4 max-w-xs text-slate-500 truncate" title="{{ $grade->notes }}">
                                {{ $grade->notes ?: '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <button onclick="openEditModal({{ json_encode($grade) }})" class="p-1.5 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 rounded-lg transition" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button type="button" @click="confirmDelete('{{ $grade->id }}', '{{ addslashes($grade->student->user->name ?? 'Siswa') }}')" class="p-1.5 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition ml-1" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400 text-xs font-medium">
                                Tidak ada data nilai siswa yang sesuai filter.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $grades->appends(['tab' => 'summary'])->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

{{-- MODAL CREATE NILAI MANUAL --}}
<div id="createModal" class="fixed inset-0 z-50 overflow-y-auto hidden bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-lg border border-slate-200 dark:border-slate-800 overflow-hidden" onclick="event.stopPropagation()">
        <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-sm font-black text-slate-900 dark:text-white">Tambah Nilai Siswa</h3>
            <button onclick="closeModal('createModal')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('admin.grades.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Kelas</label>
                <select name="class_id" required class="w-full h-10 px-3 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold rounded-xl text-slate-800 dark:text-slate-200">
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Siswa</label>
                <select name="student_id" id="create_student_id" required class="w-full">
                    @foreach($students as $st)
                        <option value="{{ $st->id }}">{{ $st->user->name ?? $st->nisn }} ({{ $st->class->name ?? '-' }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Mata Pelajaran</label>
                <select name="subject" required class="w-full h-10 px-3 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold rounded-xl text-slate-800 dark:text-slate-200">
                    @foreach($subjects as $sub)
                        @php $subName = is_object($sub) ? $sub->name : $sub; @endphp
                        <option value="{{ $subName }}">{{ $subName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Tipe Penilaian</label>
                    <select name="type" required class="w-full h-10 px-3 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold rounded-xl text-slate-800 dark:text-slate-200">
                        <option value="daily">Harian</option>
                        <option value="mid_term">UTS</option>
                        <option value="final_term">UAS</option>
                        <option value="exam">Ujian</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Nilai (0 - 100)</label>
                    <input type="number" step="0.1" min="0" max="100" name="score" required value="80" class="w-full h-10 px-3 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold rounded-xl text-slate-800 dark:text-slate-200">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Catatan</label>
                <input type="text" name="notes" placeholder="Catatan evaluasi (opsional)" class="w-full h-10 px-3 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs rounded-xl text-slate-800 dark:text-slate-200">
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModal('createModal')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-xs font-bold rounded-xl text-slate-600 dark:text-slate-300">Batal</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black rounded-xl shadow-md">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT NILAI --}}
<div id="editModal" class="fixed inset-0 z-50 overflow-y-auto hidden bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-lg border border-slate-200 dark:border-slate-800 overflow-hidden" onclick="event.stopPropagation()">
        <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-sm font-black text-slate-900 dark:text-white">Edit Nilai Siswa</h3>
            <button onclick="closeModal('editModal')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="editForm" action="" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="student_id" id="edit_student_id">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Kelas</label>
                <select name="class_id" id="edit_class_id" required class="w-full h-10 px-3 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold rounded-xl text-slate-800 dark:text-slate-200"></select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Mata Pelajaran</label>
                <select name="subject" id="edit_subject" required class="w-full h-10 px-3 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold rounded-xl text-slate-800 dark:text-slate-200"></select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Tipe Penilaian</label>
                    <select name="type" id="edit_type" required class="w-full h-10 px-3 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold rounded-xl text-slate-800 dark:text-slate-200">
                        <option value="daily">Harian</option>
                        <option value="mid_term">UTS</option>
                        <option value="final_term">UAS</option>
                        <option value="exam">Ujian</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Nilai (0 - 100)</label>
                    <input type="number" step="0.1" min="0" max="100" name="score" id="edit_score" required class="w-full h-10 px-3 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold rounded-xl text-slate-800 dark:text-slate-200">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Catatan</label>
                <input type="text" name="notes" id="edit_notes" class="w-full h-10 px-3 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs rounded-xl text-slate-800 dark:text-slate-200">
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModal('editModal')" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-xs font-bold rounded-xl text-slate-600 dark:text-slate-300">Batal</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black rounded-xl shadow-md">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL IMPORT EXCEL --}}
<div id="importExcelModal" class="fixed inset-0 z-50 overflow-y-auto hidden bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4"
     x-data="{
         isUploading: false,
         statusText: 'Membaca file Excel...',
         statusIndex: 0,
         statusList: [
             'Membaca berkas Excel...',
             'Memvalidasi data NISN & Nama Siswa...',
             'Mencocokkan Kelas & Mata Pelajaran...',
             'Menyimpan record nilai ke basis data...',
             'Hampir selesai, menyinkronkan data...'
         ],
         startImportLoading() {
             this.isUploading = true;
             setInterval(() => {
                 this.statusIndex = (this.statusIndex + 1) % this.statusList.length;
                 this.statusText = this.statusList[this.statusIndex];
             }, 1200);
         }
     }">
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-md border border-slate-200 dark:border-slate-800 overflow-hidden" onclick="event.stopPropagation()">
        <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-sm font-black text-slate-900 dark:text-white">Import Nilai Siswa (Excel)</h3>
            <button x-show="!isUploading" onclick="closeModal('importExcelModal')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form x-show="!isUploading" action="{{ route('admin.grades.import.excel') }}" method="POST" enctype="multipart/form-data" @submit="startImportLoading()" class="p-6 space-y-4">
            @csrf
            <div class="p-3.5 bg-emerald-50/80 dark:bg-emerald-950/40 rounded-2xl border border-emerald-200/80 dark:border-emerald-800/80 text-xs space-y-2 text-emerald-900 dark:text-emerald-300">
                <p class="font-bold">Petunjuk Format File Excel (.xlsx / .xls):</p>
                <p class="text-[11px] text-slate-600 dark:text-slate-300 leading-relaxed">
                    Kolom di baris 1: <strong>NISN, Nama Siswa, Kelas, Mata Pelajaran, Tipe Nilai, Nilai, Catatan</strong>.
                </p>
                <div class="pt-1 border-t border-emerald-200/60 dark:border-emerald-800/60">
                    <a href="{{ route('admin.grades.download.template') }}" class="inline-flex items-center gap-1.5 text-[11px] font-extrabold text-indigo-600 dark:text-indigo-400 hover:underline">
                        <span>Download Template Excel (.xlsx)</span>
                    </a>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Pilih Berkas Excel</label>
                <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="w-full text-xs text-slate-600 dark:text-slate-300 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModal('importExcelModal')" class="px-4 py-2.5 text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 rounded-xl text-xs font-bold">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black shadow-md">Proses Import</button>
            </div>
        </form>
        <div x-show="isUploading" x-cloak class="p-8 text-center space-y-4">
            <div class="w-12 h-12 rounded-full border-4 border-emerald-500 border-t-transparent animate-spin mx-auto"></div>
            <h4 class="text-sm font-black text-slate-900 dark:text-white" x-text="statusText"></h4>
        </div>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); document.body.style.overflow = ''; }
function openEditModal(data) {
    document.getElementById('editForm').action = '{{ url('admin/grades') }}/' + data.id;
    document.getElementById('edit_student_id').value = data.student_id;

    const classSelect = document.getElementById('edit_class_id');
    classSelect.innerHTML = '';
    @foreach($classes as $class)
    classSelect.add(new Option('{{ $class->name }}', '{{ $class->id }}'));
    @endforeach
    classSelect.value = data.class_id;

    const subjectSelect = document.getElementById('edit_subject');
    subjectSelect.innerHTML = '';
    @php
        $subjList = (isset($subjects) && count($subjects) > 0) ? $subjects : ($globalSubjects ?? []);
    @endphp
    @foreach($subjList as $subject)
    @php $sName = is_object($subject) ? $subject->name : $subject; @endphp
    subjectSelect.add(new Option('{{ $sName }}', '{{ $sName }}'));
    @endforeach
    subjectSelect.value = data.subject;

    document.getElementById('edit_type').value = data.type;
    document.getElementById('edit_score').value = data.score;
    document.getElementById('edit_notes').value = data.notes || '';
    openModal('editModal');
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { closeModal('createModal'); closeModal('editModal'); closeModal('importExcelModal'); }
});
</script>
@endsection
