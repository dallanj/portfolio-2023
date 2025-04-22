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

    safelist: [
        'cursor-default',
        'cursor-pointer',
        'cursor-move',
        'cursor-ew-resize',
        'cursor-ns-resize',
        'cursor-nesw-resize',
        'cursor-nwse-resize',
    ],

    theme: {
        content: [
            './resources/**/*.blade.php',
            './resources/**/*.js',
            './resources/**/*.vue',
            './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
            './storage/framework/views/*.php',
        ],
        extend: {
            colors: {
                sidebar: {
                    bg: '#272727',
                    text: '#bfbfbf',
                    textbghover: '#2f2f2f',
                    textcolorhover: '#ffffff',
                    border: '#151515'
                },
                dropdown: {
                    bg: '#1d1d1d',
                    text: '#f7f7f7',
                    border: '#3e3e3e',
                    hover: '#323232',
                },
                'brand-white': '#efefef',
                'brand-light-gray': '#686d77',
                'purple-500': '#c800de',
                'purple-600': '#a800b7',
                orange: '#e95420',
                topbar: {
                    grey: '#1d1d1d',
                    white: '#dddddd',
                    button: '#2c2c2c',
                    'button-active': '#424241',
                },
                app: {
                    window: {
                        bg: '#343434',
                        b: '#231E24',
                    },
                    header: {
                        bg: '#2C2C2C',
                        bt: '#3A3A3A',
                        bb: '#231E24',
                        actions: {
                            icon: '#4A4A4A',
                        }
                    },
                },
            },
            minWidth: {
                'viewport-1/4': '25vw',
                'viewport-1/3': '33vw',
                'viewport-1/2': '50vw',
                'viewport-2/3': '66vw',
                'viewport-3/4': '75vw',
                '128': '30rem',
            },
            maxWidth: {
                'viewport-1/4': '25vw',
                'viewport-1/3': '33vw',
                'viewport-1/2': '50vw',
                'viewport-2/3': '66vw',
                'viewport-3/4': '75vw',
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
