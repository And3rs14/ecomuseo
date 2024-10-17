import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',  // Esto permite que Vite sea accesible desde fuera del contenedor
        port: 5173,        // El puerto en el que Vite estará sirviendo los assets
        hmr: {
            host: 'localhost',  // Host para el hot module replacement (HMR)
        },
    },
});
