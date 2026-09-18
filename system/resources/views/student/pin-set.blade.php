@extends('layouts.student-mobile')

@section('title', 'Buat PIN Baru')
@section('header_title', 'Keamanan PIN')

@section('content')
<div class="fixed inset-0 bg-slate-950/50 dark:bg-slate-950/70 backdrop-blur-xs z-[999] flex items-center justify-center p-4 overflow-y-auto">
    <div class="w-full max-w-md bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-xl space-y-5 text-center">
        
        <!-- Icon -->
        <div class="relative w-16 h-16 bg-indigo-50 dark:bg-indigo-950/60 rounded-full flex items-center justify-center mx-auto border border-indigo-100 dark:border-indigo-800 shadow-md">
            <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
        </div>

        <!-- Info Headings -->
        <div>
            <h2 class="text-lg font-black text-slate-900 dark:text-white leading-tight">Buat PIN Keamanan Baru</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-xs mx-auto leading-relaxed">
                PIN ini akan digunakan untuk keamanan transaksi QRpay (E-Kantin) dan login sistem Anda.
            </p>
        </div>

        @if($errors->any())
            <div class="p-3 py-2 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-700 dark:text-rose-300 text-xs font-semibold text-left">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form for Setting PIN -->
        <form method="POST" action="{{ route('student.pin.set.submit') }}" class="space-y-4 text-left" x-data="{ loading: false }" @submit="loading = true">
            @csrf
            
            <!-- PIN Input -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">PIN Baru (6 Digit)</label>
                <div class="relative">
                    <input type="password" 
                           name="pin" 
                           maxlength="6"
                           pattern="[0-9]*"
                           inputmode="numeric"
                           placeholder="Masukkan 6 digit angka"
                           required
                           class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-800 rounded-2xl text-center font-mono font-black text-base focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
            </div>

            <!-- PIN Confirmation -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Konfirmasi PIN Baru</label>
                <div class="relative">
                    <input type="password" 
                           name="pin_confirmation" 
                           maxlength="6"
                           pattern="[0-9]*"
                           inputmode="numeric"
                           placeholder="Ulangi 6 digit angka"
                           required
                           class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-800 rounded-2xl text-center font-mono font-black text-base focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
            </div>

            <!-- Password Account Verification -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Konfirmasi Password Akun Anda</label>
                <div class="relative">
                    <input type="password" 
                           name="password" 
                           placeholder="Masukkan password Anda saat ini"
                           required
                           class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-800 rounded-2xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
            </div>

            <button type="submit" :disabled="loading" class="w-full py-3.5 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-extrabold rounded-2xl text-xs shadow-md shadow-indigo-600/20 transition-all flex items-center justify-center space-x-2 cursor-pointer uppercase tracking-wider disabled:opacity-50">
                <template x-if="!loading">
                    <span class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-indigo-100" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Buat PIN & Masuk Dashboard</span>
                    </span>
                </template>
                <template x-if="loading">
                    <span class="flex items-center space-x-2">
                        <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                        <span>Memproses PIN Baru...</span>
                    </span>
                </template>
            </button>
        </form>

        <!-- Logout fallback -->
        <div class="pt-4 border-t border-slate-100 dark:border-slate-850 flex justify-center">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-xs text-rose-500 hover:text-rose-600 font-bold transition-colors flex items-center space-x-1 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    <span>Log Out / Keluar</span>
                </button>
            </form>
        </div>

    </div>
</div>
@endsection
