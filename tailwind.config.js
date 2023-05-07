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
        topbar: {
          grey: '#1d1d1d',
          white: '#dddddd',
          button: '#2c2c2c',
          'button-active': '#424241',
        }
      },
    },
  },
  plugins: [],
};
