import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/tailwind.css',
                'resources/scripts/main.ts',
            ],
            refresh: [
                'resources/**/*.blade.php',
                'resources/**/*.ts',
                "node_modules/flowbite/**/*.js",
            ],
        }),
    ],
})
