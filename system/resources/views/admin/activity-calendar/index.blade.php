@extends('layouts.admin')

@section('title', 'Kalender & Absensi Kegiatan Sekolah')
@section('page_title', 'Kalender & Absen Kegiatan')

@section('content')
<div class="space-y-6" x-data="{
    activeTab: '{{ $tab }}',
    isCreateModalOpen: false,
    isEditModalOpen: false,
    editEventData: {
        id: '',
        title: '',
        category: 'academic',
        date_start: '',
        date_end: '',
        time_start: '',
        time_end: '',
        location: '',
        target_audience: 'all',
        class_id: '',
        description: ''
    },
    openEdit(ev) {
        this.editEventData = {
            id: ev.id,
            title: ev.title,
            category: ev.category,
            date_start: ev.date_start ? ev.date_start.substring(0, 10) : '',
            date_end: ev.date_end ? ev.date_end.substring(0, 10) : '',
            time_start: ev.time_start || '',
            time_end: ev.time_end || '',
            location: ev.location || '',
            target_audience: ev.target_audience || 'all',
            class_id: ev.class_id || '',
            description: ev.description || ''
        };
        this.isEditModalOpen = true;
    }
}">
    {{-- Header Banner --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200 dark:border-slate-800 shadow-xs">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="p-3 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900/50 shadow-xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </span>
                <div>
                    <h1 class="text-lg md:text-xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                        <span>KALENDER &amp; ABSENSI KEGIATAN SEKOLAH</span>
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 max-w-3xl">
                        Pusat manajemen jadwal agenda, rapat, perayaan hari besar, ujian, kegiatan ekstrakurikuler, dan pencatatan presensi kehadiran peserta.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <button type="button" @click="isCreateModalOpen = true"
                        class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-primary hover:from-indigo-700 hover:to-indigo-800 text-white font-black text-xs shadow-md shadow-indigo-600/25 transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>TAMBAH AGENDA KEGIATAN</span>
                </button>
            </div>
        </div>

        {{-- Tab Navigation --}}
        <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 mt-6 pt-2 overflow-x-auto">
            <button type="button" @click="activeTab = 'calendar'"
                    :class="activeTab === 'calendar' ? 'border-indigo-600 text-white bg-slate-900 dark:bg-slate-800 shadow-xs' : 'border-transparent text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white bg-slate-50 dark:bg-slate-800/40'"
                    class="inline-flex items-center gap-2 px-4 py-2.5 font-black text-xs rounded-t-xl transition-all border-b-2 cursor-pointer">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>KALENDER &amp; AGENDA KEGIATAN</span>
            </button>

            <button type="button" @click="activeTab = 'attendance'"
                    :class="activeTab === 'attendance' ? 'border-emerald-600 text-white bg-emerald-600 dark:bg-emerald-700 shadow-xs' : 'border-transparent text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white bg-slate-50 dark:bg-slate-800/40'"
                    class="inline-flex items-center gap-2 px-4 py-2.5 font-black text-xs rounded-t-xl transition-all border-b-2 cursor-pointer">
                <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                <span>PENCATATAN ABSEN KEGIATAN</span>
            </button>
        </div>
    </div>

    {{-- TAB 1: KALENDER & AGENDA BULANAN --}}
    <div x-show="activeTab === 'calendar'" class="space-y-6">
        {{-- Bulan Selector & Quick Stats --}}
        <div class="tailadmin-card p-4 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.activity-calendar.index', ['month' => $month == 1 ? 12 : $month - 1, 'year' => $month == 1 ? $year - 1 : $year, 'tab' => 'calendar']) }}"
                   class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div class="text-base font-black text-slate-900 dark:text-white uppercase tracking-tight">
                    {{ \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y') }}
                </div>
                <a href="{{ route('admin.activity-calendar.index', ['month' => $month == 12 ? 1 : $month + 1, 'year' => $month == 12 ? $year + 1 : $year, 'tab' => 'calendar']) }}"
                   class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                <a href="{{ route('admin.activity-calendar.index', ['month' => date('m'), 'year' => date('Y'), 'tab' => 'calendar']) }}"
                   class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline ml-2">
                    Hari Ini
                </a>
            </div>

            <div class="flex items-center gap-3 flex-wrap text-xs font-bold">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span> Akademik
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Keagamaan
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span> Libur
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span> Lomba
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span> Ekskul
                </span>
            </div>
        </div>

        {{-- Kalender Grid Bulanan --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs">
            <div class="grid grid-cols-7 border-b border-slate-200 dark:border-slate-800 bg-slate-50/75 dark:bg-slate-800/40 text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 text-center py-2.5">
                <div>Sen</div>
                <div>Sel</div>
                <div>Rab</div>
                <div>Kam</div>
                <div>Jum</div>
                <div>Sab</div>
                <div class="text-rose-500">Ahad</div>
            </div>

            <div class="grid grid-cols-7 divide-x divide-y divide-slate-100 dark:divide-slate-800">
                {{-- Empty days padding --}}
                @for($i = 0; $i < $dayOfWeekOffset; $i++)
                    <div class="min-h-[110px] bg-slate-50/30 dark:bg-slate-950/20 p-2"></div>
                @endfor

                {{-- Days of the month --}}
                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $currentDateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);
                        $isToday = $currentDateStr === date('Y-m-d');
                        $dayEvents = $events->filter(function($ev) use ($currentDateStr) {
                            $st = $ev->date_start->format('Y-m-d');
                            $en = $ev->date_end ? $ev->date_end->format('Y-m-d') : $st;
                            return $currentDateStr >= $st && $currentDateStr <= $en;
                        });
                        $dayOfWeek = (int)date('N', strtotime($currentDateStr));
                    @endphp
                    <div class="min-h-[110px] p-2 hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition flex flex-col justify-between {{ $isToday ? 'bg-indigo-50/40 dark:bg-indigo-950/20 ring-1 ring-inset ring-indigo-500' : '' }}">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-black {{ $isToday ? 'bg-indigo-600 text-white' : ($dayOfWeek == 7 ? 'text-rose-500' : 'text-slate-700 dark:text-slate-300') }}">
                                {{ $day }}
                            </span>
                            @if($isToday)
                                <span class="text-[9px] font-black text-indigo-600 dark:text-indigo-400 uppercase">Hari Ini</span>
                            @endif
                        </div>

                        <div class="space-y-1 mt-1 overflow-y-auto max-h-[85px]">
                            @foreach($dayEvents as $ev)
                                <div @click="openEdit({{ json_encode($ev) }})"
                                     class="p-1 rounded-lg text-[10px] font-bold truncate cursor-pointer transition hover:opacity-80
                                     @if($ev->category === 'religious') bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300
                                     @elseif($ev->category === 'academic') bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300
                                     @elseif($ev->category === 'holiday') bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300
                                     @elseif($ev->category === 'competition') bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300
                                     @elseif($ev->category === 'extracurricular') bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300
                                     @else bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300
                                     @endif"
                                     title="{{ $ev->title }} ({{ $ev->time_start ?? 'Sepanjang Hari' }})">
                                    {{ $ev->time_start ? substr($ev->time_start, 0, 5) . ' ' : '' }}{{ $ev->title }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        {{-- List Agenda Bulan Ini --}}
        <div class="tailadmin-card p-5 space-y-4">
            <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                <span>📋</span>
                <span>DAFTAR AGENDA &amp; KEGIATAN BULAN {{ strtoupper(\Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y')) }} ({{ $events->count() }} Kegiatan)</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($events as $ev)
                    <div class="p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 hover:border-indigo-400 dark:hover:border-indigo-600 transition flex flex-col justify-between space-y-3">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase
                                    @if($ev->category === 'religious') bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300
                                    @elseif($ev->category === 'academic') bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300
                                    @elseif($ev->category === 'holiday') bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300
                                    @elseif($ev->category === 'competition') bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300
                                    @elseif($ev->category === 'extracurricular') bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300
                                    @else bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300
                                    @endif">
                                    {{ $ev->category_label }}
                                </span>

                                <span class="text-[10px] text-slate-400 font-bold">
                                    {{ $ev->attendances->count() }} Hadir
                                </span>
                            </div>

                            <h4 class="text-sm font-black text-slate-900 dark:text-white">{{ $ev->title }}</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1.5 font-medium">
                                <span>📅</span>
                                <span>{{ $ev->date_start->translatedFormat('d M Y') }}{{ $ev->date_end && $ev->date_end != $ev->date_start ? ' s/d ' . $ev->date_end->translatedFormat('d M Y') : '' }}</span>
                            </p>
                            @if($ev->time_start)
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-1.5">
                                    <span>⏰</span>
                                    <span>{{ substr($ev->time_start, 0, 5) }}{{ $ev->time_end ? ' - ' . substr($ev->time_end, 0, 5) : '' }} WITA</span>
                                </p>
                            @endif
                            @if($ev->location)
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-1.5">
                                    <span>📍</span>
                                    <span>{{ $ev->location }}</span>
                                </p>
                            @endif
                            @if($ev->description)
                                <p class="text-xs text-slate-600 dark:text-slate-300 mt-2 line-clamp-2 bg-white dark:bg-slate-900 p-2 rounded-xl border border-slate-100 dark:border-slate-800">
                                    {{ $ev->description }}
                                </p>
                            @endif
                        </div>

                        <div class="pt-3 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between gap-2">
                            <a href="{{ route('admin.activity-calendar.index', ['tab' => 'attendance', 'event_id' => $ev->id]) }}"
                               class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 font-bold text-xs flex items-center gap-1 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                <span>Absen Kegiatan</span>
                            </a>

                            <div class="flex items-center gap-1">
                                <button type="button" @click="openEdit({{ json_encode($ev) }})"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-slate-800 transition cursor-pointer" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                <form method="POST" action="{{ route('admin.activity-calendar.destroy', $ev->id) }}" onsubmit="return confirm('Hapus kegiatan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-slate-800 transition cursor-pointer" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-slate-400 text-xs">
                        Belum ada agenda kegiatan untuk bulan ini. Klik tombol "Tambah Agenda Kegiatan" di atas.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- TAB 2: PENCATATAN ABSEN KEGIATAN --}}
    <div x-show="activeTab === 'attendance'" class="space-y-6">
        {{-- Event Selector Bar --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full md:w-auto">
                <div class="w-full sm:w-80">
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Pilih Kegiatan Sekolah</label>
                    <select onchange="window.location.href='{{ route('admin.activity-calendar.index') }}?tab=attendance&class_id={{ $selectedClassId }}&event_id=' + this.value"
                            class="h-10 w-full px-3.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600">
                        @foreach($allActiveEvents as $ev)
                            <option value="{{ $ev->id }}" {{ $selectedEventId == $ev->id ? 'selected' : '' }}>
                                📌 {{ $ev->title }} ({{ $ev->date_start->format('d/m/Y') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                @if($selectedEvent && in_array($selectedEvent->target_audience, ['all', 'students', 'class']))
                <div class="w-full sm:w-56">
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Filter Rombel Santri</label>
                    <select onchange="window.location.href='{{ route('admin.activity-calendar.index') }}?tab=attendance&event_id={{ $selectedEventId }}&class_id=' + this.value"
                            class="h-10 w-full px-3.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:border-emerald-600">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>
                                🏫 {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>

            @if($selectedEvent)
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.activity-calendar.export-attendance', $selectedEvent->id) }}"
                   class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Ekspor Excel Daftar Hadir</span>
                </a>
            </div>
            @endif
        </div>

        @if($selectedEvent)
            {{-- Form Absen Santri --}}
            @if(in_array($selectedEvent->target_audience, ['all', 'students', 'class']) && $studentsList->isNotEmpty())
            <form method="POST" action="{{ route('admin.activity-calendar.save-attendance', $selectedEvent->id) }}" class="space-y-4">
                @csrf
                <input type="hidden" name="participant_type" value="student">

                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs">
                    <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50 dark:bg-slate-800/30">
                        <div>
                            <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                                <span>👥</span>
                                <span>Absensi Santri: {{ $selectedEvent->title }}</span>
                            </h3>
                            <p class="text-xs text-slate-400 mt-0.5">
                                Catat kehadiran santri untuk kegiatan ini. Default status adalah Hadir.
                            </p>
                        </div>

                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs shadow-md shadow-emerald-600/25 transition flex items-center gap-2 cursor-pointer shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>SIMPAN ABSENSI SANTRI</span>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-100/70 dark:bg-slate-800 text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 tracking-wider border-b border-slate-200 dark:border-slate-700">
                                    <th class="py-3 px-4 w-12 text-center">No</th>
                                    <th class="py-3 px-4 min-w-[200px]">Nama Santri</th>
                                    <th class="py-3 px-4">Kelas</th>
                                    <th class="py-3 px-4 text-center min-w-[280px]">Status Kehadiran</th>
                                    <th class="py-3 px-4 min-w-[200px]">Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach($studentsList as $sIdx => $st)
                                @php
                                    $att = $existingAttendances->get('s_' . $st->id);
                                    $curStatus = $att?->status ?? 'present';
                                @endphp
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                    <td class="py-3 px-4 text-center font-bold text-slate-400">{{ $sIdx + 1 }}</td>
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-slate-900 dark:text-white">{{ $st->user?->name ?? 'Santri' }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono">NISN: {{ $st->nisn ?? $st->nis ?? '-' }}</div>
                                    </td>
                                    <td class="py-3 px-4 font-bold text-slate-600 dark:text-slate-300">
                                        {{ $st->class->name ?? '-' }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <div class="inline-flex items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-800 rounded-xl"
                                             x-data="{ stat: '{{ $curStatus }}' }">
                                            <input type="hidden" name="attendances[{{ $st->id }}][status]" :value="stat">
                                            <button type="button" @click="stat = 'present'"
                                                    :class="stat === 'present' ? 'bg-emerald-600 text-white font-black shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white font-bold'"
                                                    class="px-2.5 py-1 rounded-lg text-[11px] transition cursor-pointer">
                                                Hadir
                                            </button>
                                            <button type="button" @click="stat = 'permission'"
                                                    :class="stat === 'permission' ? 'bg-blue-600 text-white font-black shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white font-bold'"
                                                    class="px-2.5 py-1 rounded-lg text-[11px] transition cursor-pointer">
                                                Izin
                                            </button>
                                            <button type="button" @click="stat = 'sick'"
                                                    :class="stat === 'sick' ? 'bg-amber-600 text-white font-black shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white font-bold'"
                                                    class="px-2.5 py-1 rounded-lg text-[11px] transition cursor-pointer">
                                                Sakit
                                            </button>
                                            <button type="button" @click="stat = 'absent'"
                                                    :class="stat === 'absent' ? 'bg-rose-600 text-white font-black shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white font-bold'"
                                                    class="px-2.5 py-1 rounded-lg text-[11px] transition cursor-pointer">
                                                Alpa
                                            </button>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <input type="text" name="attendances[{{ $st->id }}][notes]"
                                               value="{{ $att?->notes ?? '' }}"
                                               placeholder="Catatan..."
                                               class="w-full px-3 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
            @endif

            {{-- Form Absen Guru/Pegawai --}}
            @if(in_array($selectedEvent->target_audience, ['all', 'teachers']) && $teachersList->isNotEmpty())
            <form method="POST" action="{{ route('admin.activity-calendar.save-attendance', $selectedEvent->id) }}" class="space-y-4">
                @csrf
                <input type="hidden" name="participant_type" value="teacher">

                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs">
                    <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50 dark:bg-slate-800/30">
                        <div>
                            <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                                <span>👨‍🏫</span>
                                <span>Absensi Dewan Guru &amp; Pegawai: {{ $selectedEvent->title }}</span>
                            </h3>
                            <p class="text-xs text-slate-400 mt-0.5">
                                Catat kehadiran guru/staf untuk kegiatan ini.
                            </p>
                        </div>

                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-primary hover:from-indigo-700 hover:to-indigo-800 text-white font-black text-xs shadow-md shadow-indigo-600/25 transition flex items-center gap-2 cursor-pointer shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>SIMPAN ABSENSI GURU / STAF</span>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-100/70 dark:bg-slate-800 text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 tracking-wider border-b border-slate-200 dark:border-slate-700">
                                    <th class="py-3 px-4 w-12 text-center">No</th>
                                    <th class="py-3 px-4 min-w-[200px]">Nama Guru / Staf</th>
                                    <th class="py-3 px-4">Jabatan</th>
                                    <th class="py-3 px-4 text-center min-w-[280px]">Status Kehadiran</th>
                                    <th class="py-3 px-4 min-w-[200px]">Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach($teachersList as $tIdx => $tch)
                                @php
                                    $att = $existingAttendances->get('t_' . $tch->id);
                                    $curStatus = $att?->status ?? 'present';
                                @endphp
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                    <td class="py-3 px-4 text-center font-bold text-slate-400">{{ $tIdx + 1 }}</td>
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-slate-900 dark:text-white">{{ $tch->name }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono">NIP: {{ $tch->nip ?: '-' }}</div>
                                    </td>
                                    <td class="py-3 px-4 font-bold text-slate-600 dark:text-slate-300">
                                        {{ $tch->jabatan ?: ($tch->roles->first()?->name ?? 'Staf') }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <div class="inline-flex items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-800 rounded-xl"
                                             x-data="{ stat: '{{ $curStatus }}' }">
                                            <input type="hidden" name="attendances[{{ $tch->id }}][status]" :value="stat">
                                            <button type="button" @click="stat = 'present'"
                                                    :class="stat === 'present' ? 'bg-emerald-600 text-white font-black shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white font-bold'"
                                                    class="px-2.5 py-1 rounded-lg text-[11px] transition cursor-pointer">
                                                Hadir
                                            </button>
                                            <button type="button" @click="stat = 'permission'"
                                                    :class="stat === 'permission' ? 'bg-blue-600 text-white font-black shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white font-bold'"
                                                    class="px-2.5 py-1 rounded-lg text-[11px] transition cursor-pointer">
                                                Izin
                                            </button>
                                            <button type="button" @click="stat = 'sick'"
                                                    :class="stat === 'sick' ? 'bg-amber-600 text-white font-black shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white font-bold'"
                                                    class="px-2.5 py-1 rounded-lg text-[11px] transition cursor-pointer">
                                                Sakit
                                            </button>
                                            <button type="button" @click="stat = 'absent'"
                                                    :class="stat === 'absent' ? 'bg-rose-600 text-white font-black shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white font-bold'"
                                                    class="px-2.5 py-1 rounded-lg text-[11px] transition cursor-pointer">
                                                Alpa
                                            </button>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <input type="text" name="attendances[{{ $tch->id }}][notes]"
                                               value="{{ $att?->notes ?? '' }}"
                                               placeholder="Catatan..."
                                               class="w-full px-3 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
            @endif
        @else
            <div class="tailadmin-card p-12 text-center text-slate-400">
                Pilih atau buat kegiatan terlebih dahulu untuk memulai absensi.
            </div>
        @endif
    </div>

    {{-- MODAL TAMBAH AGENDA --}}
    <div x-show="isCreateModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.away="isCreateModalOpen = false" class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/40">
                <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">Tambah Agenda Kegiatan Sekolah</h3>
                <button type="button" @click="isCreateModalOpen = false" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.activity-calendar.store') }}" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-black text-slate-700 dark:text-slate-300 mb-1">Nama / Judul Kegiatan *</label>
                    <input type="text" name="title" required placeholder="Contoh: Manasik Haji Cilik, PTS Ganjil, Upacara Hari Santri..."
                           class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-black text-slate-700 dark:text-slate-300 mb-1">Kategori Kegiatan *</label>
                        <select name="category" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white">
                            <option value="academic">Akademik &amp; Ujian</option>
                            <option value="religious">Keagamaan &amp; PHBI</option>
                            <option value="competition">Lomba &amp; Prestasi</option>
                            <option value="holiday">Libur Sekolah</option>
                            <option value="meeting">Rapat &amp; Briefing</option>
                            <option value="extracurricular">Ekstrakurikuler</option>
                            <option value="general">Kegiatan Umum</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-700 dark:text-slate-300 mb-1">Target Peserta *</label>
                        <select name="target_audience" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white">
                            <option value="all">Seluruh Santri &amp; Guru</option>
                            <option value="students">Khusus Seluruh Santri</option>
                            <option value="teachers">Khusus Dewan Guru &amp; Staf</option>
                            <option value="class">Santri Rombel / Kelas Tertentu</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-black text-slate-700 dark:text-slate-300 mb-1">Tanggal Mulai *</label>
                        <input type="date" name="date_start" required value="{{ date('Y-m-d') }}"
                               class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-700 dark:text-slate-300 mb-1">Tanggal Selesai (Opsional)</label>
                        <input type="date" name="date_end"
                               class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-black text-slate-700 dark:text-slate-300 mb-1">Jam Mulai</label>
                        <input type="time" name="time_start" value="07:30"
                               class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-700 dark:text-slate-300 mb-1">Jam Selesai</label>
                        <input type="time" name="time_end" value="11:30"
                               class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-700 dark:text-slate-300 mb-1">Lokasi / Tempat</label>
                    <input type="text" name="location" placeholder="Contoh: Masjid Sekolah, Lapangan Utama, Aula Al-Fahmi..."
                           class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-700 dark:text-slate-300 mb-1">Deskripsi / Keterangan Tambahan</label>
                    <textarea name="description" rows="2" placeholder="Catatan teknis, dresscode, ketentuan..."
                              class="w-full p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs"></textarea>
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                    <button type="button" @click="isCreateModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs shadow-xs">
                        Simpan Kegiatan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT AGENDA --}}
    <div x-show="isEditModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.away="isEditModalOpen = false" class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/40">
                <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">Edit Agenda Kegiatan</h3>
                <button type="button" @click="isEditModalOpen = false" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" :action="'{{ url('admin/activity-calendar') }}/' + editEventData.id" class="p-5 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-black text-slate-700 dark:text-slate-300 mb-1">Nama / Judul Kegiatan *</label>
                    <input type="text" name="title" required x-model="editEventData.title"
                           class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-black text-slate-700 dark:text-slate-300 mb-1">Kategori Kegiatan *</label>
                        <select name="category" required x-model="editEventData.category" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white">
                            <option value="academic">Akademik &amp; Ujian</option>
                            <option value="religious">Keagamaan &amp; PHBI</option>
                            <option value="competition">Lomba &amp; Prestasi</option>
                            <option value="holiday">Libur Sekolah</option>
                            <option value="meeting">Rapat &amp; Briefing</option>
                            <option value="extracurricular">Ekstrakurikuler</option>
                            <option value="general">Kegiatan Umum</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-700 dark:text-slate-300 mb-1">Target Peserta *</label>
                        <select name="target_audience" required x-model="editEventData.target_audience" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white">
                            <option value="all">Seluruh Santri &amp; Guru</option>
                            <option value="students">Khusus Seluruh Santri</option>
                            <option value="teachers">Khusus Dewan Guru &amp; Staf</option>
                            <option value="class">Santri Rombel / Kelas Tertentu</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-black text-slate-700 dark:text-slate-300 mb-1">Tanggal Mulai *</label>
                        <input type="date" name="date_start" required x-model="editEventData.date_start"
                               class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-700 dark:text-slate-300 mb-1">Tanggal Selesai</label>
                        <input type="date" name="date_end" x-model="editEventData.date_end"
                               class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-black text-slate-700 dark:text-slate-300 mb-1">Jam Mulai</label>
                        <input type="time" name="time_start" x-model="editEventData.time_start"
                               class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-700 dark:text-slate-300 mb-1">Jam Selesai</label>
                        <input type="time" name="time_end" x-model="editEventData.time_end"
                               class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-700 dark:text-slate-300 mb-1">Lokasi / Tempat</label>
                    <input type="text" name="location" x-model="editEventData.location"
                           class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-700 dark:text-slate-300 mb-1">Deskripsi</label>
                    <textarea name="description" rows="2" x-model="editEventData.description"
                              class="w-full p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs"></textarea>
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                    <button type="button" @click="isEditModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs shadow-xs">
                        Perbarui Kegiatan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
