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
                'guinda-ceaa': '#691B31', // Pantone 7421 C (Guinda oscuro)
                'guinda-ceaa-hover': '#A02142', // Pantone 7420 C (Guinda/Rojo medio)
                'guinda-medio': '#A02142', // Pantone 7420 C (Guinda/Rojo medio)
                'dorado-ocre': '#BC955B', // Pantone 465 C (Dorado/Ocre)
                'arena-claro': '#DDC9A3', // Pantone 468 C (Arena/Beige claro)
                'gris-claro': '#98989A', // Pantone COOL GRAY 7 C (Gris claro)
                'gris-oscuro': '#6F7271', // Pantone 424 C (Gris oscuro)
                'pantone-7421c': '#691B31', // Guinda oscuro
                'pantone-468c': '#DDC9A3',  // Arena/Beige claro
                'pantone-coolgray7c': '#98989A', // Gris claro
                'pantone-7420c': '#A02142', // Guinda/Rojo medio
                'pantone-465c': '#BC955B',  // Dorado/Ocre
                'pantone-424c': '#6F7271',  // Gris oscuro
            },
            fontFamily: {
                sans: ['Nunito', 'sans-serif'],
            },
        },
    },

    plugins: [require('@tailwindcss/forms')],
};
