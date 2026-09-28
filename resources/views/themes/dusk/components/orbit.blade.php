@props(['bodies' => [], 'top' => null, 'bottom' => null])

{{--
    Hero art: a crescent with one body per series on its own slow ring.
    Decorative; the legend below repeats the names for sighted readers.
    bodies: [['name' => 'HTML', 'meta' => 'Rule 01', 'color' => 'var(--color-moon)'], ...]
--}}
<div aria-hidden="true" {{ $attributes->class(['w-full max-w-[420px] select-none']) }}>
    @if ($top)
        <p class="micro mb-4 text-center">{{ $top }}</p>
    @endif
    <div class="dk-orbit">
        @foreach ($bodies as $body)
            <span class="dk-orbit__ring" style="--inset: {{ $loop->index * 12 }}%; --period: {{ 40 + $loop->index * 18 }}s; rotate: {{ $loop->index * 110 + 20 }}deg">
                <span class="dk-orbit__body" style="--body: {{ $body['color'] }}"></span>
            </span>
        @endforeach
        <span class="dk-orbit__moon"></span>
    </div>
    <ul class="mt-6 flex flex-wrap justify-center gap-x-6 gap-y-2">
        @foreach ($bodies as $body)
            <li class="flex items-center gap-2 text-sm text-body">
                <span class="size-2 rounded-full" style="background: {{ $body['color'] }}"></span>
                {{ $body['name'] }}
                <span class="text-faint">{{ $body['meta'] }}</span>
            </li>
        @endforeach
    </ul>
    @if ($bottom)
        <p class="mt-4 text-center font-serif text-lg text-glow italic">{{ $bottom }}</p>
    @endif
</div>
