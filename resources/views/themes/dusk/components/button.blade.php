@props(['href' => null, 'variant' => 'primary', 'size' => 'md', 'type' => 'button', 'arrow' => true])

{{--
    Pill button. Link when href is set, <button> otherwise.
    variant: primary (lavender fill + sheen) or ghost (hairline).
--}}
@php
    $classes = ['btn', "btn--{$variant}", 'btn--sm' => $size === 'sm'];
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>
@endif
    <span class="btn__label">{{ $slot }}</span>
    @if ($arrow)
        <svg class="btn__arrow" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" /></svg>
    @endif
@if ($href)
    </a>
@else
    </button>
@endif
