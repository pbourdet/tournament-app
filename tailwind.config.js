import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: [
        {
            pattern: /(bg|text)-(blue|red|yellow|green)-500/,
        },
    ],

    theme: {
        extend: {
            fontFamily: {
                body: ['"Comic Sans MS"', 'cursive', 'sans-serif'],
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
