@php
    $links = [
        ['route' => 'home', 'label' => site('nav.home')],
        ['route' => 'kit', 'label' => site('nav.kit')],
        ['route' => 'privacy', 'label' => site('nav.privacy')],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="flex min-h-dvh flex-col font-sans text-fg antialiased">
        <a href="#main" class="sr-only z-[100] rounded-full bg-glow px-4 py-2 text-night focus:not-sr-only focus:fixed focus:top-3 focus:left-3">{{ site('nav.skip') }}</a>

        <header class="dk-header" x-data="siteNav">
            <div class="dk-header__bar">
                <a href="{{ route('home') }}">
                    <x-dusk::brand />
                </a>

                <nav aria-label="{{ site('nav.primary') }}" class="hidden items-center gap-4 lg:flex">
                    <ul class="dk-nav">
                        @foreach ($links as $link)
                            <li>
                                <a href="{{ route($link['route']) }}" class="dk-nav-link" @if (request()->routeIs($link['route'])) aria-current="page" @endif>{{ $link['label'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                    <x-dusk::button :href="route('contact')" size="sm" data-nav-desktop-focus :aria-current="request()->routeIs('contact') ? 'page' : null">{{ site('nav.contact') }}</x-dusk::button>
                </nav>

                <button
                    type="button"
                    x-ref="open"
                    class="dk-toggle lg:hidden"
                    @click="openMenu()"
                    :class="{ 'is-covered': open }"
                    :aria-expanded="open.toString()"
                    aria-expanded="false"
                    aria-controls="mobile-menu"
                    aria-label="{{ site('nav.open_menu') }}"
                >
                    <span class="dk-toggle__lines" aria-hidden="true"><span></span><span></span></span>
                    {{ site('nav.menu') }}
                </button>
            </div>

            <dialog id="mobile-menu" x-ref="dialog" class="dk-menu lg:hidden" aria-labelledby="mobile-menu-title">
                <h2 id="mobile-menu-title" class="sr-only">{{ site('nav.mobile') }}</h2>

                <div class="dk-menu__bar">
                    <div class="dk-header__bar">
                        <a href="{{ route('home') }}" @click="close()">
                            <x-dusk::brand />
                        </a>
                        <button type="button" x-ref="close" class="dk-toggle is-open" @click="close()" aria-label="{{ site('nav.close_menu') }}">
                            <span class="dk-toggle__lines" aria-hidden="true"><span></span><span></span></span>
                            {{ site('nav.close') }}
                        </button>
                    </div>
                </div>

                <div class="dk-menu__panel" x-ref="panel">
                    <nav aria-label="{{ site('nav.mobile') }}" class="dk-menu__nav wrap pb-10">
                        <ul class="mt-6">
                            @foreach ([...$links, ['route' => 'contact', 'label' => site('nav.contact')]] as $link)
                                <li class="dk-menu__item" style="--i: {{ $loop->index }}">
                                    <a href="{{ route($link['route']) }}" class="dk-menu__link" @if (request()->routeIs($link['route'])) aria-current="page" @endif>{{ $link['label'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </nav>
                </div>

            </dialog>
        </header>

        <main id="main" class="flex-1">
            @yield('content')
        </main>

        <footer class="dk-footer-mark relative mt-32 overflow-hidden border-t border-line pt-16 pb-10" data-mark="{{ site('brand') }}">
            <div class="wrap">
                <div class="grid gap-10 md:grid-cols-[1.4fr_repeat(2,1fr)]">
                    <div>
                        <p class="font-serif text-3xl text-fg">{{ site('brand') }}</p>
                        <p class="mt-3 max-w-xs text-sm leading-relaxed text-faint">{{ site('footer.tagline') }}</p>
                    </div>
                    <nav aria-label="{{ site('footer.columns.product') }}">
                        <h2 class="micro mb-4">{{ site('footer.columns.product') }}</h2>
                        <ul class="space-y-2.5">
                            <li><a href="{{ route('home') }}" class="text-sm text-dim transition-colors hover:text-glow">{{ site('nav.home') }}</a></li>
                            <li><a href="{{ route('kit') }}" class="text-sm text-dim transition-colors hover:text-glow">{{ site('nav.kit') }}</a></li>
                            <li><a href="{{ route('docs.api') }}" class="text-sm text-dim transition-colors hover:text-glow">{{ site('nav.api') }}</a></li>
                        </ul>
                    </nav>
                    <nav aria-label="{{ site('footer.columns.company') }}">
                        <h2 class="micro mb-4">{{ site('footer.columns.company') }}</h2>
                        <ul class="space-y-2.5">
                            <li><a href="{{ route('contact') }}" class="text-sm text-dim transition-colors hover:text-glow">{{ site('nav.contact') }}</a></li>
                            <li><a href="{{ route('privacy') }}" class="text-sm text-dim transition-colors hover:text-glow">{{ site('nav.privacy') }}</a></li>
                        </ul>
                    </nav>
                </div>

                <div class="mt-14 border-t border-line pt-6 text-sm leading-relaxed text-faint">
                    <p>{{ str_replace([':year', ':brand'], [now()->year, site('brand')], site('footer.copyright')) }}</p>
                </div>
            </div>
        </footer>
    </body>
</html>
