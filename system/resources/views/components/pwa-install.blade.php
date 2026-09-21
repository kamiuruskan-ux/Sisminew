@php
    $pwaLogo = \App\Models\Setting::getLogoUrl();
    $pwaName = \App\Models\Setting::get('school_name', config('app.name', 'SISMI'));
    $pwaShortName = \App\Models\Setting::get('school_short_name', 'SISMI');
    $pwaColor = \App\Models\Setting::get('primary_color', '#3C50E0');
    $pwaVersion = \App\Models\Setting::getLogoVersion();
@endphp

<!-- PWA Manifest & App Metas -->
<link rel="manifest" href="/manifest.json?v={{ $pwaVersion }}">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="{{ $pwaShortName }}">
<meta name="application-name" content="{{ $pwaName }}">
<meta name="theme-color" content="{{ $pwaColor }}">
<link rel="icon" type="image/png" sizes="192x192" href="/pwa-icon/192?v={{ $pwaVersion }}">
<link rel="icon" type="image/png" sizes="512x512" href="/pwa-icon/512?v={{ $pwaVersion }}">
<link rel="apple-touch-icon" sizes="180x180" href="/pwa-icon/180?v={{ $pwaVersion }}">
<link rel="apple-touch-icon" href="/pwa-icon/192?v={{ $pwaVersion }}">

<!-- PWA Install Modal (Universal for Desktop, Android & iOS Safari) -->
<div id="pwaInstallModal" class="hidden fixed inset-0 z-[99999] overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
    <div class="bg-white dark:bg-[#24303F] rounded-3xl shadow-2xl max-w-sm w-full border border-slate-200 dark:border-slate-800 p-6 text-center transform transition-all relative">
        <button type="button" onclick="closePwaModal()" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-400 hover:text-slate-600 dark:hover:text-white flex items-center justify-center transition">
            &times;
        </button>

        <!-- System Logo dynamically loaded -->
        <div class="w-20 h-20 mx-auto mb-4 rounded-2xl p-2 bg-slate-50 dark:bg-[#1A222C] border border-slate-100 dark:border-slate-800 shadow-md flex items-center justify-center">
            <img src="/pwa-icon/192?v={{ $pwaVersion }}" alt="{{ $pwaName }}" class="w-full h-full object-contain rounded-xl">
        </div>

        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">{{ $pwaName }}</h3>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 mb-5">
            Pasang aplikasi ini di layar utama perangkat Anda untuk akses lebih cepat, praktis, dan tanpa perlu membuka browser.
        </p>

        <!-- Dynamic Content depending on Platform -->
        <div id="pwaNativePromptArea" class="space-y-3">
            <button type="button" id="pwaInstallActionBtn" onclick="executePwaInstall()" class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 active:scale-98 text-white font-extrabold text-xs rounded-xl shadow-lg shadow-indigo-600/30 transition cursor-pointer flex items-center justify-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span>Pasang Sekarang</span>
            </button>
        </div>

        <!-- iOS Safari Instructions (shown if on iOS) -->
        <div id="pwaIosInstructions" class="hidden text-left bg-slate-50 dark:bg-[#1A222C] p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 text-xs space-y-2.5 text-slate-600 dark:text-slate-300">
            <p class="font-bold text-slate-800 dark:text-white flex items-center gap-1.5">
                <span>🍎</span> Cara Pasang di iPhone / iPad:
            </p>
            <ol class="list-decimal list-inside space-y-1 text-[11px] leading-relaxed">
                <li>Ketuk tombol <strong>Share / Bagikan</strong> (<svg class="w-3.5 h-3.5 inline text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>) di menu Safari.</li>
                <li>Gulir ke bawah dan pilih <strong>"Add to Home Screen"</strong> (Tambahkan ke Layar Utama).</li>
                <li>Ketuk <strong>"Tambah"</strong> di pojok kanan atas.</li>
            </ol>
        </div>

        <button type="button" onclick="closePwaModal()" class="mt-4 text-xs font-semibold text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
            Nanti Saja
        </button>
    </div>
</div>

<script>
let deferredPwaPrompt = null;

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPwaPrompt = e;
    // Show install buttons if hidden
    const installBtns = document.querySelectorAll('.pwa-install-trigger');
    installBtns.forEach(btn => btn.classList.remove('hidden'));
});

// Register Service Worker
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(err => {
            console.debug('PWA ServiceWorker Notice:', err);
        });
    });
}

function isIos() {
    return /iphone|ipad|ipod/.test(window.navigator.userAgent.toLowerCase());
}

function isInStandaloneMode() {
    return ('standalone' in window.navigator) && (window.navigator.standalone) || window.matchMedia('(display-mode: standalone)').matches;
}

function triggerPwaInstall() {
    if (isInStandaloneMode()) {
        alert('Aplikasi sudah terpasang di perangkat Anda.');
        return;
    }

    if (deferredPwaPrompt) {
        deferredPwaPrompt.prompt();
        deferredPwaPrompt.userChoice.then((choiceResult) => {
            if (choiceResult.outcome === 'accepted') {
                console.log('User accepted PWA installation');
            }
            deferredPwaPrompt = null;
        });
        return;
    }

    // If on iOS or beforeinstallprompt hasn't fired, open instruction modal
    const modal = document.getElementById('pwaInstallModal');
    const iosArea = document.getElementById('pwaIosInstructions');
    const nativeArea = document.getElementById('pwaNativePromptArea');

    if (isIos()) {
        if (iosArea) iosArea.classList.remove('hidden');
        if (nativeArea) nativeArea.classList.add('hidden');
    } else {
        if (iosArea) iosArea.classList.add('hidden');
        if (nativeArea) nativeArea.classList.remove('hidden');
    }

    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

function executePwaInstall() {
    if (deferredPwaPrompt) {
        deferredPwaPrompt.prompt();
        deferredPwaPrompt.userChoice.then((choiceResult) => {
            deferredPwaPrompt = null;
            closePwaModal();
        });
    } else {
        alert('Untuk memasang aplikasi, buka menu browser Anda (titik tiga di kanan atas) lalu pilih "Install Aplikasi" atau "Tambahkan ke Layar Utama".');
        closePwaModal();
    }
}

function closePwaModal() {
    const modal = document.getElementById('pwaInstallModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}
</script>
