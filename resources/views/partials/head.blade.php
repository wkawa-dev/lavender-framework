{{--
    Shared <head>. Order matters for the network dependency tree:
    meta → LCP image preload → @fonts (preloads for above-fold faces +
    inline @font-face, so font requests start in parallel with CSS, not
    after it) → @vite (one CSS, one deferred module script).
    Moving @vite first was measured and scored worse; keep this order.
    No third-party origins: nothing to preconnect to.
--}}
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="theme-color" content="{{ \App\Support\Theme::color() }}">
<meta name="color-scheme" content="{{ \App\Support\Theme::scheme() }}">

<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
<link rel="canonical" href="{{ url()->current() }}">
@if (! config('site.indexable') || isset($robots))
    <meta name="robots" content="{{ config('site.indexable') ? $robots : 'noindex, nofollow' }}">
@endif

<meta property="og:site_name" content="{{ site('brand') }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:type" content="website">
<meta name="twitter:card" content="{{ config('site.og_image') ? 'summary_large_image' : 'summary' }}">
@if (config('site.og_image'))
    <meta property="og:image" content="{{ config('site.og_image') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
@endif

<link rel="icon" href="/themes/{{ \App\Support\Theme::current() }}/favicon.svg" type="image/svg+xml">
<link rel="icon" href="/favicon.ico" sizes="32x32">
{{-- Agent discovery (ARD); robots.txt and the Link header cover the rest. --}}
<link rel="ai-catalog" href="/.well-known/ai-catalog.json">

@isset($jsonLd)
    <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endisset

{{-- LCP image preload (pages @push('preload')) goes ahead of the fonts so it wins bandwidth. --}}
@stack('preload')
@fonts(\App\Support\Theme::fonts())
@vite(\App\Support\Theme::vite())
