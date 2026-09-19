@extends('layouts.admin')

@section('title', 'Profil & Biodata Pengguna')
@section('page_title', 'Profil Saya')

@section('content')
<div class="space-y-6 pb-20" x-data="{
    avatarPreview: '{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : (auth()->user()->avatar ? asset('img/avatars/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=3C50E0&color=fff&bold=true') }}',
    previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            this.avatarPreview = URL.createObjectURL(file);
        }
    }
}">

    <!-- Page Header Banner -->
    <div class="tailadmin-card p-6 border-l-4 border-primary bg-gradient-to-r from-primary/10 via-primary/5 to-transparent">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center space-x-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-primary text-white">AKUN SAYA</span>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-[#1C2434] dark:text-white tracking-tight">Pengaturan Profil &amp; Biodata</h1>
                </div>
                <p class="text-xs text-[#64748B] dark:text-[#8A99AD]">
                    Perbarui informasi biodata pribadi, foto profil, dan kata sandi akun Anda secara mandiri.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.teacher-attendances.mobile') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition flex items-center gap-1.5 border border-slate-200 dark:border-slate-700">
                    <span>📱</span>
                    <span>Portal Mobile</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 flex items-center gap-3 text-emerald-800 dark:text-emerald-300 text-xs font-bold">
            <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs space-y-1">
            <p class="font-extrabold">Terjadi kesalahan saat menyimpan perubahan:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- LEFT COLUMN: Avatar & Summary Card -->
            <div class="lg:col-span-4 space-y-6">
                <div class="tailadmin-card p-6 text-center space-y-4">
                    <div class="relative inline-block mx-auto">
                        <img :src="avatarPreview" alt="Foto Profil" class="w-28 h-28 sm:w-32 sm:h-32 rounded-3xl object-cover border-4 border-white dark:border-boxdark shadow-lg shadow-slate-200 dark:shadow-none mx-auto bg-slate-100">
                        <label for="avatarInput" class="absolute bottom-1 right-1 w-9 h-9 rounded-2xl bg-primary hover:bg-secondary text-white flex items-center justify-center shadow-md cursor-pointer transition transform hover:scale-105" title="Ubah Foto Profil">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </label>
                        <input type="file" id="avatarInput" name="avatar" accept="image/*" class="hidden" @change="previewImage($event)">
                    </div>

                    <div>
                        <h2 class="text-base font-extrabold text-[#1C2434] dark:text-white">{{ auth()->user()->name }}</h2>
                        <p class="text-xs text-[#64748B] dark:text-[#8A99AD] font-mono mt-0.5">{{ auth()->user()->email }}</p>
                    </div>

                    <div class="flex flex-wrap items-center justify-center gap-1.5 pt-1">
                        @foreach(auth()->user()->roles as $r)
                            <span class="px-3 py-1 rounded-full bg-primary/10 text-primary border border-primary/20 text-[10px] font-black uppercase tracking-wider">
                                {{ $r->name }}
                            </span>
                        @endforeach
                        <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 text-[10px] font-black uppercase tracking-wider">
                            AKTIF
                        </span>
                    </div>

                    <div class="border-t border-slate-100 dark:border-strokedark pt-4 text-left space-y-2.5 text-xs">
                        <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                            <span>NIP / NRH</span>
                            <span class="font-extrabold text-slate-800 dark:text-white font-mono">{{ auth()->user()->nip ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                            <span>No. WhatsApp</span>
                            <span class="font-bold text-slate-800 dark:text-white">{{ auth()->user()->phone ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                            <span>Terdaftar Sejak</span>
                            <span class="font-medium text-slate-700 dark:text-slate-300">{{ auth()->user()->created_at ? auth()->user()->created_at->format('d M Y') : '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Biodata & Password Fields -->
            <div class="lg:col-span-8 space-y-6">
                
                <!-- 1. BIODATA CARD -->
                <div class="tailadmin-card p-6 space-y-4">
                    <div class="border-b border-slate-100 dark:border-strokedark pb-3 flex items-center justify-between">
                        <h3 class="font-extrabold text-sm text-[#1C2434] dark:text-white flex items-center gap-2">
                            <span class="text-primary text-base">👤</span>
                            <span>Informasi Biodata Pribadi</span>
                        </h3>
                        <span class="text-[11px] text-slate-400">Wajib Diisi Lengkap</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <!-- Nama Lengkap -->
                        <div class="space-y-1.5 sm:col-span-2">
                            <label for="name" class="block font-bold text-slate-700 dark:text-slate-300">
                                Nama Lengkap &amp; Gelar <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" required value="{{ old('name', auth()->user()->name) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-strokedark bg-white dark:bg-boxdark text-slate-800 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition font-semibold">
                        </div>

                        <!-- NIP / NRH -->
                        <div class="space-y-1.5">
                            <label for="nip" class="block font-bold text-slate-700 dark:text-slate-300">
                                NIP / NRH Pegawai
                            </label>
                            <input type="text" name="nip" id="nip" value="{{ old('nip', auth()->user()->nip) }}" placeholder="Contoh: 198501012010011001" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-strokedark bg-white dark:bg-boxdark text-slate-800 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition font-mono">
                        </div>

                        <!-- Nomor WhatsApp / HP -->
                        <div class="space-y-1.5">
                            <label for="phone" class="block font-bold text-slate-700 dark:text-slate-300">
                                No. Handphone / WhatsApp
                            </label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', auth()->user()->phone) }}" placeholder="Contoh: 08123456789" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-strokedark bg-white dark:bg-boxdark text-slate-800 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition">
                        </div>

                        <!-- Email Akun -->
                        <div class="space-y-1.5 sm:col-span-2">
                            <label for="email" class="block font-bold text-slate-700 dark:text-slate-300">
                                Alamat Email Login <span class="text-rose-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" required value="{{ old('email', auth()->user()->email) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-strokedark bg-white dark:bg-boxdark text-slate-800 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition font-mono">
                        </div>
                    </div>
                </div>

                <!-- 2. KEAMANAN & PASSWORD CARD -->
                <div class="tailadmin-card p-6 space-y-4">
                    <div class="border-b border-slate-100 dark:border-strokedark pb-3 flex items-center justify-between">
                        <h3 class="font-extrabold text-sm text-[#1C2434] dark:text-white flex items-center gap-2">
                            <span class="text-amber-500 text-base">🔒</span>
                            <span>Ganti Kata Sandi (Opsional)</span>
                        </h3>
                        <span class="text-[11px] text-slate-400">Kosongkan jika tidak ingin mengubah</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div class="space-y-1.5 sm:col-span-2">
                            <label for="current_password" class="block font-bold text-slate-700 dark:text-slate-300">
                                Kata Sandi Saat Ini
                            </label>
                            <input type="password" name="current_password" id="current_password" placeholder="Masukkan password lama untuk konfirmasi" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-strokedark bg-white dark:bg-boxdark text-slate-800 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition">
                        </div>

                        <div class="space-y-1.5">
                            <label for="new_password" class="block font-bold text-slate-700 dark:text-slate-300">
                                Kata Sandi Baru
                            </label>
                            <input type="password" name="new_password" id="new_password" placeholder="Minimal 8 karakter" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-strokedark bg-white dark:bg-boxdark text-slate-800 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition">
                        </div>

                        <div class="space-y-1.5">
                            <label for="new_password_confirmation" class="block font-bold text-slate-700 dark:text-slate-300">
                                Konfirmasi Kata Sandi Baru
                            </label>
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation" placeholder="Ulangi password baru" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-strokedark bg-white dark:bg-boxdark text-slate-800 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition">
                        </div>
                    </div>
                </div>

                <!-- SAVE BUTTON -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('admin.dashboard') }}" class="px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-3 rounded-xl bg-primary hover:bg-secondary text-white font-extrabold text-xs shadow-md shadow-primary/30 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Perubahan Profil</span>
                    </button>
                </div>

            </div>

        </div>
    </form>

</div>
@endsection
