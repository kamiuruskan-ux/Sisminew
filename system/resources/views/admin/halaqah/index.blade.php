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
    }
}">

    {{-- Top Bar Tabs (Input & Evaluasi | Laporan & Grafik | Rekap Kehadiran) --}}
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-white dark:bg-slate-900 p-2.5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="flex items-center gap-1.5 w-full sm:w-auto overflow-x-auto">
            <a href="{{ route('admin.halaqah.index', ['tab' => 'input', 'class_id' => $selectedClassId, 'mode' => $mode]) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 whitespace-nowrap {{ $tab === 'input' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Input &amp; Catatan Evaluasi</span>
            </a>

            <a href="{{ route('admin.halaqah.index', ['tab' => 'reports', 'class_id' => $selectedClassId]) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 whitespace-nowrap {{ $tab === 'reports' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>Laporan Harian &amp; Bulanan + Grafik Statistik</span>
            </a>

            <a href="{{ route('admin.halaqah.index', ['tab' => 'attendance', 'class_id' => $selectedClassId]) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 whitespace-nowrap {{ $tab === 'attendance' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Rekap Kehadiran</span>
            </a>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('admin.halaqah.export-excel', ['class_id' => $selectedClassId]) }}"
               class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 text-xs font-bold rounded-xl border border-emerald-200 dark:border-emerald-800 transition flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Export Excel</span>
            </a>
            <a href="{{ route('admin.quran-raport.index', ['class_id' => $selectedClassId]) }}"
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

    {{-- ========================================================================= --}}
    {{-- TAB 1: INPUT & CATATAN EVALUASI (Screenshot 1 & Screenshot 2) --}}
    {{-- ========================================================================= --}}
    @if($tab === 'input')
    <div class="space-y-5">
        
        {{-- Sub-Toggle: Individu (Satu Santri) vs Massal Satu Kelas --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <div>
                <h3 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider">METODE PENILAIAN EVALUASI</h3>
                <p class="text-[11px] text-slate-400">Pilih input individu untuk santri tunggal atau input massal untuk satu kelas sekaligus</p>
            </div>
            <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl">
                <a href="{{ route('admin.halaqah.index', ['tab' => 'input', 'mode' => 'individual', 'class_id' => $selectedClassId]) }}"
                   class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ $mode === 'individual' ? 'bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 shadow-xs' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white' }}">
                    <span>👤 Individu (Satu Santri)</span>
                </a>
                <a href="{{ route('admin.halaqah.index', ['tab' => 'input', 'mode' => 'mass', 'class_id' => $selectedClassId]) }}"
                   class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ $mode === 'mass' ? 'bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 shadow-xs' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white' }}">
                    <span>👥 Massal Satu Kelas</span>
                </a>
            </div>
        </div>

        {{-- MODE 1: INDIVIDU (SATU SANTRI) --}}
        @if($mode === 'individual')
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            {{-- Kolom Kiri: Form Input Individu --}}
            <div class="lg:col-span-5 bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                <div class="flex items-center gap-2 text-amber-500 font-extrabold text-xs tracking-wider uppercase border-b border-slate-100 dark:border-slate-800 pb-3">
                    <span>⚡ INPUT NILAI BARU HARIAN</span>
                </div>

                <form action="{{ route('admin.halaqah.store-individual') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    {{-- Tanggal Penilaian --}}
                    <div>
                        <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Tanggal Penilaian</label>
                        <input type="date" name="assessment_date" value="{{ $selectedDate }}" required
                               class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white rounded-xl text-xs font-bold focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                    </div>

                    {{-- Pilih Kelas --}}
                    <div>
                        <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Pilih Tingkat Kelas yang Diajar</label>
                        <select onchange="location.href = '{{ route('admin.halaqah.index') }}?tab=input&mode=individual&class_id=' + this.value"
                                class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white rounded-xl text-xs font-bold focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                            <option value="">-- Pilih Tingkat Kelas Aktif Anda --</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->students_count ?? 0 }} Santri)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Pilih Nama Santri --}}
                    <div>
                        <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Pilih Nama Santri (Nama Siswa)</label>
                        @if($selectedClassId && count($students) > 0)
                            <select name="student_id" required
                                    class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white rounded-xl text-xs font-bold focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition">
                                <option value="">-- Pilih Santri --</option>
                                @foreach($students as $st)
                                    <option value="{{ $st->id }}">{{ $st->user?->name ?? 'Santri' }} (NISN: {{ $st->nisn ?? '-' }})</option>
                                @endforeach
                            </select>
                        @else
                            <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl text-xs text-slate-400 font-medium">
                                Sila pilih kelas terlebih dahulu
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
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-extrabold text-slate-500 uppercase">Detail Lembar Tajwid Tahsin:</span>
                            <div class="inline-flex p-0.5 bg-slate-200 dark:bg-slate-700 rounded-lg text-[10px] font-bold">
                                <button type="button" @click="tahsinType = 'jilid'" :class="tahsinType === 'jilid' ? 'bg-emerald-600 text-white' : 'text-slate-600 dark:text-slate-300'" class="px-2.5 py-0.5 rounded transition">Jilid</button>
                                <button type="button" @click="tahsinType = 'tilawah'" :class="tahsinType === 'tilawah' ? 'bg-emerald-600 text-white' : 'text-slate-600 dark:text-slate-300'" class="px-2.5 py-0.5 rounded transition">Tilawah (Al-Qur'an)</button>
                            </div>
                            <input type="hidden" name="tahsin_type" :value="tahsinType">
                        </div>

                        {{-- Mode 1: Tahsin Standar Jilid --}}
                        <div x-show="tahsinType === 'jilid'" class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 mb-1">Standard Jilid</label>
                                <select name="jilid_level" class="w-full px-2.5 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg text-xs font-bold">
                                    <option value="Jilid 1">Jilid 1</option>
                                    <option value="Jilid 2">Jilid 2</option>
                                    <option value="Jilid 3">Jilid 3</option>
                                    <option value="Jilid 4">Jilid 4</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 mb-1">Mulai Halaman</label>
                                <input type="number" name="page_start" value="1" min="1" class="w-full px-2.5 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg text-xs font-bold text-center">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 mb-1">Sampai Halaman</label>
                                <input type="number" name="page_end" value="10" min="1" class="w-full px-2.5 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg text-xs font-bold text-center">
                            </div>
                        </div>

                        {{-- Mode 2: Tahsin Tilawah Al-Qur'an (Dropdown Surah Lengkap) --}}
                        <div x-show="tahsinType === 'tilawah'" class="space-y-2.5">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 mb-1">Juz (Otomatis)</label>
                                    <input type="number" name="juz_number" x-model="selectedJuz" min="1" max="30" class="w-full px-2.5 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg text-xs font-bold text-center">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-400 mb-1">Pilih Nama Surah (1 - 114)</label>
                                    <select name="surah_name" @change="onSurahChange($event)" class="w-full px-2.5 py-2 border border-emerald-300 dark:border-emerald-700 bg-white dark:bg-slate-800 rounded-lg text-xs font-bold focus:ring-2 focus:ring-emerald-500">
                                        <option value="">-- Pilih Nama Surah --</option>
                                        @foreach($surahOptions ?? [] as $surah)
                                            <option value="{{ $surah['name'] }}" data-juz="{{ $surah['juz'] }}">
                                                {{ $surah['label'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 mb-1">Ayat Mulai</label>
                                    <input type="number" name="ayat_start" value="1" min="1" class="w-full px-2.5 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg text-xs font-bold text-center">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 mb-1">Ayat Selesai</label>
                                    <input type="number" name="ayat_end" value="10" min="1" class="w-full px-2.5 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg text-xs font-bold text-center">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Detail Jika Tahfidz (Dropdown Surah Lengkap) --}}
                    <div x-show="programType === 'tahfidz'" class="space-y-3 p-3.5 bg-emerald-50/50 dark:bg-emerald-950/20 rounded-2xl border border-emerald-100 dark:border-emerald-900/40">
                        <span class="text-[11px] font-extrabold text-emerald-800 dark:text-emerald-300 uppercase">Detail Setoran Tahfidz:</span>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 mb-1">Juz (Otomatis)</label>
                                <input type="number" name="juz_number" x-model="selectedJuz" min="1" max="30" class="w-full px-2.5 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg text-xs font-bold text-center">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] font-bold text-slate-400 mb-1">Pilih Nama Surah (1 - 114)</label>
                                <select name="surah_name" @change="onSurahChange($event)" class="w-full px-2.5 py-2 border border-emerald-300 dark:border-emerald-700 bg-white dark:bg-slate-800 rounded-lg text-xs font-bold focus:ring-2 focus:ring-emerald-500">
                                    <option value="">-- Pilih Nama Surah --</option>
                                    @foreach($surahOptions ?? [] as $surah)
                                        <option value="{{ $surah['name'] }}" data-juz="{{ $surah['juz'] }}" {{ $surah['number'] == 78 ? 'selected' : '' }}>
                                            {{ $surah['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 mb-1">Ayat (Mulai - Selesai)</label>
                            <div class="flex items-center gap-1">
                                <input type="number" name="ayat_start" value="1" min="1" class="w-1/2 px-2 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg text-xs font-bold text-center" placeholder="Mulai">
                                <span>-</span>
                                <input type="number" name="ayat_end" value="10" min="1" class="w-1/2 px-2 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-lg text-xs font-bold text-center" placeholder="Selesai">
                            </div>
                        </div>
                    </div>

                    {{-- Nilai Kognitif & Adab --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Nilai Kognitif (0 - 100)</label>
                            <input type="number" name="score_cognitive" x-model="scoreCognitive" min="0" max="100" required
                                   class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white rounded-xl text-xs font-black text-center focus:ring-2 focus:ring-emerald-500/30">
                        </div>
                        <div>
                            <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Nilai Adab (0 - 100)</label>
                            <input type="number" name="score_adab" x-model="scoreAdab" min="0" max="100"
                                   class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white rounded-xl text-xs font-black text-center focus:ring-2 focus:ring-emerald-500/30">
                        </div>
                    </div>

                    {{-- Predikat Terkalkulasi Otomatis --}}
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl border border-emerald-100 dark:border-emerald-900 flex items-center justify-between">
                        <span class="text-[11px] font-extrabold text-emerald-800 dark:text-emerald-300 uppercase">Predikat Terkalkulasi Otomatis:</span>
                        <span class="px-3 py-1 bg-emerald-600 text-white rounded-lg text-xs font-extrabold shadow-2xs" x-text="calculatedPredicate"></span>
                    </div>

                    {{-- Catatan Tambahan Guru --}}
                    <div>
                        <label class="block text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">Catatan Tambahan Guru (Khidmat)</label>
                        <textarea name="teacher_notes" rows="2" placeholder="e.g. MasyaAllah penekanan qalqalah kubra di akhir ayat sudah mantap..."
                                  class="w-full px-3.5 py-2.5 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white rounded-xl text-xs font-medium focus:ring-2 focus:ring-emerald-500/30"></textarea>
                    </div>

                    <button type="submit"
                            class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 active:scale-98 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-600/20 transition-all flex items-center justify-center gap-2">
                        <span>Simpan &amp; Terbitkan Hasil Halaqah</span>
                    </button>
                </form>
            </div>

            {{-- Kolom Kanan: Tabel Riwayat Penilaian Realtime --}}
            <div class="lg:col-span-7 bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider flex items-center gap-1.5">
                        <span>📜 CATATAN RIWAYAT PEMBELAJARAN JILID/HAFALAN</span>
                    </h4>
                    <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-full text-[10px] font-extrabold">
                        Total {{ $totalRecordsCount }} Data
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800">
                                <th class="py-2.5 pr-2">Tanggal</th>
                                <th class="py-2.5 px-2">Santri</th>
                                <th class="py-2.5 px-2">Materi</th>
                                <th class="py-2.5 px-2 text-center">Predikat</th>
                                <th class="py-2.5 px-2 text-center">Nilai</th>
                                <th class="py-2.5 pl-2 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($records as $rec)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                <td class="py-3 pr-2 text-slate-500 font-bold whitespace-nowrap">
                                    {{ $rec->assessment_date->format('Y-m-d') }}
                                </td>
                                <td class="py-3 px-2">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-bold text-slate-900 dark:text-white">{{ $rec->student->user?->name ?? 'Santri' }}</span>
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                                            {{ strtoupper($rec->attendance_status) }}
                                        </span>
                                    </div>
                                    <span class="text-[10px] text-slate-400">{{ $rec->class->name ?? '-' }}</span>
                                </td>
                                <td class="py-3 px-2">
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $rec->program_type === 'tahfidz' ? 'bg-teal-50 text-teal-700 dark:bg-teal-950 dark:text-teal-300' : 'bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300' }}">
                                        {{ $rec->material_summary }}
                                    </span>
                                </td>
                                <td class="py-3 px-2 text-center">
                                    <span class="font-extrabold text-emerald-600 dark:text-emerald-400">{{ $rec->predicate }}</span>
                                </td>
                                <td class="py-3 px-2 text-center whitespace-nowrap">
                                    <div class="font-black text-slate-900 dark:text-white">{{ $rec->score_cognitive }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $rec->score_adab }} Adab</div>
                                </td>
                                <td class="py-3 pl-2 text-right">
                                    <form action="{{ route('admin.halaqah.destroy', $rec->id) }}" method="POST" onsubmit="return confirm('Hapus catatan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/50 rounded-lg transition" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    Belum ada data evaluasi pembelajaran Al-Qur'an.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="pt-2">
                    {{ $records->links() }}
                </div>
            </div>

        </div>

        {{-- MODE 2: MASSAL SATU KELAS (Screenshot 2) --}}
        @elseif($mode === 'mass')
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-5">
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-4">
                <div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <span>👥 PENILAIAN MASSAL KELAS AL-QUR'AN</span>
                    </h3>
                    <p class="text-xs text-slate-400">Lakukan penilaian cepat sekaligus untuk seluruh santri yang diajar dalam tingkat/level kelas yang sama</p>
                </div>

                {{-- Filter Kelas untuk Pengisian Massal --}}
                <div class="flex items-center gap-3">
                    <div>
                        <select onchange="location.href = '{{ route('admin.halaqah.index') }}?tab=input&mode=mass&class_id=' + this.value"
                                class="px-3.5 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white rounded-xl text-xs font-bold">
                            <option value="">-- Pilih Kelas Santri --</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->students_count ?? 0 }} Santri)
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            @if($selectedClassId && count($students) > 0)
            <form action="{{ route('admin.halaqah.store-mass') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
                
                {{-- Fast-Grading Template Toolbar (Salin Template ke Semua Santri) --}}
                <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="text-amber-500 font-bold text-xs">⚡ TEMPLATE PENGISIAN MASSAL (CEPAT)</span>
                        </div>
                        <button type="button" @click="applyTemplateToAll()"
                                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs shadow-xs transition flex items-center gap-1.5 self-start sm:self-auto">
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

                {{-- Matriks Seluruh Santri di Kelas --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="text-[11px] font-extrabold text-slate-500 uppercase border-b border-slate-200 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-800/50">
                                <th class="py-3 px-3">Santri (Siswa)</th>
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
                                    <div class="text-[10px] text-slate-400">NIS: {{ $st->nis ?? '-' }} • NISN: {{ $st->nisn ?? '-' }}</div>
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
                    <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-lg shadow-emerald-600/20 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>SIMPAN SELURUH NILAI SEKALIGUS</span>
                    </button>
                </div>
            </form>
            @else
            <div class="p-8 text-center text-slate-400">
                Pilih kelas aktif terlebih dahulu untuk menampilkan daftar santri satu kelas.
            </div>
            @endif
        </div>
        @endif

    </div>
    @endif

    {{-- ========================================================================= --}}
    {{-- TAB 2: LAPORAN HARIAN & BULANAN + GRAFIK STATISTIK (Screenshot 3) --}}
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
                    <span class="text-slate-700 dark:text-slate-300 font-extrabold">>= 90 Skala Angka</span>
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
                <span>🗓️ REKAPITULASI KEHADIRAN HALAQAH SANTRI</span>
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

</div>
@endsection
