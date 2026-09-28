import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { google } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            // One CSS + one JS entry per theme. The layout loads only the active pair.
            input: [
                'resources/css/cream.css',
                'resources/js/cream.js',
                'resources/css/pixel.css',
                'resources/js/pixel.js',
            ],
            refresh: true,
            // Fonts download at build time and ship from /build with hashed names.
            // Use google(), not bunny(): Bunny's CSS lists woff2 + woff in one src,
            // and the plugin splits each into its own @font-face with the same
            // unicode-range. The later (woff) rule wins, so browsers fetch the
            // preloaded woff2 AND a larger woff. Google is queried with a woff2
            // user agent and returns woff2 only.
            // The layout calls @fonts() with the active theme's aliases (config/theme.php),
            // so each theme preloads only its own above-fold faces. Everything else is
            // declared but not preloaded: the browser fetches it on first use.
            fonts: [
                // cream: body 400/500 preloaded; 600 on demand.
                google('Geist', {
                    weights: [400, 500, 600],
                    preload: [{ weight: 400 }, { weight: 500 }],
                }),
                // cream: hero serif 400 preloaded; italic (footer, quotes) on demand.
                google('Newsreader', {
                    weights: [400],
                    styles: ['normal', 'italic'],
                    preload: [{ weight: 400, style: 'normal' }],
                }),
                // pixel: mono body 400 preloaded; 500 on demand.
                google('Geist Mono', {
                    weights: [400, 500],
                    preload: [{ weight: 400 }],
                }),
                // pixel: display face.
                google('Geist Pixel', {
                    weights: [400],
                    preload: [{ weight: 400 }],
                    fallbacks: ['ui-monospace', 'monospace'],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
