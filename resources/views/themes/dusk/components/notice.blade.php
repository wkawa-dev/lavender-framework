@props(['heading' => null])

<aside {{ $attributes->class(['notice']) }}>
    <svg class="mt-1 size-4 shrink-0 text-glow" aria-hidden="true" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2c.5 5.5 4.5 9.5 10 10-5.5.5-9.5 4.5-10 10-.5-5.5-4.5-9.5-10-10 5.5-.5 9.5-4.5 10-10Z" /></svg>
    <p>@if ($heading)<strong>{{ $heading }}</strong> @endif{{ $slot }}</p>
</aside>
