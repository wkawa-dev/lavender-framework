@props(['items' => []])

{{-- Numbered pixel FAQ on native <details>: no JS, answers always in the DOM. --}}
<div {{ $attributes->class(['px-faq']) }}>
    @foreach ($items as $item)
        <details @if ($loop->first) open @endif>
            <summary>
                <h3 class="px-faq__q"><span class="px-faq__num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>{{ $item['q'] }}</h3>
                <span class="px-faq__chevron" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="m4 6 4 4 4-4" /></svg>
                </span>
            </summary>
            <p class="px-faq__a">{{ $item['a'] }}</p>
        </details>
    @endforeach
</div>
