@extends('theme::layouts.app')

{{-- Component gallery for the pixel theme. noindex; delete the route when you ship. --}}
@section('content')
    <x-pixel::page-header :crumbs="[['name' => site('nav.kit'), 'url' => route('kit')]]" :eyebrow="site('kit.hero.eyebrow')" :title="site('kit.hero.headline')" :lede="site('kit.hero.lead')" />

    <div class="wrap">
        <nav aria-label="On this page" class="chip-nav mb-16">
            @foreach (['type' => 'Type', 'actions' => 'Actions', 'data' => 'Data', 'art' => 'Pixel art', 'chart' => 'Chart', 'table' => 'Table'] as $id => $label)
                <a href="#{{ $id }}"><span class="chip-nav__num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>{{ $label }}</a>
            @endforeach
        </nav>

        <section id="type" class="grid gap-6 border-t border-line py-16">
            <p class="eyebrow">Type</p>
            <p class="display display-1">Display one</p>
            <p class="display display-2">Display two</p>
            <p class="display display-3">Display three</p>
            <p class="lede mt-0">Lede in Geist Mono, 15px, relaxed. Body copy stays mono; only display type is pixel.</p>
            <p class="text-[13px] text-dim">Body with a <a href="#type" class="link">link</a> and a <a href="#type" class="signal-link">signal link</a>.</p>
            <p class="micro">Micro label</p>
        </section>

        <section id="actions" class="grid gap-8 border-t border-line py-16">
            <p class="eyebrow">Actions</p>
            <div class="flex flex-wrap gap-4">
                <x-pixel::retro-button href="#actions">Primary</x-pixel::retro-button>
                <x-pixel::retro-button href="#actions" color="var(--color-prime)">White slab</x-pixel::retro-button>
                <x-pixel::retro-button size="sm">Small button</x-pixel::retro-button>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="badge">Outline</span>
                <span class="badge badge--inset">Inset</span>
                <span class="badge badge--solid">Solid</span>
                <span class="badge badge--signal">Signal</span>
            </div>
            <x-pixel::notice heading="Heads up.">Callouts use an orange hairline and a faint wash. Keep them for risk and irreversible actions.</x-pixel::notice>
        </section>

        <section id="data" class="grid gap-10 border-t border-line py-16">
            <p class="eyebrow">Data</p>
            <x-pixel::stats class="max-w-2xl" :items="[
                ['label' => 'Requests', 'value' => '4'],
                ['label' => 'Transfer', 'value' => '61 KB'],
                ['label' => 'LCP', 'value' => '0.6 s'],
            ]" />
            <div class="grid gap-10 md:grid-cols-2">
                <ul class="ticks">
                    <li>Square bullets in the signal colour.</li>
                    <li>13px body, relaxed leading.</li>
                </ul>
                <dl class="kv">
                    <div><dt>Theme</dt><dd>pixel</dd></div>
                    <div><dt>Display</dt><dd>Geist Pixel</dd></div>
                    <div><dt>Body</dt><dd>Geist Mono</dd></div>
                </dl>
            </div>
            <div class="grid max-w-md gap-4">
                <x-pixel::blocks :value="62" :cap="80" label="62 of 100, capped at 80" />
                <x-pixel::blocks :value="7" :max="10" :cells="10" :meter="true" color="var(--color-prime)" label="7 of 10" />
            </div>
            <div class="panel max-w-md">Panel: hairline on neutral-950.</div>
        </section>

        <section id="art" class="grid gap-10 border-t border-line py-16 md:grid-cols-2">
            <p class="eyebrow md:col-span-2">Pixel art</p>
            <x-pixel::sprite class="w-32" label="Invader sprite" :rows="[
                '..2.....2..',
                '...2...2...',
                '..1111111..',
                '.11.111.11.',
                '11111111111',
                '1.1111111.1',
                '1.1.....1.1',
                '...11.11...',
            ]" />
            <x-pixel::pixel-stack top="Top" bottom="Bottom" highlight="Two" :rows="[
                ['name' => 'One', 'meta' => 'first', 'color' => 'var(--color-prime)', 'height' => 2],
                ['name' => 'Two', 'meta' => 'second', 'color' => 'var(--color-hy)', 'height' => 2],
                ['name' => 'Three', 'meta' => 'third', 'color' => 'var(--color-frontier)', 'height' => 1],
            ]" />
        </section>

        <section id="chart" class="border-t border-line py-16">
            <p class="eyebrow">Chart</p>
            <div class="panel md:p-8">
                <x-pixel::chart
                    title="Monthly score"
                    unit="%"
                    :labels="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']"
                    :series="[
                        ['name' => 'Prime', 'color' => 'var(--color-prime)', 'values' => [4, 4.2, 4.1, 4.4, 4.6, 4.5, 4.8, 5, 5.1, 5, 5.2, 5.4]],
                        ['name' => 'Yield', 'color' => 'var(--color-hy)', 'values' => [7, 7.4, 6.9, 7.8, 8.1, 8, 8.6, 8.4, 8.9, 9.2, 9, 9.4]],
                        ['name' => 'Frontier', 'color' => 'var(--color-frontier)', 'values' => [11, 12.5, 10.2, 13.8, 14.1, 12.9, 15.2, 16, 14.8, 16.9, 17.5, 18.2]],
                    ]"
                />
            </div>
        </section>

        <section id="table" class="border-t border-line py-16">
            <p class="eyebrow">Table</p>
            <div class="px-table-wrap">
                <table class="px-table">
                    <caption>Tables scroll horizontally inside their frame on small screens.</caption>
                    <thead>
                        <tr><th scope="col">Name</th><th scope="col">Status</th><th scope="col" class="text-right">Value</th></tr>
                    </thead>
                    <tbody>
                        <tr><td class="text-fg">Alpha</td><td><span class="badge badge--signal">Live</span></td><td class="tabular text-right">12.40</td></tr>
                        <tr><td class="text-fg">Beta</td><td><span class="badge">Queued</span></td><td class="tabular text-right">8.05</td></tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
