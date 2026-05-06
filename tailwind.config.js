import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            animation: {
                'blob': 'blob 7s infinite',
            },
            keyframes: {
                blob: {
                    '0%, 100%': {
                        transform: 'translate(0, 0) scale(1)',
                    },
                    '25%': {
                        transform: 'translate(20px, -20px) scale(1.05)',
                    },
                    '50%': {
                        transform: 'translate(-15px, 20px) scale(0.95)',
                    },
                    '75%': {
                        transform: 'translate(15px, 15px) scale(1.02)',
                    },
                },
            },
        },
    },

    plugins: [forms],
};
