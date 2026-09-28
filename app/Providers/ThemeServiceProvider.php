<?php

namespace App\Providers;

use App\Support\Theme;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Each theme owns its components: <x-cream::button>, <x-pixel::button>.
        foreach (Theme::all() as $name) {
            Blade::anonymousComponentPath(resource_path("views/themes/{$name}/components"), $name);
        }

        Theme::use(null);
    }
}
