const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/View/Middleware/ShareErrorsFromSession.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                'guinda-ceaa': '#932C43',
            },
            fontFamily: {
                sans: ['Nunito', 'sans-serif'],
            },
        },
    },

    plugins: [require('@tailwindcss/forms')],
};
