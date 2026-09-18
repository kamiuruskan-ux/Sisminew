@props([
    'chapters' => [],
    'name' => 'lms_topic_id',
    'selected' => '',
    'placeholder' => '-- Berdiri Sendiri (Tanpa Tautan Modul LMS) --',
    'required' => false,
    'label' => 'Tautkan ke Sub-Bab Modul LMS (Opsional)',
    'help' => 'Jika dipilih, tugas/ujian ini otomatis muncul dalam alur pembelajaran Sub-Bab modul LMS terkait.',
    'accentColor' => 'indigo',
    'id' => null,
])

@php
    $elementId = $id ?? 'lms_topic_select_' . \Illuminate\Support\Str::random(8);
    $topicsArray = [];
    foreach ($chapters as $ch) {
        $chTitle = is_object($ch) ? $ch->title : ($ch['title'] ?? '');
        $chSubj = is_object($ch) ? $ch->subject : ($ch['subject'] ?? '');
        $topics = is_object($ch) ? $ch->topics : ($ch['topics'] ?? []);
        foreach ($topics as $tp) {
            $tpId = is_object($tp) ? $tp->id : $tp['id'];
            $tpTitle = is_object($tp) ? $tp->title : $tp['title'];
            $topicsArray[] = [
                'id' => $tpId,
                'title' => $tpTitle,
                'chapter_title' => $chTitle,
                'subject' => $chSubj,
            ];
        }
    }

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

<div class="relative w-full" id="{{ $elementId }}" x-data="{
    topicId: '{{ $selected }}',
    searchOpen: false,
    topicSearch: '',
    topicsList: {{ json_encode($topicsArray) }},
    get selectedTopicName() {
        if (!this.topicId) return '{{ $placeholder }}';
        const found = this.topicsList.find(t => t.id == this.topicId);
        return found ? 'Sub-Bab: ' + found.title + ' (Bab: ' + found.chapter_title + ' • ' + found.subject + ')' : '{{ $placeholder }}';
    },
    get filteredTopics() {
        if (!this.topicSearch) return this.topicsList;
        const q = this.topicSearch.toLowerCase();
        return this.topicsList.filter(t => 
            t.title.toLowerCase().includes(q) || 
            (t.chapter_title && t.chapter_title.toLowerCase().includes(q)) ||
            (t.subject && t.subject.toLowerCase().includes(q))
        );
    }
}">

    @if($label)
        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
            {{ $label }} @if($required)<span class="text-rose-500">*</span>@endif
        </label>
    @endif

    <input type="hidden" name="{{ $name }}" x-model="topicId" @if($required) required @endif>

    <button type="button" @click="searchOpen = !searchOpen" 
            class="w-full min-h-[42px] px-3.5 py-2.5 bg-slate-50 dark:bg-[#1A222C] border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold flex items-center justify-between {{ $focusRing }} outline-none transition shadow-xs text-left">
        <span x-text="selectedTopicName" class="truncate" :class="!topicId ? 'text-slate-400' : 'text-slate-800 dark:text-white font-bold'"></span>
        <svg class="w-4 h-4 text-slate-400 shrink-0 ml-2 transition-transform duration-200" :class="{ 'rotate-180': searchOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <!-- Dropdown Search Panel -->
    <div x-show="searchOpen" @click.outside="searchOpen = false" x-cloak
         class="absolute z-50 mt-1 w-full bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-700 rounded-2xl shadow-2xl p-2.5 space-y-2 max-h-72 flex flex-col">
        <div class="relative">
            <input type="text" x-model="topicSearch" placeholder="Ketik kata kunci untuk mencari Sub-Bab, Bab, atau Mapel..." 
                   class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs rounded-xl p-2.5 pl-9 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>

        <div class="overflow-y-auto space-y-1 flex-1 pr-1">
            <button type="button" @click="topicId = ''; searchOpen = false" 
                    :class="!topicId ? '{{ $activeBg }}' : 'hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-800 dark:text-white font-medium'"
                    class="w-full text-left p-2.5 rounded-xl text-xs flex items-center justify-between transition">
                <span>{{ $placeholder }}</span>
            </button>

            <template x-for="t in filteredTopics" :key="t.id">
                <button type="button" @click="topicId = t.id; searchOpen = false" 
                        :class="topicId == t.id ? '{{ $activeBg }}' : 'hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-800 dark:text-white font-medium'"
                        class="w-full text-left p-2.5 rounded-xl text-xs flex items-center justify-between transition">
                    <div class="min-w-0 pr-2">
                        <p class="font-bold truncate text-slate-800 dark:text-white" x-text="'Sub-Bab: ' + t.title"></p>
                        <p class="text-[10px] text-slate-400 truncate" x-text="'Bab: ' + t.chapter_title"></p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 shrink-0 ml-2" x-text="t.subject"></span>
                </button>
            </template>

            <div x-show="filteredTopics.length === 0" class="p-4 text-center text-xs text-slate-400 font-semibold">
                Tidak ada Sub-Bab modul LMS yang sesuai kata kunci pencarian.
            </div>
        </div>
    </div>

    @if($help)
        <p class="text-[11px] text-slate-400 mt-1">{{ $help }}</p>
    @endif
</div>
