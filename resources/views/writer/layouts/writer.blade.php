<!DOCTYPE html>
<html data-bs-theme="{{ (auth()->user()->theme ?? 'paper') === 'dark' ? 'dark' : 'light' }}"
    lang="{{ app()->getLocale() }}" data-theme="{{ auth()->user()->theme ?? 'paper' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    @hasSection('portal')
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}">
        <link rel="stylesheet" href="{{ asset('css/easymde.min.css') }}">
        <script src="{{ asset('js/jquery.min.js') }}"></script>
    @endif
    @vite(['resources/css/writer.css', 'resources/js/writer/app.js'])
    @if (config('app.favicon'))
        <link rel="icon" href="{{ asset(config('app.favicon')) }}">
    @endif
    <meta name="robots" content="noindex,nofollow">
    @include('writer.partials.translations')
    @stack('styles')
    @hasSection('portal')
        <link rel="stylesheet"
            href="{{ asset('css/writer-portal.css') }}?v={{ filemtime(public_path('css/writer-portal.css')) }}">
    @endif
</head>

<body class="{{ $__env->hasSection('portal') ? 'writer-portal' : '' }}" data-app-name="{{ config('app.name') }}"
    data-user="{{ auth()->id() }}">
    <header class="site-header">
        @include('writer.partials.brand')
        <nav aria-label="{{ __('Main navigation') }}">
            @auth
                <a class="nav-icon" href="{{ route('articles.index') }}" aria-label="{{ __('My library') }}"
                    title="{{ __('My library') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M3 4h4v16H3Zm6 0h4v16H9Zm6 1 4-1 3 15-4 1Z" />
                    </svg></a>
                @unless (request()->routeIs('articles.edit'))
                    <a class="nav-icon" href="{{ route('backend.following') }}" aria-label="Favorilerim"
                        title="Favorilerim"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                            aria-hidden="true">
                            <path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9Z" />
                        </svg></a>
                    <a class="nav-icon" href="{{ route('chat') }}" aria-label="Sohbet" title="Sohbet"><svg viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                            <path d="M21 11a8 8 0 0 1-8 8H7l-5 3 2-6a8 8 0 1 1 17-5Z" />
                        </svg></a>
                    <a class="nav-icon" href="{{ route('writer.settings') }}" aria-label="{{ __('Account') }}"
                        title="{{ __('Account') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.6" stroke-linecap="round" aria-hidden="true">
                            <circle cx="12" cy="8" r="4" />
                            <path d="M4 21v-2a8 8 0 0 1 16 0v2" />
                        </svg></a>
                    @if (auth()->user()->isAdmin())
                        @include('writer.partials.admin-menu')
                    @endif
                @endunless
            @endauth
            @if (request()->routeIs('articles.edit'))
                <button type="button" id="open-typography" title="{{ __('Typography settings') }}"
                    aria-label="{{ __('Typography settings') }}">Aa <small id="ui-scale-label">100%</small></button>
            @endif
            <details class="theme-switcher" id="theme-picker">
                <summary id="theme-current"
                    aria-label="{{ __('Appearance: :name', ['name' => __(auth()->user()->theme ?? 'paper')]) }}"
                    title="{{ __('Change appearance') }}">@include('writer.partials.theme-icon', ['mode' => auth()->user()->theme ?? 'paper'])</summary>
                <div class="theme-menu" role="group" aria-label="{{ __('Appearance') }}">
                    @foreach (['light' => 'Light', 'dark' => 'Dark', 'paper' => 'Paper'] as $mode => $label)
                        <button type="button" id="theme-{{ $mode }}" data-theme-choice="{{ $mode }}"
                            aria-label="{{ __(':name mode', ['name' => __($label)]) }}"
                            aria-pressed="{{ (auth()->user()->theme ?? 'paper') === $mode ? 'true' : 'false' }}">@include('writer.partials.theme-icon', ['mode' => $mode])<span>{{ __($label) }}</span></button>
                    @endforeach
                </div>
            </details>
        </nav>
    </header>
    @include('partials.impersonation-banner')
    <div id="toast" role="status" aria-live="polite" hidden></div>
    @if ($errors->any())
        <div class="server-errors" role="alert">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif
    @if (session('status'))
        <div class="server-errors">{{ session('status') }}</div>
    @endif
    @if (session('success'))
        <div class="server-errors" role="status">{{ session('success') }}</div>
    @endif
    @yield('content')
    @hasSection('portal')
        <script src="{{ asset('assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    @endif
    @stack('scripts')
</body>

</html>
