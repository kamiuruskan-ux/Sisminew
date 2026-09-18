@extends('layouts.landing')

@section('title', 'Pendaftaran Siswa Baru - ' . Setting::get('school_name', 'Sekolah'))

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    .form-input {
        width: 100%;
        background-color: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 0.625rem 0.875rem;
        font-size: 1rem; /* 16px to prevent auto-zoom on iOS */
        font-weight: 600;
        color: #0f172a;
        transition: all 0.15s ease;
        outline: none;
        -webkit-appearance: none;
    }
    .form-input::placeholder { color: #94a3b8; font-weight: 400 !important; font-size: 0.875rem; }
    .form-input:focus { background-color: #fff; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12); }
    
    @media (min-width: 640px) {
        .form-input {
            font-size: 0.8125rem;
            padding: 0.75rem 1rem;
        }
    }

    .form-label { display: block; font-size: 0.6875rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b; margin-bottom: 0.375rem; }
    .radio-card { display: flex; align-items: center; gap: 0.625rem; padding: 0.75rem 1rem; border-radius: 0.75rem; border: 1.5px solid #e2e8f0; background-color: #f8fafc; cursor: pointer; transition: all 0.15s ease; user-select: none; }
    .radio-card.selected { background-color: #eff6ff; border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15); }
    .section-divider { display: flex; align-items: center; gap: 0.75rem; }
    .section-divider span { font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.07em; color: #94a3b8; white-space: nowrap; }
    .section-divider::before, .section-divider::after { content: ''; flex: 1; height: 1px; background: #e2e8f0; }
</style>
@endpush

@section('content')

<section class="relative bg-gradient-to-br from-slate-900 via-blue-950 to-indigo-950 text-white pt-28 pb-12 lg:pt-36 lg:pb-16 overflow-hidden">
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl translate-y-1/2 -translate-x-1/4"></div>
    </div>
    <div class="container-edunova relative z-10">
        <div class="flex items-start gap-4">
            <div class="hidden sm:flex w-12 h-12 rounded-2xl bg-blue-500/20 border border-blue-400/30 items-center justify-center shrink-0 mt-1">
                <svg class="w-6 h-6 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/15 border border-blue-400/25 text-xs font-bold text-blue-300 mb-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                    PENDAFTARAN RESMI T.A. {{ date('Y') }}/{{ date('Y') + 1 }}
                </div>
                <h1 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight mb-2">Formulir Pendaftaran Siswa Baru</h1>
                <p class="text-slate-400 text-sm leading-relaxed max-w-xl">Isi data dengan benar sesuai dokumen resmi (KK / Akta / Ijazah). Akun aktif setelah pembayaran dikonfirmasi.</p>
            </div>
        </div>
    </div>
</section>

<section class="bg-slate-50 min-h-screen py-10 lg:py-14" x-data="registrationForm()">

    <div x-show="showToast" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" :class="toastType === 'error' ? 'bg-red-600' : 'bg-emerald-600'" class="fixed top-5 left-1/2 -translate-x-1/2 z-[200] flex items-center gap-3 px-5 py-3 rounded-2xl shadow-2xl text-white text-xs font-semibold max-w-sm w-[90%]">
        <svg x-show="toastType==='error'" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <svg x-show="toastType==='success'" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        <span x-text="toastMessage" class="flex-1 leading-snug"></span>
        <button @click="showToast=false" class="opacity-70 hover:opacity-100 text-sm font-bold">x</button>
    </div>

    <div class="container-edunova">

        @if(isset($spmbEnabled) && !$spmbEnabled)
            @if(auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin')))
                <div class="mb-8 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-semibold flex items-center gap-3">
                    <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span><strong>Mode Pengujian Admin:</strong> Pendaftaran SPMB saat ini <strong>DITUTUP</strong>. Hanya Anda yang dapat mengakses halaman ini.</span>
                </div>
            @else
                <div class="max-w-lg mx-auto py-16 text-center">
                    <div class="bg-white rounded-3xl p-10 border border-slate-200 shadow-xl space-y-5">
                        <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto border border-rose-100">
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-rose-600 bg-rose-50 px-3 py-1 rounded-full border border-rose-100">Pendaftaran Ditutup</span>
                            <h2 class="text-xl font-extrabold text-slate-900 mt-3">SPMB Online Belum Dibuka</h2>
                            <p class="text-xs text-slate-500 mt-2 leading-relaxed">Formulir tidak menerima pendaftaran baru saat ini. Hubungi sekolah atau cek jadwal berkala.</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 justify-center pt-2">
                            <a href="{{ route('spmb.info') }}" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-bold text-xs uppercase tracking-wider">Lihat Info SPMB</a>
                            <a href="{{ route('home') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs uppercase tracking-wider border border-slate-200">Ke Beranda</a>
                        </div>
                    </div>
                </div>
                <?php return; ?>
            @endif
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">

            {{-- FORM (Order 1 on mobile, 2 on desktop) --}}
            <div class="lg:col-span-8 order-1 lg:order-2 space-y-5">

                {{-- Alpine reactive error alert (populated by AJAX) --}}
                <div x-show="ajaxErrors.length > 0" x-cloak
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="flex items-start gap-3 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
                    <svg class="w-4 h-4 shrink-0 mt-0.5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="flex-1">
                        <p class="font-extrabold mb-1">Pendaftaran gagal. Periksa kembali data Anda:</p>
                        <ul class="space-y-0.5">
                            <template x-for="(err, i) in ajaxErrors" :key="i">
                                <li x-text="'• ' + err"></li>
                            </template>
                        </ul>
                    </div>
                    <button @click="ajaxErrors=[]" class="opacity-60 hover:opacity-100 text-rose-600 font-black text-sm leading-none shrink-0">&times;</button>
                </div>

                {{-- Blade-level errors (non-AJAX fallback) --}}
                @if($errors->any())
                    <div class="flex items-start gap-3 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
                        <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <ul class="space-y-0.5">@foreach($errors->all() as $err)<li>• {{ $err }}</li>@endforeach</ul>
                    </div>
                @endif


                <div class="bg-white rounded-none sm:rounded-3xl border-y sm:border border-slate-200/80 shadow-xs sm:shadow-sm -mx-5 sm:mx-0 overflow-hidden">

                    <div class="flex items-center gap-3 sm:gap-4 px-4 sm:px-6 py-4 sm:py-5 border-b border-slate-100 bg-slate-50/50">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-blue-600 text-white flex items-center justify-center font-black text-xs sm:text-sm shadow-md shrink-0">1</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest text-blue-600">Langkah 1</p>
                            <h2 class="text-sm sm:text-base font-extrabold text-slate-900 leading-tight">Pembuatan Akun Portal SPMB</h2>
                        </div>
                        <span class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 border border-blue-100 rounded-full text-[10px] font-extrabold text-blue-600">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>Aktif
                        </span>
                    </div>

                    <form :action="getFormAction()" method="POST" @submit.prevent="submitForm" novalidate class="p-4 sm:p-8 space-y-5 sm:space-y-6">
                        @csrf

                        <div class="flex items-start gap-3 p-4 rounded-2xl bg-blue-50 border border-blue-100 text-xs text-blue-800">
                            <svg class="w-4 h-4 text-blue-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="leading-relaxed"><strong class="font-extrabold">Catatan:</strong> Setelah akun berhasil dibuat, Anda akan diarahkan ke halaman pembayaran biaya pendaftaran untuk melanjutkan ke Tahap 2 hingga 5.</p>
                        </div>

                        <div class="section-divider"><span>Data Diri Calon Siswa</span></div>

                        <div class="space-y-4">
                            <div>
                                <label class="form-label">Nama Lengkap Sesuai Dokumen Resmi <span class="text-rose-500">*</span></label>
                                <input type="text" name="full_name" required class="form-input" placeholder="Nama lengkap sesuai Ijazah / Akta" x-model="formData.full_name" value="{{ old('full_name') }}">
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="form-label">Email Aktif <span class="text-rose-500">*</span></label>
                                    <div class="relative">
                                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></span>
                                        <input type="email" name="email" required class="form-input pl-9" placeholder="nama@email.com" x-model="formData.email" value="{{ old('email') }}">
                                    </div>
                                </div>
                                <div>
                                    <label class="form-label">No. WhatsApp Aktif <span class="text-rose-500">*</span></label>
                                    <div class="relative">
                                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg></span>
                                        <input type="tel" name="phone" required class="form-input pl-9" placeholder="08xxxxxxxxxx" @input="formatPhone($event)" value="{{ old('phone') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="form-label">Tempat Lahir <span class="text-rose-500">*</span></label>
                                    <input type="text" name="birth_place" required class="form-input" placeholder="Kota / Kabupaten lahir" value="{{ old('birth_place') }}">
                                </div>
                                <div>
                                    <label class="form-label">Tanggal Lahir <span class="text-rose-500">*</span></label>
                                    <input type="date" name="birth_date" required class="form-input" value="{{ old('birth_date') }}">
                                </div>
                            </div>
                            <div>
                                <label class="form-label">Jenis Kelamin <span class="text-rose-500">*</span></label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="radio-card" :class="formData.gender === 'male' ? 'selected' : ''">
                                        <input type="radio" name="gender" value="male" required class="w-4 h-4 text-blue-600 shrink-0" @change="formData.gender = 'male'" {{ old('gender') === 'male' ? 'checked' : '' }}>
                                        <span class="text-xs font-extrabold text-slate-800">Laki-laki</span>
                                    </label>
                                    <label class="radio-card" :class="formData.gender === 'female' ? 'selected' : ''">
                                        <input type="radio" name="gender" value="female" required class="w-4 h-4 text-blue-600 shrink-0" @change="formData.gender = 'female'" {{ old('gender') === 'female' ? 'checked' : '' }}>
                                        <span class="text-xs font-extrabold text-slate-800">Perempuan</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="section-divider"><span>Akun dan Keamanan</span></div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Password <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></span>
                                    <input type="password" name="password" required minlength="8" class="form-input pl-9" placeholder="Minimal 8 karakter" @input="checkPasswordStrength($event)">
                                </div>
                                <div class="mt-2 space-y-1" x-show="passwordStrength > 0" x-transition>
                                    <div class="flex gap-1"><template x-for="i in 4"><div class="h-1 flex-1 rounded-full transition-all duration-300" :class="i <= passwordStrength ? getPasswordStrengthLabel().color : 'bg-slate-200'"></div></template></div>
                                    <p class="text-[10px] font-bold" :class="{'text-rose-500':passwordStrength<=1,'text-amber-500':passwordStrength===2,'text-blue-500':passwordStrength===3,'text-emerald-500':passwordStrength>=4}" x-text="getPasswordStrengthLabel().label"></p>
                                </div>
                            </div>
                            <div>
                                <label class="form-label">Konfirmasi Password <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>
                                    <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8" class="form-input pl-9" placeholder="Ketik ulang password" @input="checkPasswordMatch()">
                                </div>
                                <p id="password-match-error" class="hidden text-[10px] text-rose-500 font-bold mt-1.5">Password tidak cocok</p>
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full py-3 sm:py-3.5 rounded-xl sm:rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-extrabold text-xs sm:text-sm tracking-wide shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed disabled:transform-none" :disabled="isSubmitting">
                                <svg x-show="isSubmitting" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                <svg x-show="!isSubmitting" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span x-text="isSubmitting ? 'Memproses...' : 'Daftar Akun & Bayar'"></span>
                            </button>
                            <p class="text-center text-[11px] text-slate-400 mt-4">Sudah punya akun? <a href="{{ route('login') }}" class="text-blue-600 font-extrabold hover:underline">Masuk ke Portal SPMB</a></p>
                        </div>

                    </form>
                </div>

                <div class="bg-white rounded-none sm:rounded-3xl border-y sm:border border-slate-200/80 shadow-xs sm:shadow-sm -mx-5 sm:mx-0 overflow-hidden" x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex items-center gap-3 sm:gap-4 px-4 sm:px-6 py-4 text-left hover:bg-slate-50 transition-colors focus:outline-none">
                        <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100 shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-extrabold text-slate-900">Informasi dan Ketentuan Pendaftaran</p>
                            <p class="text-[10px] text-slate-500">Panduan tata cara pendaftaran dan verifikasi berkas</p>
                        </div>
                        <svg class="w-5 h-5 text-slate-400 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-transition class="px-4 sm:px-6 pb-5 sm:pb-6 space-y-2.5">
                        @foreach([['1','Isi data sesuai dokumen resmi: Ijazah SMP/MTs, Kartu Keluarga, dan Akta Kelahiran.'],['2','Gunakan email dan WhatsApp orang tua yang aktif untuk menerima notifikasi dan hasil seleksi.'],['3','Setelah mendaftar, lakukan pembayaran biaya pendaftaran untuk mengaktifkan akun portal.'],['4','Simpan nomor bukti pendaftaran yang dikirim ke email untuk keperluan seleksi.'],['5','Pantau status dan pengumuman secara berkala melalui Portal SPMB Online.']] as $item)
                        <div class="flex items-start gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-600">
                            <span class="w-6 h-6 rounded-full bg-blue-600 text-white font-extrabold text-[10px] flex items-center justify-center shrink-0 mt-0.5">{{ $item[0] }}</span>
                            <span class="leading-relaxed pt-0.5">{{ $item[1] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>

            {{-- SIDEBAR (Order 2 on mobile, 1 on desktop) --}}
            <aside class="lg:col-span-4 order-2 lg:order-1 space-y-5 lg:sticky lg:top-28">

                {{-- Langkah Pendaftaran --}}
                <div class="bg-gradient-to-br from-blue-900 to-indigo-900 text-white rounded-2xl sm:rounded-3xl p-5 shadow-xl border border-white/10">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-[10px] font-extrabold uppercase tracking-widest text-blue-300 mb-0.5">Langkah Pendaftaran</p>
                            <h3 class="text-sm font-extrabold">Registrasi Awal</h3>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-blue-500/20 text-blue-200 text-[10px] font-extrabold border border-blue-400/30">Langkah 1 dari 1</span>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center gap-3 p-3 rounded-2xl bg-blue-500/25 border border-blue-400/40">
                            <div class="w-7 h-7 rounded-xl bg-blue-500 text-white flex items-center justify-center text-xs font-black shrink-0">1</div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-extrabold">Buat Akun Portal</p>
                                <p class="text-[10px] text-blue-300">Mengisi email, no. WA, & tanggal lahir</p>
                            </div>
                            <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse shrink-0"></span>
                        </div>
                    </div>
                </div>

                {{-- Biaya Pendaftaran --}}
                <div class="bg-white rounded-2xl sm:rounded-3xl p-5 border border-slate-200 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Biaya Pendaftaran</p>
                            <p class="text-lg font-extrabold text-slate-900">Rp {{ number_format((float)Setting::get('spmb_registration_fee', 0), 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <p class="text-[10px] text-slate-400 leading-relaxed mt-3 pt-3 border-t border-slate-100">Dibayar setelah akun terdaftar. Mendukung Transfer Bank Manual, Midtrans, Tripay, dan Duitku.</p>
                </div>

                {{-- Helpdesk --}}
                <div class="bg-white rounded-2xl sm:rounded-3xl p-5 border border-slate-200 shadow-sm flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-green-50 text-green-600 flex items-center justify-center border border-green-100 shrink-0">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-extrabold text-slate-900">Butuh Bantuan?</p>
                        <p class="text-[10px] text-slate-500 mt-0.5 leading-relaxed">Hubungi helpdesk SPMB via WhatsApp jika ada kendala pendaftaran.</p>
                        <a href="https://wa.me/{{ Setting::get('spmb_whatsapp', '6281234567890') }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-extrabold text-green-600 hover:text-green-700 mt-2 transition-colors">
                            Chat Helpdesk
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </div>
                </div>

            </aside>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
function registrationForm() {
    return {
        showToast: false, toastMessage: '', toastType: 'error',
        isSubmitting: false, passwordStrength: 0,
        ajaxErrors: [],
        formData: { full_name: '{{ old('full_name') }}', email: '{{ old('email') }}', gender: '{{ old('gender') }}' },

        getFormAction() { return '{{ route('spmb.register.store') }}'; },

        checkPasswordStrength(e) {
            const p = e.target.value; let s = 0;
            if (p.length >= 8) s++; if (/[A-Z]/.test(p)) s++; if (/[0-9]/.test(p)) s++; if (/[^A-Za-z0-9]/.test(p)) s++;
            this.passwordStrength = s;
        },
        getPasswordStrengthLabel() {
            if (this.passwordStrength <= 1) return { label: 'Sangat Lemah', color: 'bg-rose-500' };
            if (this.passwordStrength === 2) return { label: 'Lemah', color: 'bg-amber-500' };
            if (this.passwordStrength === 3) return { label: 'Sedang', color: 'bg-blue-500' };
            return { label: 'Kuat', color: 'bg-emerald-500' };
        },
        checkPasswordMatch() {
            const p = document.querySelector('input[name="password"]')?.value || '';
            const c = document.getElementById('password_confirmation')?.value || '';
            const err = document.getElementById('password-match-error');
            if (err) err.classList.toggle('hidden', !(c && p !== c));
        },
        showToastMsg(msg, type = 'error') {
            this.toastMessage = msg; this.toastType = type;
            this.showToast = true; setTimeout(() => this.showToast = false, 5000);
        },
        formatPhone(e) { e.target.value = e.target.value.replace(/[^0-9]/g, ''); },

        async submitForm(e) {
            // Client-side pre-checks
            const pass = document.querySelector('input[name="password"]')?.value || '';
            const conf = document.getElementById('password_confirmation')?.value || '';
            if (pass !== conf) {
                this.ajaxErrors = ['Konfirmasi password tidak sesuai.'];
                this.$nextTick(() => this.$el.querySelector('[x-show="ajaxErrors.length > 0"]')?.scrollIntoView({ behavior: 'smooth', block: 'center' }));
                return;
            }
            if (!this.formData.gender) {
                this.ajaxErrors = ['Mohon pilih jenis kelamin.'];
                this.$nextTick(() => this.$el.querySelector('[x-show="ajaxErrors.length > 0"]')?.scrollIntoView({ behavior: 'smooth', block: 'center' }));
                return;
            }

            this.isSubmitting = true;
            this.ajaxErrors = [];

            const form = e.target;
            const formData = new FormData(form);

            try {
                const response = await fetch(form.action || this.getFormAction(), {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                            || document.querySelector('input[name="_token"]')?.value || '',
                    },
                    body: formData,
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Success — redirect to SPMB dashboard
                    this.showToastMsg('Akun berhasil dibuat! Mengalihkan...', 'success');
                    setTimeout(() => { window.location.href = data.redirect; }, 800);
                    return;
                }

                // Validation or server errors
                if (data.errors) {
                    this.ajaxErrors = Array.isArray(data.errors) ? data.errors : Object.values(data.errors).flat();
                } else if (data.message) {
                    this.ajaxErrors = [data.message];
                } else {
                    this.ajaxErrors = ['Terjadi kesalahan. Silakan coba lagi.'];
                }

                // Scroll error alert into view
                this.$nextTick(() => {
                    const alertEl = this.$el.querySelector('[x-show="ajaxErrors.length > 0"]');
                    if (alertEl) alertEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                });

            } catch (err) {
                this.ajaxErrors = ['Koneksi gagal. Periksa internet Anda dan coba lagi.'];
            } finally {
                this.isSubmitting = false;
            }
        },
    };
}
</script>
@endpush