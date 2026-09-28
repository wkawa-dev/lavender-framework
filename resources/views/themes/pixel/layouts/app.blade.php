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
    <body class="flex min-h-dvh flex-col bg-void font-mono text-fg antialiased">
        <a href="#main" class="sr-only z-[100] rounded-md bg-white px-3 py-2 text-black focus:not-sr-only focus:fixed focus:top-3 focus:left-3">{{ site('nav.skip') }}</a>

        <header class="px-header" x-data="siteNav">
            <div class="px-header__bar">
                <a href="{{ route('home') }}">
                    <x-pixel::brand />
                </a>

                <nav aria-label="{{ site('nav.primary') }}" class="hidden items-center gap-6 lg:flex">
                    <ul class="flex items-center gap-5 border-r border-line pr-6">
                        @foreach ($links as $link)
                            <li>
                                <a href="{{ route($link['route']) }}" class="px-nav-link" @if (request()->routeIs($link['route'])) aria-current="page" @endif>
                                    {{ $link['label'] }}
                                    <span class="px-nav-link__bar" aria-hidden="true"><span></span><span></span><span></span><span></span></span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('contact') }}" class="px-nav-cta" data-nav-desktop-focus @if (request()->routeIs('contact')) aria-current="page" @endif>
                        <span class="px-nav-cta__tile" aria-hidden="true">+</span>
                        {{ site('nav.contact') }}
                    </a>
                </nav>

                <button
                    type="button"
                    x-ref="open"
                    class="px-toggle lg:hidden"
                    @click="openMenu()"
                    :class="{ 'is-covered': open }"
                    :aria-expanded="open.toString()"
                    aria-expanded="false"
                    aria-controls="mobile-menu"
                    aria-label="{{ site('nav.open_menu') }}"
                >
                    <span class="px-toggle__lines" aria-hidden="true"><span></span><span></span><span></span></span>
                    {{ site('nav.menu') }}
                </button>
            </div>

            <dialog id="mobile-menu" x-ref="dialog" class="px-menu lg:hidden" aria-labelledby="mobile-menu-title">
                <h2 id="mobile-menu-title" class="sr-only">{{ site('nav.mobile') }}</h2>

                <div class="px-menu__bar">
                    <div class="px-header__bar">
                        <a href="{{ route('home') }}" @click="close()">
                            <x-pixel::brand />
                        </a>
                        <button type="button" x-ref="close" class="px-toggle is-open" @click="close()" aria-label="{{ site('nav.close_menu') }}">
                            <span class="px-toggle__lines" aria-hidden="true"><span></span><span></span><span></span></span>
                            {{ site('nav.close') }}
                        </button>
                    </div>
                </div>

                <div class="px-menu__panel" x-ref="panel">
                    <nav aria-label="{{ site('nav.mobile') }}" class="wrap py-8">
                        <ul>
                            @foreach ([...$links, ['route' => 'contact', 'label' => site('nav.contact')]] as $link)
                                <li class="px-menu__item" style="--i: {{ $loop->index }}">
                                    <a href="{{ route($link['route']) }}" class="px-menu__link" @if (request()->routeIs($link['route'])) aria-current="page" @endif>{{ $link['label'] }}</a>
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

        <footer class="px-footer-mark relative mt-32 overflow-hidden border-t border-line pt-16 pb-10" data-mark="{{ site('brand') }}">
            <div class="wrap">
                <div class="grid gap-10 md:grid-cols-[1.4fr_repeat(2,1fr)]">
                    <div>
                        <p class="font-pixel text-2xl text-fg">{{ site('brand') }}</p>
                        <p class="mt-3 max-w-xs text-[13px] leading-relaxed text-faint">{{ site('footer.tagline') }}</p>
                    </div>
                    <nav aria-label="{{ site('footer.columns.product') }}">
                        <h2 class="eyebrow">{{ site('footer.columns.product') }}</h2>
                        <ul class="space-y-2.5">
                            <li><a href="{{ route('home') }}" class="text-[13px] text-dim transition-colors hover:text-fg">{{ site('nav.home') }}</a></li>
                            <li><a href="{{ route('kit') }}" class="text-[13px] text-dim transition-colors hover:text-fg">{{ site('nav.kit') }}</a></li>
                            <li><a href="{{ route('docs.api') }}" class="text-[13px] text-dim transition-colors hover:text-fg">{{ site('nav.api') }}</a></li>
                        </ul>
                    </nav>
                    <nav aria-label="{{ site('footer.columns.company') }}">
                        <h2 class="eyebrow">{{ site('footer.columns.company') }}</h2>
                        <ul class="space-y-2.5">
                            <li><a href="{{ route('contact') }}" class="text-[13px] text-dim transition-colors hover:text-fg">{{ site('nav.contact') }}</a></li>
                            <li><a href="{{ route('privacy') }}" class="text-[13px] text-dim transition-colors hover:text-fg">{{ site('nav.privacy') }}</a></li>
                        </ul>
                    </nav>
                </div>

                <div class="mt-14 border-t border-line pt-6 text-xs leading-relaxed text-faint">
                    <p>{{ str_replace([':year', ':brand'], [now()->year, site('brand')], site('footer.copyright')) }}</p>
                </div>
            </div>
        </footer>
    </body>
</html>
