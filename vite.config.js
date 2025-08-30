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
                manualChunks: (id) => {
                    // Hanya apply manual chunks untuk client build, bukan SSR
                    if (process.env.VITE_BUILD_SSR) {
                        return undefined;
                    }
                    
                    if (id.includes('node_modules')) {
                        if (id.includes('vue') || id.includes('@inertiajs/vue3') || id.includes('axios')) {
                            return 'vendor';
                        }
                        if (id.includes('chart.js')) {
                            return 'charts';
                        }
                        if (id.includes('lodash')) {
                            return 'lodash';
                        }
                    }
                }
            }
        }
    },
    // Optimasi untuk development
    optimizeDeps: {
        include: ['vue', '@inertiajs/vue3', 'axios']
    }
});
