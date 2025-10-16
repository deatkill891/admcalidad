import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        host: '0.0.0.0', // Resuelve problemas de conexión en bucle
        hmr: {
            host: 'localhost',
        },
    },
    plugins: [
        laravel({
            input: [
                'resources/scss/app.scss', // El único archivo de estilos que necesitamos
                'resources/js/app.js',   // El único archivo de scripts
            ],
            refresh: true,
        }),
    ],
});