@props([
    'eyebrow' => null,
    'headline',
    'lead' => null,
    'headlineClass' => 'max-w-3xl',
    'image' => null,
])

<section @class([
    'hero' => $image,
    'border-b border-line' => ! $image,
])>
    @if ($image)
        <div class="hero-media" aria-hidden="true">
            <x-image :slug="$image" :eager="true" sizes="100vw" />
        </div>
        <div class="hero-fade-x" aria-hidden="true"></div>
        <div class="hero-fade-bottom" aria-hidden="true"></div>
    @endif

    <div @class([
        'wrap page-hero',
        'relative' => $image,
    ])>
        @if (! empty($eyebrow))
            <p class="eyebrow hero-enter">{{ $eyebrow }}</p>
        @endif
        <h1 @class([
            'hero-enter',
            'hero-enter--2' => ! empty($eyebrow),
            'mt-6' => ! empty($eyebrow),
            $headlineClass,
            'text-ink' => $image,
        ])>{{ $headline }}</h1>

        @if ($lead)
            <p @class([
                'hero-enter mt-8 max-w-xl text-lg leading-relaxed text-muted min-h-[3lh]',
                'hero-enter--2' => empty($eyebrow),
                'hero-enter--3' => ! empty($eyebrow),
            ])>{{ $lead }}</p>
        @endif

        @isset($actions)
            {{ $actions }}
        @endisset

        {{ $slot }}
    </div>
</section>
