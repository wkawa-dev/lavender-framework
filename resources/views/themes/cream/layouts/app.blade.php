@php
    $links = [
        ['route' => 'home', 'label' => site('nav.home')],
        ['route' => 'kit', 'label' => site('nav.kit')],
        ['route' => 'contact', 'label' => site('nav.contact')],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="flex min-h-screen flex-col bg-paper font-sans text-ink antialiased" x-data>
        <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:bg-ink focus:px-3 focus:py-2 focus:text-paper">{{ site('nav.skip') }}</a>

        <header class="site-header" x-data="siteNav">
            <div class="site-header__bar wrap">
                <a href="{{ route('home') }}" class="site-header__brand text-ink">
                    <x-cream::wordmark />
                </a>

                <nav class="site-header__nav site-nav hidden md:flex" aria-label="{{ site('nav.primary') }}">
                    @foreach ($links as $link)
                        <a href="{{ route($link['route']) }}" @class(['nav-link nav-underline', 'nav-link-active' => request()->routeIs($link['route'])]) @if (request()->routeIs($link['route'])) aria-current="page" @endif>{{ $link['label'] }}</a>
                    @endforeach
                </nav>

                <div class="site-header__actions">
                    <div class="hidden items-center gap-6 md:flex">
                        <a href="{{ route('contact') }}" class="btn btn-primary" data-nav-desktop-focus>{{ site('nav.get_started') }}</a>
                    </div>

                    <button
                        type="button"
                        x-ref="open"
                        class="header-menu md:hidden"
                        @click="openMenu()"
                        :class="{ 'is-open': open, 'is-covered': open }"
                        :aria-expanded="open.toString()"
                        aria-expanded="false"
                        aria-controls="mobile-nav"
                        aria-label="{{ site('nav.open_menu') }}"
                    >
                        <span class="header-menu__line" aria-hidden="true"></span>
                        <span class="header-menu__line" aria-hidden="true"></span>
                    </button>
                </div>
            </div>

            <span class="site-header__rule" aria-hidden="true"></span>

            <dialog id="mobile-nav" x-ref="dialog" class="mobile-nav md:hidden" aria-labelledby="mobile-nav-title">
                <h2 id="mobile-nav-title" class="sr-only">{{ site('nav.mobile') }}</h2>

                <a href="{{ route('home') }}" class="mobile-nav__brand text-ink" @click="close()">
                    <x-cream::wordmark />
                </a>

                <button
                    type="button"
                    x-ref="close"
                    class="header-menu mobile-nav__close"
                    @click="close()"
                    :class="{ 'is-open': open }"
                    aria-label="{{ site('nav.close_menu') }}"
                >
                    <span class="header-menu__line" aria-hidden="true"></span>
                    <span class="header-menu__line" aria-hidden="true"></span>
                </button>

                <div class="mobile-nav__panel" x-ref="panel">
                    <div class="mobile-nav__inner">
                        <ul class="mobile-nav__list">
                            @foreach ($links as $link)
                                <li class="mobile-nav__item" style="--i: {{ $loop->index }}">
                                    <a
                                        href="{{ route($link['route']) }}"
                                        @class(['mobile-nav__link', 'is-active' => request()->routeIs($link['route'])])
                                        @if (request()->routeIs($link['route'])) aria-current="page" @endif
                                    >
                                        <span class="mobile-nav__index" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                        <span class="mobile-nav__label">{{ $link['label'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                        <div class="mobile-nav__footer">
                            <a href="{{ route('contact') }}" class="btn btn-primary w-full">{{ site('nav.get_started') }}</a>
                        </div>
                    </div>
                </div>
            </dialog>
        </header>

        <main id="main" class="flex-1">
            @yield('content')
        </main>

        <footer class="mt-auto border-t border-line">
            <div class="wrap flex flex-col gap-10 py-16 md:flex-row md:items-end md:justify-between md:py-20">
                <p class="max-w-3xl font-serif text-5xl italic leading-[1.08] tracking-tight text-ink md:text-6xl lg:text-7xl" data-reveal="clip">
                    {{ site('footer.headline') }}
                </p>
                <a href="{{ route('contact') }}" class="btn btn-primary btn-lg shrink-0 self-start px-5 py-2.5 md:self-auto md:px-7 md:py-3.5">{{ site('nav.get_started') }}</a>
            </div>

            <div class="footer-rule" data-reveal="line" aria-hidden="true"></div>

            <div class="wrap grid gap-10 py-14 sm:gap-12 md:grid-cols-12 md:gap-x-10 md:gap-y-12 md:py-16 lg:gap-x-12">
                <div class="md:col-span-4 lg:col-span-3">
                    <a href="{{ route('home') }}" class="inline-block text-ink">
                        <x-cream::wordmark class="text-2xl" />
                    </a>
                    <p class="mt-4 max-w-xs text-sm leading-relaxed text-muted">{{ site('footer.tagline') }}</p>
                </div>

                <nav class="grid grid-cols-2 gap-8 sm:gap-10 md:col-span-8 md:gap-x-12 lg:col-span-9 lg:gap-x-16" aria-label="{{ site('footer.nav') }}">
                    <div>
                        <p class="eyebrow font-semibold text-ink">{{ site('footer.columns.product') }}</p>
                        <ul class="mt-3 flex flex-col gap-1.5">
                            <li><a href="{{ route('home') }}" class="nav-link nav-underline">{{ site('nav.home') }}</a></li>
                            <li><a href="{{ route('kit') }}" class="nav-link nav-underline">{{ site('nav.kit') }}</a></li>
                            <li><a href="{{ route('docs.api') }}" class="nav-link nav-underline">{{ site('nav.api') }}</a></li>
                        </ul>
                    </div>
                    <div>
                        <p class="eyebrow font-semibold text-ink">{{ site('footer.columns.company') }}</p>
                        <ul class="mt-3 flex flex-col gap-1.5">
                            <li><a href="{{ route('contact') }}" class="nav-link nav-underline">{{ site('nav.contact') }}</a></li>
                            <li><a href="{{ route('privacy') }}" class="nav-link nav-underline">{{ site('nav.privacy') }}</a></li>
                        </ul>
                    </div>
                </nav>
            </div>

            <div class="border-t border-line">
                <div class="wrap flex flex-col gap-3 py-5 text-sm text-muted sm:flex-row sm:items-center sm:justify-between">
                    <p>{{ str_replace([':year', ':brand'], [now()->year, site('brand')], site('footer.copyright')) }}</p>
                    @if (config('site.consent_banner'))
                        <button type="button" class="nav-link" @click="$store.consent.reopen()">{{ site('consent.manage') }}</button>
                    @endif
                </div>
            </div>
        </footer>

        @if (config('site.consent_banner'))
            <x-cream::cookie-banner />
        @endif
    </body>
</html>
