@extends('layouts.admin')

@section('title', 'Cryptographic License Generator')
@section('page_title', 'Cryptographic License Generator')

@section('content')
<div class="space-y-6" x-data="{
    copied: false,
    copyKey() {
        const text = document.getElementById('generatedKeyOutput').innerText;
        navigator.clipboard.writeText(text);
        this.copied = true;
        setTimeout(() => this.copied = false, 3000);
    }
}">

    <!-- Page Header -->
    <div class="tailadmin-card p-6 border-l-4 border-purple-600 bg-gradient-to-r from-purple-900/10 via-indigo-900/5 to-transparent">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center space-x-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-purple-600 text-white">DEVELOPER TOOL</span>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-[#1C2434] dark:text-white tracking-tight">Cryptographic Domain License Generator</h1>
                </div>
                <p class="text-xs text-[#64748B] dark:text-[#8A99AD]">
                    Fitur khusus pemilik aplikasi untuk membuat Kunci Lisensi Kriptografik terikat nama domain (*Domain-Bound Cryptographic Signature*).
                </p>
            </div>
            <div class="flex items-center space-x-3 shrink-0">
                <a href="{{ route('admin.database-maintenance.index') }}" class="btn-secondary text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Generator Result Banner if generated -->
    @if(session('generated_key'))
    <div class="tailadmin-card p-6 border-2 border-emerald-500/50 bg-emerald-50/50 dark:bg-emerald-950/20 space-y-4">
        <div class="flex items-center justify-between border-b border-emerald-200 dark:border-emerald-800 pb-3">
            <div class="flex items-center space-x-2 text-emerald-700 dark:text-emerald-400 font-extrabold text-sm">
                <svg class="w-5 h-5 text-emerald-500 animate-bounce" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>LISENSI KRIPTOGRAFIK RESMI BERHASIL DIBUAT!</span>
            </div>
            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-600 text-white">STATUS: VALID</span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
            <div class="p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                <p class="text-[10px] text-slate-400 font-bold uppercase">Domain Target</p>
                <p class="font-mono font-extrabold text-purple-600 dark:text-purple-400 text-sm mt-0.5">{{ session('gen_domain') }}</p>
            </div>
            <div class="p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                <p class="text-[10px] text-slate-400 font-bold uppercase">Nama Klien / Sekolah</p>
                <p class="font-extrabold text-slate-800 dark:text-slate-200 text-sm mt-0.5">{{ session('gen_client') }}</p>
            </div>
            <div class="p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                <p class="text-[10px] text-slate-400 font-bold uppercase">Tipe Lisensi</p>
                <p class="font-extrabold text-indigo-600 dark:text-indigo-400 text-sm mt-0.5">{{ session('gen_type') }}</p>
            </div>
            <div class="p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
                <p class="text-[10px] text-slate-400 font-bold uppercase">Masa Berlaku</p>
                <p class="font-extrabold text-emerald-600 dark:text-emerald-400 text-sm mt-0.5">{{ session('gen_expires') }}</p>
            </div>
        </div>

        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label class="text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider">KUNCI LISENSI APLIKASI (LICENSE KEY)</label>
                <button type="button" @click="copyKey()" class="inline-flex items-center space-x-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    <span x-text="copied ? 'Tersalin ke Clipboard!' : 'Salin Kunci Lisensi'"></span>
                </button>
            </div>
            <div id="generatedKeyOutput" class="p-4 bg-slate-950 text-emerald-400 font-mono text-xs break-all rounded-xl border border-slate-800 shadow-inner select-all leading-relaxed">
                {{ session('generated_key') }}
            </div>
        </div>
        
        @if(session('auto_installed'))
            <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400 flex items-center space-x-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <span>Lisensi telah otomatis dipasang ke aplikasi server ini.</span>
            </p>
        @endif
    </div>
    @endif

    <!-- Main Generator Form -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 tailadmin-card p-6 space-y-6">
            <h3 class="font-extrabold text-base text-[#1C2434] dark:text-white border-b border-slate-200 dark:border-slate-800 pb-3 flex items-center space-x-2">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                <span>Form Buat Kunci Lisensi Baru</span>
            </h3>

            <form method="POST" action="{{ route('admin.license-generator.process') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                        Nama Domain Target <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="domain" required placeholder="Contoh: sekolah-lrv.lapakcode.com atau *.lapakcode.com" value="{{ old('domain', request()->getHost()) }}" class="w-full text-xs font-mono py-2.5 px-3.5">
                    <p class="text-[11px] text-slate-400 mt-1">Dukungan domain exact (`sekolah.sch.id`), wildcard (`*.domain.com`), atau `localhost`.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                        Nama Klien / Sekolah Pembeli <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="client_name" required placeholder="Contoh: SMA Negeri 1 Jakarta" value="{{ old('client_name', Setting::get('school_name', 'Sekolah')) }}" class="w-full text-xs py-2.5 px-3.5">
                </div>

                <div class="p-3 bg-purple-50 dark:bg-purple-950/30 rounded-xl border border-purple-200 dark:border-purple-800 flex items-center justify-between text-xs">
                    <div class="flex items-center space-x-2 text-purple-700 dark:text-purple-300 font-bold">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Sifat Masa Berlaku Lisensi:</span>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-purple-600 text-white font-extrabold text-[10px] uppercase">LIFETIME (Permanen Selamanya)</span>
                </div>

                <div class="pt-2">
                    <label class="inline-flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="auto_install" value="1" class="rounded border-slate-300 text-purple-600 focus:ring-purple-500">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Langsung pasang lisensi ini ke server lokal ini sekarang</span>
                    </label>
                </div>

                <div class="pt-4 border-t border-slate-200 dark:border-slate-800">
                    <button type="submit" class="btn-primary w-full py-3 text-xs uppercase tracking-wider font-extrabold bg-purple-600 hover:bg-purple-700 text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 0121 9z"/></svg>
                        <span>Generate Kunci Lisensi Kriptografik</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- System Info & Command Line Info Card -->
        <div class="space-y-6">
            <div class="tailadmin-card p-6 space-y-4">
                <h4 class="font-extrabold text-sm text-[#1C2434] dark:text-white border-b border-slate-200 dark:border-slate-800 pb-2 flex items-center space-x-2">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Artisan Command Line (Terminal)</span>
                </h4>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Lisensi juga dapat dibuat langsung melalui Terminal CLI di server menggunakan command berikut:
                </p>
                <div class="p-3 bg-slate-950 text-slate-200 font-mono text-[11px] rounded-xl border border-slate-800 space-y-2">
                    <p class="text-indigo-400"># Contoh Lisensi Permanen Domain:</p>
                    <p class="break-all text-emerald-400">php system/artisan license:generate --domain="{{ request()->getHost() }}" --client="SMA 1" --install</p>
                </div>
            </div>

            <div class="tailadmin-card p-6 space-y-3">
                <h4 class="font-extrabold text-sm text-[#1C2434] dark:text-white border-b border-slate-200 dark:border-slate-800 pb-2">
                    Informasi Proteksi Kriptografik
                </h4>
                <ul class="text-xs text-slate-600 dark:text-slate-400 space-y-2 list-disc list-inside leading-relaxed">
                    <li>Kunci disandikan dengan tanda tangan HMAC-SHA256 bergaransi anti-tamper.</li>
                    <li>Jika domain diubah atau kunci dipindah tanpa izin, aplikasi otomatis mengunci akses.</li>
                    <li>Kunci aman dan kompatibel penuh dengan environment cPanel & WAMP.</li>
                </ul>
            </div>
        </div>
    </div>

</div>
@endsection
