@extends('theme::layouts.app')

@section('content')
    {{-- Hero --}}
    <section aria-labelledby="hero-h" class="wrap grid items-center gap-14 pt-20 pb-24 md:pt-28 lg:grid-cols-[1.25fr_1fr]">
        <div>
            <p class="eyebrow px-enter">{{ site('home.hero.eyebrow') }}</p>
            <h1 id="hero-h" class="display display-1 px-enter" style="--enter-delay: 50ms">{{ site('home.hero.headline') }}</h1>
            <p class="px-enter mt-7 max-w-xl text-[15px] leading-relaxed text-dim" style="--enter-delay: 100ms">{{ site('home.hero.lead') }}</p>
            <div class="px-enter mt-10 flex flex-wrap gap-4" style="--enter-delay: 150ms">
                <x-pixel::retro-button :href="route('contact')">{{ site('home.hero.cta_primary') }}</x-pixel::retro-button>
                <x-pixel::retro-button :href="route('kit')" color="var(--color-prime)">{{ site('home.hero.cta_secondary') }}</x-pixel::retro-button>
            </div>
            <x-pixel::stats
                class="px-enter mt-12 max-w-lg"
                style="--enter-delay: 200ms"
                :items="array_map(fn ($p) => ['label' => $p['label'], 'value' => $p['display']], site('home.proof.items'))"
            />
        </div>
        <div class="flex justify-center lg:justify-end">
            <x-pixel::pixel-stack
                :top="site('home.art.top')"
                :bottom="site('home.art.bottom')"
                :rows="collect(site('home.art.rows'))->map(fn ($name, $i) => [
                    'name' => $name,
                    'meta' => site('home.how.kicker').' 0'.($i + 1),
                    'color' => ['var(--color-prime)', 'var(--color-hy)', 'var(--color-frontier)'][$i % 3],
                    'height' => $i === 0 ? 3 : 2,
                ])->all()"
            />
        </div>
    </section>

    {{-- How it works --}}
    <section id="how" aria-labelledby="how-h" class="wrap section">
        <div data-reveal>
            <p class="eyebrow">{{ site('home.how.eyebrow') }}</p>
            <h2 id="how-h" class="display display-2">{{ site('home.how.headline') }}</h2>
            <p class="lede">{{ site('home.join.body') }}</p>
        </div>
        <ul class="mt-12 grid gap-4 lg:grid-cols-3">
            @foreach (site('home.how.steps') as $step)
                <li class="flex" data-reveal style="--reveal-delay: {{ $loop->index * 60 }}ms">
                    <article class="panel panel--link flex w-full flex-col gap-6">
                        <header class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs text-faint">{{ site('home.how.kicker') }} 0{{ $loop->iteration }}</p>
                                <h3 class="mt-2 font-pixel text-3xl text-fg">{{ $step['title'] }}</h3>
                            </div>
                            <span class="badge">{{ $step['caption'] }}</span>
                        </header>
                        <p class="text-[13px] leading-relaxed text-body">{{ $step['accent'] }}</p>
                        <p class="text-[13px] leading-relaxed text-dim">{{ $step['body'] }}</p>
                        <div class="mt-auto">
                            <p class="micro mb-2 flex justify-between"><span>{{ site('home.how.meter') }}</span><span>{{ $loop->iteration * 3 }}/10</span></p>
                            <x-pixel::blocks :value="$loop->iteration * 3" :max="10" :cells="10" :meter="true" :color="['var(--color-prime)', 'var(--color-hy)', 'var(--color-frontier)'][$loop->index % 3]" :label="$step['title'].': '.($loop->iteration * 3).' of 10'" />
                        </div>
                    </article>
                </li>
            @endforeach
        </ul>
    </section>

    {{-- FAQ --}}
    <section aria-labelledby="faq-h" class="wrap section grid gap-12 lg:grid-cols-[1fr_1.3fr] lg:gap-24">
        <div class="lg:sticky lg:top-32 lg:self-start" data-reveal>
            <p class="eyebrow">{{ site('home.faq.eyebrow') }}</p>
            <h2 id="faq-h" class="display display-2">{{ site('home.faq.headline') }}</h2>
            <p class="mt-4 max-w-sm text-[13px] leading-relaxed text-dim"><a href="{{ route('contact') }}" class="link">{{ site('nav.contact') }}</a></p>
        </div>
        <x-pixel::faq :items="site('home.faq.items')" />
    </section>

    {{-- CTA --}}
    <section aria-labelledby="cta-h" class="wrap pt-10">
        <div class="panel panel--accent flex flex-col gap-8 md:flex-row md:items-end md:justify-between md:p-10" data-reveal>
            <div>
                <h2 id="cta-h" class="display display-2">{{ site('home.cta.headline') }} <span class="text-neutral-500">{{ site('home.cta.muted') }}</span></h2>
                <p class="mt-4 max-w-xl text-[13px] leading-relaxed text-dim">{{ site('home.cta.body') }}</p>
            </div>
            <div class="grid grid-cols-1 gap-4">
                <x-pixel::retro-button :href="route('contact')" size="sm" class="w-full">{{ site('home.cta.primary') }}</x-pixel::retro-button>
                <x-pixel::retro-button :href="route('kit')" size="sm" color="var(--color-prime)" class="w-full">{{ site('home.cta.secondary') }}</x-pixel::retro-button>
            </div>
        </div>
    </section>
@endsection
