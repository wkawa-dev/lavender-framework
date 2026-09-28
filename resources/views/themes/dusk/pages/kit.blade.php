@extends('theme::layouts.app')

{{-- Component gallery for the dusk theme. noindex; delete the route when you ship. --}}
@section('content')
    <x-dusk::page-header :crumbs="[['name' => site('nav.kit'), 'url' => route('kit')]]" :eyebrow="site('kit.hero.eyebrow')" :title="site('kit.hero.headline')" :lede="site('kit.hero.lead')" />

    <div class="wrap">
        <nav aria-label="On this page" class="chip-nav mb-16">
            @foreach (['type' => 'Type', 'actions' => 'Actions', 'data' => 'Data', 'art' => 'Orbit', 'chart' => 'Chart', 'table' => 'Table', 'faq' => 'FAQ'] as $id => $label)
                <a href="#{{ $id }}"><span class="chip-nav__num">{{ $loop->iteration }}</span>{{ $label }}</a>
            @endforeach
        </nav>

        <section id="type" class="grid gap-6 border-t border-line py-16">
            <p class="eyebrow">Type</p>
            <p class="display display-1">Display one</p>
            <p class="display display-2">Display two, <span class="glow-text">with a glow</span></p>
            <p class="display display-3">Display three</p>
            <p class="lede mt-0">Lede in Instrument Sans, 17px, relaxed. Headlines are Instrument Serif; italics carry the accent.</p>
            <p class="text-[15px] text-dim">Body with a <a href="#type" class="link">link</a> and a <a href="#type" class="glow-link">glow link <span aria-hidden="true">→</span></a></p>
            <p class="micro">Small label</p>
        </section>

        <section id="actions" class="grid gap-8 border-t border-line py-16">
            <p class="eyebrow">Actions</p>
            <div class="flex flex-wrap gap-3">
                <x-dusk::button href="#actions">Primary</x-dusk::button>
                <x-dusk::button href="#actions" variant="ghost">Ghost</x-dusk::button>
                <x-dusk::button size="sm">Small button</x-dusk::button>
                <x-dusk::button size="sm" variant="ghost" :arrow="false">No arrow</x-dusk::button>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="badge">Outline</span>
                <span class="badge badge--inset">Inset</span>
                <span class="badge badge--solid">Solid</span>
                <span class="badge badge--glow">Glow</span>
            </div>
            <x-dusk::notice heading="Heads up." class="max-w-2xl">Callouts use a lavender hairline and a faint wash. Keep them for risk and irreversible actions.</x-dusk::notice>
        </section>

        <section id="data" class="grid gap-10 border-t border-line py-16">
            <p class="eyebrow">Data</p>
            <x-dusk::stats class="max-w-2xl" :items="[
                ['label' => 'Requests', 'value' => '4'],
                ['label' => 'Transfer', 'value' => '64 KB'],
                ['label' => 'LCP', 'value' => '0.6 s'],
            ]" />
            <div class="grid gap-10 md:grid-cols-2">
                <ul class="stars">
                    <li>Star bullets in the glow colour.</li>
                    <li>15px body, relaxed leading.</li>
                </ul>
                <dl class="kv">
                    <div><dt>Theme</dt><dd>dusk</dd></div>
                    <div><dt>Display</dt><dd>Instrument Serif</dd></div>
                    <div><dt>Body</dt><dd>Instrument Sans</dd></div>
                </dl>
            </div>
            <div class="grid max-w-md gap-4" data-reveal>
                <x-dusk::meter :value="62" label="62 of 100" />
                <x-dusk::meter :value="7" :max="10" color="var(--color-rose)" label="7 of 10" />
            </div>
            <div class="grid gap-5 md:grid-cols-2">
                <div class="card">Card: plum surface, hairline, 16px radius.</div>
                <div class="card card--glow">Glow card: for the one thing that matters on the page.</div>
            </div>
        </section>

        <section id="art" class="grid gap-10 border-t border-line py-16">
            <p class="eyebrow">Orbit</p>
            <x-dusk::orbit top="Top caption" bottom="Bottom caption" :bodies="[
                ['name' => 'One', 'meta' => 'first', 'color' => 'var(--color-moon)'],
                ['name' => 'Two', 'meta' => 'second', 'color' => 'var(--color-lilac)'],
                ['name' => 'Three', 'meta' => 'third', 'color' => 'var(--color-rose)'],
            ]" />
        </section>

        <section id="chart" class="border-t border-line py-16">
            <p class="eyebrow">Chart</p>
            <div class="card md:p-8">
                <x-dusk::chart
                    title="Monthly score"
                    unit="%"
                    :labels="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']"
                    :series="[
                        ['name' => 'Moon', 'color' => 'var(--color-moon)', 'values' => [4, 4.2, 4.1, 4.4, 4.6, 4.5, 4.8, 5, 5.1, 5, 5.2, 5.4]],
                        ['name' => 'Lilac', 'color' => 'var(--color-lilac)', 'values' => [7, 7.4, 6.9, 7.8, 8.1, 8, 8.6, 8.4, 8.9, 9.2, 9, 9.4]],
                        ['name' => 'Rose', 'color' => 'var(--color-rose)', 'values' => [11, 12.5, 10.2, 13.8, 14.1, 12.9, 15.2, 16, 14.8, 16.9, 17.5, 18.2]],
                    ]"
                />
            </div>
        </section>

        <section id="table" class="border-t border-line py-16">
            <p class="eyebrow">Table</p>
            <div class="dk-table-wrap">
                <table class="dk-table">
                    <caption>Tables scroll horizontally inside their frame on small screens.</caption>
                    <thead>
                        <tr><th scope="col">Name</th><th scope="col">Status</th><th scope="col" class="text-right">Value</th></tr>
                    </thead>
                    <tbody>
                        <tr><td class="text-fg">Alpha</td><td><span class="badge badge--glow">Live</span></td><td class="tabular text-right">12.40</td></tr>
                        <tr><td class="text-fg">Beta</td><td><span class="badge">Queued</span></td><td class="tabular text-right">8.05</td></tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section id="faq" class="grid gap-8 border-t border-line py-16">
            <p class="eyebrow">FAQ</p>
            <x-dusk::faq class="max-w-3xl" :items="[
                ['q' => 'Why native details?', 'a' => 'Answers stay in the DOM for search, screen readers, and the FAQPage JSON-LD. Height animates where ::details-content is supported.'],
                ['q' => 'Does it need JavaScript?', 'a' => 'No. The plus folds into a minus with a CSS transition.'],
            ]" />
        </section>
    </div>
@endsection
