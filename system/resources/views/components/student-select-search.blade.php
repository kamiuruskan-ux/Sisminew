@props([
    'students' => [],
    'name' => 'student_id',
    'selected' => '',
    'placeholder' => '-- Cari & Pilih Siswa (Nama / NISN / Kelas) --',
    'allowAll' => false,
    'allLabel' => 'Semua Siswa',
    'required' => false,
    'label' => '',
    'classId' => null,
    'accentColor' => 'indigo',
    'id' => null,
])

@php
    $elementId = $id ?? 'student_select_' . \Illuminate\Support\Str::random(8);
    $studentsArray = collect($students)->map(function($s) {
        return [
            'id' => is_array($s) ? $s['id'] : $s->id,
            'name' => is_array($s) ? ($s['name'] ?? 'Siswa') : ($s->user->name ?? $s->name ?? $s->nisn ?? 'Siswa'),
            'nisn' => is_array($s) ? ($s['nisn'] ?? '-') : ($s->nisn ?? '-'),
            'class_id' => is_array($s) ? ($s['class_id'] ?? null) : ($s->class_id ?? null),
            'class_name' => is_array($s) ? ($s['class_name'] ?? 'Tanpa Kelas') : ($s->class->name ?? 'Tanpa Kelas'),
        ];
    })->values();

    $activeBg = match($accentColor) {
        'rose' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 font-bold border border-rose-500/30',
        'purple' => 'bg-purple-500/10 text-purple-600 dark:text-purple-400 font-bold border border-purple-500/30',
        'amber' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 font-bold border border-amber-500/30',
        'emerald' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold border border-emerald-500/30',
        default => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-bold border border-indigo-500/30',
    };
    $focusRing = match($accentColor) {
        'rose' => 'focus:ring-rose-500/30 focus:border-rose-500',
        'purple' => 'focus:ring-purple-500/30 focus:border-purple-500',
        'amber' => 'focus:ring-amber-500/30 focus:border-amber-500',
        'emerald' => 'focus:ring-emerald-500/30 focus:border-emerald-500',
        default => 'focus:ring-indigo-500/30 focus:border-indigo-500',
    };
@endphp

<div class="relative w-full" :class="{ 'z-50': searchOpen, 'z-10': !searchOpen }" id="{{ $elementId }}" x-data="{
    studentId: '{{ $selected }}',
    filterClassId: '{{ $classId }}',
    searchOpen: false,
    studentSearch: '',
    studentsList: {{ json_encode($studentsArray) }},
    get selectedStudentName() {
        if (!this.studentId) return '{{ $allowAll ? $allLabel : $placeholder }}';
        const found = this.studentsList.find(s => s.id == this.studentId);
        return found ? found.name + ' (NISN: ' + (found.nisn || '-') + ' • Kelas ' + found.class_name + ')' : '{{ $allowAll ? $allLabel : $placeholder }}';
    },
    get filteredStudents() {
        let list = this.studentsList;
        if (this.filterClassId) {
            list = list.filter(s => s.class_id == this.filterClassId);
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

    @if($label)
        <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">
            {{ $label }} @if($required)<span class="text-rose-500">*</span>@endif
        </label>
    @endif

    <input type="hidden" name="{{ $name }}" x-model="studentId" @if($required) required @endif>

    <button type="button" @click="searchOpen = !searchOpen" 
            class="w-full h-10 px-3.5 bg-slate-50 dark:bg-[#24303F] border border-slate-200 dark:border-[#2E3A47] text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold flex items-center justify-between {{ $focusRing }} outline-none transition shadow-2xs text-left">
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
            @if($allowAll)
                <button type="button" @click="studentId = ''; searchOpen = false" 
                        :class="!studentId ? '{{ $activeBg }}' : 'hover:bg-slate-100 dark:hover:bg-[#1A222C] text-slate-800 dark:text-white font-medium'"
                        class="w-full text-left p-2.5 rounded-xl text-xs flex items-center justify-between transition">
                    <span>{{ $allLabel }}</span>
                </button>
            @endif

            <template x-for="s in filteredStudents" :key="s.id">
                <button type="button" @click="studentId = s.id; searchOpen = false" 
                        :class="studentId == s.id ? '{{ $activeBg }}' : 'hover:bg-slate-100 dark:hover:bg-[#1A222C] text-slate-800 dark:text-white font-medium'"
                        class="w-full text-left p-2.5 rounded-xl text-xs flex items-center justify-between transition">
                    <div>
                        <p class="font-bold" x-text="s.name"></p>
                        <p class="text-[10px] text-slate-400" x-text="'NISN: ' + (s.nisn || '-')"></p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 shrink-0" x-text="s.class_name"></span>
                </button>
            </template>

            <div x-show="filteredStudents.length === 0" class="p-4 text-center text-xs text-slate-400 font-semibold">
                Tidak ada siswa yang sesuai kata kunci pencarian.
            </div>
        </div>
    </div>
</div>
