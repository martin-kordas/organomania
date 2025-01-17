import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/app-bootstrap.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    css: {
      devSourcemap: true
    },
    build: {
      rollupOptions: {
        external: [
          /*'vis-timeline/standalone',*/
        ]
      }
    }
    /* na serveru odkomentováno */
    /*server: {
        hmr: {
            host: 'organomania.cz'
        },
    }*/
});
