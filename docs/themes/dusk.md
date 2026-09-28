# dusk

Deep plum night, Instrument Serif headlines, Instrument Sans body, one lavender glow. Motion is slow and eased: a sky that drifts, bodies on slow orbits, sections that rise into place. Blade + CSS with no runtime motion library.

Visual source of truth: `resources/css/dusk.css` (`@theme`) and `resources/css/dusk/*.css`.

## Tokens

| Token | Value | Use |
| --- | --- | --- |
| `night` | `#0f0b16` | Page background |
| `panel` | `#17111f` | Cards, tables, FAQ |
| `raise` | `#221a2e` | Current nav chip, inset badges, code |
| `line` / `line-soft` | `#2f2540` / `#1f1829` | Hairlines / inner rules, meter track |
| `fg` | `#f3effa` | Headlines, values |
| `body` | `#d6cfe3` | Emphasised body |
| `dim` | `#aca3bd` | Body, nav |
| `faint` | `#8f86a3` | Small labels, captions (4.9:1 on `raise`, the darkest pairing) |
| `glow` / `glow-strong` | `#c4b5fd` / `#a78bfa` | Eyebrows, focus, links on hover, the primary button |
| `moon`, `lilac`, `rose` | near-white, violet, pink | Three-step series for charts, meters, the orbit |

## Type

- **Instrument Sans** (400 preloaded, 500 on demand): all body and UI text. Sentence case; dusk never uses uppercase labels.
- **Instrument Serif** (400 preloaded, italic on demand): display (`.display .display-1|2|3`), brand name, stat values, step kickers.
- `.glow-text`: italic lavender → rose gradient for a phrase inside display type. One per viewport.
- Small labels: `micro` utility, 12px medium.
- Eyebrows: `.eyebrow` renders `✦ Label`; the star is CSS and silent to screen readers.

## Layout

- Width: `.wrap` (92%, max 1180px). Sections: `.section` (`py-24`).
- Cards: `.card` (16px radius, hairline, plum gradient), `.card--glow` (lavender hairline and a soft shadow, for the one thing that matters), `.card--link` (lifts 3px on hover).
- Stat strips (`.stats`), key/value (`.kv`), star-bullet lists (`.stars`).

## Components (`<x-dusk::…>`)

| Component | Notes |
| --- | --- |
| `button` | Pill. `variant="primary"` (lavender fill, sheen sweeps across on hover/focus) or `ghost`; `size="sm"`; `:arrow="false"`; `href` for links |
| `brand` | Crescent mark + serif name; the moon tilts on hover |
| `page-header` | Breadcrumbs, eyebrow, serif h1, lede |
| `stats`, `meter` | Serif values over labels; rounded gradient meter that fills when its `[data-reveal]` parent appears |
| `orbit` | Hero art: a crescent with one body per series on a slow ring, plus a legend. Decorative |
| `chart` | Smooth area chart (SVG) with round end markers and a data table |
| `faq` | Card-per-question native `<details>`; plus folds into minus; height animates via `::details-content` |
| `notice` | Lavender hairline callout with a star icon |
| `field` | Label + control + error |

Classes: `.badge` (+ `--solid|--inset|--glow`), `.dk-table-wrap .dk-table`, `.crumbs`, `.chip-nav`, `.link`, `.glow-link`, `.prose-dusk`, `.dk-aurora` (three drifting light sources behind a section; needs a `relative isolate` parent).

## Motion

- Easing: `--ease-out` `cubic-bezier(0.16, 1, 0.3, 1)`; `--ease-in-out` `cubic-bezier(0.65, 0, 0.35, 1)` for loops and the menu close.
- Section entrance: rise 24px + fade, 900ms, once (`data-reveal`). Hero uses `.dk-enter` with `@starting-style` and `--enter-delay`.
- Sky: three radial gradients drift on 26–38s alternating loops (transform only). Never the LCP; it is a gradient, not an image.
- Orbit: rings rotate over 40–76s, alternating direction.
- Menu: a `clip-path` circle grows out of the toggle in 550ms and closes back into it in 350ms; links rise 14px with a 50ms stagger.
- Header frosts over the first 24px of scroll (`animation-timeline: scroll()`).
- Reduced motion: no drift, orbit, transforms, or clip-path; the menu fades.

## Do not

- Light mode, pure black, or neutral greys. Every surface is tinted plum.
- Uppercase labels or wide tracking. That is pixel's voice.
- More than one `.glow-text` or `.card--glow` per viewport. The glow is a highlight, not a fill.
- `filter: blur()` on large layers. Soft light comes from radial gradients, which do not repaint.
- Text in `line` or `raise` colours on `night`. Use `faint` or lighter.
