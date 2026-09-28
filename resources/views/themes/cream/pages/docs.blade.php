@extends('theme::layouts.app')

@section('content')
    <x-cream::page-hero :eyebrow="site('docs.api.eyebrow')" :headline="site('docs.api.headline')" :lead="site('docs.api.lead')" />

    <section class="wrap py-16 md:py-24">
        <article class="prose-cream">{!! $body !!}</article>
    </section>
@endsection
