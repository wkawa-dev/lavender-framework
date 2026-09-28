<?php

namespace App\Support;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use League\HTMLToMarkdown\HtmlConverter;
use Symfony\Component\HttpFoundation\Response;

/**
 * One source of truth for what agents can discover: the public pages
 * (sitemap, llms.txt, MCP list_pages), the skills index, robots.txt policy,
 * and the HTML to markdown rendering behind Accept: text/markdown.
 */
class Agents
{
    /**
     * Indexable pages, in the order agents should read them.
     *
     * @return list<array{route: string, url: string, title: string, description: string}>
     */
    public static function pages(): array
    {
        return array_map(fn (array $page) => [
            'route' => $page[0],
            'url' => route($page[0]),
            'title' => site($page[1].'.seo.title'),
            'description' => site($page[1].'.seo.description'),
        ], [
            ['home', 'home'],
            ['docs.api', 'docs.api'],
            ['contact', 'contact'],
            ['privacy', 'privacy'],
        ]);
    }

    /**
     * Resolve a same-origin path or URL to one of pages(), or null.
     *
     * @return array{route: string, url: string, title: string, description: string}|null
     */
    public static function page(string $path): ?array
    {
        $path = '/'.trim((string) parse_url($path, PHP_URL_PATH), '/');

        foreach (self::pages() as $page) {
            if ('/'.trim((string) parse_url($page['url'], PHP_URL_PATH), '/') === $path) {
                return $page;
            }
        }

        return null;
    }

    /**
     * Render a page to markdown without an HTTP round trip (MCP read_page).
     * Runs the route's action directly, so no session or middleware side effects.
     */
    public static function pageMarkdown(string $routeName): string
    {
        $route = Route::getRoutes()->getByName($routeName);
        $request = Request::create(route($routeName));
        $request->setLaravelSession(app('session')->driver());
        $route->bind($request);
        $request->setRouteResolver(fn () => $route);

        $original = app('request');
        app()->instance('request', $request);

        try {
            $result = $route->run();
            $html = $result instanceof View ? $result->render() : ($result instanceof Response ? (string) $result->getContent() : (string) $result);
        } finally {
            app()->instance('request', $original);
        }

        return self::toMarkdown($html, route($routeName));
    }

    /**
     * Title, description, and <main> as markdown. Navigation, footer, forms,
     * scripts, and decoration are dropped: agents get the content only.
     */
    public static function toMarkdown(string $html, string $url): string
    {
        $title = preg_match('/<title>(.*?)<\/title>/s', $html, $m) ? html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5) : '';
        $description = preg_match('/<meta name="description" content="([^"]*)"/', $html, $m) ? html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5) : '';
        $main = preg_match('/<main\b[^>]*>(.*)<\/main>/s', $html, $m) ? $m[1] : $html;
        // Decorative images (empty alt) carry nothing for a reader.
        $main = (string) preg_replace('/<img\b[^>]*\balt=""[^>]*>/', '', $main);
        // Leaf aria-hidden glyphs (icons, step numbers) likewise. Nested ones stay:
        // a split headline hides its lines but they are the visible text.
        $main = (string) preg_replace('/<([a-z]+)\b[^>]*\baria-hidden="true"[^>]*>[^<]*<\/\1>/', '', $main);

        $converter = new HtmlConverter([
            'strip_tags' => true,
            'remove_nodes' => 'script style svg form button noscript video template dialog',
            'header_style' => 'atx',
            'hard_break' => true,
        ]);

        $body = trim((string) preg_replace("/\n{3,}/", "\n\n", self::tidy($converter->convert($main))));

        return "# {$title}\n\n> {$description}\n\nSource: {$url}\n\n{$body}\n";
    }

    /**
     * Template indentation leaks into the converted text; flatten it outside
     * code fences, keeping nested list markers.
     */
    private static function tidy(string $markdown): string
    {
        $fenced = false;

        return implode("\n", array_map(function (string $line) use (&$fenced) {
            if (str_starts_with(ltrim($line), '```')) {
                $fenced = ! $fenced;
            }

            if ($fenced || preg_match('/^\s+([-*]|\d+\.)\s/', $line)) {
                return rtrim($line);
            }

            return (string) preg_replace('/\s{2,}/', ' ', trim($line));
        }, explode("\n", $markdown)));
    }

    /**
     * Skills in resources/agent-skills/{name}/SKILL.md, frontmatter parsed,
     * placeholders filled. The digest covers the exact bytes we serve.
     *
     * @return array<string, array{name: string, description: string, body: string, url: string, digest: string}>
     */
    public static function skills(): array
    {
        $skills = [];

        foreach (File::glob(resource_path('agent-skills/*/SKILL.md')) as $path) {
            $body = self::fill(File::get($path));
            preg_match('/^---\s*\n(.*?)\n---/s', $body, $front);
            preg_match('/^name:\s*(.+)$/m', $front[1] ?? '', $name);
            preg_match('/^description:\s*(.+)$/m', $front[1] ?? '', $description);
            $slug = trim($name[1] ?? basename(dirname($path)));

            $skills[$slug] = [
                'name' => $slug,
                'description' => trim($description[1] ?? ''),
                'body' => $body,
                'url' => url("/.well-known/agent-skills/{$slug}/SKILL.md"),
                'digest' => 'sha256:'.hash('sha256', $body),
            ];
        }

        return $skills;
    }

    /**
     * Markdown documents with {url} and {brand} placeholders (auth.md, API docs, skills).
     */
    public static function document(string $file): string
    {
        return self::fill(File::get(resource_path("content/{$file}")));
    }

    public static function fill(string $text): string
    {
        return strtr($text, ['{url}' => rtrim(url('/'), '/'), '{brand}' => site('brand')]);
    }

    public static function robots(): string
    {
        if (! config('site.indexable')) {
            return "User-agent: *\nDisallow: /\n";
        }

        $training = config('agents.allow_training');
        $signal = 'Content-Signal: search=yes, ai-input=yes, ai-train='.($training ? 'yes' : 'no');

        $lines = ['User-agent: *', $signal, 'Allow: /', ''];

        foreach (config('agents.crawlers.search') as $bot) {
            array_push($lines, "User-agent: {$bot}", 'Allow: /', '');
        }

        foreach (config('agents.crawlers.training') as $bot) {
            array_push($lines, "User-agent: {$bot}", $training ? 'Allow: /' : 'Disallow: /', '');
        }

        $lines[] = 'Sitemap: '.route('sitemap');

        return implode("\n", $lines)."\n";
    }

    public static function host(): string
    {
        return (string) parse_url(url('/'), PHP_URL_HOST);
    }
}
