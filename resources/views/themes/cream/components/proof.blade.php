@props(['items' => [], 'label' => null])

{{--
    Proof strip: stacked figure rows, number + claim on one baseline,
    asymmetric offsets per row, swash highlighter behind the numeral, and a
    count-up (lib/count-up.js). The server renders the final number, so
    no-JS and reduced motion show the real value. Offsets exist for 3 rows.
--}}
<section class="proof" @if ($label) aria-label="{{ $label }}" @endif>
    <div class="wrap py-10 md:py-12 lg:pb-4 lg:pt-14">
        <ul class="proof__list">
            @foreach ($items as $item)
                <li class="proof__item proof__item--{{ $loop->iteration }}" data-reveal data-proof-item style="--reveal-delay: {{ $loop->index * 70 }}ms">
                    <p class="proof__value">
                        <span class="proof__swash" aria-hidden="true"></span>
                        <span class="proof__num" data-proof-num data-proof-display="{{ $item['display'] }}">{{ $item['display'] }}</span>
                    </p>
                    <p class="proof__label">{{ $item['label'] }}</p>
                </li>
            @endforeach
        </ul>
    </div>
</section>
