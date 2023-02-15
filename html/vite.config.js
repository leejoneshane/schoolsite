import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
	plugins: [
		laravel([
			'resources/css/tailwind.css',
			'resources/scripts/main.ts',
		]),
	],
})
