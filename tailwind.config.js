/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './Templates/**/*.html.twig',
        './assets/**/*.ts',
        './node_modules/flowbite/**/*.js'
    ],
    theme: {
        extend: {},
    },
    plugins: [
        require('flowbite/plugin')
    ],
}

