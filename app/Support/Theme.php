<?php

namespace App\Support;

use Illuminate\Support\Facades\View;
use InvalidArgumentException;

final class Theme
{
    /**
     * Per-request preview override (ResolveTheme). Config stays untouched so
     * long-running workers (Octane, FrankenPHP) never leak a preview.
     */
    private static ?string $override = null;

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return array_keys(config('theme.themes'));
    }

    public static function exists(string $name): bool
    {
        return in_array($name, self::all(), true);
    }

    public static function current(): string
    {
        return self::$override ?? (string) config('theme.active');
    }

    /**
     * Point the `theme::` view namespace at a theme. Null returns to the configured one.
     */
    public static function use(?string $name): void
    {
        if ($name !== null && ! self::exists($name)) {
            throw new InvalidArgumentException("Unknown theme [{$name}]. Available: ".implode(', ', self::all()).'.');
        }

        self::$override = $name;
        View::replaceNamespace('theme', resource_path('views/themes/'.self::current()));
        // The finder caches resolved `theme::` paths for the life of the process.
        View::getFinder()->flush();
    }

    /**
     * @return list<string>
     */
    public static function vite(): array
    {
        return config('theme.themes.'.self::current().'.vite');
    }

    /**
     * @return list<string>
     */
    public static function fonts(): array
    {
        return config('theme.themes.'.self::current().'.fonts');
    }

    public static function color(): string
    {
        return config('theme.themes.'.self::current().'.color');
    }

    public static function scheme(): string
    {
        return config('theme.themes.'.self::current().'.scheme');
    }
}
