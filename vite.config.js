import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/forum.js', 'resources/css/writer.css', 'resources/js/writer/app.js'],
            refresh: true,
        }),
    ],
});
