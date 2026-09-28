# cream

Light editorial product site, closer to a software product page than a brochure. Paper, type, and thin rules. No neon, no stock photos, no heavy cards, no gradients as decoration, no dark mode.

Visual source of truth: `resources/css/cream.css` (`@theme`) and `resources/css/cream/*.css`.

## Tokens

| Token | Value | Use |
| --- | --- | --- |
| `paper` | `#f6f4ef` | Page background, inputs |
| `ink` | `#171614` | Body, headlines, primary buttons |
| `muted` | `#6f675e` | Leads, captions, nav (5:1 on paper; never below this) |
| `line` | `#e4dfd6` | Hairline borders, rules, splits |
| `accent` | `#1f3a8a` | Ink-blue links in prose only |

Primary actions are ink on paper, not blue pills.

## Type

- **Geist** (400/500/600, one variable file): UI, body, labels, buttons.
- **Newsreader** (400, italic available): page titles, section headlines, step numbers, pull lines. Never bold.
- Headlines: `font-serif`, tight tracking, line-height ~1.08. Body stays sans, `leading-relaxed` on long lines.

## Layout

- Width: `.wrap` (`max-w-6xl`, `px-6` / `lg:px-8`).
- Sections stack with `border-b border-line` (`.section`). No drop shadows.
- Hero: `.page-hero`, large vertical padding. Interior sections: `.section-pad` (`py-20` / `md:py-28`).
- Lists of notes or steps: hairline `divide-y`, not cards (`<x-cream::list-rows>`, `<x-cream::steps-section>`).

## Components (`<x-cream::…>`)

| Component | Notes |
| --- | --- |
| `page-hero` | Eyebrow, serif h1, lead, `actions` slot, optional background `image` slug |
| `proof` | Stacked figure rows, swash highlighter, count-up. Offsets exist for 3 rows |
| `scroll-stack` | Sticky card handoff (GSAP, lazy). Static stack without JS or with reduced motion. Last card is ink |
| `steps-section`, `list-rows`, `faq`, `quote-section` | Hairline lists, native `<details>`, centred italic quote |
| `field` | Label + control + server error |
| `video-text` | Word clipped to a looping video through an SVG text mask |
| `cookie-banner` | Behind `SITE_CONSENT_BANNER` |
| `wordmark` | Typeset brand; swap for an inline SVG with `fill="currentColor"` |

Classes: `.btn .btn-primary|.btn-secondary .btn-lg`, `.field`, `.label`, `.eyebrow`, `.link`, `.nav-link .nav-underline`, `.hero-headline`, `.how-heading`, `.prose-cream`.

Success states after forms: the same hairline box, serif title, muted body. No green banners.

## Motion

CSS only, except the scroll-stack (GSAP ScrollTrigger, loaded on approach). No scroll-jacking, no marquees, no pinning elsewhere.

| Token | Value | Use |
| --- | --- | --- |
| `--ease-out` | `cubic-bezier(0.23, 1, 0.32, 1)` | Entrances, presses, hover underlines |
| `--ease-in-out` | `cubic-bezier(0.77, 0, 0.175, 1)` | On-screen movement, clip-path reveals |

- Button press: `scale(0.97)` at 160ms.
- Hero: headline lines rise from a clip (700ms, 120ms stagger); strikethrough draws at 950ms; lead and CTAs use `@starting-style`.
- Reveals once: `data-reveal` (rise), `data-reveal="clip"` (footer statement), `data-reveal="line"` (footer rule).
- Header lock: a 1px ink rule driven by `animation-timeline: scroll()`. Never animate header height.
- Mobile nav: `clip-path` curtain, 380ms in / 220ms out, rows stagger ≤130ms. Two-line toggle morphs to X in 200ms.
- Hover underline and 2px row nudge only under `(hover: hover) and (pointer: fine)`.
- Reduced motion keeps colour and opacity, drops transform and clip-path, shows final count-up numbers and drawn swashes.

## Do not

- Dark theme, glassmorphism, glow, particle backgrounds.
- `rounded-xl` cards, coloured left borders, icon-in-circle feature grids.
- Stock photography or illustrations beyond faded abstract backdrops.
- Inter or system-only type as the brand face.
- Marketing sentences in Blade; they belong in `resources/copy.php`.
