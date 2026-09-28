@props([
    'headline',
    'steps',
    'eyebrow' => null,
])

<section {{ $attributes->merge(['class' => 'border-b border-line']) }}>
    <div class="wrap py-20">
        @if ($eyebrow)
            <p class="eyebrow">{{ $eyebrow }}</p>
        @endif
        <h2 @class([
            'font-serif text-3xl tracking-tight md:text-4xl',
            'mt-4' => ! empty($eyebrow),
        ])>{{ $headline }}</h2>
        <ol class="mt-16 divide-y divide-line border-t border-line md:mt-10">
            @foreach ($steps as $step)
                <li class="grid gap-4 py-10 md:grid-cols-[6rem_1fr] md:gap-8 md:py-6">
                    <p class="font-serif text-2xl text-muted">{{ $step['number'] }}</p>
                    <div>
                        <h3 class="font-medium">{{ $step['title'] }}</h3>
                        <p class="mt-3 max-w-xl text-sm leading-relaxed text-muted">{{ $step['body'] }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </div>
</section>
