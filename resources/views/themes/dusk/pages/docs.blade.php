@extends('theme::layouts.app')

@section('content')
    <x-dusk::page-header :crumbs="[['name' => site('docs.api.headline'), 'url' => route('docs.api')]]" :eyebrow="site('docs.api.eyebrow')" :title="site('docs.api.headline')" :lede="site('docs.api.lead')" />

    <section class="wrap pb-10">
        <article class="prose-dusk">{!! $body !!}</article>
    </section>
@endsection
