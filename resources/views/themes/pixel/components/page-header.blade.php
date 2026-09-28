@props(['crumbs' => [], 'eyebrow' => null, 'title', 'lede' => null])

{{-- Inner-page hero: breadcrumb trail, bracketed eyebrow, pixel h1, lede. crumbs: [['name' => ..., 'url' => ...]] --}}
<header {{ $attributes->class(['wrap pt-14 pb-14 md:pt-20']) }}>
    @if ($crumbs)
        <nav aria-label="Breadcrumb">
            <ol class="crumbs">
                <li><a href="{{ route('home') }}">{{ site('nav.home') }}</a></li>
                @foreach ($crumbs as $crumb)
                    <li>
                        @if ($loop->last)
                            <span aria-current="page">{{ $crumb['name'] }}</span>
                        @else
                            <a href="{{ $crumb['url'] }}">{{ $crumb['name'] }}</a>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>
    @endif
    <div class="mt-10">
        @if ($eyebrow)
            <p class="eyebrow px-enter">{{ $eyebrow }}</p>
        @endif
        <h1 class="display display-1 px-enter max-w-4xl" style="--enter-delay: 50ms">{{ $title }}</h1>
        @if ($lede)
            <p class="px-enter mt-6 max-w-2xl text-[15px] leading-relaxed text-dim" style="--enter-delay: 100ms">{{ $lede }}</p>
        @endif
        {{ $slot }}
    </div>
</header>
