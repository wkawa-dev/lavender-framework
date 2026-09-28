{{-- Typeset wordmark. Swap for an inline SVG with fill="currentColor" when you have a logo. --}}
<span {{ $attributes->merge(['class' => 'wordmark']) }}>{{ site('brand') }}</span>
