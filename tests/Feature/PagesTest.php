<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function pages(): array
    {
        $cases = [];

        foreach (['cream', 'pixel', 'dusk'] as $theme) {
            foreach (['/', '/kit', '/contact', '/privacy', '/docs/api'] as $path) {
                $cases["{$theme} {$path}"] = [$theme, $path];
            }
        }

        return $cases;
    }

    #[DataProvider('pages')]
    public function test_every_page_renders_in_every_theme(string $theme, string $path): void
    {
        config(['theme.active' => $theme]);

        $this->get($path)
            ->assertOk()
            ->assertSee('<link rel="canonical"', false)
            ->assertSee('<meta name="description"', false)
            ->assertSee("/themes/{$theme}/favicon.svg", false)
            ->assertSee(site('nav.skip'));
    }

    public function test_home_renders_copy_and_faq_json_ld(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(site('home.hero.lead'))
            ->assertSee('"@type":"FAQPage"', false);
    }

    public function test_query_param_previews_a_theme_for_the_session_locally(): void
    {
        $this->get('/?theme=pixel')->assertSee('px-header', false)->assertSessionHas('theme', 'pixel');
        $this->withSession(['theme' => 'pixel'])->get('/kit')->assertSee('px-header', false);
        $this->withSession(['theme' => 'pixel'])->get('/?theme=cream')->assertSee('site-header', false);
        $this->get('/kit')->assertSee('site-header', false);
    }

    public function test_unknown_theme_query_is_ignored(): void
    {
        $this->get('/?theme=neon')->assertOk()->assertSee('site-header', false);
    }

    public function test_kit_is_noindex(): void
    {
        $this->get('/kit')->assertSee('<meta name="robots" content="noindex">', false);
    }

    public function test_unindexable_site_emits_noindex_and_disallow(): void
    {
        config(['site.indexable' => false]);

        $this->get('/')->assertSee('<meta name="robots" content="noindex, nofollow">', false);
        $this->get('/robots.txt')->assertSee('Disallow: /');
    }

    public function test_robots_and_sitemap(): void
    {
        $this->get('/robots.txt')->assertOk()->assertSee('Sitemap: '.route('sitemap'), false);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee(route('contact'))
            ->assertSee(route('docs.api'))
            ->assertDontSee(route('kit'));
    }

    #[DataProvider('themes')]
    public function test_missing_page_uses_the_theme_404(string $theme): void
    {
        config(['theme.active' => $theme]);

        $this->get('/does-not-exist')->assertNotFound()->assertSee(site('errors.404.headline'));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function themes(): array
    {
        return ['cream' => ['cream'], 'pixel' => ['pixel'], 'dusk' => ['dusk']];
    }
}
