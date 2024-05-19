import './bootstrap';

import Alpine from 'alpinejs';
import htmx from 'htmx.org';

import.meta.glob([
    '../images/**',
]);

window.Alpine = Alpine;
window.htmx = htmx

if (import.meta.env.VITE_INCLUDE_JQUERY === 'true') {
    import('jquery').then(({ default: $ }) => {
        window.jQuery = $;
    });
}

Alpine.start();
