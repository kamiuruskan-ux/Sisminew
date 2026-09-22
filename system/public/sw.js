// SISMI Web PWA Service Worker with Push Notification Support
const CACHE_NAME = 'sismi-pwa-v2';

self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(clients.claim());
});

self.addEventListener('fetch', (event) => {
    // Network-first strategy for dynamic system
    if (event.request.method !== 'GET') return;

    // Do not cache API or poll routes
    if (event.request.url.includes('/api/') || event.request.url.includes('/live-') || event.request.url.includes('/admin/')) {
        return;
    }

    event.respondWith(
        fetch(event.request).catch(() => {
            return caches.match(event.request);
        })
    );
});

// Push Event: Handle incoming push notification on Mobile and Desktop
self.addEventListener('push', (event) => {
    let data = {
        title: 'SISMI Notifikasi',
        body: 'Ada pemberitahuan baru di sistem sekolah.',
        icon: '/pwa-icon/192',
        badge: '/pwa-icon/192',
        url: '/admin/dashboard'
    };

    if (event.data) {
        try {
            const parsed = event.data.json();
            data = Object.assign(data, parsed);
        } catch (e) {
            data.body = event.data.text();
        }
    }

    const options = {
        body: data.body,
        icon: data.icon || '/pwa-icon/192',
        badge: data.badge || '/pwa-icon/192',
        vibrate: [200, 100, 200],
        data: {
            url: data.url || '/'
        },
        actions: [
            { action: 'open', title: 'Buka' },
            { action: 'close', title: 'Tutup' }
        ],
        requireInteraction: true
    };

    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

// Notification Click Event: Focus or Open Target URL
self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    if (event.action === 'close') return;

    const targetUrl = (event.notification.data && event.notification.data.url) ? event.notification.data.url : '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if (client.url === targetUrl && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});
