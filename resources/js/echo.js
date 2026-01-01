import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

export const echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    encrypted: true,
    auth: {
        headers: {
            Authorization: `Bearer ${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}`,
            // or use 'X-CSRF-TOKEN' if using standard Laravel CSRF
        }
    }
});