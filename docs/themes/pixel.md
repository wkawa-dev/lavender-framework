# pixel

Pitch black, mono body, Geist Pixel display type, one orange signal colour. Motion advances in hard frames (`steps()`) like an 8-bit sprite. Blade + CSS with no runtime motion library.

Visual source of truth: `resources/css/pixel.css` (`@theme`) and `resources/css/pixel/*.css`.

## Tokens

| Token | Value | Use |
| --- | --- | --- |
| `void` | `#000` | Page background |
| `panel` | `#0a0a0a` | Cards, tables, fields |
| `raise` | `#171717` | Buttons, table header |
| `line` / `line-soft` | `#262626` / `#171717` | Hairlines / inner rules |
| `fg` | `#fafafa` | Headlines, values |
| `body` | `#d4d4d4` | Emphasised body |
| `dim` | `#a3a3a3` | Body, nav |
| `faint` | `#808080` | Micro labels, captions (5.2:1; neutral-500 fails on black) |
| `signal` / `signal-strong` | `#fdba74` / `#fb923c` | Eyebrows, focus, active states, the button slab |
| `prime`, `hy`, `frontier` | white, grey, orange | Three-step series for charts, meters, stacks |

## Type

- **Geist Mono** (variable): all body and UI text. Uppercase + `tracking-widest` for labels and nav.
- **Geist Pixel** (400): display only (`.display .display-1|2|3`), brand name, big numbers.
- Micro labels: `micro` utility, 11px uppercase.
- Eyebrows: `.eyebrow` renders `[ LABEL ]`; brackets are CSS and silent to screen readers.

## Layout

- Width: `.wrap` (95%, max 1230px). Sections: `.section` (`py-20`).
- Panels: `.panel`, `.panel--accent` (orange hairline), `.panel--link` (orange on hover). `rounded-lg` hairline on `panel`, no shadows.
- Grids of 3 cards, stat strips (`.stats`), key/value (`.kv`), square-bullet lists (`.ticks`).

## Components (`<x-pixel::…>`)

| Component | Notes |
| --- | --- |
| `retro-button` | Slab + pixel chevron springs across on hover/focus; label flickers. `href` for links, `color` for the slab, `size="sm"` |
| `brand` | Three stacked series bars + pixel name |
| `page-header` | Breadcrumbs, eyebrow, pixel h1, lede |
| `stats`, `blocks` (bar/meter), `pixel-stack`, `sprite` | Data and pixel art, all server-rendered |
| `chart` | Stepped SVG line chart with square markers and a data table |
| `faq` | Numbered native `<details>`; height animates via `::details-content` where supported |
| `notice` | Orange hairline callout with a triangle icon |
| `field` | Micro label + control + error |

Classes: `.badge` (+ `--solid|--inset|--signal`), `.px-table-wrap .px-table`, `.crumbs`, `.chip-nav`, `.link`, `.signal-link`, `.prose-pixel`.

## Motion

- Easing: `--ease-out` `cubic-bezier(0.16, 1, 0.3, 1)`; `--ease-spring` `cubic-bezier(0.34, 1.36, 0.64, 1)` stands in for spring physics.
- Section entrance: rise 16px + blur 8px → sharp, 600ms, once (`data-reveal`). Hero uses `.px-enter` with `@starting-style` and `--enter-delay`.
- Nav underline: four 2px blocks that step in 40ms apart with `steps(1)`.
- Menu: `clip-path` drawn in `steps(7)` over 350ms, closed in `steps(5)`; links slide 12px in `steps(3)`; toggle lines morph in `steps(3)`.
- Pixel art cells pop in (scale 0.4 → 1) with a 12ms per-cell stagger.
- Header hairline fades in over the first 10px of scroll (`animation-timeline: scroll()`).
- Reduced motion: no transforms, blur, or stepped draws; menu fades.

## Do not

- Light mode, gradients, glow, or rounded pills.
- Pixel font for body text; it is display only.
- Colours outside the tokens. The orange is a signal, not a fill: one or two per viewport.
- Text in `neutral-500` or darker on black.
