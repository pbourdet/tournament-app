import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import colors from 'tailwindcss/colors';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './vendor/livewire/flux-pro/stubs/**/*.blade.php',
        './vendor/livewire/flux/stubs/**/*.blade.php',
    ],

    safelist: [
        {
            pattern: /(bg|text)-(blue|red|yellow|green)-500/,
        },
    ],

    theme: {
        extend: {
            colors: {
                zinc: colors.gray,
                accent: {
                    DEFAULT: 'var(--color-accent)',
                    content: 'var(--color-accent-content)',
                    foreground: 'var(--color-accent-foreground)',
                },
            },
            fontFamily: {
                sans: ['Inter', 'sans-serif'],
            },
            boxShadow: {
                soft: '4px 4px 8px 0 rgba(0, 0, 0, 0.1)',
            },
            borderRadius: {
                xl: '1rem',
            },
            spacing: {
                18: '4.5rem',
            },
        },
    },

    plugins: [forms],
};
