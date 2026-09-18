@extends('layouts.spmb-mobile')

@section('title', 'Dashboard Pendaftaran')
@section('header_title', 'Dashboard')

@section('content')
@php
    $user = auth()->user();
    $registration = $registration ?? null;
@endphp

<div class="space-y-5">
    <!-- Success / Error Alerts -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1">
            @foreach($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    @if($needsPayment ?? false)
    {{-- Banner: Belum Bayar - auto-scroll ke seksi pembayaran --}}
    <div id="payment-reminder" class="p-4 rounded-2xl bg-amber-50 border border-amber-300 text-amber-900 flex items-start gap-3">
        <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs font-extrabold">Selesaikan Pembayaran Pendaftaran</p>
            <p class="text-[10px] mt-0.5 leading-relaxed text-amber-800">Akun Anda belum aktif. Lakukan pembayaran biaya pendaftaran agar dapat melengkapi data pendaftaran Anda.</p>
        </div>
        <a href="#payment-section" class="shrink-0 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-bold text-[10px] uppercase tracking-wide rounded-lg transition-colors">Bayar</a>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const el = document.getElementById('payment-section');
                if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 400);
        });
    </script>
    @endif

    <!-- Welcome Card -->
    <div class="bg-gradient-to-br from-amber-500 via-orange-500 to-red-500 rounded-2xl p-5 text-white shadow-lg relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-8 translate-x-8"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-4 -translate-x-4"></div>

        <div class="relative z-10">
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=ffffff&color=f59e0b&size=128"
                         alt="Profile"
                         class="w-16 h-16 rounded-full object-cover border-3 border-white/30 shadow-lg">
                    <div class="absolute bottom-0 right-0 w-4 h-4 bg-green-400 border-2 border-white rounded-full"></div>
                </div>

                <div class="flex-1">
                    <p class="text-xs text-orange-100 mb-1 font-semibold">Selamat Datang,</p>
                    <h2 class="text-lg font-extrabold truncate">{{ $user->name }}</h2>
                    <p class="text-xs text-orange-100 mt-1 flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                        Calon Siswa Baru
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-2 sm:gap-3 mt-4 pt-3 border-t border-white/15">
                <div class="bg-white/10 backdrop-blur-md border border-white/10 rounded-xl p-2 text-center min-w-0">
                    <p class="text-[9px] text-orange-100 font-semibold uppercase tracking-tight truncate">No. Daftar</p>
                    <p class="text-xs font-bold font-mono text-white mt-0.5 truncate">{{ $registration?->registration_number ?? '-' }}</p>
                </div>
                <div class="bg-white/10 backdrop-blur-md border border-white/10 rounded-xl p-2 text-center min-w-0">
                    <p class="text-[9px] text-orange-100 font-semibold uppercase tracking-tight truncate">Pembayaran</p>
                    <p class="text-xs font-extrabold capitalize text-white mt-0.5 truncate">
                        @if($registration?->payment_status === 'paid')
                            Lunas
                        @elseif($registration?->payment_status === 'pending')
                            Pending
                        @else
                            Belum Bayar
                        @endif
                    </p>
                </div>
                <div class="bg-white/10 backdrop-blur-md border border-white/10 rounded-xl p-2 text-center min-w-0">
                    <p class="text-[9px] text-orange-100 font-semibold uppercase tracking-tight truncate">Status Berkas</p>
                    <p class="text-xs font-extrabold capitalize text-white mt-0.5 truncate">{{ $registration?->status ?? 'draft' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN INTERFACE: UNPAID STATE -->
    @if($registration && $registration->payment_status !== 'paid')
        
        @if($registration->payment_status === 'pending')
            <!-- Pending Verification Page -->
            <div class="bg-white rounded-2xl p-6 border border-amber-200 shadow-sm text-center space-y-4">
                <div class="w-16 h-16 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center mx-auto border border-amber-100">
                    <svg class="w-8 h-8 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18"/>
                    </svg>
                </div>
                <div class="space-y-1">
                    <h3 class="font-extrabold text-slate-900 text-base">Pembayaran Sedang Diverifikasi</h3>
                    <p class="text-xs text-slate-500 leading-relaxed max-w-sm mx-auto">
                        Bukti transfer manual atau status pembayaran online Anda sedang diverifikasi oleh sistem / bendahara sekolah. Akun Anda akan diaktifkan secara otomatis setelah pembayaran sukses dikonfirmasi.
                    </p>
                </div>
                @if($latestTransaction && $latestTransaction->payment_gateway === 'manual')
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-left space-y-2">
                        <p class="text-[10px] font-extrabold uppercase text-slate-400">Bukti Transfer Diunggah</p>
                        <a href="{{ asset('img/' . $latestTransaction->payment_proof) }}" target="_blank" class="text-xs text-indigo-600 underline font-semibold flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Lihat File Bukti
                        </a>
                    </div>
                @endif
            </div>
        @else
            <!-- Checkout Selection Page -->
            <div id="payment-section" class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm space-y-5">
                <div class="pb-3 border-b border-slate-100">
                    <h3 class="font-extrabold text-slate-900 text-sm">Pembayaran Biaya Pendaftaran</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Selesaikan pembayaran untuk mengaktifkan akun dan melengkapi data.</p>
                </div>

                <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-center justify-between">
                    <div>
                        <p class="text-[10px] text-amber-800 font-extrabold uppercase tracking-wide">Total Pembayaran</p>
                        <p class="text-lg font-black text-amber-900">Rp {{ number_format($registrationFee, 0, ',', '.') }}</p>
                    </div>
                    <span class="px-3 py-1 bg-amber-200 text-amber-900 rounded-full text-[10px] font-extrabold uppercase border border-amber-300">WAJIB BAYAR</span>
                </div>

                @php
                    $activeOnlineGateway = null;
                    if (in_array('midtrans', $activeGateways)) {
                        $activeOnlineGateway = 'midtrans';
                    } elseif (in_array('tripay', $activeGateways)) {
                        $activeOnlineGateway = 'tripay';
                    } elseif (in_array('duitku', $activeGateways)) {
                        $activeOnlineGateway = 'duitku';
                    }
                @endphp

                <form action="{{ route('spmb.dashboard.checkout') }}" method="POST" class="space-y-5" x-data="{ gateway: '{{ in_array('manual', $activeGateways) ? 'manual' : ($activeOnlineGateway ?? '') }}', channel: '' }">
                    @csrf
                    
                    <div class="space-y-2.5">
                        <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wide">Pilih Metode Pembayaran</label>
                        
                        <div class="space-y-2">
                            <!-- Manual Bank Transfer Toggle -->
                            @if(in_array('manual', $activeGateways))
                                <label :class="gateway === 'manual' ? 'border-indigo-600 bg-indigo-50/40 ring-1 ring-indigo-500/20' : 'border-slate-200 bg-white'" class="flex items-center justify-between p-4 rounded-xl border-2 cursor-pointer transition-all">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center">
                                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-extrabold text-slate-900 text-xs">Transfer Bank (Verifikasi Manual)</p>
                                            <p class="text-[10px] text-slate-400">Verifikasi transfer manual oleh bendahara (1-24 jam)</p>
                                        </div>
                                    </div>
                                    <input type="radio" name="payment_gateway" value="manual" x-model="gateway" class="w-4 h-4 text-indigo-600">
                                </label>
                            @endif

                            <!-- Pembayaran Otomatis Toggle -->
                            @if($activeOnlineGateway)
                                <label :class="gateway === '{{ $activeOnlineGateway }}' ? 'border-indigo-600 bg-indigo-50/40 ring-1 ring-indigo-500/20' : 'border-slate-200 bg-white'" class="flex items-center justify-between p-4 rounded-xl border-2 cursor-pointer transition-all">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-black text-xs shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        </div>
                                        <div>
                                            <p class="font-extrabold text-slate-900 text-xs">Pembayaran Otomatis (Instan)</p>
                                            <p class="text-[10px] text-slate-400">Konfirmasi otomatis via QRIS, Virtual Account, dll.</p>
                                        </div>
                                    </div>
                                    <input type="radio" name="payment_gateway" value="{{ $activeOnlineGateway }}" x-model="gateway" class="w-4 h-4 text-indigo-600">
                                </label>
                            @endif
                        </div>
                    </div>

                    <!-- Tripay Channels Selection dropdown -->
                    <div x-show="gateway === 'tripay'" x-transition class="space-y-2">
                        <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wide">Pilih Metode Channel VA / QRIS</label>
                        <select name="payment_channel" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none text-xs font-bold text-slate-800">
                            <option value="">-- Pilih Saluran Pembayaran --</option>
                            @foreach($tripayChannels as $ch)
                                <option value="{{ $ch['code'] }}">{{ $ch['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Info Rekening Bank (muncul saat pilih Transfer Manual) -->
                    <div x-show="gateway === 'manual'" x-transition class="space-y-3 pt-2 border-t border-slate-100">
                        <p class="text-[11px] text-slate-500 leading-relaxed font-semibold flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            <span>Silakan transfer ke salah satu rekening berikut sebesar <strong class="text-slate-800">Rp {{ number_format($registrationFee, 0, ',', '.') }}</strong>:</span>
                        </p>
                        @if($bankAccounts->isEmpty())
                            <p class="text-xs text-slate-400 italic">Belum ada rekening yang dikonfigurasi oleh sekolah.</p>
                        @else
                            <div class="grid gap-3">
                                @foreach($bankAccounts as $bank)
                                    <div class="p-3.5 bg-indigo-50/50 border border-indigo-100 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <p class="font-extrabold text-slate-900 text-xs">{{ $bank->bank_name }}</p>
                                            @if($bank->account_number)
                                                <p class="text-[11px] font-mono text-indigo-600 font-extrabold tracking-wide mt-0.5 flex items-center gap-1.5">
                                                    <span>{{ $bank->account_number }}</span>
                                                    <button type="button"
                                                        onclick="navigator.clipboard.writeText('{{ $bank->account_number }}'); this.textContent='✓ Disalin'; setTimeout(()=>this.textContent='Salin',1500)"
                                                        class="text-[9px] bg-indigo-100 text-indigo-600 px-1.5 py-0.5 rounded font-bold hover:bg-indigo-200 transition-colors">Salin</button>
                                                </p>
                                            @endif
                                            <p class="text-[10px] text-slate-400 font-semibold mt-0.5">a.n. {{ $bank->account_name }}</p>
                                        </div>
                                        @if($bank->qr_code)
                                            <a href="{{ \Illuminate\Support\Str::startsWith($bank->qr_code, 'img/') ? asset($bank->qr_code) : asset('img/' . $bank->qr_code) }}" target="_blank" title="Tap untuk buka QR Code" class="flex sm:flex-col items-center gap-2 sm:gap-0.5 flex-shrink-0 bg-white sm:bg-transparent p-2 sm:p-0 rounded-xl border border-indigo-100 sm:border-0">
                                                <img src="{{ \Illuminate\Support\Str::startsWith($bank->qr_code, 'img/') ? asset($bank->qr_code) : asset('img/' . $bank->qr_code) }}" alt="QR Code {{ $bank->bank_name }}"
                                                    class="w-12 h-12 sm:w-16 sm:h-16 object-contain rounded-lg sm:rounded-xl border-2 border-indigo-200 bg-white p-0.5 hover:scale-105 transition-transform shadow-xs cursor-zoom-in">
                                                <p class="text-[9px] text-indigo-500 font-extrabold uppercase tracking-tight">Buka QR</p>
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Action Trigger -->
                    <button type="submit" class="w-full py-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white rounded-xl font-extrabold text-xs uppercase tracking-wider shadow-md transition-all flex items-center justify-center space-x-2">
                        <span x-text="gateway === 'manual' ? 'Simpan & Unggah Bukti Transfer' : 'Lanjutkan Pembayaran'">Lanjutkan Pembayaran</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                    </button>
                </form>

                <!-- Upload Bukti Transfer (muncul setelah memilih manual dan sudah ada transaksi pending) -->
                @if(in_array('manual', $activeGateways))
                    <div class="mt-4 pt-5 border-t border-slate-100 space-y-4" x-data="{ openUpload: {{ ($latestTransaction && $latestTransaction->payment_gateway === 'manual') ? 'true' : 'false' }} }">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-extrabold text-slate-900 uppercase">Unggah Bukti Transfer</h4>
                            <button type="button" @click="openUpload = !openUpload" class="text-xs text-indigo-600 font-bold hover:underline">
                                <span x-show="!openUpload">Buka Form ↗</span>
                                <span x-show="openUpload" x-cloak>Tutup Form</span>
                            </button>
                        </div>
                        <div x-show="openUpload" x-transition class="p-4 bg-slate-50/50 rounded-2xl border border-slate-200/80 space-y-4">
                            <div class="pb-2 border-b border-slate-200">
                                <h4 class="text-xs font-extrabold text-slate-900">Form Unggah Bukti Pendaftaran</h4>
                            </div>
                            <form action="{{ route('spmb.dashboard.upload-proof') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-[10px] font-extrabold text-slate-600 uppercase mb-2">Transfer Ke Bank Tujuan <span class="text-rose-500">*</span></label>
                                    <select name="bank_account_id" required class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-700">
                                        <option value="">-- Pilih Bank Tujuan --</option>
                                        @foreach($bankAccounts as $bank)
                                            <option value="{{ $bank->id }}">{{ $bank->bank_name }} - {{ $bank->account_number }} (a.n. {{ $bank->account_name }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-extrabold text-slate-600 uppercase mb-2">File Bukti Transfer <span class="text-rose-500">*</span></label>
                                    <x-file-upload name="payment_proof" accept="image/*" required="true" label="Upload Struk Transfer SPMB" help="Seret & lepas foto bukti bayar di sini" />
                                </div>
                                <div>
                                    <label class="block text-[10px] font-extrabold text-slate-600 uppercase mb-1">Catatan (Opsional)</label>
                                    <textarea name="notes" rows="2" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs placeholder-slate-400" placeholder="e.g. Pembayaran SPMB atas nama pendaftar..."></textarea>
                                </div>
                                <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-extrabold uppercase tracking-wider">
                                    Kirim Bukti Pembayaran
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        @endif

    @else
        <!-- Action Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <a href="{{ route('spmb.dashboard.edit') }}" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md hover:border-amber-200 transition-all duration-200 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <span class="hidden sm:inline-block text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full uppercase">Lengkapi</span>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider">Edit & Isi</p>
                    <p class="text-sm font-extrabold text-gray-850 mt-0.5 leading-tight">Lengkapi Data</p>
                </div>
            </a>

            <a href="{{ route('spmb.dashboard.account') }}" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md hover:border-blue-200 transition-all duration-200 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <span class="hidden sm:inline-block text-[10px] font-bold text-blue-700 bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-full uppercase">Profil</span>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider">Keamanan</p>
                    <p class="text-sm font-extrabold text-gray-850 mt-0.5 leading-tight">Akun Saya</p>
                </div>
            </a>

            <a href="{{ route('spmb.dashboard.announcements') }}" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md hover:border-orange-200 transition-all duration-200 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                        </svg>
                    </div>
                    <span class="hidden sm:inline-block text-[10px] font-bold text-orange-700 bg-orange-50 border border-orange-200 px-2 py-0.5 rounded-full uppercase">Info</span>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider">Pengumuman</p>
                    <p class="text-sm font-extrabold text-gray-850 mt-0.5 leading-tight">Info Terbaru</p>
                </div>
            </a>

            <a href="https://wa.me/{{ Setting::get('spmb_whatsapp', '6281234567890') }}" target="_blank" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md hover:border-green-200 transition-all duration-200 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <span class="hidden sm:inline-block text-[10px] font-bold text-green-700 bg-green-50 border border-green-200 px-2 py-0.5 rounded-full uppercase">Bantuan</span>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider">Hubungi CS</p>
                    <p class="text-sm font-extrabold text-gray-850 mt-0.5 leading-tight">Ada Bantuan?</p>
                </div>
            </a>
        </div>

        <!-- Final Submission Warning/Trigger for Draft status -->
        @if($registration && $registration->status === 'draft')
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-2xl p-5 space-y-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <h4 class="font-extrabold text-amber-900 text-sm">Lengkapi Data Pendaftaran</h4>
                        <p class="text-xs text-amber-800 leading-relaxed mt-0.5">
                            Pembayaran Anda telah dikonfirmasi. Silakan klik menu <strong class="font-extrabold text-amber-950">Lengkapi Data</strong> untuk mengisi data pribadi lengkap, data orang tua, dan mengunggah dokumen pasfoto, KK, serta Akta Kelahiran.
                        </p>
                    </div>
                </div>
                
                <div class="pt-3 border-t border-amber-200/60 flex flex-col sm:flex-row gap-2.5 sm:items-center sm:justify-between">
                    <a href="{{ route('spmb.dashboard.edit') }}" class="w-full sm:w-auto text-center px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-extrabold rounded-xl uppercase tracking-wider shadow-sm">
                        Lengkapi Sekarang
                    </a>
                    
                    <!-- Final Submit Form -->
                    <form action="{{ route('spmb.dashboard.final-submit') }}" method="POST" class="w-full sm:w-auto m-0">
                        @csrf
                        <button type="submit" class="w-full px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold rounded-xl uppercase tracking-wider shadow-sm">
                            Kirim Pendaftaran Resmi
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <!-- Registration Status -->
        @if($registration)
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Status Pendaftaran
            </h3>
            
            <div class="space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 p-3 bg-gradient-to-r from-amber-50 to-orange-50 rounded-lg border border-amber-100">
                    <span class="text-xs sm:text-sm text-gray-600 font-semibold">Nomor Pendaftaran</span>
                    <span class="text-xs sm:text-sm font-bold text-amber-700 font-mono">{{ $registration->registration_number }}</span>
                </div>
                
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 p-3 bg-gray-50 rounded-lg">
                    <span class="text-xs sm:text-sm text-gray-600">Gelombang</span>
                    <span class="text-xs sm:text-sm font-semibold text-gray-800">{{ $registration->wave?->name ?? '-' }}</span>
                </div>
                
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 p-3 bg-gray-50 rounded-lg">
                    <span class="text-xs sm:text-sm text-gray-600">Tanggal Daftar</span>
                    <span class="text-xs sm:text-sm font-semibold text-gray-800">{{ $registration->created_at->format('d M Y') }}</span>
                </div>
                
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 p-3 {{ $registration->status === 'accepted' ? 'bg-green-50' : ($registration->status === 'rejected' ? 'bg-red-50' : 'bg-blue-50') }} rounded-lg">
                    <span class="text-xs sm:text-sm text-gray-600 font-semibold">Status Kelulusan</span>
                    <span class="px-3 py-1 text-[10px] sm:text-xs font-bold rounded-full text-center {{ $registration->status === 'accepted' ? 'bg-green-100 text-green-700' : ($registration->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }}">
                        @if($registration->status === 'submitted')
                            Menunggu Verifikasi Berkas
                        @elseif($registration->status === 'verified')
                            Berkas Terverifikasi
                        @elseif($registration->status === 'accepted')
                            Diterima / Lulus
                        @elseif($registration->status === 'rejected')
                            Ditolak
                        @else
                            Draft / Belum Dikirim
                        @endif
                    </span>
                </div>
            </div>
        </div>
        @endif

        <!-- Quick Info -->
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h4 class="font-bold text-amber-900 text-sm mb-1">Informasi Penting</h4>
                    <p class="text-xs text-amber-800 leading-relaxed">
                        Pastikan semua berkas persyaratan asli yang diunggah dapat terbaca secara jelas agar mempercepat proses verifikasi berkas oleh admin penerimaan siswa baru.
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
