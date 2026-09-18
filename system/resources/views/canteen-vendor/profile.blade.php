@extends('layouts.canteen-vendor')

@section('title', 'Profil Stand Kantin')
@section('header_title', 'Profil Toko Stand')

@section('content')
<div x-data="{
        logoPreview: {{ json_encode($stall->logo_url) }},
        bannerPreview: {{ json_encode($stall->banner_url) }},
        
        handleImageChange(event, type) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    if (type === 'logo') this.logoPreview = e.target.result;
                    if (type === 'banner') this.bannerPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    }" 
    class="space-y-6 pb-24 w-full">

    <!-- Error Validation Alert -->
    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-500/10 dark:bg-rose-950/60 border border-rose-500/30 text-rose-800 dark:text-rose-200 text-xs space-y-1 shadow-xs">
            <div class="font-bold flex items-center space-x-2">
                <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Gagal memperbarui profil toko:</span>
            </div>
            <ul class="list-disc list-inside pl-5 space-y-0.5 font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Responsive 2-Column Grid on Desktop PC -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left Column: Store Summary Overview Card (lg:col-span-4) -->
        <div class="lg:col-span-4 space-y-5">
            <div class="relative overflow-hidden rounded-2xl text-white pt-4 px-4 pb-5 border border-indigo-500/20 shadow-lg space-y-4 m-1" style="background: linear-gradient(135deg, {{ Setting::get('primary_color', '#6366f1') }} 0%, {{ Setting::get('secondary_color', '#4f46e5') }} 100%);">
                <!-- Background Banner Image -->
                <div class="absolute inset-0 bg-cover bg-center opacity-30 transition-all duration-300 pointer-events-none" :style="'background-image: url(\'' + bannerPreview + '\')'"></div>
                <!-- Soft Blending Overlay -->
                <div class="absolute inset-0 bg-black/15 pointer-events-none"></div>

                <div class="relative z-10 flex items-center space-x-3.5 text-left ml-2">
                    <!-- Stall Logo -->
                    <img :src="logoPreview" class="w-16 h-16 rounded-2xl object-cover border-2 border-white/30 shadow-md bg-white shrink-0">
                    
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center space-x-1.5 mb-1.5 flex-wrap gap-y-1">
                            <span class="px-2 py-0.5 text-[8px] font-black rounded-md shrink-0 leading-none {{ $stall->is_open_now ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white' }}">
                                {{ $stall->is_open_now ? 'BUKA' : 'TUTUP' }}
                            </span>
                            <h2 class="text-sm sm:text-base font-extrabold truncate text-white leading-none">{{ $stall->name }}</h2>
                        </div>
                        <p class="text-[10px] text-white/90 leading-normal truncate">Pemilik: <span class="font-bold text-white">{{ $stall->owner_name ?? '-' }}</span></p>
                        <p class="text-[10px] text-white/90 leading-normal truncate">No. HP: <span class="font-bold text-white">{{ $stall->phone ?? '-' }}</span></p>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="relative z-10 grid grid-cols-3 gap-1.5 pt-3 border-t border-white/15 text-center">
                    <div class="py-2 px-0.5 bg-white/10 backdrop-blur-md rounded-xl border border-white/10">
                        <span class="text-[9px] text-white/80 font-bold uppercase block tracking-wider leading-none">Total Menu</span>
                        <span class="text-xs font-black text-white mt-1 block leading-none">{{ $totalItems ?? 0 }}</span>
                    </div>
                    <div class="py-2 px-0.5 bg-white/10 backdrop-blur-md rounded-xl border border-white/10">
                        <span class="text-[9px] text-white/80 font-bold uppercase block tracking-wider leading-none">Trx Selesai</span>
                        <span class="text-xs font-black text-white mt-1 block leading-none">{{ $totalCompletedOrders ?? 0 }}</span>
                    </div>
                    <div class="py-2 px-0.5 bg-white/10 backdrop-blur-md rounded-xl border border-white/10">
                        <span class="text-[9px] text-white/80 font-bold uppercase block tracking-wider leading-none">Saldo</span>
                        <span class="text-[10px] font-black text-white mt-1 block leading-none truncate" title="Rp {{ number_format($stall->balance, 0, ',', '.') }}">Rp{{ number_format($stall->balance, 0, '', '') }}</span>
                    </div>
                </div>
            </div>

            <!-- Operating Hours Info Badge -->
            <div class="bg-primary/5 dark:bg-primary/10 rounded-3xl p-5 border border-primary/20 dark:border-primary/20 shadow-xs space-y-1">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-2xl bg-sky-50 dark:bg-sky-950 text-sky-600 dark:text-sky-400 flex items-center justify-center font-bold shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-xs font-extrabold text-slate-900 dark:text-white">Jam Operasional Stand</h4>
                        <p class="text-xs font-bold text-primary truncate">{{ $stall->operating_hours }}</p>
                    </div>
                </div>
            </div>

            <!-- Canteen Balance Card -->
            <div class="bg-emerald-500/5 dark:bg-emerald-500/10 rounded-3xl p-5 border border-emerald-500/20 dark:border-emerald-500/20 shadow-xs space-y-1">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-2xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="text-xs font-extrabold text-slate-900 dark:text-white">Saldo Aktif Kantin</h4>
                        <div class="flex items-center justify-between gap-1.5 mt-0.5">
                            <span class="text-xs font-black text-emerald-600 dark:text-emerald-400 truncate">Rp {{ number_format($stall->balance, 0, ',', '.') }}</span>
                            <a href="{{ route('canteen.vendor.finance') }}" class="text-[10px] text-indigo-600 dark:text-indigo-400 hover:underline font-extrabold shrink-0">Kelola &rarr;</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Edit Profile Form (lg:col-span-8) -->
        <div class="lg:col-span-8">
            <div class="bg-primary/5 dark:bg-primary/10 rounded-3xl p-6 border border-primary/20 dark:border-primary/20 shadow-xs space-y-5">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Pengaturan Detail Stand Kantin</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Informasi ini akan tampil pada aplikasi E-Kantin mobile siswa.</p>
                </div>

                <form method="POST" action="{{ route('canteen.vendor.profile.update') }}" enctype="multipart/form-data" class="space-y-4 text-xs">
                    @csrf

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Stand Kantin</label>
                        <input type="text" name="name" value="{{ old('name', $stall->name) }}" required class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-xs font-medium text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Pemilik / Penanggung Jawab</label>
                            <input type="text" name="owner_name" value="{{ old('owner_name', $stall->owner_name) }}" class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-xs font-medium text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">No. WhatsApp / Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone', $stall->phone) }}" placeholder="08xxxxxxxxxx" class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-xs font-medium text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <!-- Jam Operasional Toko (Time Inputs) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jam Buka Kantin</label>
                            <input type="time" name="open_time" value="{{ old('open_time', $stall->open_time ?? '07:00') }}" required class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Jam Tutup Kantin</label>
                            <input type="time" name="close_time" value="{{ old('close_time', $stall->close_time ?? '15:00') }}" required class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <!-- Status Stand Kantin Toggle Switch -->
                    <div x-data="{ isActive: {{ old('is_active', $stall->is_active) ? 'true' : 'false' }} }" class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-800 flex items-center justify-between gap-3">
                        <input type="hidden" name="is_active" :value="isActive ? 1 : 0">
                        <div class="space-y-0.5 min-w-0">
                            <label class="block font-extrabold text-slate-900 dark:text-white text-xs">Status Stand Kantin</label>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium truncate" x-text="isActive ? 'Toko Buka (Siap menerima pesanan dari siswa)' : 'Toko Tutup Sementara (Siswa tidak dapat memesan)'"></p>
                        </div>
                        <button type="button" 
                                @click="isActive = !isActive" 
                                class="relative inline-flex shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary/20"
                                :class="isActive ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-700'"
                                style="width: 44px; height: 24px; padding: 0; border: none; display: inline-flex; align-items: center;">
                            <span class="sr-only">Toggle Status Stand</span>
                            <span class="pointer-events-none rounded-full bg-white shadow-md"
                                  :style="isActive ? 'position: absolute; top: 2px; width: 20px; height: 20px; transition: left 200ms ease-in-out; left: 22px;' : 'position: absolute; top: 2px; width: 20px; height: 20px; transition: left 200ms ease-in-out; left: 2px;'"></span>
                        </button>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Deskripsi & Catatan Stand</label>
                        <textarea name="description" rows="3" placeholder="Informasi seputar katering, jenis masakan..." class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-xs font-medium text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">{{ old('description', $stall->description) }}</textarea>
                    </div>

                    <!-- Upload File Fields with Live Preview -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 space-y-2">
                            <label class="block font-bold text-slate-700 dark:text-slate-300">Logo Stand (Maks: 2MB)</label>
                            <img :src="logoPreview" class="w-20 h-20 rounded-2xl object-cover border border-slate-200 dark:border-slate-800 bg-white shadow-xs">
                            <input type="file" name="logo" accept="image/*" @change="handleImageChange($event, 'logo')" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-950 dark:file:text-indigo-300 hover:file:bg-indigo-100">
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 space-y-2">
                            <label class="block font-bold text-slate-700 dark:text-slate-300">Banner Sampul Stand (Maks: 3MB)</label>
                            <img :src="bannerPreview" class="w-full h-20 rounded-2xl object-cover border border-slate-200 dark:border-slate-800 bg-white shadow-xs">
                            <input type="file" name="banner" accept="image/*" @change="handleImageChange($event, 'banner')" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-950 dark:file:text-indigo-300 hover:file:bg-indigo-100">
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:justify-end">
                        <button type="submit" class="w-full sm:w-auto px-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 active:scale-[0.98] text-white font-extrabold rounded-2xl text-xs shadow-md shadow-indigo-600/20 transition-all flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Perubahan Profil Stand</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
