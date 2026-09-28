@props(['slug', 'alt' => '', 'eager' => false, 'sizes' => '(max-width: 768px) 100vw, 960px', 'width' => null])

{{--
    Responsive WebP from `php artisan images:optimize`. Unknown slugs render
    nothing rather than a broken <img>. `eager` is for the LCP image only:
    fetchpriority=high + loading=eager. Everything else lazy-loads.
    `width` pins one variant with no srcset: use it for faded, scaled, or
    blurred backgrounds where DPR-driven upgrades buy nothing but bytes.
--}}
@if (\App\Support\Images::has($slug))
    @php([$intrinsicWidth, $intrinsicHeight] = \App\Support\Images::size($slug))
    <img
        @if ($width)
            src="{{ \App\Support\Images::url($slug, \App\Support\Images::closest($slug, $width)) }}"
        @else
            src="{{ \App\Support\Images::src($slug) }}"
            srcset="{{ \App\Support\Images::srcset($slug) }}"
            sizes="{{ $sizes }}"
        @endif
        width="{{ $intrinsicWidth }}"
        height="{{ $intrinsicHeight }}"
        alt="{{ $alt }}"
        @if ($eager)
            fetchpriority="high"
            loading="eager"
        @else
            loading="lazy"
        @endif
        decoding="async"
        {{ $attributes }}
    >
@endif
