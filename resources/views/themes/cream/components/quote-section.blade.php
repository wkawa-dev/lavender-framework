@props([
    'quote',
    'name' => null,
    'role' => null,
    'photo' => null,
])

<section {{ $attributes->merge(['class' => 'border-b border-line']) }}>
    <div class="wrap py-20 md:py-28">
        <figure class="mx-auto max-w-2xl text-center">
            <blockquote class="font-serif text-2xl italic leading-snug tracking-tight md:text-3xl">“{{ $quote }}”</blockquote>
            @if ($name || $photo)
                <figcaption class="mt-8 text-sm">
                    @if ($photo)
                        <img src="{{ $photo }}" alt="{{ $name ?? '' }}" loading="lazy" class="mx-auto size-10 rounded-full object-cover" />
                    @endif
                    @if ($name)
                        <p @class(['mt-4' => $photo])>
                            <span class="font-medium text-ink">{{ $name }}</span>
                            @if ($role)
                                <span class="text-muted"> · {{ $role }}</span>
                            @endif
                        </p>
                    @endif
                </figcaption>
            @endif
        </figure>
    </div>
</section>
