@extends('theme::layouts.app')

@section('content')
    <x-pixel::page-header :crumbs="[['name' => site('nav.contact'), 'url' => route('contact')]]" :eyebrow="site('contact.hero.eyebrow')" :title="site('contact.hero.headline')" :lede="site('contact.hero.lead')" />

    <section class="wrap pb-10">
        <div class="max-w-xl">
            @if (session('status'))
                <div class="panel panel--accent" role="status">
                    <p class="font-pixel text-3xl text-fg">{{ site('contact.form.success_title') }}</p>
                    <p class="mt-3 text-[13px] leading-relaxed text-dim">{{ session('status') }}</p>
                </div>
            @else
                <form method="POST" action="{{ route('contact.store') }}" toolname="contact_form" tooldescription="{{ site('agents.contact_form') }}" class="panel grid gap-5" novalidate>
                    @csrf

                    <div class="absolute -left-[9999px]" aria-hidden="true">
                        <label for="website">{{ site('contact.form.honeypot') }}</label>
                        <input type="text" name="website" id="website" tabindex="-1" autocomplete="off" toolparamdescription="{{ site('agents.honeypot') }}">
                    </div>

                    <x-pixel::field name="name" :label="site('contact.form.name')" required autocomplete="name" />
                    <x-pixel::field name="email" type="email" :label="site('contact.form.email')" required class="lowercase" autocomplete="email" autocapitalize="none" spellcheck="false" inputmode="email" />
                    <x-pixel::field name="company" :label="site('contact.form.company')" :optional="site('contact.form.optional')" autocomplete="organization" />
                    <x-pixel::field name="message" type="textarea" :label="site('contact.form.message')" :placeholder="site('contact.form.message_placeholder')" required />

                    <x-pixel::retro-button type="submit" size="sm" class="w-full">{{ site('contact.form.submit') }}</x-pixel::retro-button>
                    <p class="text-xs leading-relaxed text-faint">
                        {{ site('contact.form.privacy_before') }}
                        <a href="{{ route('privacy') }}" class="link">{{ site('contact.form.privacy_link') }}</a>{{ site('contact.form.privacy_after') }}
                    </p>
                </form>
            @endif
        </div>
    </section>
@endsection
