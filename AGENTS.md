# Lavender: agent instructions

Lavender is an opinionated Laravel 13 starter: server-rendered Blade, Tailwind v4, Alpine, Vite. It is built to score 100 on Lighthouse and to be agent-ready by default (see [Agent readiness](#agent-readiness)). It ships three themes on the same routes and copy:

| Theme | Look |
| --- | --- |
| `cream` | Light editorial. Paper, ink, hairlines. Geist + Newsreader. |
| `pixel` | Pitch black, mono body, Geist Pixel display, one orange signal, stepped motion. |
| `dusk` | Deep plum night. Instrument Serif + Instrument Sans, one lavender glow, slow eased motion. |

The performance budget is the product. Served with compression (see [Measuring](#measuring)), every theme scores 100 on every indexable page in all five Lighthouse 13 categories (performance, accessibility, best practices, SEO, agentic browsing), mobile and desktop. Cream's home page can read 97–99 on an unlucky mobile run. Every rule below exists because breaking it cost points. Do not trade one away for convenience without measuring.

## Commands

```sh
composer setup                    # deps, .env, key, migrate, npm install, build
composer dev                      # server + queue + logs + Vite
npm run build                     # after any CSS, JS, or new Tailwind class in Blade
php artisan images:optimize       # resources/images/* → public/images/*.webp + manifest
php artisan agents:dns            # DNS-AID + ai-catalog records to add at your DNS provider
php artisan test                  # needs a build: pages in both themes, perf contract, forms, images
vendor/bin/pint --dirty
```

## Layout

| Path | Owns |
| --- | --- |
| `config/theme.php` | Active theme (`THEME`), per-theme Vite entries, font aliases, theme colour |
| `app/Support/Theme.php` | `theme::` view namespace, per-request preview override |
| `app/Http/Middleware/ResolveTheme.php` | `?theme=pixel` preview, local/testing only, sticks for the session |
| `resources/copy.php` | Every user-facing string. Read with `site('home.hero.lead')` |
| `resources/views/partials/head.blade.php` | The `<head>`. Order is load-bearing, see below |
| `app/Support/Agents.php` | Public page registry (sitemap, llms.txt, MCP), skills, robots.txt, HTML → markdown |
| `config/agents.php` | AI crawler policy, Content Signals, agent token lifetime and scopes |
| `routes/ai.php` | Machine routes: `/mcp`, `/oauth/*`, `/.well-known/*`, `/llms.txt`, `/auth.md`, `/openapi.json` (no session, no CSRF) |
| `app/Mcp/` | The MCP server and its tools (`laravel/mcp`) |
| `resources/agent-skills/{name}/SKILL.md` | Agent skills, published with digests at `/.well-known/agent-skills/index.json` |
| `resources/js/lib/webmcp.js` | The same tools for in-browser agents via `navigator.modelContext` |
| `resources/views/themes/{cream,pixel,dusk}/` | `layouts/`, `components/` (`<x-cream::…>`, `<x-pixel::…>`, `<x-dusk::…>`), `pages/`, `errors/` |
| `resources/views/components/image.blade.php` | `<x-image>`, shared by both themes |
| `resources/css/{cream,pixel,dusk}.css` + folder | One Tailwind entry per theme |
| `resources/js/{cream,pixel,dusk}.js`, `resources/js/lib/` | One JS entry per theme; shared modules in `lib/` |
| `resources/images/` | Image sources (commit them). `public/images/` is generated (commit it too) |
| `docs/themes/*.md` | Visual rules per theme. Read the active one before touching UI |
| `docker/`, `Dockerfile` | FrankenPHP production image with compression and immutable caching |

Rules:

- Pages render `theme::pages.*`. A new page needs a view in **every** theme and a row in `PagesTest::pages()`. If it is public, add it to `Agents::pages()` too: that one list feeds the sitemap, llms.txt, and the MCP `list_pages` / `read_page` tools.
- Copy lives in `resources/copy.php`, never in Blade. Both themes read the same keys.
- A theme's components never reach into the other theme. Shared, class-free markup (like `<x-image>`) goes in `resources/views/components/`.
- `/kit` is the component gallery for the active theme. Add every new component there. It is `noindex` and out of the sitemap.

## Loading playbook

This is how every theme reaches 100. Follow it for every page.

### Fonts

1. **Self-host at build time.** Fonts are declared in `vite.config.js` (`fonts: [...]`) through `laravel-vite-plugin/fonts`. Vite downloads them, fingerprints them into `public/build/assets`, and writes `public/build/fonts-manifest.json`. At runtime there is no request to Google, Bunny, or any other origin, so nothing to preconnect to and nothing third-party on the critical path.
2. **Never `@import url(https://fonts.googleapis.com/...)` in CSS.** That is what the original pixel site did: HTML → CSS → Google CSS → font file is a three-hop render-blocking chain on a second origin. It was the largest single loss in its Lighthouse score.
3. **Use `google()`, not `bunny()`.** Bunny's CSS lists woff2 and woff in one `src`. The plugin splits each into its own `@font-face` with the same `unicode-range`, and the later (woff) rule wins. Browsers then download the preloaded woff2 **and** a bigger woff. Google is queried with a woff2 user agent and returns woff2 only, and often a single variable file (Geist 400–600 is one 29 KB file instead of three).
4. **Preload only above-the-fold faces.** `preload: [{ weight: 400 }]` per family. Everything else is declared and loads on first use. Cream preloads Geist (variable) and Newsreader 400 normal; the italic loads later for the footer. Pixel preloads Geist Mono and Geist Pixel. Dusk preloads Instrument Sans 400 and Instrument Serif 400 normal.
5. **Load only the active theme's faces.** The head calls `@fonts(\App\Support\Theme::fonts())` with the aliases from `config/theme.php`. `@fonts` emits `<link rel="preload">` for those aliases plus an inline `<style>` with their `@font-face` rules. The inline style means the font URLs are known before the main CSS arrives. Adding a family: add it in `vite.config.js`, then add its alias (slug of the family name, e.g. `geist-mono`) to the theme in `config/theme.php`.
6. **Keep `fontaine` installed.** It is an optional peer of the plugin; without it no fallback faces are generated and nothing warns loudly. With it, `@fonts` emits `"<Family> fallback"` faces (a local font with `size-adjust` and ascent/descent overrides) so the `font-display: swap` swap does not shift layout.
7. **Reference the fallback in the theme stack.** `@theme { --font-sans: 'Geist', 'Geist fallback', ui-sans-serif, ... }`. A stack without the fallback name gets no benefit from item 6.

### The `<head>` and the network dependency tree

Order in `partials/head.blade.php`:

1. meta, title, description, canonical, robots, OG
2. `@stack('preload')`: the page's LCP image preload, **before** fonts so it wins bandwidth
3. `@fonts(...)`: font preloads + inline `@font-face`
4. `@vite(...)`: one stylesheet (the only render-blocking request) plus the module script and `modulepreload` for its chunks

Putting `@vite` first was measured and scored worse; keep this order.

Targets: one render-blocking CSS file, no third-party origin, no request chain longer than HTML → asset. Module scripts are deferred by definition; never add a classic `<script>` in the head.

### CSS

- One entry per theme: `@import 'tailwindcss' source(none);` then explicit `@source` lines for that theme's views. Without `source(none)` Tailwind scans the whole repo and each theme ships the other's classes.
- Budget: ~10 KB gzip per theme. Check the `npm run build` output.
- No `@tailwindcss/typography`. It added ~14 KB raw for one privacy page. Themes hand-write `.prose-cream` / `.prose-pixel` (~1 KB).
- Do not `@source` vendor pagination views unless you render pagination. It pulls in `dark:` variants.
- Motion is CSS: transitions, `@starting-style`, keyframes, `animation-timeline: view()` / `scroll()`, and `steps()` for the pixel look. Animate `transform`, `opacity`, `clip-path`, `filter`. Never `transition: all`. Every effect has a `prefers-reduced-motion: reduce` branch in the theme's `motion.css`.

### JavaScript

- Alpine for small state only (nav, toggles). It is the ~20 KB gzip shared chunk; do not add a second framework.
- Content is server-rendered. JS enhances; with JS off every word and link is still there.
- Heavy libraries load on demand. The cream scroll-stack (GSAP + ScrollTrigger, ~44 KB gzip) is a dynamic `import()` triggered by an `IntersectionObserver` with `rootMargin: 800px`, with a `requestIdleCallback` (4 s timeout) fallback, and skipped entirely under reduced motion (`lib/lazy-scroll-stack.js`). Copy that pattern for any library over ~5 KB.
- No runtime animation library for things CSS can do. The pixel retro button, stepped menu, pixel underline, FAQ accordion, and pixel-art pop-in are all CSS.

### Reveals and LCP

- `[data-reveal]` (`lib/reveal.js`) only hides elements that start **below** the fold (`reveal-armed`). Above-the-fold content is never hidden, so it can be the LCP at first paint.
- Where `animation-timeline: view()` is supported, reveals are pure CSS and JS only marks on-screen nodes visible.
- Hero entrances use `@starting-style` (`.hero-enter`, `.px-enter`) or a CSS keyframe that starts at paint. Never gate hero content on JS.
- Headless tools that grow the viewport (full-page screenshots) can capture unrevealed below-fold sections as blank. That is a capture artifact; check with a normal viewport.

### Images

- Put originals in `resources/images/{slug}.{jpg,png,webp}` and run `php artisan images:optimize`. It writes `public/images/{slug}-{640,960,1280}.webp` at q80 (never upscaled), skips unchanged sources by SHA-256, deletes variants whose source is gone, and records widths plus intrinsic size in `public/images/manifest.json`.
- Render with `<x-image slug="..." alt="..." />`. It emits `srcset`/`sizes`, intrinsic `width`/`height` (no CLS), `loading="lazy"`, and `decoding="async"`. Unknown slugs render nothing.
- The LCP image, and only that one, gets `:eager="true"` (`fetchpriority="high"`, `loading="eager"`) and a preload pushed from the page:
  ```blade
  @push('preload')
      <link rel="preload" as="image" href="{{ \App\Support\Images::url('hero-backdrop', 640) }}" type="image/webp" fetchpriority="high">
  @endpush
  ```
- Decorative backgrounds (faded, scaled, blurred) use `:width="640"` to pin one small variant with no `srcset`. A 3× scaled image at 45% opacity gains nothing from a DPR upgrade. The cream hero LCP dropped from 960w to 640w this way.
- `ASSET_CDN_URL` points image URLs at a CDN (e.g. an R2 custom domain) without template changes.

### Server

- Compression is mandatory. `docker/Caddyfile` runs `encode zstd gzip`: cream CSS goes from ~46 KB to ~10 KB. Without it, render-blocking CSS alone costs several points.
- `Cache-Control: public, max-age=31536000, immutable` for `/build`, `/images`, `/video`, `/themes`, `/favicon.ico`. Build files are fingerprinted; image variants change name when you rename the source.
- The entrypoint runs `config:cache`, `route:cache`, `view:cache`, then migrations. Because config is cached, read env only in `config/*.php`. `resources/copy.php` uses `config('app.name')`, not `env()`.

### Accessibility and SEO checks Lighthouse enforces

- Contrast: pixel's `--color-faint` (#808080) replaces neutral-500, which is 4.4:1 on black and fails. Cream `text-muted/60` fails; use `text-muted`. Decorative giant text (pixel footer wordmark) is CSS `content` with empty alt text, so it is out of the accessibility tree and the contrast audit.
- An `aria-label` must contain the visible text (`label-content-name-mismatch`). Brand links show the brand name and carry no `aria-label`.
- Bracketed eyebrows (`[ LABEL ]`) draw the brackets with `content: '[ ' / ''` so screen readers skip them.
- Every page passes `title` and `description` from `copy.php`. Canonical, OG, `theme-color`, and `color-scheme` come from the head partial. `/robots.txt` and `/sitemap.xml` are routes; `SITE_INDEXABLE=false` switches the site to `noindex` + `Disallow: /`.
- FAQ blocks are native `<details>` so answers stay in the DOM and match the `FAQPage` JSON-LD.

### The contract test

`tests/Feature/PerformanceContractTest.php` turns the rules above into assertions for every page in `PagesTest::pages()`, in every theme, against the real build: one stylesheet, no classic scripts, no third-party origin in `<head>`, preload order (LCP image → fonts → CSS), fontaine fallbacks present, sized images with at most one high-priority, and gzip budgets per theme (CSS 12 KB, initial JS 25 KB, preloaded fonts 60 KB). When it fails, fix the cause rather than raising the budget. Raise a budget only after measuring that the page still scores 100, and say so in the commit.

## Agent readiness

Lavender passes Cloudflare's [isitagentready.com](https://isitagentready.com/) checks out of the box. The only one it can't pass from code is DNS-AID, because that lives in DNS. Every advertised URL is a real, tested route; nothing points at a placeholder. `tests/Feature/AgentReadinessTest.php` asserts each check.

| Check | How Lavender passes it | Where |
| --- | --- | --- |
| robots.txt, sitemap | Routes, not static files, so they follow `SITE_INDEXABLE` and `APP_URL` | `routes/web.php`, `Agents::robots()` |
| AI bot rules | Named `User-agent` groups: search and user fetchers allowed, training crawlers disallowed unless `AGENTS_ALLOW_TRAINING=true` | `config/agents.php` |
| Content Signals | `Content-Signal: search=yes, ai-input=yes, ai-train=no` under `User-agent: *` | `Agents::robots()` |
| Link headers (RFC 8288) | `api-catalog`, `service-desc`, `service-doc`, `describedby`, `status` on every page | `AdvertiseAgentResources` middleware |
| Markdown for Agents | `Accept: text/markdown` returns the page's `<main>` as markdown with `X-Markdown-Tokens`; `Vary: Accept` on every HTML response | `NegotiateMarkdown` middleware |
| API catalog (RFC 9727) | `application/linkset+json` with anchors for the site and `/mcp` | `/.well-known/api-catalog` |
| OAuth discovery (RFC 8414) + protected resource (RFC 9728) | Same-origin issuer; `client_credentials` for agents; opaque Sanctum tokens, so an empty JWKS | `OAuthController`, `/.well-known/oauth-*` |
| auth.md | `/auth.md` with the registration flow, and an `agent_auth` block (anonymous identity) in the authorization server metadata | `resources/content/auth.md` |
| MCP server card | Generated from `SiteServer`'s tools, so it can't drift | `/.well-known/mcp/server-card.json` |
| Agent Skills index (v0.2.0) | One folder per skill; digests are computed over the exact bytes served | `resources/agent-skills/` |
| WebMCP | `list_pages`, `read_page`, `open_page`, `send_inquiry` registered with `navigator.modelContext` on page load; contact forms carry declarative `toolname`/`tooldescription` (the honeypot says to stay empty) | `resources/js/lib/webmcp.js`, `pages/contact.blade.php` |
| ARD | `/.well-known/ai-catalog.json` with `urn:air:` identifiers, ARD media types only (`application/mcp-server-card+json`, `text/markdown; profile="urn:air:agent-skills"`), and 2 to 5 representative queries per entry, plus `<link rel="ai-catalog">` | `AgentDiscoveryController::aiCatalog` |
| Lighthouse 13 Agentic Browsing | Accessibility tree, CLS, llms.txt, ai-catalog schema, WebMCP tools; gated at 100 by `scripts/lighthouse.mjs` | |
| DNS-AID | DNS, not HTTP. `php artisan agents:dns` prints the records; publish them and sign the zone with DNSSEC | `app/Console/Commands/AgentsDns.php` |

How agents write: `POST /oauth/register` (RFC 7591) returns client credentials; `POST /oauth/token` with `grant_type=client_credentials` returns a bearer token that expires after `AGENTS_TOKEN_TTL` seconds; the MCP tool `send_inquiry` checks `tokenCan('inquiries:write')`; `POST /oauth/revoke` (RFC 7009) revokes it. The authorization endpoint exists only to return `unsupported_response_type`, because there is no user login.

Rules:

- Adding an MCP tool: `php artisan make:mcp-tool`, give it `#[Name('snake_case')]`, register it in `SiteServer::$tools`, and mirror it in `webmcp.js` if browsers should have it. The server card updates itself. Read-only tools get `#[IsReadOnly]`. Write tools check a scope from `config('agents.scopes')`.
- Adding a skill: create `resources/agent-skills/{name}/SKILL.md` with `name` and `description` frontmatter, and add 2 to 5 queries under `agents.queries.skills.{name}` in `copy.php`. `{url}` and `{brand}` are filled in when the file is served.
- Machine routes go in `routes/ai.php`. They have no session and no CSRF, so they must not trust cookies, and every write needs a token and a named rate limiter (`agents`, `agent-register`).
- Keep markdown clean: content that should reach agents belongs in `<main>`. Decorative glyphs get `aria-hidden="true"` and decorative images `alt=""`; the converter drops both.
- The WebMCP module runs only where `navigator.modelContext` exists and adds about 1 KB to the theme's JavaScript; the contract test's JS budget still applies.

## Mobile navigation

Both themes use `lib/nav.js` on a native `<dialog>` (`showModal()` gives inert background and focus trapping). Contract: `x-data="siteNav"` on the header; refs `open`, `close`, `dialog`, `panel`; the panel animates while `[data-open]` is set on the dialog; `[data-nav-desktop-focus]` receives focus when the viewport crosses the desktop breakpoint.

- Scroll lock pins `<body>` with `position: fixed` and restores the exact offset with `scroll-behavior` forced to `auto`. Do not switch to `overflow: hidden`; it breaks sticky headers on iOS Safari.
- Close waits for the panel's `clip-path` transition with a timer fallback, then calls `finishClose()` directly. Do not rely on the dialog's native `close` event alone: background tabs may never deliver it and the page stays scroll-locked.
- Verify on a 375px viewport: open, Escape, scroll position unchanged, focus back on the toggle.

## Measuring

`php artisan serve` has no compression and one worker, so Lighthouse under-reports it. For a production-like local run use the Docker image, or at least run the PHP server with workers:

```sh
npm run build
docker build -t lavender .
docker run --rm -p 8080:8080 -e APP_KEY="base64:$(openssl rand -base64 32)" -e THEME=cream \
  -e DB_CONNECTION=sqlite -e DB_DATABASE=/app/database/database.sqlite lavender
npx lighthouse@13 http://localhost:8080/ --preset=desktop --view
npx lighthouse@13 http://localhost:8080/ --view     # mobile (default)
```

The same run, scripted and gated: `node scripts/lighthouse.mjs http://localhost:8080` (three mobile runs per page; set `CHROME_PATH` if Chrome is not installed). CI has it as the manual `lighthouse` workflow. Use Lighthouse 13 or newer: older versions flag the `Content-Signal` line in robots.txt as an unknown directive (SEO 92) and have no Agentic Browsing category.

Run mobile at least three times; simulated throttling varies by a point or two. Check `/`, `/contact`, and `/privacy` in both themes (`/kit` is `noindex`, so its SEO score is expected to be low). Any score under 100 on a page you touched is a regression to fix before merging.

## Adding a theme

1. Add it to `config/theme.php` (`vite`, `fonts`, `color`, `scheme`).
2. Add `resources/css/{name}.css` with `source(none)` and `@source` for its views, and `resources/js/{name}.js`.
3. Add both entries to `vite.config.js` `input`, and any new font families to `fonts`.
4. Create `resources/views/themes/{name}/` with `layouts/app.blade.php`, `components/`, `pages/{home,kit,contact,privacy}.blade.php`, `errors/404.blade.php`.
5. Add `public/themes/{name}/favicon.svg` and `docs/themes/{name}.md`.
6. Add the theme to the data providers in `tests/Feature/PagesTest.php`, build, test, and measure.
