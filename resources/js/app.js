import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Passkey helpers exposed to window for inline Alpine usage.
import * as passkey from './passkey.js';
window.passkey = passkey;

document.addEventListener('DOMContentLoaded', () => {
    Alpine.start();
});
