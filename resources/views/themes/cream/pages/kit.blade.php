@extends('theme::layouts.app')

{{-- Component gallery for the cream theme. noindex; delete the route when you ship. --}}
@section('content')
    <x-cream::page-hero :eyebrow="site('kit.hero.eyebrow')" :headline="site('kit.hero.headline')" :lead="site('kit.hero.lead')" />

    <section class="section">
        <div class="wrap section-pad grid gap-16 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <p class="eyebrow">Type</p>
                <p class="mt-3 text-sm text-muted">Newsreader for display, Geist for everything else. Serif is never bold.</p>
            </div>
            <div class="space-y-8 lg:col-span-8">
                <p class="hero-headline mt-0">Display headline</p>
                <p class="how-heading">Section heading</p>
                <p class="font-serif text-3xl tracking-tight md:text-4xl">Sub heading</p>
                <p class="how-lead how-lead--serif">Serif italic lead for pull lines.</p>
                <p class="max-w-xl text-lg leading-relaxed text-muted">Muted lead paragraph. Relaxed leading for long lines, max width around 36rem.</p>
                <p>Body copy with an <a href="#" class="link">inline link</a> that underlines in the line colour and inks on hover.</p>
                <p class="font-serif text-5xl leading-[1.08] tracking-tight">Text with <x-cream::video-text text="video" src="/video/ink-paper-480.mp4" /> inside.</p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="wrap section-pad grid gap-16 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <p class="eyebrow">Actions</p>
                <p class="mt-3 text-sm text-muted">Ink on paper. Press scales to 0.97 in 160ms.</p>
            </div>
            <div class="flex flex-wrap items-center gap-4 lg:col-span-8">
                <a href="#" class="btn btn-primary">Primary</a>
                <a href="#" class="btn btn-secondary">Secondary</a>
                <a href="#" class="btn btn-primary btn-lg hero-cta">Large<span class="hero-cta__arrow" aria-hidden="true">→</span></a>
                <a href="#" class="nav-link nav-underline">Nav link</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="wrap section-pad grid gap-16 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <p class="eyebrow">Fields</p>
                <p class="mt-3 text-sm text-muted">Paper fill, hairline border, ink on focus. No coloured rings.</p>
            </div>
            <div class="max-w-md space-y-5 lg:col-span-8">
                <div>
                    <label for="kit-text" class="label">Text</label>
                    <input id="kit-text" type="text" class="field" placeholder="Placeholder">
                </div>
                <div>
                    <label for="kit-select" class="label">Select</label>
                    <select id="kit-select" class="field">
                        <option>One</option>
                        <option>Two</option>
                    </select>
                </div>
                <div>
                    <label for="kit-area" class="label">Textarea</label>
                    <textarea id="kit-area" rows="3" class="field"></textarea>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="wrap section-pad">
            <p class="eyebrow">List rows</p>
            <x-cream::list-rows class="mt-8" :rows="[
                ['href' => '#', 'meta' => 'Sep 26, 2026', 'title' => 'Hairline rows instead of cards'],
                ['href' => '#', 'meta' => 'Sep 19, 2026', 'title' => 'Hover nudges 2px on fine pointers only'],
            ]" />
        </div>
    </section>

    <x-cream::steps-section eyebrow="Steps" headline="Numbered steps" :steps="[
        ['number' => '01', 'title' => 'Divide, do not box', 'body' => 'Steps sit on hairline dividers with serif numerals.'],
        ['number' => '02', 'title' => 'Keep it flat', 'body' => 'No shadows, no rounded cards, no icon circles.'],
    ]" />

    <x-cream::quote-section quote="Quotes are italic serif, centred, no marks beyond the curly quotes." name="Name" role="Role" />
@endsection
