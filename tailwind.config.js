/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        'color-1' : '#E6E6E6',
        'color-2' : '#6D6D6D',
        'color-3' : '#4A4A4A',
        'color-4' : '#1C1C1C',
        'color-5' : '#010101'
      }
    },
  },
  plugins: [require('tailwindcss-font-inter')],
}

