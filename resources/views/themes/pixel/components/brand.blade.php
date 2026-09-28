{{-- Brand: three stacked bars (series colours, shrinking) + name in Geist Pixel. --}}
<span {{ $attributes->merge(['class' => 'px-brand']) }}>
    <span class="px-brand__mark" aria-hidden="true">
        <span class="bg-prime" style="width: 100%"></span>
        <span class="bg-hy" style="width: 75%"></span>
        <span class="bg-frontier" style="width: 50%"></span>
    </span>
    <span class="px-brand__name">{{ site('brand') }}</span>
</span>
