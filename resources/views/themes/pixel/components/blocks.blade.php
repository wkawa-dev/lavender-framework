@props(['value', 'max' => 100, 'cells' => 20, 'cap' => null, 'color' => null, 'label' => null, 'meter' => false])

{{--
    Segmented bar: `value` of `max` lit across `cells` blocks. `cap` shades
    the blocks between value and cap. `meter` uses taller 3px-gap cells.
--}}
@php
    $on = (int) round($value / $max * $cells);
    $capAt = $cap !== null ? (int) round($cap / $max * $cells) : null;
@endphp
<span
    role="img"
    aria-label="{{ $label ?? "{$value} of {$max}" }}"
    {{ $attributes->class(['blocks', 'blocks--meter' => $meter])->merge(['style' => $color ? "--on: {$color}" : null]) }}
>
    @for ($i = 0; $i < $cells; $i++)
        <span @class(['is-on' => $i < $on, 'is-cap' => $i >= $on && $capAt !== null && $i < $capAt])></span>
    @endfor
</span>
