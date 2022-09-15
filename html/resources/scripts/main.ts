import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

declare global {
    interface Window {
        axios: any;
        Pusher: any;
        Echo: any;
    }
}

window.axios = axios;

window.Pusher = Pusher;
window.Pusher.key = import.meta.env.VITE_PUSHER_APP_KEY;

window.Echo =  new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    wsHost: import.meta.env.VITE_PUSHER_HOST,
    wssPort: import.meta.env.VITE_PUSHER_PORT,
    forceTLS: import.meta.env.VITE_PUSHER_SCHEME ===  'https',
    enabledTransports: ['wss'],
});
