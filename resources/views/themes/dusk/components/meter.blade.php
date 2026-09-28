@props(['value', 'max' => 100, 'color' => null, 'label' => null])

{{-- Rounded meter: `value` of `max`. Fills when its [data-reveal] ancestor appears. --}}
@php($ratio = max(0, min(1, $value / $max)))
<span
    role="img"
    aria-label="{{ $label ?? "{$value} of {$max}" }}"
    {{ $attributes->class(['meter'])->merge(['style' => ($color ? "--on: {$color}; " : '')."--value: {$ratio}"]) }}
><span></span></span>
