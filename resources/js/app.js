import './bootstrap';
import.meta.glob([
    '../images/**',
]);

if (import.meta.env.VITE_INCLUDE_JQUERY === 'true') {
    import('jquery').then(({ default: $ }) => {
        window.jQuery = $;
    });
}
