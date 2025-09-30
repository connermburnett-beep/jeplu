import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                headline: ['Fredoka One', 'cursive'],
                mono: ['Roboto Mono', 'monospace'],
            },
            colors: {
                'jeopardy-blue': '#1E3A8A',
                'jeopardy-yellow': '#FACC15',
                'jeopardy-red': '#EF4444',
                'jeopardy-green': '#22C55E',
            },
        },
    },

    plugins: [forms, typography],
};
