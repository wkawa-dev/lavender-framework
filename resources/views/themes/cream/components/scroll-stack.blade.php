@props(['steps' => [], 'kicker' => null, 'tone' => 'how'])

{{--
    Scroll-stack cards. Every card is server-rendered (SEO + no-JS safe);
    resources/js/scroll-stack.js only owns the scroll handoff between cards.
    Without JS (or under reduced motion) this renders as a plain static stack.
    Card imagery is decoration (aria-hidden); the title + body carry the story.

    Tones:
    - how: instructional and crisp, numbered steps with caption outcomes.
    - matching: expressive and italic-led, accent pull line above the title.
    Step keys: title, body, accent?, marks? (phrases set in italic serif),
    caption?, image? (slug from `php artisan images:optimize`).
--}}
@php
    $tone = in_array($tone, ['how', 'matching'], true) ? $tone : 'how';

    $emphasize = function (string $text, array $marks, string $class): string {
        $escaped = e($text);
        $marks = array_values(array_filter(array_map('trim', $marks)));

        usort($marks, fn ($a, $b) => mb_strlen($b) <=> mb_strlen($a));

        foreach ($marks as $mark) {
            $markEscaped = e($mark);

            if ($markEscaped === '' || ! str_contains($escaped, $markEscaped)) {
                continue;
            }

            $escaped = str_replace(
                $markEscaped,
                '<em class="'.e($class).'">'.$markEscaped.'</em>',
                $escaped
            );
        }

        return $escaped;
    };
@endphp

<ol {{ $attributes->merge(['class' => 'scroll-stack__list scroll-stack__list--'.$tone]) }} role="list">
    @foreach ($steps as $step)
        @php
            $number = $step['number'] ?? str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT);
            $marks = is_array($step['marks'] ?? null) ? $step['marks'] : [];
            $title = $emphasize((string) ($step['title'] ?? ''), $marks, 'scroll-stack__em');
            $body = $emphasize((string) ($step['body'] ?? ''), $marks, 'scroll-stack__em');
        @endphp
        <li class="scroll-stack__row">
            <div class="scroll-stack__sticky" style="z-index: {{ $loop->iteration }}">
                <article class="scroll-stack__card scroll-stack__card--{{ $tone }}{{ $loop->last ? ' scroll-stack__card--ink' : '' }}">
                    @if (! empty($step['image']) && \App\Support\Images::has($step['image']))
                        <div class="scroll-stack__media" aria-hidden="true">
                            <x-image :slug="$step['image']" sizes="(max-width: 768px) 100vw, 790px" />
                            <div class="scroll-stack__scrim" aria-hidden="true"></div>
                        </div>
                    @endif
                    <div class="scroll-stack__content">
                        <div class="scroll-stack__eyebrow">
                            <span class="scroll-stack__number" aria-hidden="true">{{ $number }}</span>
                            @if ($kicker)
                                <span class="scroll-stack__kicker">{{ $kicker }}</span>
                            @endif
                        </div>
                        <div class="scroll-stack__rule" aria-hidden="true"></div>
                        @if (! empty($step['accent']))
                            <p class="scroll-stack__accent">{{ $step['accent'] }}</p>
                        @endif
                        <h3 class="scroll-stack__title"><span class="sr-only">Step {{ $loop->iteration }}: </span>{!! $title !!}</h3>
                        <p class="scroll-stack__body">{!! $body !!}</p>
                        @if (! empty($step['caption']))
                            <p class="scroll-stack__caption">{{ $step['caption'] }}</p>
                        @endif
                    </div>
                </article>
            </div>
        </li>
    @endforeach
</ol>
