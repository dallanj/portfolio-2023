import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

const plugin = require('tailwindcss/plugin');

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      colors: {
        dropdown: {
          bg: '#1d1d1d',
          text: '#f7f7f7',
          border: '#3e3e3e',
          hover: '#323232',
        },
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
      },
      maxWidth: {
          'viewport-1/4': '25vw',
          'viewport-1/3': '33vw',
          'viewport-1/2': '50vw',
          'viewport-2/3': '66vw',
          'viewport-3/4': '75vw',
      }
    },
  },
  plugins: [],
};
