@props(['crumbs' => [], 'eyebrow' => null, 'title', 'lede' => null])

{{-- Inner-page hero: breadcrumb trail, starred eyebrow, serif h1, lede. crumbs: [['name' => ..., 'url' => ...]] --}}
<header {{ $attributes->class(['wrap pt-14 pb-16 md:pt-20']) }}>
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
    <div class="mt-12">
        @if ($eyebrow)
            <p class="eyebrow dk-enter">{{ $eyebrow }}</p>
        @endif
        <h1 class="display display-1 dk-enter max-w-4xl" style="--enter-delay: 60ms">{{ $title }}</h1>
        @if ($lede)
            <p class="lede dk-enter" style="--enter-delay: 120ms">{{ $lede }}</p>
        @endif
        {{ $slot }}
    </div>
</header>
