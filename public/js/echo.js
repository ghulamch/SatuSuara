/**
 * Inisialisasi Laravel Echo & Pusher untuk Reverb tanpa Vite.
 *
 * Tambahkan CDN berikut di Blade jika ingin menggunakan WebSocket Reverb:
 * <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
 * <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
 * <script src="/js/echo.js"></script>
 */

if (typeof window.Pusher !== 'undefined' && typeof window.Echo !== 'undefined') {
    window.echoInstance = new window.Echo({
        broadcaster: 'reverb',
        key: '{{ config("reverb.apps.apps.0.key") }}',
        wsHost: window.location.hostname,
        wsPort: 8080,
        wssPort: 8080,
        forceTLS: false,
        enabledTransports: ['ws', 'wss'],
    });
}

