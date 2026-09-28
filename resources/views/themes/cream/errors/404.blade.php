@extends('theme::layouts.app', ['title' => site('errors.404.title'), 'description' => site('errors.404.body'), 'robots' => 'noindex'])

@section('content')
    <x-cream::page-hero :eyebrow="'404'" :headline="site('errors.404.headline')" :lead="site('errors.404.body')">
        <x-slot:actions>
            <div class="hero-enter hero-enter--3 mt-10">
                <a href="{{ route('home') }}" class="btn btn-primary btn-lg">{{ site('errors.404.home') }}</a>
            </div>
        </x-slot:actions>
    </x-cream::page-hero>
@endsection
