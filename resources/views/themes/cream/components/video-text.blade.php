@props(['text', 'src', 'font' => 'Newsreader, Georgia, serif'])
@php
    // Text clipped to a looping video via an SVG text mask. The mask width is
    // an em-per-character estimate, so unusual glyphs may clip slightly.
    // Reduced motion and no mask support fall back to plain ink text.
    $fs = 100;
    $raw = (string) $text;
    $spaces = substr_count($raw, ' ');
    $letters = max(1, mb_strlen($raw) - $spaces);
    $w = max(60, (int) ceil(($letters * 0.52 + $spaces * 0.22) * $fs));
    $h = (int) ceil($fs * 1.15);
    $svg = "<svg xmlns='http://www.w3.org/2000/svg' width='{$w}' height='{$h}'><text x='0' y='50%' font-family='".e($font)."' font-size='{$fs}' font-weight='400' dominant-baseline='middle' textLength='{$w}' lengthAdjust='spacingAndGlyphs'>".e($text).'</text></svg>';
    $mask = 'url("data:image/svg+xml,'.rawurlencode($svg).'")';
@endphp
<span {{ $attributes->merge(['class' => 'video-text']) }} style='--vt-mask: {!! $mask !!}'>
    <span class="video-text__ghost">{{ $text }}</span>
    <span class="video-text__media" aria-hidden="true">
        <video src="{{ $src }}" autoplay muted loop playsinline preload="metadata"></video>
    </span>
</span>
