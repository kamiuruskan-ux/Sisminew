@extends('layouts.admin')

@section('title', 'Manajemen User & Data Guru')
@section('page_title', 'Manajemen User & Data Guru')

@section('content')
<div class="space-y-6" x-data="{
    selected: [],
    selectAll: false,
    showDeleteModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    isBulkDelete: false,
    showImportModal: false,
    toast: { show: false, message: '', type: 'success' },

    toggleSelectAll() {
        if (this.selectAll) {
            this.selected = [{{ implode(',', $users->pluck('id')->toArray()) }}];
        } else {
            this.selected = [];
        }
    },
    
    updateSelectAll() {
        const allIds = [{{ implode(',', $users->pluck('id')->toArray()) }}];
        this.selectAll = allIds.length > 0 && allIds.every(id => this.selected.includes(id));
    },

    confirmDelete(encodedId, userName) {
        this.isBulkDelete = false;
        this.deleteTarget = { id: encodedId, name: userName };
        this.deleteFormAction = '{{ url('admin/users') }}/' + encodedId;
        this.showDeleteModal = true;
    },

    confirmBulkDelete() {
        if (this.selected.length === 0) return;
        this.isBulkDelete = true;
        this.deleteTarget = { id: null, name: this.selected.length + ' data user terpilih' };
        this.deleteFormAction = '{{ route('admin.users.bulk-destroy') }}';
        this.showDeleteModal = true;
    }
}"
@notify-toast.window="
    toast.message = $event.detail.message;
    toast.type = $event.detail.type || 'success';
    toast.show = true;
    setTimeout(() => { toast.show = false; }, 3500);
