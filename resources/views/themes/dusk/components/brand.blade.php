{{-- Brand: crescent moon + name in Instrument Serif. --}}
<span {{ $attributes->merge(['class' => 'dk-brand']) }}>
    <svg class="dk-brand__mark" viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M20.5 14.6A8.5 8.5 0 0 1 9.4 3.5a8.5 8.5 0 1 0 11.1 11.1Z" /><path d="m18 3 .6 1.4L20 5l-1.4.6L18 7l-.6-1.4L16 5l1.4-.6Z" opacity=".7" /></svg>
    <span class="dk-brand__name">{{ site('brand') }}</span>
</span>
