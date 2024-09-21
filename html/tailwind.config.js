const colors = require('tailwindcss/colors')
const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.ts',
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
        extend: {
            backgroundImage: {
                'game-map': "url('/images/game/map.png')",
                'game-map50': "url('/images/game/map50.png')",
                'game-wheel': "url('/images/game/wheel.png')",
                'game-timer': "url('/images/game/timer.png')",
                'game-sound': "url('/images/game/sound.png')",
            }
        }
    },
    plugins: [
        require('@tailwindcss/typography'),
        require('@tailwindcss/forms'),
        require('@tailwindcss/aspect-ratio'),
        require('flowbite/plugin'),
    ],
}
