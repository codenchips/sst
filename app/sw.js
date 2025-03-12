// sw.js
const CACHE_NAME = "sst-offline-cache-v1";
const ASSETS = [
    "/",             // Home page
    "/index.php",
    "/css/app.css",
    "/js/scripts.js",
    "/images/tamlite-logo.jpg",
];

// Install event
self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(ASSETS); // Cache app shell
        })
    );
});

// Fetch event (to serve cached assets)
self.addEventListener("fetch", (event) => {
    event.respondWith(
        caches.match(event.request).then((response) => {
            return response || fetch(event.request);
        })
    );
});
