const colors = require('tailwindcss/colors')
const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.ts',
        './resources/**/*.vue',
        "./node_modules/flowbite/**/*.js",
    ],
    theme: {
        screens: {
            'sm': '640px',
            // => @media (min-width: 640px) { ... }
            'md': '768px',
            // => @media (min-width: 768px) { ... }
            'lg': '1024px',
            // => @media (min-width: 1024px) { ... }
            'xl': '1280px',
            // => @media (min-width: 1280px) { ... }
            '2xl': '1536px',
            // => @media (min-width: 1536px) { ... }
            'portrait': {'raw': '(orientation: portrait)'},
            // => @media (orientation: portrait) { ... }
            'print': {'raw': 'print'},
            // => @media print { ... }
        },
        fontFamily: {
            'sans': ['Noto Sans TC', ...defaultTheme.fontFamily.sans],
            'serif': ['Noto Serif TC', ...defaultTheme.fontFamily.serif],
            'mono': ['cwTeXFangSong', ...defaultTheme.fontFamily.mono],
        },
    },
    plugins: [
        require('@tailwindcss/typography'),
        require('@tailwindcss/forms'),
        require('@tailwindcss/line-clamp'),
        require('@tailwindcss/aspect-ratio'),
        require('flowbite/plugin'),
    ],
}
