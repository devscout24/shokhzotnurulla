import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import glob from 'fast-glob';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Global
                'resources/sass/app.scss',
                'resources/css/app.css',
                'resources/js/app.js',
                // Frontend
                'resources/css/frontend/app.css',
                'resources/js/frontend/app.js',
                // Auth
                'resources/css/auth/app.css',
                'resources/js/auth/app.js',
                // Admin
                'resources/css/admin/app.css',
                'resources/js/admin/app.js',
                // Dealer
                'resources/css/dealer/app.css',
                'resources/js/dealer/app.js',

                // Page-level files (automatic)
                ...glob.sync('resources/css/**/*.css'),
                ...glob.sync('resources/sass/**/*.scss'),
                ...glob.sync('resources/js/**/*.js'),
            ],
            refresh: true,
        }),
    ],
});