">
    <!-- Floating Toast Notification -->
    <div x-show="toast.show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 -translate-y-4 scale-95"
         class="fixed top-5 right-5 z-[9999] max-w-md px-5 py-3.5 rounded-2xl shadow-2xl flex items-center justify-between gap-3 border"
         :class="toast.type === 'error' ? 'bg-rose-600 text-white border-rose-400' : 'bg-emerald-600 text-white border-emerald-400'"
         style="display: none;">
        <div class="flex items-center space-x-3">
            <div class="w-7 h-7 bg-white/20 rounded-xl flex items-center justify-center shrink-0">
                <template x-if="toast.type !== 'error'">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </template>
                <template x-if="toast.type === 'error'">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </template>
            </div>
            <p class="text-xs font-semibold text-white" x-text="toast.message"></p>
        </div>
        <button @click="toast.show = false" class="text-white/80 hover:text-white font-bold text-lg leading-none">&times;</button>
    </div>

    @component('components.delete-modal', ['title' => 'Hapus Data User', 'message' => 'Apakah Anda yakin ingin menghapus <strong x-text="deleteTarget?.name"></strong>? Tindakan ini tidak dapat dibatalkan.'])
        <template x-if="isBulkDelete">
            <template x-for="id in selected" :key="id">
                <input type="hidden" name="ids[]" :value="id">
            </template>
        </template>
    @endcomponent

    <!-- TailAdmin Top Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#1C2434] dark:text-white tracking-tight">Manajemen User & Data Guru</h1>
            <p class="text-xs text-[#64748B] dark:text-[#8A99AD] mt-1">Kelola data lengkap guru/pendidik (NIP, No WA, Bina Kelas) serta akun administrator & staf sistem</p>
        </div>
        <div class="flex items-center flex-wrap gap-2.5">
            <a href="{{ route('admin.users.export', request()->query()) }}"
               class="inline-flex items-center space-x-1.5 px-3.5 py-2.5 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-600 hover:text-white text-emerald-700 dark:text-emerald-300 text-xs font-bold rounded-xl transition-all border border-emerald-200 dark:border-emerald-800 shadow-sm" title="Export Data Guru & Pegawai ke Excel (.xlsx)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Export Excel</span>
            </a>
            <a href="{{ route('admin.users.template') }}"
               class="inline-flex items-center space-x-1.5 px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl transition-all border border-slate-200 dark:border-slate-700 shadow-sm">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Template Excel</span>
            </a>
            @permission('create-users')
            <button @click="showImportModal = true"
                    class="inline-flex items-center space-x-1.5 px-3.5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-emerald-600/20 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <span>Import Excel Guru</span>
            </button>
            @endpermission
            <a href="{{ route('admin.teacher-attendances.register-face-page') }}"
               class="inline-flex items-center space-x-2 px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-purple-600/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Face ID Guru</span>
            </a>
            @permission('create-users')
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-[#3C50E0] hover:bg-[#3C50E0]/90 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-[#3C50E0]/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah User / Guru</span>
            </a>
            @endpermission
        </div>
    </div>


    <!-- Navigation Tabs (Data Guru vs Semua Akun System) -->
    <div class="flex items-center space-x-2 border-b border-[#E2E8F0] dark:border-[#2E3A47]">
        <a href="{{ route('admin.users.index', ['tab' => 'guru']) }}"
           class="px-5 py-3 text-xs font-extrabold border-b-2 transition flex items-center space-x-2 {{ $activeTab === 'guru' ? 'border-[#3C50E0] text-[#3C50E0] dark:text-indigo-400' : 'border-transparent text-[#64748B] hover:text-[#1C2434] dark:text-[#8A99AD] dark:hover:text-white' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
            <span>DATA GURU & PENDIDIK</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-mono {{ $activeTab === 'guru' ? 'bg-[#3C50E0] text-white' : 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">{{ $teachersCount }}</span>
        </a>

        <a href="{{ route('admin.users.index', ['tab' => 'all']) }}"
           class="px-5 py-3 text-xs font-extrabold border-b-2 transition flex items-center space-x-2 {{ $activeTab === 'all' ? 'border-[#3C50E0] text-[#3C50E0] dark:text-indigo-400' : 'border-transparent text-[#64748B] hover:text-[#1C2434] dark:text-[#8A99AD] dark:hover:text-white' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <span>SEMUA AKUN SISTEM</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-mono {{ $activeTab === 'all' ? 'bg-[#3C50E0] text-white' : 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">{{ $allUsersCount }}</span>
        </a>
    </div>

    <!-- Filter & Search Bar (Real-Time Auto Submit) -->
    <div class="tailadmin-card p-5">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3.5">
            <input type="hidden" name="tab" value="{{ $activeTab }}">
            
            <!-- Search Keyword -->
            <div class="xl:col-span-2">
                <label class="block text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider mb-1.5">Cari User / Guru</label>
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama, NIP, email, WA..." 
                           @input.debounce.400ms="$el.closest('form').submit()"
                           x-init="if ('{{ request('search') }}') { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length); }"
                           class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-[#1C2434] dark:text-white text-xs rounded-xl pl-10 pr-8 py-2.5 focus:outline-none focus:border-[#3C50E0]">
                    <svg class="w-4 h-4 text-[#64748B] dark:text-[#8A99AD] absolute left-3.5 top-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    @if(request('search'))
                        <a href="{{ route('admin.users.index', array_merge(request()->except('search'), ['tab' => $activeTab])) }}" class="absolute right-3 top-2.5 text-[#64748B] hover:text-rose-500 transition font-bold text-sm" title="Hapus Pencarian">&times;</a>
                    @endif
                </div>
            </div>

            <!-- Role Filter -->
            <div>
                <label class="block text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider mb-1.5">Role Sistem</label>
                <select name="role" @change="$el.closest('form').submit()" class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-[#1C2434] dark:text-white text-xs rounded-xl p-2.5 focus:outline-none focus:border-[#3C50E0] font-semibold">
                    <option value="">Semua Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->slug }}" {{ request('role') == $role->slug ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Jabatan Filter -->
            <div>
                <label class="block text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider mb-1.5">Jabatan / Posisi</label>
                <select name="jabatan" @change="$el.closest('form').submit()" class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-[#1C2434] dark:text-white text-xs rounded-xl p-2.5 focus:outline-none focus:border-[#3C50E0] font-semibold">
                    <option value="">Semua Jabatan</option>
                    @foreach($jabatanList as $jbt)
                        <option value="{{ $jbt }}" {{ request('jabatan') == $jbt ? 'selected' : '' }}>{{ $jbt }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Sort Filter -->
            <div>
                <label class="block text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider mb-1.5">Urutan</label>
                <select name="sort" @change="$el.closest('form').submit()" class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-[#1C2434] dark:text-white text-xs rounded-xl p-2.5 focus:outline-none focus:border-[#3C50E0] font-semibold">
                    <option value="name_asc" {{ request('sort', $sort) === 'name_asc' ? 'selected' : '' }}>Nama (A - Z)</option>
                    <option value="name_desc" {{ request('sort', $sort) === 'name_desc' ? 'selected' : '' }}>Nama (Z - A)</option>
                    <option value="latest" {{ request('sort', $sort) === 'latest' ? 'selected' : '' }}>Terbaru</option>
                </select>
            </div>

            <!-- Per Page Filter -->
            <div>
                <label class="block text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider mb-1.5">Tampilkan</label>
                <select name="per_page" @change="$el.closest('form').submit()" class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-[#1C2434] dark:text-white text-xs rounded-xl p-2.5 focus:outline-none focus:border-[#3C50E0] font-semibold">
                    <option value="10" {{ request('per_page', $perPage) == '10' ? 'selected' : '' }}>10 Baris</option>
                    <option value="20" {{ request('per_page', $perPage) == '20' ? 'selected' : '' }}>20 Baris</option>
                    <option value="50" {{ request('per_page', $perPage) == '50' ? 'selected' : '' }}>50 Baris</option>
                    <option value="100" {{ request('per_page', $perPage) == '100' ? 'selected' : '' }}>100 Baris</option>
                </select>
            </div>
        </form>
    </div>

    <!-- TailAdmin Table Card -->
    <div class="tailadmin-card overflow-hidden">
        <div class="px-6 py-4 border-b border-[#E2E8F0] dark:border-[#2E3A47] flex items-center justify-between bg-slate-50/50 dark:bg-[#1A222C]/40">
            <h3 class="font-extrabold text-[#1C2434] dark:text-white text-sm uppercase tracking-wider">
                {{ $activeTab === 'guru' ? 'Daftar Guru & Staf Pendidik' : 'Daftar Pengguna Sistem' }}
            </h3>
            <span class="text-xs text-[#64748B] dark:text-[#8A99AD] font-mono">Total {{ $users->total() }} Data</span>
        </div>

        <!-- Bulk Action Banner Bar -->
        <div x-show="selected.length > 0" x-cloak x-transition class="flex items-center justify-between px-6 py-3 bg-indigo-50 dark:bg-indigo-950/40 border-b border-indigo-200 dark:border-indigo-800">
            <div class="flex items-center space-x-2 text-indigo-700 dark:text-indigo-300 font-bold text-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span x-text="selected.length + ' data user / guru dipilih'"></span>
            </div>
            <div class="flex items-center space-x-2">
                @permission('delete-users')
                <button type="button" @click="confirmBulkDelete()" class="px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition shadow-xs flex items-center space-x-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    <span>Hapus Terpilih</span>
                </button>
                @endpermission
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F1F5F9] dark:bg-[#1A222C] text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider font-bold border-b border-[#E2E8F0] dark:border-[#2E3A47]">
                    <tr>
                        <th class="px-4 py-3.5 w-10 text-center">
                            <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()" class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 text-[#3C50E0] focus:ring-[#3C50E0] cursor-pointer" title="Pilih Semua">
                        </th>
                        <th class="px-6 py-3.5">Pendidik / User</th>
                        <th class="px-6 py-3.5">No. WhatsApp / HP</th>
                        <th class="px-6 py-3.5">Role / Jabatan</th>
                        <th class="px-6 py-3.5">TMT &amp; Pendidikan</th>
                        <th class="px-6 py-3.5">Bina Kelas</th>
                        <th class="px-6 py-3.5">Status Akun</th>
                        <th class="px-6 py-3.5">Face ID</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#2E3A47] text-[#1C2434] dark:text-[#DEE4EE]">
                    @forelse($users as $user)
                        <tr class="hover:bg-[#F1F5F9]/60 dark:hover:bg-[#1A222C]/50 transition-colors" :class="{ 'bg-rose-50/30 dark:bg-rose-950/20': selected.includes({{ $user->id }}) }">
                            <td class="px-4 py-4 text-center">
                                <input type="checkbox" :value="{{ $user->id }}" x-model="selected" @change="updateSelectAll()" class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 text-[#3C50E0] focus:ring-[#3C50E0] cursor-pointer">
                            </td>
                            <!-- User Profile & NIP -->
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3.5">
                                    <div class="w-10 h-10 bg-[#3C50E0]/10 text-[#3C50E0] rounded-xl flex items-center justify-center font-extrabold border border-[#3C50E0]/20 shrink-0 overflow-hidden">
                                        @if($user->avatar)
                                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-xl object-cover">
                                        @else
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-bold text-[#1C2434] dark:text-white text-xs">{{ $user->name }}</p>
                                        <div class="flex items-center space-x-2 text-[10px] text-slate-400 mt-0.5">
                                            @if($user->nip)
                                                <span class="font-mono text-indigo-600 dark:text-indigo-400 font-bold">NIP: {{ $user->nip }}</span>
                                                <span>•</span>
                                            @endif
                                            <span>{{ $user->email }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- WhatsApp / HP -->
                            <td class="px-6 py-4">
                                @if($user->phone)
                                    @php
                                        $cleanPhone = preg_replace('/[^0-9]/', '', $user->phone);
                                        if (str_starts_with($cleanPhone, '0')) {
                                            $cleanPhone = '62' . substr($cleanPhone, 1);
                                        }
                                    @endphp
                                    <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="inline-flex items-center space-x-1.5 px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 font-mono font-bold rounded-lg hover:underline text-[11px]">
                                        <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-1.099 4.018 4.142-1.086z"/></svg>
                                        <span>{{ $user->phone }}</span>
                                    </a>
                                @else
                                    <span class="text-slate-400 font-mono text-xs">-</span>
                                @endif
                            </td>

                            <!-- Role / Jabatan -->
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($user->roles as $role)
                                            <span class="px-2.5 py-0.5 text-[10px] font-extrabold rounded-full uppercase tracking-wider
                                                @if($role->slug == 'super-admin' || $role->slug == 'admin') bg-[#3C50E0] text-white
                                                @elseif($role->slug == 'guru-quran') bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800
                                                @elseif($role->slug == 'guru' || $role->slug == 'teacher') bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800
                                                @else bg-slate-100 dark:bg-[#1A222C] text-[#1C2434] dark:text-white border border-[#E2E8F0] dark:border-[#2E3A47] @endif">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                    @if($user->jabatan)
                                        <div class="text-[11px] font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1 mt-0.5">
                                            <span class="text-[9px] text-slate-400 font-semibold uppercase">Jabatan:</span>
                                            <span>{{ $user->jabatan }}</span>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- TMT & Pendidikan Terakhir -->
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <div class="flex items-center space-x-1.5">
                                        <span class="text-[10px] text-slate-400 font-semibold">Pendidikan:</span>
                                        <span class="px-2 py-0.5 bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 font-bold text-[10px] rounded-md border border-blue-200 dark:border-blue-800">
                                            {{ $user->last_education ?: '-' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-1.5">
                                        <span class="text-[10px] text-slate-400 font-semibold">TMT:</span>
                                        <span class="font-mono text-[10px] text-slate-700 dark:text-slate-300 font-semibold">
                                            {{ $user->tmt ? \Carbon\Carbon::parse($user->tmt)->format('d/m/Y') : '-' }}
                                        </span>
                                    </div>
                                    @if($user->tmt)
                                        <div class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                            ⏱️ {{ $user->masa_kerja }}
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Bina Kelas (Wali & Qur'an) -->
                            <td class="px-6 py-4">
                                @if($user->homeroomClasses->count() > 0)
                                    <div class="mb-1">
                                        <span class="text-[9px] font-bold text-purple-700 uppercase">Wali:</span>
                                        <div class="flex flex-wrap gap-1 mt-0.5">
                                            @foreach($user->homeroomClasses as $cls)
                                                <span class="px-2 py-0.5 bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-300 font-extrabold text-[9px] rounded-md border border-purple-200 dark:border-purple-800">
                                                    Kelas {{ $cls->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                @if($user->quranClasses && $user->quranClasses->count() > 0)
                                    <div>
                                        <span class="text-[9px] font-bold text-emerald-700 uppercase">Qur'an:</span>
                                        <div class="flex flex-wrap gap-1 mt-0.5">
                                            @foreach($user->quranClasses as $cls)
                                                <span class="px-2 py-0.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 font-extrabold text-[9px] rounded-md border border-emerald-200 dark:border-emerald-800">
                                                    Kelas {{ $cls->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                @if($user->homeroomClasses->count() === 0 && (!$user->quranClasses || $user->quranClasses->count() === 0))
                                    <span class="text-slate-400 text-[11px]">-</span>
                                @endif
                            </td>

                            <!-- Status Akun & Toggle Unlock Bruteforce -->
                            <td class="px-6 py-4" x-data="{ 
                                isActive: {{ ($user->status !== 'inactive' && !$user->isLockedOut()) ? 'true' : 'false' }},
                                isLocked: {{ $user->isLockedOut() ? 'true' : 'false' }},
                                loading: false,
                                async toggleStatus() {
                                    this.loading = true;
                                    try {
                                        const res = await fetch('{{ route('admin.users.toggle-status', $user->id) }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Accept': 'application/json'
                                            }
                                        });
                                        const data = await res.json();
                                        if (data.success) {
                                            this.isActive = (data.status === 'active' && !data.is_locked);
                                            this.isLocked = data.is_locked;
                                            window.dispatchEvent(new CustomEvent('notify-toast', { detail: { message: data.message, type: 'success' } }));
                                        } else {
                                            window.dispatchEvent(new CustomEvent('notify-toast', { detail: { message: data.message || 'Gagal mengubah status akun.', type: 'error' } }));
                                        }
                                    } catch(e) {
                                        window.dispatchEvent(new CustomEvent('notify-toast', { detail: { message: 'Kesalahan jaringan: ' + e.message, type: 'error' } }));
                                    } finally {
                                        this.loading = false;
                                    }
                                }
                            }">
                                <div class="flex items-center space-x-2">
                                    <button type="button" @click="toggleStatus()" :disabled="loading"
                                            :class="isActive ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-700'"
                                            class="relative inline-flex h-5 w-10 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none" title="Klik toggle untuk mengaktifkan / membuka kuncian bruteforce akun">
                                        <span :class="isActive ? 'translate-x-5' : 'translate-x-0'"
                                              class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out"></span>
                                    </button>
                                    <template x-if="isLocked">
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-300 animate-pulse flex items-center gap-1" title="Terkunci otomatis karena salah password berulang (bruteforce). Klik toggle untuk membuka kuncian.">
                                            🔒 Bruteforce
                                        </span>
                                    </template>
                                    <template x-if="!isLocked">
                                        <span :class="isActive ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400'"
                                              class="text-[10px] font-extrabold"
                                              x-text="isActive ? 'Aktif' : 'Nonaktif'"></span>
                                    </template>
                                </div>
                            </td>

                            <!-- Status Face ID -->
                            <td class="px-6 py-4">
                                @if($user->face_photo)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200">
                                        Terdaftar
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                        Belum
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-1.5">
                                    <a href="{{ route('admin.teacher-attendances.register-face-page', ['user_id' => $user->id]) }}"
                                       class="p-1.5 text-purple-600 hover:bg-purple-50 rounded-xl transition" title="Registrasi Face ID">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </a>
                                    @permission('edit-users')
                                    <a href="{{ route('admin.users.edit', encode_id($user->id)) }}" 
                                       class="p-1.5 text-[#64748B] hover:text-[#3C50E0] hover:bg-slate-100 dark:hover:bg-[#1A222C] rounded-xl transition-colors" title="Edit User & NIP">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    @endpermission
                                    @permission('delete-users')
                                    <button @click="confirmDelete('{{ encode_id($user->id) }}', '{{ addslashes($user->name) }}')" 
                                            class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-slate-100 dark:hover:bg-[#1A222C] rounded-xl transition-colors" title="Hapus User">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                    @endpermission
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-[#64748B] dark:text-[#8A99AD] text-xs font-semibold">
                                Belum ada data user terdaftar pada tab ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-[#E2E8F0] dark:border-[#2E3A47] bg-slate-50/50 dark:bg-[#1A222C]/40">
                {{ $users->links() }}
            </div>
        @endif
    <!-- Modal Import Data Guru & Pegawai dari Excel -->
    <div x-show="showImportModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[999] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
         style="display: none;">
        
        <div @click.away="showImportModal = false"
             class="bg-white dark:bg-[#1E293B] rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-700 space-y-4">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-700">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 flex items-center justify-center font-black">
                        📊
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Import Massal Guru & Pegawai</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Unggah berkas Excel (.xlsx, .xls) lengkap kredensial login</p>
                    </div>
                </div>
                <button @click="showImportModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-2xl font-bold leading-none">&times;</button>
            </div>

            <!-- Petunjuk Format Kolom -->
            <div class="p-3.5 rounded-2xl bg-blue-50/80 dark:bg-blue-950/40 border border-blue-200/60 dark:border-blue-800/40 text-xs text-blue-900 dark:text-blue-200 space-y-1.5">
                <div class="flex items-center justify-between font-bold">
                    <span class="flex items-center gap-1.5">💡 Format Kolom Excel:</span>
                    <a href="{{ route('admin.users.template') }}" class="text-blue-700 dark:text-blue-300 underline text-[11px] font-extrabold hover:text-blue-800">
                        Unduh Template (.xlsx) ↗
                    </a>
                </div>
                <p class="text-[11px] text-blue-800/90 dark:text-blue-300 leading-relaxed">
                    Kolom yang didukung: <strong>Nama Lengkap*</strong>, <strong>NIP / NRH</strong>, <strong>Email (Username Login)*</strong>, <strong>Password*</strong>, <strong>No. WA</strong>, <strong>Peran (guru/staff)</strong>, dan <strong>Wali Kelas</strong>.
                </p>
                <p class="text-[10px] text-blue-700/80 dark:text-blue-400">
                    *Jika password dikosongkan, sistem otomatis memberikan kata sandi bawaan <code>guru123</code>.
                </p>
            </div>

            <!-- Form Upload -->
            <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Pilih Berkas Excel / CSV</label>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-300 dark:border-slate-600 rounded-2xl p-2 cursor-pointer">
                </div>

                <div class="p-3 bg-amber-50 dark:bg-amber-950/40 rounded-2xl border border-amber-200 dark:border-amber-800/60">
                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input type="checkbox" name="merge_only" value="1" class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-slate-700 mt-0.5">
                        <div class="text-[11px]">
                            <span class="font-bold text-amber-900 dark:text-amber-200">Mode Merge / Pembaruan Saja</span>
                            <p class="text-amber-700 dark:text-amber-300 text-[10px] mt-0.5">Hanya perbarui data guru/pegawai yang sudah terdaftar di sistem (berdasarkan NIP / Email / Nama) tanpa menambahkan user baru.</p>
                        </div>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="showImportModal = false"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold shadow-md shadow-emerald-600/30 transition flex items-center gap-2">
                        <span>Mulai Proses Import</span>
                        <span>🚀</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
