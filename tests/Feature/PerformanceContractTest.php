<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * The loading rules from AGENTS.md as assertions, so a page or component
 * that would cost Lighthouse points fails here before anyone measures.
 * Runs against the real Vite build: `npm run build` first.
 */
class PerformanceContractTest extends TestCase
{
    use RefreshDatabase;

    /** Gzipped bytes a theme may ship before first paint. */
    private const CSS_BUDGET = 12_000;

    private const INITIAL_JS_BUDGET = 25_000;

    private const PRELOADED_FONT_BUDGET = 60_000;

    protected function setUp(): void
    {
        parent::setUp();

        if (! is_file(public_path('build/manifest.json'))) {
            $this->markTestSkipped('Run `npm run build` first; the contract checks real build output.');
        }
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function pages(): array
    {
        return PagesTest::pages();
    }

    /**
     * @return array<string, array{string}>
     */
    public static function themes(): array
    {
        return PagesTest::themes();
    }

    #[DataProvider('pages')]
    public function test_head_keeps_the_critical_path_short(string $theme, string $path): void
    {
        config(['theme.active' => $theme]);
        $html = $this->get($path)->assertOk()->getContent();
        $head = strstr($html, '</head>', true);

        $this->assertSame(1, preg_match_all('/<link rel="stylesheet"/', $head), 'Exactly one render-blocking stylesheet.');
        $this->assertDoesNotMatchRegularExpression('/<script(?![^>]*type="(module|application\/ld\+json)")[^>]*>/', $html, 'No classic (parser-blocking) scripts.');
        $this->assertStringNotContainsString('fonts.googleapis.com', $html);
        $this->assertStringNotContainsString('fonts.bunny.net', $html);

        preg_match_all('/<(?:link|script)\b[^>]*\b(?:href|src)="(https?:\/\/[^"]+)"/', $head, $urls);
        foreach ($urls[1] as $url) {
            $this->assertStringStartsWith(url('/'), $url, "Third-party origin on the critical path: {$url}");
        }

        $fontPreload = strpos($head, 'as="font"');
        $stylesheet = strpos($head, 'rel="stylesheet"');
        $this->assertNotFalse($fontPreload, 'Above-the-fold fonts are preloaded.');
        $this->assertLessThan($stylesheet, $fontPreload, 'Font preloads come before the stylesheet.');
        $this->assertStringContainsString(' fallback"', $head, 'fontaine fallback faces are generated (is fontaine installed?).');

        $imagePreload = strpos($head, 'as="image"');
        if ($imagePreload !== false) {
            $this->assertLessThan($fontPreload, $imagePreload, 'The LCP image preload comes before fonts.');
        }
    }

    #[DataProvider('pages')]
    public function test_images_are_sized_and_only_the_lcp_is_eager(string $theme, string $path): void
    {
        config(['theme.active' => $theme]);
        $html = $this->get($path)->assertOk()->getContent();

        preg_match_all('/<img\b[^>]*>/s', $html, $images);
        $eager = 0;

        foreach ($images[0] as $img) {
            $this->assertMatchesRegularExpression('/\swidth="\d+"/', $img, 'Images carry intrinsic width (no CLS).');
            $this->assertMatchesRegularExpression('/\sheight="\d+"/', $img, 'Images carry intrinsic height (no CLS).');
            $this->assertMatchesRegularExpression('/\salt="/', $img, 'Images have alt text (empty for decoration).');
            $this->assertMatchesRegularExpression('/loading="(lazy|eager)"/', $img);
            $eager += (int) str_contains($img, 'fetchpriority="high"');
        }

        $this->assertLessThanOrEqual(1, $eager, 'At most one high-priority image per page.');
    }

    #[DataProvider('themes')]
    public function test_theme_stays_inside_its_byte_budget(string $theme): void
    {
        $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);

        $css = $this->gzipSize($manifest["resources/css/{$theme}.css"]['file']);
        $this->assertLessThanOrEqual(self::CSS_BUDGET, $css, "{$theme} CSS is {$css} B gzip.");

        $js = array_sum(array_map($this->gzipSize(...), $this->staticChunks($manifest, "resources/js/{$theme}.js")));
        $this->assertLessThanOrEqual(self::INITIAL_JS_BUDGET, $js, "{$theme} initial JS is {$js} B gzip; lazy-load it with import().");

        config(['theme.active' => $theme]);
        preg_match_all('/<link rel="preload" as="font" href="[^"]*\/build\/(assets\/[^"]+)"/', $this->get('/')->getContent(), $fonts);
        $fontBytes = array_sum(array_map(fn ($file) => filesize(public_path("build/{$file}")), $fonts[1]));
        $this->assertLessThanOrEqual(self::PRELOADED_FONT_BUDGET, $fontBytes, "{$theme} preloads {$fontBytes} B of fonts.");
    }

    public function test_built_css_never_imports_remote_fonts(): void
    {
        foreach (glob(public_path('build/assets/*.css')) as $file) {
            $this->assertDoesNotMatchRegularExpression('/@import\s+url\(\s*["\']?https?:/', file_get_contents($file), basename($file));
        }
    }

    private function gzipSize(string $file): int
    {
        return strlen(gzencode(file_get_contents(public_path("build/{$file}")), 9));
    }

    /**
     * Entry chunk plus its static imports: what loads before any dynamic import().
     *
     * @param  array<string, array<string, mixed>>  $manifest
     * @return list<string>
     */
    private function staticChunks(array $manifest, string $key, array $seen = []): array
    {
        if (isset($seen[$key])) {
            return [];
        }

        $seen[$key] = true;
        $files = [$manifest[$key]['file']];

        foreach ($manifest[$key]['imports'] ?? [] as $import) {
            $files = [...$files, ...$this->staticChunks($manifest, $import, $seen)];
        }

        return array_values(array_unique($files));
    }
}
