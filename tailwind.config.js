/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      colors: {
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
        }
      },
    },
  },
  plugins: [],
};
