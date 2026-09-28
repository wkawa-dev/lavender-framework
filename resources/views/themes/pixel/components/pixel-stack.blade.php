@props(['rows' => [], 'columns' => 12, 'top' => null, 'bottom' => null, 'highlight' => null])

{{--
    Hero art: a stack of pixel rows, one band per series. Decorative.
    rows: [['name' => 'Prime', 'meta' => 'Senior', 'color' => 'var(--color-prime)', 'height' => 3], ...]
    Cells pop in with a 12ms stagger (CSS only).
--}}
@php($k = 0)
<div aria-hidden="true" {{ $attributes->class(['w-full max-w-[440px] select-none font-mono']) }}>
    @if ($top)
        <p class="micro mb-3">{{ $top }}</p>
    @endif
    <div class="grid gap-3">
        @foreach ($rows as $row)
            @php($dim = $highlight && $highlight !== $row['name'])
            <div class="grid grid-cols-[1fr_auto] items-center gap-4">
                <div class="pixel-grid pixel-grid--pop" style="grid-template-columns: repeat({{ $columns }}, minmax(0, 1fr))">
                    @for ($i = 0; $i < $row['height'] * $columns; $i++)
                        <span style="--k: {{ $k++ }}; background: {{ $dim ? 'var(--color-line)' : $row['color'] }}"></span>
                    @endfor
                </div>
                <div class="w-28 text-right">
                    <p @class(['text-sm uppercase', 'text-neutral-600' => $dim, 'text-fg' => ! $dim])>@if ($highlight === $row['name'])<span class="text-signal">▸ </span>@endif{{ $row['name'] }}</p>
                    <p class="text-[11px] tracking-wider text-faint uppercase">{{ $row['meta'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
    @if ($bottom)
        <p class="mt-3 text-[11px] tracking-widest text-signal uppercase">{{ $bottom }}</p>
    @endif
</div>
