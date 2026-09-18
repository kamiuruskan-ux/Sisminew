@extends('layouts.student-mobile')

@section('title', 'Verifikasi PIN')
@section('header_title', 'Keamanan PIN')

@section('content')
<div class="fixed inset-0 bg-slate-950/50 dark:bg-slate-950/70 backdrop-blur-xs z-[999] flex items-center justify-center p-4 overflow-y-auto">
    <div class="relative w-full max-w-md bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-xl space-y-6 text-center"
         x-data="{ 
            pin: '', 
            maxLength: 6,
            loading: false,
            init() {
                this.$nextTick(() => {
                    this.focusInput();
                });
            },
            focusInput() {
                this.$refs.pinInput.focus();
            },
            onInput(e) {
                this.pin = e.target.value.replace(/\D/g, '').substring(0, this.maxLength);
                if (this.pin.length === this.maxLength) {
                    this.loading = true;
                    document.getElementById('pin-form').submit();
                }
            }
         }" @click="focusInput()">
        
        <!-- Loading State Overlay -->
        <div x-show="loading" x-cloak class="absolute inset-0 bg-white/90 dark:bg-slate-900/90 rounded-3xl flex flex-col items-center justify-center space-y-3 z-30">
            <div class="w-10 h-10 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
            <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Memverifikasi PIN...</p>
        </div>

        <!-- Lock Icon Header with Pulse Animation -->
        <div class="relative w-20 h-20 bg-indigo-50 dark:bg-indigo-950/60 rounded-full flex items-center justify-center mx-auto border border-indigo-100 dark:border-indigo-800 shadow-md">
            <div class="absolute inset-0 rounded-full bg-indigo-400/10 animate-ping"></div>
            <svg class="w-10 h-10 text-indigo-600 dark:text-indigo-400 relative z-10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>

        <!-- Info Headings -->
        <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white leading-tight">Masukkan PIN Keamanan</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 max-w-xs mx-auto leading-relaxed">
                Silakan masukkan 6 digit PIN keamanan akun siswa Anda untuk mengakses sistem.
            </p>
        </div>

        @if($errors->any())
            <div class="p-3.5 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-700 dark:text-rose-300 text-xs font-semibold">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- PIN Form using Hidden Input and Custom Dots -->
        <form method="POST" action="{{ route('student.pin.verify.submit') }}" id="pin-form">
            @csrf
            
            <!-- Hidden Input -->
            <input type="tel" 
                   name="pin" 
                   x-ref="pinInput" 
                   @input="onInput($event)"
                   :value="pin"
                   class="absolute opacity-0 -z-50 pointer-events-none w-0 h-0" 
                   maxlength="6"
                   autocomplete="one-time-code"
                   autofocus>

            <!-- Custom 6-Digit Dots UI -->
            <div class="flex items-center justify-center gap-2 sm:gap-3 py-4">
                <template x-for="(item, index) in Array.from({length: maxLength})">
                    <div class="w-9 h-12 sm:w-11 sm:h-14 rounded-xl sm:rounded-2xl border-2 flex items-center justify-center text-lg font-black transition-all duration-150 cursor-pointer shadow-xs select-none"
                         :class="{
                             'border-indigo-600 dark:border-indigo-500 bg-indigo-50/30 dark:bg-indigo-950/20 text-slate-900 dark:text-white ring-2 ring-indigo-500/20': pin.length === index,
                             'border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white': pin.length > index,
                             'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900': pin.length <= index
                         }"
                         @click="focusInput()">
                        <!-- Dot or Hidden Character -->
                        <span x-text="pin.length > index ? pin.charAt(index) : ''" class="text-2xl font-black text-slate-900 dark:text-white leading-none"></span>
                    </div>
                </template>
            </div>

            <!-- Custom Helper Info -->
            <div class="text-[11px] text-slate-400 dark:text-slate-500 pt-2 font-medium">
                Ketuk di mana saja untuk memfokuskan input keyboard
            </div>
        </form>

        <!-- Logout fallback if student wants to sign out -->
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
