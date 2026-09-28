@props(['items' => []])

{{-- FAQ cards on native <details>: no JS, answers always in the DOM. --}}
<div {{ $attributes->class(['dk-faq']) }}>
    @foreach ($items as $item)
        <details @if ($loop->first) open @endif>
            <summary>
                <h3 class="dk-faq__q">{{ $item['q'] }}</h3>
                <span class="dk-faq__icon" aria-hidden="true"></span>
            </summary>
            <p class="dk-faq__a">{{ $item['a'] }}</p>
        </details>
    @endforeach
</div>
