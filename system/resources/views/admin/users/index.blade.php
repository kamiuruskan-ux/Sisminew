@extends('layouts.admin')

@section('title', 'Manajemen User & Data Guru')
@section('page_title', 'Manajemen User & Data Guru')

@section('content')
<div class="space-y-6" x-data="{
    showDeleteModal: false,
    deleteTarget: null,
    deleteFormAction: '',
    toast: { show: false, message: '', type: 'success' },
    confirmDelete(encodedId, userName) {
        this.deleteTarget = { id: encodedId, name: userName };
        this.deleteFormAction = '{{ url('admin/users') }}/' + encodedId;
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

    @include('components.delete-modal', ['title' => 'Hapus Data User', 'message' => 'Apakah Anda yakin ingin menghapus user <strong x-text="deleteTarget?.name"></strong>? Tindakan ini tidak dapat dibatalkan.'])

    <!-- TailAdmin Top Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#1C2434] dark:text-white tracking-tight">Manajemen User & Data Guru</h1>
            <p class="text-xs text-[#64748B] dark:text-[#8A99AD] mt-1">Kelola data lengkap guru/pendidik (NIP, No WA, Bina Kelas) serta akun administrator & staf sistem</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.teacher-attendances.register-face-page') }}"
               class="inline-flex items-center space-x-2 px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-purple-600/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Registrasi Face ID Guru</span>
            </a>
            @permission('create-users')
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-[#3C50E0] hover:bg-[#3C50E0]/90 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-[#3C50E0]/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah User / Guru Baru</span>
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
    <div class="tailadmin-card p-4">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <input type="hidden" name="tab" value="{{ $activeTab }}">
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <!-- Role Filter -->
                <select name="role" @change="$el.closest('form').submit()" class="text-xs py-2.5 px-3.5 rounded-xl border-[#E2E8F0] dark:border-[#2E3A47] bg-white dark:bg-[#1A222C] text-[#1C2434] dark:text-[#DEE4EE] font-semibold">
                    <option value="">Semua Role / Jabatan</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->slug }}" {{ request('role') == $role->slug ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-center gap-2 w-full sm:w-96">
                <div class="relative w-full">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama, NIP, email, WA..." 
                           @input.debounce.400ms="$el.closest('form').submit()"
                           x-init="if ('{{ request('search') }}') { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length); }"
                           class="w-full text-xs py-2.5 pl-9 pr-8 rounded-xl border-[#E2E8F0] dark:border-[#2E3A47] bg-slate-50 dark:bg-[#1A222C] focus:bg-white text-[#1C2434] dark:text-[#DEE4EE]">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    @if(request('search'))
                        <a href="{{ route('admin.users.index', array_merge(request()->except('search'), ['tab' => $activeTab])) }}" class="absolute right-2.5 top-2 text-slate-400 hover:text-rose-500 font-bold text-sm" title="Hapus Pencarian">&times;</a>
                    @endif
                </div>
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

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F1F5F9] dark:bg-[#1A222C] text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider font-bold border-b border-[#E2E8F0] dark:border-[#2E3A47]">
                    <tr>
                        <th class="px-6 py-3.5">Pendidik / User</th>
                        <th class="px-6 py-3.5">No. WhatsApp / HP</th>
                        <th class="px-6 py-3.5">Role / Jabatan</th>
                        <th class="px-6 py-3.5">Bina Kelas (Wali)</th>
                        <th class="px-6 py-3.5">Status Akun</th>
                        <th class="px-6 py-3.5">Face ID</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#2E3A47] text-[#1C2434] dark:text-[#DEE4EE]">
                    @forelse($users as $user)
                        <tr class="hover:bg-[#F1F5F9]/60 dark:hover:bg-[#1A222C]/50 transition-colors">
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
                                <div class="flex flex-wrap gap-1">
                                    @foreach($user->roles as $role)
                                        <span class="px-2.5 py-0.5 text-[10px] font-extrabold rounded-full uppercase tracking-wider
                                            @if($role->slug == 'super-admin' || $role->slug == 'admin') bg-[#3C50E0] text-white
                                            @elseif($role->slug == 'guru' || $role->slug == 'teacher') bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800
                                            @else bg-slate-100 dark:bg-[#1A222C] text-[#1C2434] dark:text-white border border-[#E2E8F0] dark:border-[#2E3A47] @endif">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>

                            <!-- Bina Kelas -->
                            <td class="px-6 py-4">
                                @if($user->homeroomClasses->count() > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($user->homeroomClasses as $cls)
                                            <span class="px-2 py-0.5 bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-300 font-extrabold text-[9px] rounded-md border border-purple-200 dark:border-purple-800">
                                                Kelas {{ $cls->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
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
                            <td colspan="7" class="px-6 py-12 text-center text-[#64748B] dark:text-[#8A99AD] text-xs font-semibold">
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
    </div>
</div>
@endsection
