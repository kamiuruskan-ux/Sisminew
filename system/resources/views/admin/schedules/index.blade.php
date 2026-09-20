@extends('layouts.admin')

@section('title', 'Jadwal Pelajaran')
@section('page_title', 'Manajemen Jadwal Pelajaran')

@section('content')
<div class="space-y-6" x-data="{
    selected: [],
    selectAll: false,
    showDeleteModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    isBulkDelete: false,

    toggleSelectAll() {
        if (this.selectAll) {
            this.selected = [{{ implode(',', $schedules->pluck('id')->toArray()) }}];
        } else {
            this.selected = [];
        }
    },
    updateSelectAll() {
        const allIds = [{{ implode(',', $schedules->pluck('id')->toArray()) }}];
        this.selectAll = allIds.length > 0 && allIds.every(id => this.selected.includes(id));
    },
    confirmDelete(id, name) {
        this.isBulkDelete = false;
        this.deleteTarget = { id: id, name: name };
        this.deleteFormAction = '{{ url('admin/schedules') }}/' + id;
        this.showDeleteModal = true;
    },
    confirmBulkDelete() {
        if (this.selected.length === 0) return;
        this.isBulkDelete = true;
        this.deleteTarget = { id: null, name: this.selected.length + ' jadwal pelajaran terpilih' };
        this.deleteFormAction = '{{ route('admin.schedules.bulk-destroy') }}';
        this.showDeleteModal = true;
    }
}">
    @component('components.delete-modal', ['title' => 'Hapus Jadwal Pelajaran', 'message' => 'Apakah Anda yakin ingin menghapus <strong x-text="deleteTarget?.name"></strong>? Tindakan ini tidak dapat dibatalkan.'])
        <template x-if="isBulkDelete">
            <template x-for="id in selected" :key="id">
                <input type="hidden" name="ids[]" :value="id">
            </template>
        </template>
    @endcomponent


    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Jadwal Pelajaran</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola alokasi jadwal mata pelajaran, filter per guru, ruangan, hari, dan kelas secara terpusat.</p>
        </div>
        <div class="flex items-center space-x-3">
            <button type="button" onclick="openModal('createModal')" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-98 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-indigo-600/20 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Jadwal Baru</span>
            </button>
        </div>
    </div>


    <!-- KPI Summary Ribbon -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-2xs flex items-center justify-between hover:border-indigo-300 transition-all">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Sesi Jadwal</p>
                <p class="text-2xl font-extrabold text-slate-900 mt-1 tracking-tight">{{ number_format($totalSchedules) }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100/80 shrink-0 shadow-2xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-2xs flex items-center justify-between hover:border-emerald-300 transition-all">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Jadwal Aktif Berjalan</p>
                <p class="text-2xl font-extrabold text-emerald-600 mt-1 tracking-tight">{{ number_format($activeSchedules) }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100/80 shrink-0 shadow-2xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-2xs flex items-center justify-between hover:border-blue-300 transition-all">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Ruangan Digunakan</p>
                <p class="text-2xl font-extrabold text-blue-600 mt-1 tracking-tight">{{ number_format($totalRooms) }} Ruang</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100/80 shrink-0 shadow-2xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Filters & Search Bar (Real-Time Auto Submit) -->
    <div class="bg-white dark:bg-[#24303F] rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5 shadow-2xs">
        <form method="GET" action="{{ route('admin.schedules.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-3.5 items-end">
            <!-- 1. Search Keyword -->
            <div class="sm:col-span-2 lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Cari Pelajaran / Ruang</label>
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Mata pelajaran, nama sesi, ruang..." 
                           @input.debounce.400ms="$el.closest('form').submit()"
                           x-init="if ('{{ request('search') }}') { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length); }"
                           class="w-full bg-slate-50 dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-xl px-3.5 py-2.5 pr-8 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition outline-none">
                    @if(request('search'))
                        <a href="{{ route('admin.schedules.index', request()->except('search')) }}" class="absolute right-3 top-2.5 text-slate-400 hover:text-rose-500 font-bold text-sm" title="Hapus Pencarian">&times;</a>
                    @endif
                </div>
            </div>

            <!-- 2. Filter Guru Pengajar -->
            <div class="sm:col-span-2 lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Guru Pengajar</label>
                <select name="teacher" @change="$el.closest('form').submit()" class="w-full bg-slate-50 dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition outline-none font-medium">
                    <option value="">Semua Guru Pengajar</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->name }}" {{ request('teacher') == $teacher->name ? 'selected' : '' }}>
                            {{ $teacher->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 3 & 4. Jurusan & Kelas -->
            <x-major-class-select 
                :selected-major="request('major_id')" 
                :selected-class="request('class_id')" 
                :is-filter="true" 
                layout="inline" 
                major-label="Jurusan" 
                class-label="Kelas" 
                select-class="w-full bg-slate-50 dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition outline-none" 
                label-class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5" 
                :on-major-change="'$el.closest(\'form\').submit()'"
                :on-class-change="'$el.closest(\'form\').submit()'"
            />

            <!-- 5. Filter Hari -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Hari</label>
                <select name="day" @change="$el.closest('form').submit()" class="w-full bg-slate-50 dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition outline-none">
                    <option value="">Semua Hari</option>
                    @foreach($days as $dayCode => $dayName)
                        <option value="{{ $dayCode }}" {{ request('day') == $dayCode ? 'selected' : '' }}>
                            {{ $dayName }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 6. Filter Ruangan -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Ruangan</label>
                <select name="room" @change="$el.closest('form').submit()" class="w-full bg-slate-50 dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition outline-none">
                    <option value="">Semua Ruang</option>
                    @foreach($roomsList as $roomOption)
                        <option value="{{ $roomOption }}" {{ request('room') == $roomOption ? 'selected' : '' }}>
                            {{ $roomOption }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 7. Urutan (Sorting) -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Urutan</label>
                <select name="sort" @change="$el.closest('form').submit()" class="w-full bg-slate-50 dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition outline-none font-semibold">
                    <option value="day_asc" {{ request('sort', $sort ?? 'day_asc') === 'day_asc' ? 'selected' : '' }}>Hari & Jam Mulai</option>
                    <option value="teacher_asc" {{ request('sort', $sort ?? '') === 'teacher_asc' ? 'selected' : '' }}>Guru (A - Z)</option>
                    <option value="class_asc" {{ request('sort', $sort ?? '') === 'class_asc' ? 'selected' : '' }}>Urutan Kelas</option>
                    <option value="latest" {{ request('sort', $sort ?? '') === 'latest' ? 'selected' : '' }}>Terbaru</option>
                </select>
            </div>

            <!-- 8. Per Page Filter -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Tampilkan</label>
                <select name="per_page" @change="$el.closest('form').submit()" class="w-full bg-slate-50 dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white text-xs rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition outline-none font-semibold">
                    <option value="10" {{ request('per_page', $perPage ?? 20) == '10' ? 'selected' : '' }}>10 Baris</option>
                    <option value="20" {{ request('per_page', $perPage ?? 20) == '20' ? 'selected' : '' }}>20 Baris</option>
                    <option value="50" {{ request('per_page', $perPage ?? 20) == '50' ? 'selected' : '' }}>50 Baris</option>
                    <option value="100" {{ request('per_page', $perPage ?? 20) == '100' ? 'selected' : '' }}>100 Baris</option>
                </select>
            </div>

            <!-- Reset Filter Button (If Active) -->
            @if(request()->hasAny(['search', 'teacher', 'major_id', 'class_id', 'day', 'room', 'sort', 'per_page']))
            <div class="sm:col-span-2 lg:col-span-2 xl:col-span-2">
                <a href="{{ route('admin.schedules.index') }}" class="inline-flex items-center justify-center w-full px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-bold rounded-xl transition border border-slate-200 dark:border-slate-700">
                    <svg class="w-4 h-4 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Reset Semua Filter</span>
                </a>
            </div>
            @endif
        </form>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white dark:bg-[#24303F] rounded-2xl border border-slate-200/80 dark:border-slate-800 overflow-hidden shadow-2xs">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/60 dark:bg-[#1A222C]/40">
            <div>
                <h3 class="font-bold text-slate-900 dark:text-white text-sm">Daftar Sesi Jadwal Pelajaran</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Menampilkan {{ $schedules->firstItem() ?? 0 }} - {{ $schedules->lastItem() ?? 0 }} dari {{ $schedules->total() }} total jadwal</p>
            </div>
            @if(request('teacher'))
                <span class="inline-flex items-center px-3 py-1 bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300 rounded-lg text-xs font-bold border border-indigo-200 dark:border-indigo-800">
                    Guru: {{ request('teacher') }}
                </span>
            @endif
        </div>

        <!-- Bulk Action Banner Bar -->
        <div x-show="selected.length > 0" x-cloak x-transition class="flex items-center justify-between px-6 py-3 bg-indigo-50 dark:bg-indigo-950/40 border-b border-indigo-200 dark:border-indigo-800">
            <div class="flex items-center space-x-2 text-indigo-700 dark:text-indigo-300 font-bold text-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span x-text="selected.length + ' jadwal pelajaran dipilih'"></span>
            </div>
            <div class="flex items-center space-x-2">
                <button type="button" @click="confirmBulkDelete()" class="px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition shadow-xs flex items-center space-x-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    <span>Hapus Terpilih</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 dark:bg-[#1A222C] text-slate-500 dark:text-slate-400 uppercase tracking-wider font-bold text-[11px] border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 w-10 text-center">
                            <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer" title="Pilih Semua">
                        </th>
                        <th class="px-5 py-3.5">Mata Pelajaran & Sesi</th>
                        <th class="px-5 py-3.5">Guru Pengajar</th>
                        <th class="px-5 py-3.5">Kelas Target</th>
                        <th class="px-5 py-3.5">Hari & Jam Pelaksanaan</th>
                        <th class="px-5 py-3.5">Ruangan</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                    @forelse($schedules as $schedule)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-[#1A222C]/50 transition-colors" :class="selected.includes({{ $schedule->id }}) ? 'bg-indigo-50/40 dark:bg-indigo-950/20' : ''">
                            <!-- Checkbox -->
                            <td class="px-4 py-4 text-center">
                                <input type="checkbox" :value="{{ $schedule->id }}" x-model="selected" @change="updateSelectAll()" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                            </td>

                            <!-- Subject & Name -->
                            <td class="px-5 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 font-extrabold flex items-center justify-center text-xs border border-indigo-100 dark:border-indigo-900 shrink-0 shadow-2xs">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-slate-900 dark:text-white text-xs">{{ $schedule->subject }}</p>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">{{ $schedule->name }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Teacher -->
                            <td class="px-5 py-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center font-bold text-[11px] shrink-0 border border-slate-200 dark:border-slate-700">
                                        {{ strtoupper(substr($schedule->teacher, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.schedules.index', ['teacher' => $schedule->teacher]) }}" class="font-bold text-slate-900 dark:text-white text-xs hover:text-indigo-600 hover:underline" title="Lihat semua jadwal guru ini">
                                            {{ $schedule->teacher }}
                                        </a>
                                    </div>
                                </div>
                            </td>

                            <!-- Class -->
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-lg font-bold text-xs border border-slate-200/80 dark:border-slate-700">
                                    {{ $schedule->class->name ?? '-' }}
                                </span>
                            </td>

                            <!-- Day & Time -->
                            <td class="px-5 py-4">
                                <div class="space-y-1">
                                    <span class="inline-flex px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 font-extrabold text-[10px] border border-indigo-200/80 dark:border-indigo-800">
                                        {{ $days[$schedule->day] ?? $schedule->day }}
                                    </span>
                                    <p class="text-xs font-mono font-bold text-slate-900 dark:text-white flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>{{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}</span>
                                    </p>
                                </div>
                            </td>

                            <!-- Room -->
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 rounded-lg text-xs font-mono font-bold border border-blue-200 dark:border-blue-900">
                                    {{ $schedule->room }}
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="px-5 py-4 text-center">
                                @if($schedule->is_active)
                                    <span class="inline-flex px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-[10px] font-extrabold uppercase tracking-wider">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700 text-[10px] font-extrabold uppercase tracking-wider">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <!-- Action Buttons -->
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end space-x-1">
                                    <button type="button" onclick="openEditModal({{ json_encode($schedule) }})"
                                            class="p-2 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 rounded-xl transition-all cursor-pointer" title="Edit Jadwal">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    <button type="button" @click="confirmDelete({{ $schedule->id }}, '{{ addslashes(($schedule->subject ?: 'Jadwal') . ' - ' . ($schedule->teacher ?: '') . ' (' . ($schedule->class->name ?? '') . ')') }}')" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/50 rounded-xl transition-all cursor-pointer" title="Hapus Jadwal">
                                         <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                             <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                         </svg>
                                     </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400 text-xs font-semibold">
                                <div class="max-w-sm mx-auto space-y-2">
                                    <svg class="w-10 h-10 mx-auto text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <p class="font-bold text-slate-600 dark:text-slate-300">Tidak ada data jadwal pelajaran yang ditemukan.</p>
                                    <p class="text-[11px] text-slate-400">Silakan sesuaikan filter guru, kelas, atau hari di atas.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($schedules->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-[#1A222C]/40">
                {{ $schedules->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Create Schedule Modal -->
<div id="createModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 overflow-y-auto flex items-center justify-center p-4" onclick="closeModal('createModal')">
    <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full border border-slate-200 my-8 transform transition-all relative" onclick="event.stopPropagation()">
        <!-- Modal Header -->
        <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100 bg-slate-50/80 rounded-t-2xl">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Tambah Jadwal Pelajaran</h3>
                    <p class="text-xs text-slate-500">Lengkapi rincian sesi mata pelajaran, kelas, waktu, dan tentukan guru pengajar.</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('createModal')" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-600 transition-colors flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('admin.schedules.store') }}" method="POST" class="p-6 md:p-8 space-y-5">
            @csrf

            <!-- Field Grid (2 Columns) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Nama Sesi / Jadwal (Full Width) -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Sesi / Jadwal <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Matematika Wajib - Sesi Pagi" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition outline-none">
                </div>

                <!-- Mata Pelajaran -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Mata Pelajaran <span class="text-rose-500">*</span></label>
                    @php
                        $sList = (isset($subjectsList) && count($subjectsList) > 0) ? $subjectsList : ($globalSubjects ?? []);
                    @endphp
                    <select name="subject" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition outline-none">
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach($sList as $sub)
                            @php $sName = is_object($sub) ? $sub->name : $sub; @endphp
                            <option value="{{ $sName }}">{{ $sName }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Guru Pengajar (Pilih Data Guru) -->
                <div>
                    <x-teacher-select-search 
                        :teachers="$teachers" 
                        name="teacher" 
                        label="Guru Pengajar" 
                        :required="true" 
                        placeholder="-- Cari & Pilih Guru Pengajar --" 
                    />
                </div>

                <!-- Jurusan & Kelas (Full Width 2 Columns) -->
                <div class="md:col-span-2 border-t border-b border-slate-100 py-3 my-1">
                    <x-major-class-select :required="true" major-label="Jurusan / Program Keahlian" class-label="Kelas Target" select-class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition outline-none" label-class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" />
                </div>

                <!-- Hari -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Hari <span class="text-rose-500">*</span></label>
                    <select name="day" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition outline-none">
                        <option value="">-- Pilih Hari --</option>
                        @foreach($days as $dayCode => $dayName)
                            <option value="{{ $dayCode }}">{{ $dayName }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Ruangan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Ruangan <span class="text-rose-500">*</span></label>
                    <input type="text" name="room" required placeholder="Contoh: R.101 / Lab Komputer" class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition outline-none">
                </div>

                <!-- Waktu Mulai -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Waktu Mulai <span class="text-rose-500">*</span></label>
                    <input type="time" name="start_time" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition outline-none">
                </div>

                <!-- Waktu Selesai -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Waktu Selesai <span class="text-rose-500">*</span></label>
                    <input type="time" name="end_time" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition outline-none">
                </div>

                <!-- Status Aktif Checkbox Card -->
                <div class="md:col-span-2 bg-slate-50 rounded-xl p-3.5 border border-slate-200/80 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <input type="checkbox" name="is_active" value="1" checked id="is_active_sch_chk" class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500 cursor-pointer">
                        <div>
                            <label for="is_active_sch_chk" class="text-xs font-bold text-slate-800 cursor-pointer">Jadwal Aktif Berjalan</label>
                            <p class="text-[11px] text-slate-500">Jadwal yang aktif akan secara otomatis tampil pada portal siswa dan guru.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="flex items-center justify-end space-x-3 pt-5 border-t border-slate-100">
                <button type="button" onclick="closeModal('createModal')" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition">
                    Batal
                </button>
                <button type="submit" class="inline-flex items-center space-x-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Simpan Jadwal Baru</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Schedule Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 overflow-y-auto flex items-center justify-center p-4" onclick="closeModal('editModal')">
    <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full border border-slate-200 my-8 transform transition-all relative" onclick="event.stopPropagation()">
        <!-- Modal Header -->
        <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100 bg-slate-50/80 rounded-t-2xl">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Edit Jadwal Pelajaran</h3>
                    <p class="text-xs text-slate-500">Perbarui rincian sesi mata pelajaran, lokasi kelas, waktu, dan pengajar.</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('editModal')" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-600 transition-colors flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="editForm" method="POST" class="p-6 md:p-8 space-y-5">
            @csrf
            @method('PUT')

            <!-- Field Grid (2 Columns) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Nama Sesi / Jadwal (Full Width) -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Sesi / Jadwal <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="edit_name" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition outline-none">
                </div>

                <!-- Mata Pelajaran -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Mata Pelajaran <span class="text-rose-500">*</span></label>
                    @php
                        $sList = (isset($subjectsList) && count($subjectsList) > 0) ? $subjectsList : ($globalSubjects ?? []);
                    @endphp
                    <select name="subject" id="edit_subject" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition outline-none">
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach($sList as $sub)
                            @php $sName = is_object($sub) ? $sub->name : $sub; @endphp
                            <option value="{{ $sName }}">{{ $sName }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Guru Pengajar (Pilih Data Guru Search) -->
                <div>
                    <x-teacher-select-search 
                        :teachers="$teachers" 
                        name="teacher" 
                        id="edit_teacher"
                        label="Guru Pengajar" 
                        :required="true" 
                        placeholder="-- Cari & Pilih Guru Pengajar --" 
                    />
                </div>

                <!-- Hari -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Hari <span class="text-rose-500">*</span></label>
                    <select name="day" id="edit_day" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition outline-none">
                        @foreach($days as $dayCode => $dayName)
                            <option value="{{ $dayCode }}">{{ $dayName }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Kelas Target -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kelas Target <span class="text-rose-500">*</span></label>
                    <select name="class_id" id="edit_class_id" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition outline-none">
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Waktu Mulai -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Waktu Mulai <span class="text-rose-500">*</span></label>
                    <input type="time" name="start_time" id="edit_start_time" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition outline-none">
                </div>

                <!-- Waktu Selesai -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Waktu Selesai <span class="text-rose-500">*</span></label>
                    <input type="time" name="end_time" id="edit_end_time" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition outline-none">
                </div>

                <!-- Ruangan -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Ruangan <span class="text-rose-500">*</span></label>
                    <input type="text" name="room" id="edit_room" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 text-xs rounded-xl px-3.5 py-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition outline-none">
                </div>

                <!-- Status Aktif Checkbox Card -->
                <div class="md:col-span-2 bg-slate-50 rounded-xl p-3.5 border border-slate-200/80 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500 cursor-pointer">
                        <div>
                            <label for="edit_is_active" class="text-xs font-bold text-slate-800 cursor-pointer">Jadwal Aktif Berjalan</label>
                            <p class="text-[11px] text-slate-500">Jadwal yang aktif akan secara otomatis tampil pada portal siswa dan guru.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="flex items-center justify-end space-x-3 pt-5 border-t border-slate-100">
                <button type="button" onclick="closeModal('editModal')" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition">
                    Batal
                </button>
                <button type="submit" class="inline-flex items-center space-x-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Perbarui Jadwal</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
    document.body.style.overflow = '';
}

function formatTimeVal(timeStr) {
    if (!timeStr) return '';
    const match = String(timeStr).match(/(\d{2}:\d{2})/);
    return match ? match[1] : timeStr;
}

function openEditModal(schedule) {
    document.getElementById('editForm').action = '{{ url('admin/schedules') }}/' + schedule.id;
    document.getElementById('edit_name').value = schedule.name || '';
    document.getElementById('edit_subject').value = schedule.subject || '';
    document.getElementById('edit_day').value = schedule.day || '';
    document.getElementById('edit_class_id').value = schedule.class_id || '';
    document.getElementById('edit_start_time').value = formatTimeVal(schedule.start_time);
    document.getElementById('edit_end_time').value = formatTimeVal(schedule.end_time);
    document.getElementById('edit_room').value = schedule.room || '';
    
    // Update teacher select search component
    const teacherInput = document.getElementById('edit_teacher');
    if (teacherInput) {
        teacherInput.value = schedule.teacher || '';
        teacherInput.dispatchEvent(new Event('input', { bubbles: true }));
        teacherInput.dispatchEvent(new Event('change', { bubbles: true }));
    }

    document.getElementById('edit_is_active').checked = !!schedule.is_active;
    openModal('editModal');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal('createModal');
        closeModal('editModal');
    }
});
</script>
@endsection
