<?php

namespace App\Support;

/**
 * Responsive WebP images produced by `php artisan images:optimize`.
 *
 * Sources live in resources/images/{slug}.{jpg,jpeg,png,webp}. The command
 * writes public/images/{slug}-{width}.webp for each configured width (never
 * upscaled) plus public/images/manifest.json, which this class reads so
 * views get srcset and intrinsic width/height without touching the disk.
 */
final class Images
{
    /**
     * @var list<int>
     */
    public const WIDTHS = [640, 960, 1280];

    public const QUALITY = 80;

    /**
     * @var array<string, array{widths: list<int>, width: int, height: int}>|null
     */
    private static ?array $manifest = null;

    public static function manifestPath(): string
    {
        return public_path('images/manifest.json');
    }

    /**
     * @return array<string, array{widths: list<int>, width: int, height: int}>
     */
    public static function manifest(): array
    {
        if (self::$manifest === null) {
            $path = self::manifestPath();
            self::$manifest = is_file($path) ? (json_decode((string) file_get_contents($path), true) ?: []) : [];
        }

        return self::$manifest;
    }

    public static function flush(): void
    {
        self::$manifest = null;
    }

    public static function has(string $slug): bool
    {
        return isset(self::manifest()[$slug]);
    }

    public static function url(string $slug, int $width): string
    {
        $cdn = rtrim((string) config('site.asset_url'), '/');
        $path = "images/{$slug}-{$width}.webp";

        return $cdn !== '' ? "{$cdn}/{$path}" : asset($path);
    }

    /**
     * Default src: the middle width, or the largest available below it.
     */
    public static function src(string $slug): string
    {
        $widths = self::manifest()[$slug]['widths'];
        $pick = array_values(array_filter($widths, fn (int $w) => $w <= 960));

        return self::url($slug, $pick === [] ? $widths[0] : end($pick));
    }

    /**
     * The generated width nearest to $width (for pinned, srcset-free images).
     */
    public static function closest(string $slug, int $width): int
    {
        $widths = self::manifest()[$slug]['widths'];
        usort($widths, fn (int $a, int $b) => abs($a - $width) <=> abs($b - $width));

        return $widths[0];
    }

    public static function srcset(string $slug): string
    {
        return implode(', ', array_map(
            fn (int $w) => self::url($slug, $w)." {$w}w",
            self::manifest()[$slug]['widths'],
        ));
    }

    /**
     * Intrinsic size of the largest variant, for width/height attributes (CLS).
     *
     * @return array{int, int}
     */
    public static function size(string $slug): array
    {
        $entry = self::manifest()[$slug];

        return [$entry['width'], $entry['height']];
    }
}
