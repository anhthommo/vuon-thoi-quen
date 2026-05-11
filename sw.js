// 🌻 Vườn Thói Quen — Service Worker
const CACHE = 'vuon-v3';
const ASSETS = ['./', './index.html', './manifest.json',
  './icons/icon-72.png', './icons/icon-96.png',
  './icons/icon-128.png', './icons/icon-192.png', './icons/icon-512.png'];

self.addEventListener('install', e => {
  e.waitUntil(caches.open(CACHE).then(c => c.addAll(ASSETS)));
  self.skipWaiting();
});

self.addEventListener('activate', e => {
  e.waitUntil(caches.keys().then(keys =>
    Promise.all(keys.filter(k => k !== CACHE).map(k => caches.delete(k)))
  ));
  self.clients.claim();
});

self.addEventListener('fetch', e => {
  // Firebase và OneSignal luôn fetch từ network
  if (e.request.url.includes('firestore') ||
      e.request.url.includes('googleapis') ||
      e.request.url.includes('onesignal')) {
    return;
  }
  e.respondWith(
    caches.match(e.request).then(cached =>
      cached || fetch(e.request).catch(() => caches.match('./index.html'))
    )
  );
});

self.addEventListener('push', e => {
  const d = e.data ? e.data.json() : {};
  e.waitUntil(self.registration.showNotification(d.title || '🌻 Vườn Thói Quen', {
    body: d.body || 'Đừng quên tick thói quen hôm nay! 💪',
    icon: './icons/icon-192.png',
    badge: './icons/icon-72.png',
    tag: 'daily-reminder',
    data: { url: './' }
  }));
});

self.addEventListener('notificationclick', e => {
  e.notification.close();
  e.waitUntil(
    clients.matchAll({ type: 'window' }).then(list => {
      for (const c of list) if ('focus' in c) return c.focus();
      return clients.openWindow('./index.html');
    })
  );
});
