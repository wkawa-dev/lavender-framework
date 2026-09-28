@extends('theme::layouts.app')

@section('content')
    <x-cream::page-hero :eyebrow="site('privacy.eyebrow')" :headline="site('privacy.headline')" />

    <section class="wrap py-16 md:py-24">
        <article class="prose-cream">{!! $body !!}</article>
    </section>
@endsection
