@extends('theme::layouts.app')

@push('preload')
    <link rel="preload" as="image" href="{{ \App\Support\Images::url('hero-backdrop', 640) }}" type="image/webp" fetchpriority="high">
@endpush

@section('content')
    <section class="hero">
        {{-- LCP candidate is the headline text, not this image: decorative, eager, low opacity. --}}
        <div class="hero-media" aria-hidden="true">
            <x-image slug="hero-backdrop" :eager="true" :width="640" class="rotate-[270deg] scale-300" />
        </div>
        <div class="hero-fade-x" aria-hidden="true"></div>
        <div class="hero-fade-bottom" aria-hidden="true"></div>

        <div class="wrap page-hero relative">
            @php
                $headline = (string) site('home.hero.headline');
                $strike = (string) site('home.hero.strike', '');
                $lines = array_values(array_filter(array_map('trim', preg_split('/,(?=\s)/', $headline) ?: [])));
            @endphp
            {{-- Lines rise in with CSS only; the full sentence is the accessible name. --}}
            <h1 class="hero-headline" aria-label="{{ $headline }}">
                @foreach ($lines as $line)
                    <span class="hero-line" aria-hidden="true">
                        <span class="hero-line__inner" style="--line-delay: {{ 100 + $loop->index * 120 }}ms">
                            @if ($strike !== '' && str_contains($line, $strike))
                                {!! str_replace(e($strike), '<span class="hero-strike">'.e($strike).'</span>', e($line)) !!}{{ $loop->last ? '' : ',' }}
                            @else
                                {{ $line }}{{ $loop->last ? '' : ',' }}
                            @endif
                        </span>
                    </span>
                @endforeach
            </h1>

            <p class="hero-enter hero-enter--3 mt-8 max-w-xl text-lg leading-relaxed text-muted">{{ site('home.hero.lead') }}</p>

            <div class="hero-enter hero-enter--3 mt-10 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('contact') }}" class="btn btn-primary btn-lg hero-cta">{{ site('home.hero.cta_primary') }}<span class="hero-cta__arrow" aria-hidden="true">→</span></a>
                <a href="{{ route('kit') }}" class="btn btn-secondary btn-lg hero-cta">{{ site('home.hero.cta_secondary') }}</a>
            </div>
        </div>
    </section>

    <x-cream::proof :items="site('home.proof.items')" :label="site('home.proof.aria')" />

    <section class="how-section scroll-stack isolate" aria-labelledby="how-heading">
        <div class="hero-media" aria-hidden="true">
            <x-image slug="bg-abstract" :width="640" style="opacity: .22" />
        </div>
        <div class="hero-fade-x" aria-hidden="true"></div>
        <div class="hero-fade-bottom" aria-hidden="true"></div>

        <div class="wrap how-layout relative">
            <h2 id="how-heading" class="how-heading" data-reveal>{{ site('home.how.headline') }}</h2>
            <x-cream::scroll-stack :steps="site('home.how.steps')" :kicker="site('home.how.kicker')" tone="how" />
        </div>
    </section>

    <section class="relative isolate overflow-hidden border-b border-line">
        <div class="wrap relative py-20 md:py-16" data-reveal>
            <div class="lg:grid lg:grid-cols-12 lg:items-end lg:gap-10">
                <h2 class="max-w-2xl font-serif text-3xl leading-[1.1] tracking-tight md:text-[2rem] md:leading-[1.08] lg:col-span-6 lg:max-w-none lg:text-[2.25rem]">{{ site('home.join.headline') }}</h2>
                <div class="mt-5 lg:col-span-6 lg:mt-0">
                    <p class="max-w-xl text-sm leading-relaxed text-muted lg:max-w-md">{{ site('home.join.body') }}</p>
                    <div class="mt-5 flex flex-col gap-3 sm:flex-row md:mt-6">
                        <a href="{{ route('contact') }}" class="btn btn-primary btn-lg hero-cta">{{ site('home.join.primary') }}<span class="hero-cta__arrow" aria-hidden="true">→</span></a>
                        <a href="{{ route('kit') }}" class="btn btn-secondary btn-lg hero-cta">{{ site('home.join.secondary') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-cream::quote-section :quote="site('home.quote.body')" :name="site('home.quote.name')" :role="site('home.quote.role')" />

    <x-cream::faq :items="site('home.faq.items')" :headline="site('home.faq.headline')" />
@endsection
