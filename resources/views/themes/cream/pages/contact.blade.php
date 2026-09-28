@extends('theme::layouts.app')

@section('content')
    <section>
        <div class="wrap grid gap-16 py-24 md:grid-cols-2 md:py-32 lg:gap-24">
            <div>
                <p class="eyebrow hero-enter">{{ site('contact.hero.eyebrow') }}</p>
                <h1 class="hero-enter hero-enter--2 mt-6 font-serif text-5xl leading-[1.08] tracking-tight md:text-6xl">{{ site('contact.hero.headline') }}</h1>
                <p class="hero-enter hero-enter--3 mt-8 max-w-md text-lg leading-relaxed text-muted">{{ site('contact.hero.lead') }}</p>
            </div>

            <div>
                @if (session('status'))
                    <div class="border border-line bg-white/40 px-5 py-8" role="status">
                        <p class="font-serif text-2xl tracking-tight">{{ site('contact.form.success_title') }}</p>
                        <p class="mt-3 text-sm leading-relaxed text-muted">{{ session('status') }}</p>
                    </div>
                @else
                    <form method="POST" action="{{ route('contact.store') }}" toolname="contact_form" tooldescription="{{ site('agents.contact_form') }}" class="space-y-5" novalidate>
                        @csrf

                        <div class="absolute -left-[9999px]" aria-hidden="true">
                            <label for="website">{{ site('contact.form.honeypot') }}</label>
                            <input type="text" name="website" id="website" tabindex="-1" autocomplete="off" toolparamdescription="{{ site('agents.honeypot') }}">
                        </div>

                        <x-cream::field name="name" :label="site('contact.form.name')" required autocomplete="name" />
                        <x-cream::field name="email" type="email" :label="site('contact.form.email')" required class="lowercase-field" autocomplete="email" autocapitalize="none" spellcheck="false" inputmode="email" />
                        <x-cream::field name="company" :label="site('contact.form.company')" :optional="site('contact.form.optional')" autocomplete="organization" />
                        <x-cream::field name="message" type="textarea" :label="site('contact.form.message')" :placeholder="site('contact.form.message_placeholder')" required />

                        <button type="submit" class="btn btn-primary w-full">{{ site('contact.form.submit') }}</button>
                        <p class="text-sm leading-relaxed text-muted">
                            {{ site('contact.form.privacy_before') }}
                            <a href="{{ route('privacy') }}" class="link">{{ site('contact.form.privacy_link') }}</a>{{ site('contact.form.privacy_after') }}
                        </p>
                    </form>
                @endif
            </div>
        </div>
    </section>
@endsection
