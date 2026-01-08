import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite'
import i18n from 'laravel-vue-i18n/vite';
import path from 'path';

const modules = [
  'Core',
  'Auth',
  'Dashboard',
  'Cmr',
  'Users',
]

const aliasModules = modules.reduce((acc, module) => {
  acc[`@${module}`] = path.resolve(__dirname, `./Modules/${module}/Resources`);
  return acc;
}, {})

const alias = {
  ...aliasModules,
  'ziggy-js': path.resolve(__dirname, './vendor/tightenco/ziggy'),
}

export default defineConfig({
    plugins: [
        laravel({
            input: [
              'resources/css/app.css',
              'resources/css/website.css',
              'resources/js/app.js'
            ],
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
        tailwindcss(),
        i18n({
          additionalLangPaths: modules.map(module => `Modules/${module}/Lang`)
        }),
    ],
    build: {
        sourcemap: false,
        chunkSizeWarningLimit: 1000,
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        if (id.includes('primevue') || id.includes('primeicons')) {
                            return 'primevue';
                        }
                        if (id.includes('apexcharts') || id.includes('vue3-apexcharts')) {
                            return 'apexcharts';
                        }
                        return 'vendor';
                    }
                }
            }
        }
    },
    resolve: {
        alias: alias,
    },
});
