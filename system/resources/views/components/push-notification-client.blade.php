{{-- SISMI Push Notification Client Component (Universal Mobile & Desktop) --}}
<div id="sismiPushNotificationPrompt" class="hidden fixed bottom-5 right-5 z-[99998] max-w-sm w-full p-4 bg-white dark:bg-[#24303F] rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700/80 transform transition-all duration-300">
    <div class="flex items-start space-x-3.5">
        <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0">
            <h4 class="text-xs font-extrabold text-slate-800 dark:text-white leading-tight">
                Aktifkan Notifikasi HP & PC
            </h4>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                Dapatkan pengingat presensi otomatis dan pemberitahuan penting langsung di layar perangkat Anda.
            </p>
            <div class="mt-3 flex items-center space-x-2">
                <button type="button" onclick="enablePushNotifications()" class="px-3.5 py-1.5 bg-primary hover:bg-primary/90 text-white font-bold text-[11px] rounded-lg shadow-sm transition active:scale-95 cursor-pointer">
                    Izinkan Notifikasi
                </button>
                <button type="button" onclick="dismissPushPrompt()" class="px-2.5 py-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 font-semibold text-[11px] transition">
                    Nanti
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    // 1. Cek dukungan Push & Notification API
    if (!('Notification' in window) || !('serviceWorker' in navigator)) {
        return;
    }

    const shownNotifIds = new Set(JSON.parse(sessionStorage.getItem('sismi_shown_notifs') || '[]'));

    function isMobileDevice() {
        return /Android|iPhone|iPad|iPod/i.test(navigator.userAgent) || window.innerWidth < 768;
    }

    // 2. Inisialisasi saat window dimuat
    window.addEventListener('load', () => {
        if (Notification.permission === 'default') {
            // Tampilkan prompt halus setelah 3 detik jika belum pernah ditolak
            if (!localStorage.getItem('sismi_push_dismissed')) {
                setTimeout(() => {
                    const prompt = document.getElementById('sismiPushNotificationPrompt');
                    if (prompt) prompt.classList.remove('hidden');
                }, 3000);
            }
        } else if (Notification.permission === 'granted') {
            // Sudah diizinkan, pastikan subscription terdaftar ke server
            registerDeviceSubscription();
            // Jalankan poller berkala
            startRealtimeNotificationPoller();
        }
    });

    // 3. Fungsi aktifkan notifikasi saat tombol diklik
    window.enablePushNotifications = function() {
        dismissPushPrompt();

        Notification.requestPermission().then((permission) => {
            if (permission === 'granted') {
                registerDeviceSubscription();
                startRealtimeNotificationPoller();
                
                // Tampilkan test notifikasi perdana
                showLocalNotification('Notifikasi Aktif! 🎉', {
                    body: 'Perangkat ini sekarang siap menerima pengingat presensi dan pemberitahuan penting.',
                    url: window.location.href
                });
            } else {
                console.log('Notifikasi tidak diizinkan oleh pengguna.');
            }
        });
    };

    window.dismissPushPrompt = function() {
        const prompt = document.getElementById('sismiPushNotificationPrompt');
        if (prompt) prompt.classList.add('hidden');
        localStorage.setItem('sismi_push_dismissed', '1');
    };

    // 4. Daftarkan subscription ke backend
    async function registerDeviceSubscription() {
        try {
            const reg = await navigator.serviceWorker.ready;
            let sub = await reg.pushManager.getSubscription();

            // Jika belum ada subscription, buat baru (menggunakan browser push manager)
            if (!sub) {
                try {
                    sub = await reg.pushManager.subscribe({
                        userVisibleOnly: true,
                        // Dummy applicationServerKey fallback jika VAPID tidak diset spesifik
                        applicationServerKey: urlB64ToUint8Array('BEl62iUYgUivxIkv69yViEuiBIa-Ib9-SkvMeAtA3LFgDzkrxZJjSgSnfckjBJuBkr3qBUYIHBQFLXYp5Nksh8U')
                    });
                } catch(e) {
                    // Fallback create simple registration
                }
            }

            const endpoint = sub ? sub.endpoint : 'device-' + (navigator.userAgent.replace(/[^a-zA-Z0-9]/g, '').slice(0, 30)) + '-' + (window.screen.width + 'x' + window.screen.height);
            let p256dh = null;
            let auth = null;

            if (sub && sub.getKey) {
                const key = sub.getKey('p256dh');
                const token = sub.getKey('auth');
                p256dh = key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null;
                auth = token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null;
            }

            // Kirim ke backend
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            await fetch('{{ route('api.push.subscribe') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    endpoint: endpoint,
                    public_key: p256dh,
                    auth_token: auth,
                    device_type: isMobileDevice() ? 'mobile' : 'desktop',
                })
            });
        } catch (err) {
            console.debug('Push registration notice:', err);
        }
    }

    // 5. Poller Notifikasi Real-time (HP & PC)
    let pollerInterval = null;
    function startRealtimeNotificationPoller() {
        if (pollerInterval) return;

        // Poll pertama kali setelah 5 detik
        setTimeout(checkIncomingNotifications, 5000);

        // Poll berkala setiap 25 detik
        pollerInterval = setInterval(checkIncomingNotifications, 25000);
    }

    async function checkIncomingNotifications() {
        if (Notification.permission !== 'granted') return;

        try {
            const res = await fetch('{{ route('api.notifications.poll') }}');
            if (!res.ok) return;

            const data = await res.json();
            if (data.notifications && data.notifications.length > 0) {
                data.notifications.forEach(notif => {
                    if (!shownNotifIds.has(notif.id)) {
                        shownNotifIds.add(notif.id);
                        sessionStorage.setItem('sismi_shown_notifs', JSON.stringify(Array.from(shownNotifIds)));

                        showLocalNotification(notif.title, {
                            body: notif.body,
                            url: notif.url,
                            badge: '/pwa-icon/192',
                            icon: '/pwa-icon/192',
                        });
                    }
                });
            }
        } catch (e) {
            // Ignore polling errors
        }
    }

    // 6. Tampilkan notifikasi di layar OS (Ponsel & PC)
    async function showLocalNotification(title, options) {
        try {
            if ('serviceWorker' in navigator) {
                const reg = await navigator.serviceWorker.ready;
                if (reg.showNotification) {
                    reg.showNotification(title, {
                        body: options.body,
                        icon: options.icon || '/pwa-icon/192',
                        badge: options.badge || '/pwa-icon/192',
                        vibrate: [200, 100, 200],
                        data: { url: options.url || '/' },
                        requireInteraction: true
                    });
                    return;
                }
            }

            // Fallback native window Notification
            const n = new Notification(title, {
                body: options.body,
                icon: options.icon || '/pwa-icon/192',
            });
            n.onclick = function() {
                window.focus();
                if (options.url) window.location.href = options.url;
            };
        } catch(e) {
            console.debug('Show notification fallback:', e);
        }
    }

    function urlB64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }
})();
</script>
