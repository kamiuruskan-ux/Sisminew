@extends('layouts.admin')

@section('title', 'Input Presensi Siswa')

@section('content')
<div class="space-y-6" x-data="attendanceForm({{ json_encode($students) }}, '{{ old('class_id', '') }}')">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.attendances.index') }}" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">Input Presensi Siswa</h1>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 ml-9">Catat data kehadiran siswa secara manual perorangan atau rombel kelas.</p>
        </div>
        <div class="flex items-center space-x-2 ml-9 sm:ml-0">
            <a href="{{ route('admin.attendances.index') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl transition border border-slate-200 dark:border-slate-700">
                Kembali ke Log
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs font-semibold space-y-1">
            @foreach($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Card Form -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 shadow-xs">
        <form action="{{ route('admin.attendances.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Mode Switcher -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Metode Input Presensi</span>
                <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl">
                    <button type="button" @click="inputMode = 'single'" :class="{ 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-xs font-bold': inputMode === 'single', 'text-slate-500 dark:text-slate-400 font-semibold': inputMode !== 'single' }" class="px-4 py-1.5 text-xs rounded-lg transition">
                        Siswa Perorangan
                    </button>
                    <button type="button" @click="inputMode = 'bulk'" :class="{ 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-xs font-bold': inputMode === 'bulk', 'text-slate-500 dark:text-slate-400 font-semibold': inputMode !== 'bulk' }" class="px-4 py-1.5 text-xs rounded-lg transition">
                        Input Rombel Kelas
                    </button>
                </div>
            </div>

            <!-- Global Form Header -->
            <div class="grid grid-cols-1 {{ \App\Models\Setting::get('is_vocational', '1') == '1' ? 'sm:grid-cols-3' : 'sm:grid-cols-2' }} gap-4">
                <x-major-class-select 
                    :majors="$majors" 
                    :classes="$classes" 
                    :selected-class="old('class_id')" 
                    required-class="true" 
                    layout="contents" 
                    on-class-change="selectedClassId = $event.target.value" 
                    select-class="w-full h-10 px-3.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-semibold rounded-xl focus:bg-white dark:focus:bg-slate-850 focus:ring-2 focus:ring-indigo-500 outline-none transition" 
                    label-class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5" 
                />

                <!-- Select Date -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Tanggal Presensi <span class="text-rose-500">*</span></label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="w-full h-10 px-3.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-semibold rounded-xl focus:bg-white dark:focus:bg-slate-850 focus:ring-2 focus:ring-indigo-500 outline-none transition">
                </div>
            </div>

            <!-- Single Mode -->
            <div x-show="inputMode === 'single'" class="space-y-4 pt-2">
                <div>
                    <x-student-select-search 
                        :students="$students" 
                        :selected="old('student_id')" 
                        label="Pilih Siswa" 
                        placeholder="-- Cari & Pilih Siswa (Ketik Nama / NISN / Kelas) --" 
                        accent-color="indigo"
                    />
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Status Kehadiran <span class="text-rose-500">*</span></label>
                    <select name="status" class="w-full h-10 px-3.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-semibold rounded-xl focus:bg-white dark:focus:bg-slate-850 focus:ring-2 focus:ring-indigo-500 outline-none transition">
                        <option value="present" {{ old('status') === 'present' ? 'selected' : '' }}>Hadir (Present)</option>
                        <option value="late" {{ old('status') === 'late' ? 'selected' : '' }}>Terlambat (Late)</option>
                        <option value="excused" {{ old('status') === 'excused' ? 'selected' : '' }}>Izin / Sakit (Excused)</option>
                        <option value="absent" {{ old('status') === 'absent' ? 'selected' : '' }}>Alpha / Tanpa Keterangan (Absent)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Catatan / Keterangan</label>
                    <textarea name="notes" rows="3" placeholder="Tambahkan catatan jika diperlukan..." class="w-full p-3.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs rounded-xl focus:bg-white dark:focus:bg-slate-850 focus:ring-2 focus:ring-indigo-500 outline-none transition">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Bulk Mode -->
            <div x-show="inputMode === 'bulk'" class="space-y-4 pt-2">
                <div x-show="!selectedClassId" class="p-8 text-center bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-dashed border-slate-200 dark:border-slate-700 text-slate-500 text-xs font-medium">
                    Silakan pilih kelas rombel di atas terlebih dahulu untuk menampilkan daftar siswa.
                </div>

                <div x-show="selectedClassId" class="space-y-4">
                    <!-- Bulk Controls & Search -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200/80 dark:border-slate-700">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase mr-1">Set Masal:</span>
                            <button type="button" @click="setAllStatus('present')" class="px-2.5 py-1 bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 rounded-lg text-[11px] font-bold hover:bg-emerald-200 transition">Semua Hadir</button>
                            <button type="button" @click="setAllStatus('late')" class="px-2.5 py-1 bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 rounded-lg text-[11px] font-bold hover:bg-amber-200 transition">Semua Late</button>
                            <button type="button" @click="setAllStatus('excused')" class="px-2.5 py-1 bg-indigo-100 text-indigo-800 dark:bg-indigo-950/60 dark:text-indigo-300 rounded-lg text-[11px] font-bold hover:bg-indigo-200 transition">Semua Izin</button>
                            <button type="button" @click="setAllStatus('absent')" class="px-2.5 py-1 bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 rounded-lg text-[11px] font-bold hover:bg-rose-200 transition">Semua Alpha</button>
                        </div>
                        <div class="w-full md:w-64">
                            <input type="text" x-model="searchQuery" placeholder="Cari nama siswa / NISN..." class="w-full h-8 px-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-xs rounded-lg outline-none focus:ring-1 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400 px-1">
                        <span>Daftar Siswa Kelas</span>
                        <span x-text="filteredStudents().length + ' Siswa Ditampilkan'"></span>
                    </div>

                    <!-- Students List -->
                    <div class="border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden divide-y divide-slate-100 dark:divide-slate-800 max-h-[450px] overflow-y-auto">
                        <template x-for="(s, idx) in filteredStudents()" :key="s.id">
                            <div class="p-3.5 bg-white dark:bg-slate-900 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50 dark:hover:bg-slate-850 transition">
                                <div>
                                    <input type="hidden" :name="'attendances[' + s.id + '][student_id]'" :value="s.id">
                                    <div class="flex items-center space-x-2">
                                        <p class="font-bold text-xs text-slate-900 dark:text-white" x-text="s.user ? s.user.name : 'Siswa ID: ' + s.id"></p>
                                        <template x-if="s.major && {{ \App\Models\Setting::get('is_vocational', '1') == '1' ? 'true' : 'false' }}">
                                            <span class="px-2 py-0.5 bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-300 text-[10px] font-bold rounded-full border border-indigo-100 dark:border-indigo-800" x-text="s.major.name"></span>
                                        </template>
                                    </div>
                                    <p class="text-[10px] text-slate-400 font-mono mt-0.5" x-text="'NISN: ' + (s.nisn || '-') + (s.class ? ' • Kelas ' + s.class.name : '')"></p>
                                </div>

                                <div class="flex items-center space-x-3 bg-slate-50 dark:bg-slate-800/80 p-2 rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                                    <label class="inline-flex items-center space-x-1 cursor-pointer">
                                        <input type="radio" :name="'attendances[' + s.id + '][status]'" value="present" x-model="bulkStatus[s.id]" class="text-emerald-600 focus:ring-emerald-500">
                                        <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400">Hadir</span>
                                    </label>
                                    <label class="inline-flex items-center space-x-1 cursor-pointer">
                                        <input type="radio" :name="'attendances[' + s.id + '][status]'" value="late" x-model="bulkStatus[s.id]" class="text-amber-600 focus:ring-amber-500">
                                        <span class="text-xs font-bold text-amber-700 dark:text-amber-400">Terlambat</span>
                                    </label>
                                    <label class="inline-flex items-center space-x-1 cursor-pointer">
                                        <input type="radio" :name="'attendances[' + s.id + '][status]'" value="excused" x-model="bulkStatus[s.id]" class="text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-xs font-bold text-indigo-700 dark:text-indigo-400">Izin</span>
                                    </label>
                                    <label class="inline-flex items-center space-x-1 cursor-pointer">
                                        <input type="radio" :name="'attendances[' + s.id + '][status]'" value="absent" x-model="bulkStatus[s.id]" class="text-rose-600 focus:ring-rose-500">
                                        <span class="text-xs font-bold text-rose-700 dark:text-rose-400">Alpha</span>
                                    </label>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('admin.attendances.index') }}" class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-md shadow-indigo-600/30">
                    Simpan Presensi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function attendanceForm(studentsData, defaultClassId) {
    return {
        inputMode: 'single',
        selectedClassId: defaultClassId || '',
        selectedMajorId: '',
        searchQuery: '',
        bulkStatus: {},
        students: studentsData || [],
        
        init() {
            let initial = {};
            this.students.forEach(s => {
                initial[s.id] = 'present';
            });
            this.bulkStatus = initial;
        },

        filteredStudents() {
            return this.students.filter(s => {
                let matchClass = !this.selectedClassId || s.class_id == this.selectedClassId;
                let matchMajor = !this.selectedMajorId || s.major_id == this.selectedMajorId;
                let name = (s.user ? s.user.name : '').toLowerCase();
                let nisn = (s.nisn || '').toLowerCase();
                let query = this.searchQuery.toLowerCase();
                let matchSearch = !query || name.includes(query) || nisn.includes(query);
                return matchClass && matchMajor && matchSearch;
            });
        },

        setAllStatus(status) {
            this.filteredStudents().forEach(s => {
                this.bulkStatus[s.id] = status;
            });
        }
    };
}
</script>
@endsection
