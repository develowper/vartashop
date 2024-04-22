import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import manifestSRI from 'vite-plugin-manifest-sri';
import legacy from '@vitejs/plugin-legacy'
import ckeditor5 from '@ckeditor/vite-plugin-ckeditor5';
import {VitePWA} from 'vite-plugin-pwa'
import {createRequire} from 'node:module';

const require = createRequire(import.meta.url);

export default defineConfig({
    optimizeDeps: {
        exclude: ['leaflet-bing-layer']
    },
    plugins: [

        // manifestSRI(),
        VitePWA({
            registerType: 'autoUpdate',
            devOptions: {
                enabled: true
            }
        }),
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
        ckeditor5({theme: require.resolve('@ckeditor/ckeditor5-theme-lark')})


        // legacy({
        //     targets: ['since 2011',/*'defaults', 'not IE 11'*/],
        // }),
    ],
    server: {
        host: "localhost",
        port: 3000,
        // cors: false,
        // proxy: {
        //     // string shorthand: http://localhost:5173/foo -> http://localhost:4567/foo
        //     '/foo': 'http://localhost:4567',
        //     // with options: http://localhost:5173/api/bar-> http://jsonplaceholder.typicode.com/bar
        //     '/api': {
        //         target: 'http://jsonplaceholder.typicode.com',
        //         changeOrigin: true,
        //         rewrite: (path) => path.replace(/^\/api/, ''),
        //     },
        //     // with RegEx: http://localhost:5173/fallback/ -> http://jsonplaceholder.typicode.com/
        //     '^/fallback/.*': {
        //         target: 'http://jsonplaceholder.typicode.com',
        //         changeOrigin: true,
        //         rewrite: (path) => path.replace(/^\/fallback/, ''),
        //     },
        //
        //     // Proxying websockets or socket.io: ws://localhost:5173/socket.io -> ws://localhost:5174/socket.io
        //     '/socket.io': {
        //         target: 'ws://localhost:5174',
        //         ws: true,
        //     },
        // },
    },

});
