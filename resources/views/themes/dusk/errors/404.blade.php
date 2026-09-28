@extends('theme::layouts.app', ['title' => site('errors.404.title'), 'description' => site('errors.404.body'), 'robots' => 'noindex'])

@section('content')
    <x-dusk::page-header eyebrow="404" :title="site('errors.404.headline')" :lede="site('errors.404.body')">
        <div class="dk-enter mt-10" style="--enter-delay: 180ms">
            <x-dusk::button :href="route('home')">{{ site('errors.404.home') }}</x-dusk::button>
        </div>
    </x-dusk::page-header>
@endsection
