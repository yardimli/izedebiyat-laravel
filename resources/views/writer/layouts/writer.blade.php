<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="{{ auth()->user()->theme ?? 'paper' }}">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/writer.css', 'resources/js/writer/app.js'])
@if(config('app.favicon'))<link rel="icon" href="{{ asset(config('app.favicon')) }}">@endif
<meta name="robots" content="noindex,nofollow">
@include('writer.partials.translations')
</head>
<body data-app-name="{{ config('app.name') }}" data-user="{{ auth()->id() }}">
<header class="site-header">
    @include('writer.partials.brand')
    <nav aria-label="{{ __('Main navigation') }}">
        @auth
        <a class="nav-icon" href="{{ route('articles.index') }}" aria-label="{{ __('My library') }}" title="{{ __('My library') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 4h4v16H3Zm6 0h4v16H9Zm6 1 4-1 3 15-4 1Z"/></svg></a>
        <a href="{{ route('backend.account') }}">{{ __('Site account') }}</a>
        <a class="nav-icon" href="{{ route('writer.settings') }}" aria-label="{{ __('Account') }}" title="{{ __('Account') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21v-2a8 8 0 0 1 16 0v2"/></svg></a>
        @endauth
        @if(request()->routeIs('articles.edit'))<button type="button" id="open-typography" title="{{ __('Typography settings') }}" aria-label="{{ __('Typography settings') }}">Aa <small id="ui-scale-label">100%</small></button>@endif
        <details class="theme-switcher" id="theme-picker">
            <summary id="theme-current" aria-label="{{ __('Appearance: :name', ['name' => __(auth()->user()->theme ?? 'paper')]) }}" title="{{ __('Change appearance') }}">@include('writer.partials.theme-icon', ['mode' => auth()->user()->theme ?? 'paper'])</summary>
            <div class="theme-menu" role="group" aria-label="{{ __('Appearance') }}">
                @foreach (['light' => 'Light', 'dark' => 'Dark', 'paper' => 'Paper'] as $mode => $label)
                <button type="button" id="theme-{{ $mode }}" data-theme-choice="{{ $mode }}" aria-label="{{ __(':name mode', ['name' => __($label)]) }}" aria-pressed="{{ (auth()->user()->theme ?? 'paper') === $mode ? 'true' : 'false' }}">@include('writer.partials.theme-icon', ['mode' => $mode])<span>{{ __($label) }}</span></button>
                @endforeach
            </div>
        </details>
    </nav>
</header>
<div id="toast" role="status" aria-live="polite" hidden></div>
@if ($errors->any()) <div class="server-errors" role="alert">@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div> @endif
@if (session('status')) <div class="server-errors">{{ session('status') }}</div> @endif
@yield('content')
</body></html>
