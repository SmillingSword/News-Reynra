import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            ssr: 'resources/js/ssr.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    build: {
        // Optimasi untuk mengurangi penggunaan memory
        chunkSizeWarningLimit: 1000,
        rollupOptions: {
            output: {
                manualChunks: {
                    // Memisahkan chunk untuk mengurangi beban memory
                    vendor: ['vue', 'vue-router', 'axios'],
                    charts: ['chart.js'],
                    editor: ['@tinymce/tinymce-vue'],
                }
            }
        }
    },
    // Optimasi untuk development
    optimizeDeps: {
        include: ['vue', 'vue-router', 'axios']
    }
});
