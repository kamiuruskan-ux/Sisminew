@props([
    'teachers' => [],
    'name' => 'teacher',
    'selected' => '',
    'placeholder' => '-- Cari & Pilih Guru Pengajar --',
    'allowAll' => false,
    'allLabel' => 'Semua Guru',
    'required' => false,
    'label' => '',
    'valueType' => 'name', // 'name' (string) or 'id' (integer)
    'accentColor' => 'indigo',
    'id' => null,
    'selectClass' => '',
    'labelClass' => '',
])

@php
    $elementId = $id ? $id . '_wrapper' : 'teacher_select_' . \Illuminate\Support\Str::random(8);
    $teachersArray = collect($teachers)->map(function($t) {
        return [
            'id' => (string)(is_array($t) ? ($t['id'] ?? '') : ($t->id ?? '')),
            'name' => is_array($t) ? ($t['name'] ?? 'Guru') : ($t->name ?? 'Guru'),
            'nip' => is_array($t) ? ($t['nip'] ?? '') : ($t->nip ?? ''),
            'email' => is_array($t) ? ($t['email'] ?? '') : ($t->email ?? ''),
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

    $defaultSelectClass = 'w-full px-3.5 py-2.5 bg-slate-50 dark:bg-[#24303F] border border-slate-200 dark:border-[#2E3A47] text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold flex items-center justify-between transition shadow-2xs text-left outline-none';
    $defaultLabelClass = 'block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5';

    $finalSelectClass = $selectClass ?: $defaultSelectClass;
    $finalLabelClass = $labelClass ?: $defaultLabelClass;
@endphp

<div class="relative w-full" :class="{ 'z-50': searchOpen, 'z-10': !searchOpen }" id="{{ $elementId }}" x-data="{
    teacherVal: '{{ $selected }}',
    valueType: '{{ $valueType }}',
    searchOpen: false,
    teacherSearch: '',
    teachersList: {{ \Illuminate\Support\Js::from($teachersArray) }},

    get selectedTeacherText() {
        if (!this.teacherVal) return '{{ $allowAll ? $allLabel : $placeholder }}';
        const found = this.teachersList.find(t => this.valueType === 'id' ? String(t.id) === String(this.teacherVal) : t.name === this.teacherVal);
        if (found) {
            return found.name + (found.nip ? ' (NIP: ' + found.nip + ')' : '');
        }
        return this.teacherVal || '{{ $allowAll ? $allLabel : $placeholder }}';
    },

    get filteredTeachers() {
        if (!this.teacherSearch) return this.teachersList;
        const q = this.teacherSearch.toLowerCase();
        return this.teachersList.filter(t =>
            t.name.toLowerCase().includes(q) ||
            (t.nip && t.nip.toLowerCase().includes(q)) ||
            (t.email && t.email.toLowerCase().includes(q))
        );
    },

    selectTeacher(t) {
        this.teacherVal = this.valueType === 'id' ? t.id : t.name;
        this.searchOpen = false;
        if (this.$refs.hiddenInput) {
            this.$refs.hiddenInput.value = this.teacherVal;
            this.$refs.hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
            this.$refs.hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }
}" x-init="
    if ($refs.hiddenInput) {
        $watch('teacherVal', (val) => {
            $refs.hiddenInput.value = val || '';
        });
    }
">

    @if($label)
        <label class="{{ $finalLabelClass }}">
            {{ $label }} @if($required)<span class="text-rose-500">*</span>@endif
        </label>
    @endif

    <input type="hidden" name="{{ $name }}" x-ref="hiddenInput" x-model="teacherVal" @if($id) id="{{ $id }}" @endif @if($required) required @endif>

    <button type="button" @click="searchOpen = !searchOpen" 
            class="{{ $finalSelectClass }} {{ $focusRing }}">
        <span x-text="selectedTeacherText" class="truncate"></span>
        <svg class="w-4 h-4 text-slate-400 shrink-0 ml-2 transition-transform duration-200" :class="{ 'rotate-180': searchOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <!-- Dropdown Search Panel -->
    <div x-show="searchOpen" @click.outside="searchOpen = false" x-cloak
         class="absolute z-50 mt-1 w-full min-w-[280px] bg-white dark:bg-[#24303F] border border-slate-200 dark:border-[#2E3A47] rounded-2xl shadow-2xl p-2.5 space-y-2 max-h-72 flex flex-col">
        <div class="relative">
            <input type="text" x-model="teacherSearch" placeholder="Cari nama guru, NIP, atau email..." 
                   class="w-full bg-slate-50 dark:bg-[#1A222C] border border-slate-200 dark:border-[#2E3A47] text-xs rounded-xl p-2.5 pl-9 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>

        <div class="overflow-y-auto space-y-1 flex-1 pr-1">
            @if($allowAll)
                <button type="button" @click="teacherVal = ''; searchOpen = false" 
                        :class="!teacherVal ? '{{ $activeBg }}' : 'hover:bg-slate-100 dark:hover:bg-[#1A222C] text-slate-800 dark:text-white font-medium'"
                        class="w-full text-left p-2.5 rounded-xl text-xs flex items-center justify-between transition">
                    <span>{{ $allLabel }}</span>
                </button>
            @endif

            <template x-for="t in filteredTeachers" :key="t.id">
                <button type="button" @click="selectTeacher(t)" 
                        :class="(valueType === 'id' ? String(teacherVal) === String(t.id) : teacherVal === t.name) ? '{{ $activeBg }}' : 'hover:bg-slate-100 dark:hover:bg-[#1A222C] text-slate-800 dark:text-white font-medium'"
                        class="w-full text-left p-2.5 rounded-xl text-xs flex items-center justify-between transition">
                    <div>
                        <p class="font-bold text-slate-900 dark:text-white" x-text="t.name"></p>
                        <p class="text-[10px] text-slate-400" x-text="t.nip ? 'NIP: ' + t.nip : (t.email || '-')"></p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shrink-0" x-text="t.nip ? 'Guru' : 'Pengajar'"></span>
                </button>
            </template>

            <div x-show="filteredTeachers.length === 0" class="p-4 text-center text-xs text-slate-400 font-semibold">
                Tidak ada data guru yang sesuai kata kunci pencarian.
            </div>
        </div>
    </div>
</div>
