{{-- FORM FILTER KOMPREHENSIF HALAQAH AL-QUR'AN --}}
<div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4"
     x-data="{ timeMode: '{{ $timeFilter }}' }">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800 gap-2">
        <div class="flex items-center gap-2 flex-wrap">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider">
                Menu Filter &amp; Pencarian Terpadu
            </h4>
            @if($isAdmin)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                    👑 Akses Admin: Menampilkan Seluruh Sekolah / Per Guru
                </span>
            @else
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                    📖 Bimbingan Ust. {{ $activeTeacher->name }}
                </span>
            @endif
        </div>
        <div class="text-[11px] text-slate-400 font-medium">
            Filter otomatis menyaring grafik, riwayat, dan rekapitulasi kehadiran
        </div>
    </div>

    <form action="{{ route('admin.halaqah.index') }}" method="GET" class="space-y-4">
        <input type="hidden" name="tab" value="{{ $tab }}">

        {{-- Baris 1: Guru (Admin), Tingkat Kelas, Kelas Rombel, Program, Jilid, Juz --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            
            {{-- 1. Filter Guru Pembimbing (Hanya untuk Admin / Super Admin / Kepala Sekolah) --}}
            @if($isAdmin)
            <div>
                <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Guru Pembimbing</label>
                <select name="teacher_id" class="w-full px-2.5 py-2 border border-indigo-200 dark:border-indigo-800 bg-indigo-50/40 dark:bg-indigo-950/30 text-indigo-950 dark:text-indigo-200 rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="all" {{ $filterTeacherId === 'all' ? 'selected' : '' }}>👥 Semua Guru Pembimbing</option>
                    @foreach($quranTeachers as $t)
                        <option value="{{ $t->id }}" {{ (string)$filterTeacherId === (string)$t->id ? 'selected' : '' }}>
                            Ust. {{ $t->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- 2. Filter Tingkat Kelas --}}
            <div class="{{ !$isAdmin ? 'sm:col-span-1' : '' }}">
                <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Tingkat Kelas</label>
                <select name="grade" class="w-full px-2.5 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="all" {{ $filterGrade === 'all' ? 'selected' : '' }}>Semua Tingkat</option>
                    @foreach($availableGrades as $g)
                        <option value="{{ $g }}" {{ (string)$filterGrade === (string)$g ? 'selected' : '' }}>Tingkat {{ $g }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 3. Filter Rombel Kelas --}}
            <div>
                <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Kelas Asal (Rombel)</label>
                <select name="class_id" class="w-full px-2.5 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="all" {{ $filterClassId === 'all' ? 'selected' : '' }}>Semua Kelas</option>
                    @foreach($allClasses as $c)
                        <option value="{{ $c->id }}" {{ (string)$filterClassId === (string)$c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- 4. Filter Program --}}
            <div>
                <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Program Halaqah</label>
                <select name="program" class="w-full px-2.5 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="all" {{ $filterProgram === 'all' ? 'selected' : '' }}>Semua Program</option>
                    <option value="tahsin" {{ $filterProgram === 'tahsin' ? 'selected' : '' }}>Tahsin (Bimbingan)</option>
                    <option value="tahfidz" {{ $filterProgram === 'tahfidz' ? 'selected' : '' }}>Tahfidz (Hafalan)</option>
                </select>
            </div>

            {{-- 5. Filter Jilid (Tahsin) --}}
            <div>
                <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Jilid (Tahsin)</label>
                <select name="jilid" class="w-full px-2.5 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="all" {{ $filterJilid === 'all' ? 'selected' : '' }}>Semua Jilid</option>
                    @foreach($allJilidOptions as $jld)
                        <option value="{{ $jld }}" {{ $filterJilid === $jld ? 'selected' : '' }}>{{ $jld }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 6. Filter Hafalan / Juz (Tahfidz) --}}
            <div>
                <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Hafalan / Juz</label>
                <select name="juz" class="w-full px-2.5 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="all" {{ $filterJuz === 'all' ? 'selected' : '' }}>Semua Juz</option>
                    @foreach($allJuzOptions as $jz)
                        <option value="{{ $jz }}" {{ (string)$filterJuz === (string)$jz ? 'selected' : '' }}>Juz {{ $jz }}</option>
                    @endforeach
                </select>
            </div>

        </div>

        {{-- Baris 2: Periode Waktu Dinamis & Pencarian Santri & Tombol Eksekusi --}}
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end pt-2.5 border-t border-slate-100 dark:border-slate-800">
            
            {{-- Mode Periode Waktu --}}
            <div class="sm:col-span-3">
                <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Periode Waktu</label>
                <select name="time_filter" x-model="timeMode"
                        class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="all">Semua Waktu (Sepanjang Masa)</option>
                    <option value="daily">Harian (Per Tanggal)</option>
                    <option value="monthly">Bulanan (Bulan &amp; Tahun)</option>
                    <option value="range">Rentang Tanggal Khusus</option>
                </select>
            </div>

            {{-- Input Harian (Per Tanggal) --}}
            <div class="sm:col-span-3" x-show="timeMode === 'daily'" style="display: none;">
                <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Pilih Tanggal</label>
                <input type="date" name="date" value="{{ $selectedDate }}"
                       class="w-full px-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            {{-- Input Bulanan --}}
            <div class="sm:col-span-3 grid grid-cols-2 gap-2" x-show="timeMode === 'monthly'" style="display: none;">
                <div>
                    <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Bulan</label>
                    <select name="month" class="w-full px-2 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $selectedMonth === $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Tahun</label>
                    <input type="number" name="year" value="{{ $selectedYear }}" min="2020" max="2035"
                           class="w-full px-2 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200">
                </div>
            </div>

            {{-- Input Rentang Tanggal --}}
            <div class="sm:col-span-3 grid grid-cols-2 gap-2" x-show="timeMode === 'range'" style="display: none;">
                <div>
                    <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}"
                           class="w-full px-2 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200">
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}"
                           class="w-full px-2 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200">
                </div>
            </div>

            {{-- Space placeholder ketika 'all' agar flex seimbang --}}
            <div class="sm:col-span-3 hidden sm:flex items-center text-[11px] text-slate-400 italic py-2" x-show="timeMode === 'all'">
                Menampilkan data histori dari awal hingga sekarang.
            </div>

            {{-- Search Box Santri / NISN --}}
            <div class="sm:col-span-4">
                <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Cari Santri / NISN</label>
                <div class="relative">
                    <input type="text" name="search_student" value="{{ $searchStudent }}" placeholder="Ketik nama atau NISN santri..."
                           class="w-full pl-8 pr-3 py-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-xl text-xs font-medium text-slate-700 dark:text-slate-200 outline-none focus:ring-2 focus:ring-emerald-500">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            {{-- Action Buttons (Filter & Reset) --}}
            <div class="sm:col-span-2 flex items-center gap-1.5">
                <button type="submit"
                        class="flex-1 py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black transition flex items-center justify-center gap-1.5 cursor-pointer shadow-xs"
                        title="Terapkan Filter">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Filter</span>
                </button>
                <a href="{{ route('admin.halaqah.index', ['tab' => $tab]) }}"
                   class="py-2 px-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold transition flex items-center justify-center cursor-pointer"
                   title="Reset Filter">
                    ↺ Reset
                </a>
            </div>

        </div>

    </form>
</div>
