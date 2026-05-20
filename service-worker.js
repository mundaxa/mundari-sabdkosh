const CACHE_NAME = 'mundari-sabdkosh-v1';
const ASSETS = [
    '/',
    '/index.php',
    '/dictionary.php',
    '/login.php',
    '/register.php',
    '/404.php',
    '/assets/css/style.css',
    '/assets/css/dark.css',
    '/assets/css/light.css',
    '/assets/css/responsive.css',
    '/assets/js/theme.js',
    '/assets/js/main.js',
    '/assets/js/search.js',
    '/assets/js/animations.js',
    '/manifest.json'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(ASSETS))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.filter((name) => name !== CACHE_NAME)
                    .map((name) => caches.delete(name))
            );
        }).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') return;

    event.respondWith(
        caches.match(event.request)
            .then((cached) => {
                const fetchPromise = fetch(event.request)
                    .then((response) => {
                        if (response && response.status === 200 && response.type === 'basic') {
                            const clone = response.clone();
                            caches.open(CACHE_NAME).then((cache) => {
                                cache.put(event.request, clone);
                            });
                        }
                        return response;
                    })
                    .catch(() => cached);

                return cached || fetchPromise;
            })
    );
});

self.addEventListener('message', (event) => {
    if (event.data === 'skipWaiting') {
        self.skipWaiting();
    }
});
