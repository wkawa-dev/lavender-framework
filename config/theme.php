<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Active theme
    |--------------------------------------------------------------------------
    |
    | cream: light editorial paper, Geist + Newsreader, ink hairlines.
    | pixel: pitch black, Geist Mono + Geist Pixel, orange signal, stepped motion.
    | dusk:  deep plum night, Instrument Serif + Instrument Sans, lavender glow.
    |
    | Local and testing environments also accept ?theme=pixel to preview the
    | other theme without touching .env (see ResolveTheme middleware).
    |
    */

    'active' => env('THEME', 'cream'),

    /*
    |--------------------------------------------------------------------------
    | Themes
    |--------------------------------------------------------------------------
    |
    | vite:  entry points loaded by the theme layout (one CSS + one JS each).
    | fonts: aliases from vite.config.js passed to @fonts(), so a page only
    |        preloads and inlines @font-face rules for its own theme's faces.
    | color: <meta name="theme-color"> and the manifest background.
    |
    */

    'themes' => [
        'cream' => [
            'vite' => ['resources/css/cream.css', 'resources/js/cream.js'],
            'fonts' => ['geist', 'newsreader'],
            'color' => '#f6f4ef',
            'scheme' => 'light',
        ],
        'pixel' => [
            'vite' => ['resources/css/pixel.css', 'resources/js/pixel.js'],
            'fonts' => ['geist-mono', 'geist-pixel'],
            'color' => '#000000',
            'scheme' => 'dark',
        ],
        'dusk' => [
            'vite' => ['resources/css/dusk.css', 'resources/js/dusk.js'],
            'fonts' => ['instrument-sans', 'instrument-serif'],
            'color' => '#0f0b16',
            'scheme' => 'dark',
        ],
    ],

];
