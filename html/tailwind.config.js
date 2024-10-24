const colors = require('tailwindcss/colors')
const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
    mode: 'jit',
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
                'game-door': "url('/images/game/door.png')",
                'game-arena': "url('/images/game/arena.png')",
                'game-dungeon': "url('/images/game/dungeon.png')",
                'game-map': "url('/images/game/map.png')",
                'game-workshop': "url('/images/game/furniture.png')",
                'game-itemshop': "url('/images/game/item.png')",
                'game-petshop': "url('/images/game/pet.png')",
                'game-book': "url('/images/game/book.png')",
                'game-chest': "url('/images/game/chest.png')",
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
