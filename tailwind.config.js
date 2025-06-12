/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./*.php",
    "./dashboard/*.php",
    "./includes/*.php"
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Poppins', 'sans-serif'],
      },
    },
  },
  plugins: [],
}