@extends('layouts.admin')

@section('title', 'Kelola Akun Kantin')

@section('content')
<div x-data="{ 
    createModalOpen: false, 
    editModalOpen: false, 
    deleteModalOpen: false,
    createPassword: '',
    createPasswordConfirm: '',
    showCreatePassword: false,
    showCreatePasswordConfirm: false,
    editPassword: '',
    editPasswordConfirm: '',
    showEditPassword: false,
    showEditPasswordConfirm: false,
    editData: { id: '', name: '', email: '', phone: '', avatar: '' },
    deleteData: { id: '', name: '' },
    openCreateModal() {
        this.createPassword = '';
        this.createPasswordConfirm = '';
        this.showCreatePassword = false;
        this.showCreatePasswordConfirm = false;
        this.createModalOpen = true;
    },
    openEditModal(user) {
        this.editData = { ...user };
        this.editPassword = '';
        this.editPasswordConfirm = '';
        this.showEditPassword = false;
        this.showEditPasswordConfirm = false;
        this.editModalOpen = true;
    },
    openDeleteModal(id, name) {
        this.deleteData = { id, name };
        this.deleteModalOpen = true;
    },
    get isCreatePasswordMatching() {
        return this.createPassword.length >= 8 && this.createPasswordConfirm.length > 0 && this.createPassword === this.createPasswordConfirm;
    },
    get isCreatePasswordMismatch() {
        return this.createPasswordConfirm.length > 0 && this.createPassword !== this.createPasswordConfirm;
    },
    get isEditPasswordMatching() {
        return this.editPassword.length >= 8 && this.editPasswordConfirm.length > 0 && this.editPassword === this.editPasswordConfirm;
    },
    get isEditPasswordMismatch() {
        return this.editPasswordConfirm.length > 0 && this.editPassword !== this.editPasswordConfirm;
    }
}" class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Pengelolaan Akun Vendor Kantin</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Manajemen akun login dan otorisasi pengelola kantin sekolah secara khusus.</p>
        </div>
        <div>
            <button type="button" @click="openCreateModal()" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-2xl transition-all shadow-xs hover:shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                <span>Tambah Akun Kantin</span>
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="p-4 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs flex items-center space-x-4">
            <div class="w-11 h-11 rounded-2xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div>
                <span class="text-xs text-slate-500 dark:text-slate-400 font-medium block">Total Akun Kantin</span>
                <span class="text-lg font-black text-slate-900 dark:text-white">{{ $stats['total'] }} Akun</span>
            </div>
        </div>

        <div class="p-4 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xs flex items-center space-x-4">
            <div class="w-11 h-11 rounded-2xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <span class="text-xs text-slate-500 dark:text-slate-400 font-medium block">Akun Aktif</span>
                <span class="text-lg font-black text-slate-900 dark:text-white">{{ $stats['active'] }} Pengguna</span>
            </div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-4 shadow-xs">
        <form method="GET" action="{{ route('admin.canteen.users') }}" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pengelola, email, atau nomor telepon..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs focus:ring-2 focus:ring-indigo-500 dark:text-white">
            </div>
            <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white text-xs font-bold rounded-2xl transition-all">
                Cari Akun
            </button>
            @if(request()->filled('search'))
            <a href="{{ route('admin.canteen.users') }}" class="w-full sm:w-auto px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-bold rounded-2xl transition-all text-center">
                Reset
            </a>
            @endif
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-850 border-b border-slate-200 dark:border-slate-800 text-slate-500 uppercase font-extrabold text-[10px]">
                        <th class="py-3.5 px-4">Pengelola Kantin</th>
                        <th class="py-3.5 px-4">No. Telepon / WA</th>
                        <th class="py-3.5 px-4">Status Akun</th>
                        <th class="py-3.5 px-4">Terdaftar</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($canteenUsers as $u)
                        @php
                            $userAvatarUrl = $u->avatar ? ( \Illuminate\Support\Str::startsWith($u->avatar, ['http://', 'https://']) ? $u->avatar : asset('img/' . ltrim($u->avatar, '/')) ) : 'https://ui-avatars.com/api/?name=' . urlencode($u->name) . '&background=6366f1&color=ffffff';
                        @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/50 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ $userAvatarUrl }}" alt="{{ $u->name }}" class="w-9 h-9 rounded-2xl object-cover border border-slate-200 dark:border-slate-700 shrink-0">
                                    <div>
                                        <span class="font-bold text-slate-900 dark:text-white block">{{ $u->name }}</span>
                                        <span class="text-[10px] text-slate-400 block">{{ $u->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 dark:text-slate-300 font-medium">
                                {{ $u->phone ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4">
                                <form method="POST" action="{{ route('admin.canteen.users.toggle', encode_id($u->id)) }}" class="inline-block">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold transition-all {{ $u->status === 'active' ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $u->status === 'active' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                        <span>{{ $u->status === 'active' ? 'Aktif' : 'Nonaktif' }}</span>
                                    </button>
                                </form>
                            </td>
                            <td class="py-3.5 px-4 text-slate-500 dark:text-slate-400 text-[11px]">
                                {{ $u->created_at ? $u->created_at->format('d/m/Y') : '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <button type="button" @click="openEditModal({ id: '{{ encode_id($u->id) }}', name: '{{ addslashes($u->name) }}', email: '{{ addslashes($u->email) }}', phone: '{{ addslashes($u->phone ?? '') }}' })" class="p-2 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 rounded-xl transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button type="button" @click="openDeleteModal('{{ encode_id($u->id) }}', '{{ addslashes($u->name) }}')" class="p-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/50 rounded-xl transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400 dark:text-slate-500 text-xs">
                                Belum ada akun pengelola kantin terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($canteenUsers->hasPages())
        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $canteenUsers->links() }}
        </div>
        @endif
    </div>

    <!-- Modal Tambah Akun Kantin -->
    <div x-show="createModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="createModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="createModalOpen = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="createModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200 dark:border-slate-800 p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Tambah Akun Pengelola Kantin</h3>
                    <button type="button" @click="createModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.canteen.users.store') }}" enctype="multipart/form-data" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Pengelola / Kantin <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" required placeholder="Contoh: Ibu Ani / Kantin Utama" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs dark:text-white">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                            <input type="email" name="email" required placeholder="kantin@sekolah.id" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs dark:text-white">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">No. Telepon / WhatsApp</label>
                            <input type="text" name="phone" placeholder="081234567890" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs dark:text-white">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Password <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <input :type="showCreatePassword ? 'text' : 'password'" 
                                       name="password" 
                                       x-model="createPassword" 
                                       required minlength="8" 
                                       placeholder="Minimal 8 karakter" 
                                       :class="{
                                           'border-slate-200 dark:border-slate-700': createPassword.length === 0,
                                           'border-amber-500 focus:ring-amber-500': createPassword.length > 0 && createPassword.length < 8,
                                           'border-emerald-500 focus:ring-emerald-500': createPassword.length >= 8
                                       }"
                                       class="w-full pl-3.5 pr-10 py-2.5 bg-slate-50 dark:bg-slate-800 border rounded-xl text-xs dark:text-white transition-colors">
                                <button type="button" @click="showCreatePassword = !showCreatePassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none">
                                    <svg x-show="!showCreatePassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="showCreatePassword" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a8.959 8.959 0 014.122-.977c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-4.692-4.692a3 3 0 00-4.243-4.243"/></svg>
                                </button>
                            </div>
                            <template x-if="createPassword.length > 0 && createPassword.length < 8">
                                <p class="text-[11px] font-semibold text-amber-600 dark:text-amber-400 mt-1 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    <span>Minimal 8 karakter</span>
                                </p>
                            </template>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Konfirmasi Password <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <input :type="showCreatePasswordConfirm ? 'text' : 'password'" 
                                       name="password_confirmation" 
                                       x-model="createPasswordConfirm" 
                                       required minlength="8" 
                                       placeholder="Ulangi password" 
                                       :class="{
                                           'border-slate-200 dark:border-slate-700': createPasswordConfirm.length === 0,
                                           'border-rose-500 focus:ring-rose-500': isCreatePasswordMismatch,
                                           'border-emerald-500 focus:ring-emerald-500': isCreatePasswordMatching
                                       }"
                                       class="w-full pl-3.5 pr-10 py-2.5 bg-slate-50 dark:bg-slate-800 border rounded-xl text-xs dark:text-white transition-colors">
                                <button type="button" @click="showCreatePasswordConfirm = !showCreatePasswordConfirm" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none">
                                    <svg x-show="!showCreatePasswordConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="showCreatePasswordConfirm" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a8.959 8.959 0 014.122-.977c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-4.692-4.692a3 3 0 00-4.243-4.243"/></svg>
                                </button>
                            </div>
                            <template x-if="isCreatePasswordMismatch">
                                <p class="text-[11px] font-semibold text-rose-600 dark:text-rose-400 mt-1 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 shrink-0 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Konfirmasi password tidak cocok!</span>
                                </p>
                            </template>
                            <template x-if="isCreatePasswordMatching">
                                <p class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 mt-1 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Password cocok</span>
                                </p>
                            </template>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Foto Profil (Opsional)</label>
                        <input type="file" name="avatar" accept="image/jpeg,image/png,image/jpg,image/webp" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-950 dark:file:text-indigo-300 hover:file:bg-indigo-100">
                    </div>

                    <div class="pt-3 flex items-center justify-end space-x-2">
                        <button type="button" @click="createModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-xl transition-all">Batal</button>
                        <button type="submit" :disabled="createPassword.length < 8 || createPassword !== createPasswordConfirm" :class="{ 'opacity-50 cursor-not-allowed': createPassword.length < 8 || createPassword !== createPasswordConfirm }" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition-all">Simpan Akun</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Akun Kantin -->
    <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="editModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="editModalOpen = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="editModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200 dark:border-slate-800 p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Edit Akun Kantin</h3>
                    <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" :action="'{{ url('admin/canteen/users') }}/' + editData.id" enctype="multipart/form-data" class="space-y-4 text-xs">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Pengelola / Kantin <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" x-model="editData.name" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs dark:text-white">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                            <input type="email" name="email" x-model="editData.email" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs dark:text-white">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">No. Telepon / WhatsApp</label>
                            <input type="text" name="phone" x-model="editData.phone" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs dark:text-white">
                        </div>
                    </div>

                    <div class="p-3.5 bg-amber-50/50 dark:bg-amber-950/20 border border-amber-200/60 dark:border-amber-900/40 rounded-2xl space-y-2">
                        <span class="font-bold text-amber-800 dark:text-amber-300 block">Ubah Password Akun (Opsional):</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <div class="relative">
                                    <input :type="showEditPassword ? 'text' : 'password'" 
                                           name="password" 
                                           x-model="editPassword" 
                                           minlength="8" 
                                           placeholder="Password Baru (Opsional)" 
                                           :class="{
                                               'border-slate-200 dark:border-slate-700': editPassword.length === 0,
                                               'border-amber-500 focus:ring-amber-500': editPassword.length > 0 && editPassword.length < 8,
                                               'border-emerald-500 focus:ring-emerald-500': editPassword.length >= 8
                                           }"
                                           class="w-full pl-3.5 pr-9 py-2 bg-white dark:bg-slate-800 border rounded-xl text-xs dark:text-white transition-colors">
                                    <button type="button" @click="showEditPassword = !showEditPassword" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none">
                                        <svg x-show="!showEditPassword" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg x-show="showEditPassword" x-cloak class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a8.959 8.959 0 014.122-.977c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-4.692-4.692a3 3 0 00-4.243-4.243"/></svg>
                                    </button>
                                </div>
                                <template x-if="editPassword.length > 0 && editPassword.length < 8">
                                    <p class="text-[10px] font-semibold text-amber-600 dark:text-amber-400 mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Minimal 8 karakter</span>
                                    </p>
                                </template>
                            </div>
                            <div>
                                <div class="relative">
                                    <input :type="showEditPasswordConfirm ? 'text' : 'password'" 
                                           name="password_confirmation" 
                                           x-model="editPasswordConfirm" 
                                           minlength="8" 
                                           placeholder="Konfirmasi Password Baru" 
                                           :class="{
                                               'border-slate-200 dark:border-slate-700': editPasswordConfirm.length === 0,
                                               'border-rose-500 focus:ring-rose-500': isEditPasswordMismatch,
                                               'border-emerald-500 focus:ring-emerald-500': isEditPasswordMatching
                                           }"
                                           class="w-full pl-3.5 pr-9 py-2 bg-white dark:bg-slate-800 border rounded-xl text-xs dark:text-white transition-colors">
                                    <button type="button" @click="showEditPasswordConfirm = !showEditPasswordConfirm" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none">
                                        <svg x-show="!showEditPasswordConfirm" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg x-show="showEditPasswordConfirm" x-cloak class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a8.959 8.959 0 014.122-.977c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-4.692-4.692a3 3 0 00-4.243-4.243"/></svg>
                                    </button>
                                </div>
                                <template x-if="isEditPasswordMismatch">
                                    <p class="text-[10px] font-semibold text-rose-600 dark:text-rose-400 mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Konfirmasi tidak cocok!</span>
                                    </p>
                                </template>
                                <template x-if="isEditPasswordMatching">
                                    <p class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 mt-1 flex items-center gap-1">
                                        <svg class="w-3 h-3 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        <span>Password cocok</span>
                                    </p>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Ganti Foto Profil (Opsional)</label>
                        <input type="file" name="avatar" accept="image/jpeg,image/png,image/jpg,image/webp" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-950 dark:file:text-indigo-300 hover:file:bg-indigo-100">
                    </div>

                    <div class="pt-3 flex items-center justify-end space-x-2">
                        <button type="button" @click="editModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-xl transition-all">Batal</button>
                        <button type="submit" :disabled="editPassword.length > 0 && (editPassword.length < 8 || editPassword !== editPasswordConfirm)" :class="{ 'opacity-50 cursor-not-allowed': editPassword.length > 0 && (editPassword.length < 8 || editPassword !== editPasswordConfirm) }" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition-all">Perbarui Akun</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Hapus Akun -->
    <div x-show="deleteModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="deleteModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="deleteModalOpen = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="deleteModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-200 dark:border-slate-800 p-6 space-y-4">
                <div class="flex items-center space-x-3 text-rose-600">
                    <div class="w-10 h-10 rounded-2xl bg-rose-50 dark:bg-rose-950 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Konfirmasi Hapus Akun</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                </div>

                <p class="text-xs text-slate-600 dark:text-slate-300">
                    Apakah Anda yakin ingin menghapus akun kantin <b class="text-slate-900 dark:text-white" x-text="deleteData.name"></b>? Akses login pengelola ke portal vendor akan dicabut.
                </p>

                <form method="POST" :action="'{{ url('admin/canteen/users') }}/' + deleteData.id" class="pt-2 flex items-center justify-end space-x-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="deleteModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-xl transition-all text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl transition-all text-xs">Ya, Hapus Akun</button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
