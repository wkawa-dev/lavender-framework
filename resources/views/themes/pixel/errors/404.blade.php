@extends('theme::layouts.app', ['title' => site('errors.404.title'), 'description' => site('errors.404.body'), 'robots' => 'noindex'])

@section('content')
    <x-pixel::page-header eyebrow="404" :title="site('errors.404.headline')" :lede="site('errors.404.body')">
        <div class="px-enter mt-10" style="--enter-delay: 150ms">
            <x-pixel::retro-button :href="route('home')">{{ site('errors.404.home') }}</x-pixel::retro-button>
        </div>
    </x-pixel::page-header>
@endsection
