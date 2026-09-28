<?php

use Illuminate\Support\Arr;

if (! function_exists('site')) {
    /**
     * Read copy from resources/copy.php, with resources/copy/{locale}.php
     * merged over it when present (missing keys fall back to English).
     */
    function site(?string $key = null, mixed $default = null): mixed
    {
        static $cache = [];

        $locale = app()->getLocale();

        $cache[$locale] ??= (function () use ($locale): array {
            $base = require resource_path('copy.php');
            $overlay = resource_path("copy/{$locale}.php");

            return is_file($overlay) ? array_replace_recursive($base, require $overlay) : $base;
        })();

        return $key === null ? $cache[$locale] : Arr::get($cache[$locale], $key, $default ?? $key);
    }
}
