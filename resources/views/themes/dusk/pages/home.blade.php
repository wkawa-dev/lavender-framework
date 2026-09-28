@extends('theme::layouts.app')

@section('content')
    {{-- Hero --}}
    <section aria-labelledby="hero-h" class="relative isolate">
        <div class="dk-aurora" aria-hidden="true"><span></span><span></span><span></span></div>
        <div class="wrap grid items-center gap-16 pt-20 pb-24 md:pt-28 lg:grid-cols-[1.3fr_1fr]">
            <div>
                <p class="eyebrow dk-enter">{{ site('home.hero.eyebrow') }}</p>
                <h1 id="hero-h" class="display display-1 dk-enter" style="--enter-delay: 60ms">{{ site('home.hero.headline') }}</h1>
                <p class="lede dk-enter mt-7 max-w-xl" style="--enter-delay: 120ms">{{ site('home.hero.lead') }}</p>
                <div class="dk-enter mt-10 flex flex-wrap gap-3" style="--enter-delay: 180ms">
                    <x-dusk::button :href="route('contact')">{{ site('home.hero.cta_primary') }}</x-dusk::button>
                    <x-dusk::button :href="route('kit')" variant="ghost">{{ site('home.hero.cta_secondary') }}</x-dusk::button>
                </div>
                <x-dusk::stats
                    class="dk-enter mt-14 max-w-lg"
                    style="--enter-delay: 240ms"
                    :items="array_map(fn ($p) => ['label' => $p['label'], 'value' => $p['display']], site('home.proof.items'))"
                />
            </div>
            <div class="dk-enter flex justify-center lg:justify-end" style="--enter-delay: 200ms">
                <x-dusk::orbit
                    :top="site('home.art.top')"
                    :bottom="site('home.art.bottom')"
                    :bodies="collect(site('home.art.rows'))->map(fn ($name, $i) => [
                        'name' => $name,
                        'meta' => '0'.($i + 1),
                        'color' => ['var(--color-moon)', 'var(--color-lilac)', 'var(--color-rose)'][$i % 3],
                    ])->all()"
                />
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section id="how" aria-labelledby="how-h" class="wrap section">
        <div class="max-w-2xl" data-reveal>
            <p class="eyebrow">{{ site('home.how.eyebrow') }}</p>
            <h2 id="how-h" class="display display-2">{{ site('home.how.headline') }}</h2>
            <p class="lede">{{ site('home.join.body') }}</p>
        </div>
        <ul class="mt-14 grid gap-5 lg:grid-cols-3">
            @foreach (site('home.how.steps') as $step)
                <li class="flex" data-reveal style="--reveal-delay: {{ $loop->index * 80 }}ms">
                    <article class="card card--link flex w-full flex-col gap-5">
                        <header>
                            <p class="font-serif text-lg text-glow italic">{{ site('home.how.kicker') }} {{ $loop->iteration }}</p>
                            <h3 class="mt-2 font-serif text-3xl text-fg">{{ $step['title'] }}</h3>
                        </header>
                        <p class="text-[15px] leading-relaxed text-body">{{ $step['accent'] }}</p>
                        <p class="text-[15px] leading-relaxed text-dim">{{ $step['body'] }}</p>
                        <div class="mt-auto pt-2">
                            <p class="micro mb-2.5 flex justify-between"><span>{{ site('home.how.meter') }}</span><span class="tabular">{{ $loop->iteration * 3 }}/10</span></p>
                            <x-dusk::meter :value="$loop->iteration * 3" :max="10" :color="['var(--color-moon)', 'var(--color-lilac)', 'var(--color-rose)'][$loop->index % 3]" :label="$step['title'].': '.($loop->iteration * 3).' of 10'" />
                            <span class="badge mt-5">{{ $step['caption'] }}</span>
                        </div>
                    </article>
                </li>
            @endforeach
        </ul>
    </section>

    {{-- FAQ --}}
    <section aria-labelledby="faq-h" class="wrap section grid gap-12 lg:grid-cols-[1fr_1.4fr] lg:gap-20">
        <div class="lg:sticky lg:top-32 lg:self-start" data-reveal>
            <p class="eyebrow">{{ site('home.faq.eyebrow') }}</p>
            <h2 id="faq-h" class="display display-2">{{ site('home.faq.headline') }}</h2>
            <p class="mt-5 text-[15px] text-dim"><a href="{{ route('contact') }}" class="glow-link">{{ site('nav.contact') }} <span aria-hidden="true">→</span></a></p>
        </div>
        <x-dusk::faq :items="site('home.faq.items')" />
    </section>

    {{-- CTA --}}
    <section aria-labelledby="cta-h" class="wrap pt-10">
        <div class="card card--glow relative isolate overflow-hidden md:p-14" data-reveal>
            <div class="dk-aurora opacity-70" aria-hidden="true"><span></span><span></span><span></span></div>
            <div class="flex flex-col gap-10 md:flex-row md:items-end md:justify-between">
                <div>
                    <h2 id="cta-h" class="display display-2">{{ site('home.cta.headline') }} <span class="glow-text">{{ site('home.cta.muted') }}</span></h2>
                    <p class="mt-5 max-w-xl text-[15px] leading-relaxed text-dim">{{ site('home.cta.body') }}</p>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row md:flex-col">
                    <x-dusk::button :href="route('contact')">{{ site('home.cta.primary') }}</x-dusk::button>
                    <x-dusk::button :href="route('kit')" variant="ghost">{{ site('home.cta.secondary') }}</x-dusk::button>
                </div>
            </div>
        </div>
    </section>
@endsection
