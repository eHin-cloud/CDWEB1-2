import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;

try {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY || 'bswzxtdcwppv6ew4srhl',
        wsHost: import.meta.env.VITE_REVERB_HOST || window.location.hostname,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 8085,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 8085,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
        enabledTransports: ['ws', 'wss'],
    });
} catch (e) {
    console.warn('Laravel Echo Reverb initialization notice:', e);
}

