import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/forum/blade-tailwind/css/forum.css', 'resources/css/app.css', 'resources/forum/blade-tailwind/js/forum.js', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
