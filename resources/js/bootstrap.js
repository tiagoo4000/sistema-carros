import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;

const PUSHER_KEY = import.meta.env.VITE_PUSHER_APP_KEY;
const PUSHER_HOST = import.meta.env.VITE_PUSHER_HOST;
if (PUSHER_KEY && PUSHER_HOST) {
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: PUSHER_KEY,
        wsHost: PUSHER_HOST,
        wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
        wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
        forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
        encrypted: true,
        disableStats: true,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
        enabledTransports: ['ws', 'wss'],
    });
} else {
    window.Echo = null;
}
