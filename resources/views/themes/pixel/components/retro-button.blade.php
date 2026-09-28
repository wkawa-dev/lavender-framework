@props(['href' => null, 'size' => 'md', 'color' => null, 'type' => 'button'])

{{--
    Retro pixel button. Link when href is set, <button> otherwise.
    color: any CSS colour for the slab (defaults to the orange signal).
--}}
@php
    $classes = ['btn-retro', 'btn-retro--sm' => $size === 'sm'];
    $style = $color ? "--pix: {$color}" : null;
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes)->merge(['style' => $style]) }}>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes)->merge(['style' => $style]) }}>
@endif
    <span class="btn-retro__slab" aria-hidden="true">
        <span class="btn-retro__icon">
            <svg viewBox="0 0 16 34" fill="currentColor">
                <rect x="7" y="6" width="5" height="5" />
                <rect x="11" y="10" width="5" height="5" />
                <rect x="15" y="14" width="5" height="5" />
                <rect x="11" y="18" width="5" height="5" />
                <rect x="7" y="22" width="5" height="5" />
            </svg>
        </span>
    </span>
    <span class="btn-retro__label">{{ $slot }}</span>
@if ($href)
    </a>
@else
    </button>
@endif
