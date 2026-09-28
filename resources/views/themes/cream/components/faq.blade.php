@props(['items' => [], 'headline' => null, 'id' => 'faq'])

{{-- Native <details>: answers stay in the DOM for crawlers and FAQPage JSON-LD, no JS. --}}
<section {{ $attributes->merge(['class' => 'section']) }} @if ($headline) aria-labelledby="{{ $id }}-heading" @endif>
    <div class="wrap section-pad lg:grid lg:grid-cols-12 lg:gap-10">
        @if ($headline)
            <h2 id="{{ $id }}-heading" class="how-heading lg:col-span-4">{{ $headline }}</h2>
        @endif
        <div @class(['divide-y divide-line border-y border-line', 'mt-12 lg:col-span-8 lg:mt-0' => $headline, 'lg:col-span-12' => ! $headline])>
            @foreach ($items as $item)
                <details class="faq-item py-6" @if ($loop->first) open @endif>
                    <summary class="flex cursor-pointer items-baseline justify-between gap-6 font-medium">
                        <h3>{{ $item['q'] }}</h3>
                        <span class="faq-item__icon text-muted" aria-hidden="true">+</span>
                    </summary>
                    <p class="mt-3 max-w-2xl leading-relaxed text-muted">{{ $item['a'] }}</p>
                </details>
            @endforeach
        </div>
    </div>
</section>
