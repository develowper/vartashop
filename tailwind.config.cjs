const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors')
/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        "./node_modules/tw-elements/dist/js/**/*.js"
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Yekan', 'Tanha', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: colors.teal,
            },
        },

    },

    plugins: [require('@tailwindcss/forms'), require("tw-elements/dist/plugin.cjs")],
};
