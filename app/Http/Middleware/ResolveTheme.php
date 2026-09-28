<?php

namespace App\Http\Middleware;

use App\Support\Theme;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTheme
{
    /**
     * Preview another theme with ?theme=pixel; the choice sticks for the
     * session so links keep it (?theme=cream switches back). Local and
     * testing only, so production never serves two looks for one URL.
     */
    public function handle(Request $request, Closure $next): Response
    {
        Theme::use(null);

        if (! app()->environment('local', 'testing')) {
            return $next($request);
        }

        $query = $request->query('theme');

        if (is_string($query) && Theme::exists($query)) {
            $request->session()->put('theme', $query);
        }

        $theme = $request->session()->get('theme');

        if (is_string($theme) && Theme::exists($theme)) {
            Theme::use($theme);
        }

        return $next($request);
    }
}
