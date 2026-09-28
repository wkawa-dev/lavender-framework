@extends('theme::layouts.app')

@section('content')
    <x-dusk::page-header :crumbs="[['name' => site('privacy.headline'), 'url' => route('privacy')]]" :eyebrow="site('privacy.eyebrow')" :title="site('privacy.headline')" />

    <section class="wrap pb-10">
        <article class="prose-dusk">{!! $body !!}</article>
    </section>
@endsection
