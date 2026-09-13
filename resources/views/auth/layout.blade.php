<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') · İzEdebiyat</title>
    <link rel="icon" href="/favicon-32x32.png">
    <link rel="stylesheet" href="{{ asset('css/auth-pages.css') }}?v={{ filemtime(public_path('css/auth-pages.css')) }}">
    @stack('head')
</head>

<body>
    <a class="skip-link" href="#auth-form">{{ __('auth-page.skip') }}</a>
    <main class="auth-shell">
        <aside class="auth-story">
            <a class="auth-logo" href="{{ url('/') }}" aria-label="İzEdebiyat"><img
                    src="{{ asset('assets/images/logo/logo-large.png') }}" alt="İzEdebiyat" width="180"></a>
            <div class="story-copy"><span class="eyebrow">{{ __('auth-page.eyebrow') }}</span>
                <h1>@yield('story-title')</h1>
                <p>@yield('story-copy')</p>
                <div class="story-rule" aria-hidden="true"><span>❧</span></div>
                <div class="story-notes">
                    <span>{{ __('auth-page.read') }}</span><span>{{ __('auth-page.write') }}</span><span>{{ __('auth-page.share') }}</span>
                </div>
            </div>
            <div class="story-footer"><span>© {{ date('Y') }}
                    İzEdebiyat</span><span>{{ __('auth-page.footer') }}</span></div>
            <div class="page-art" aria-hidden="true"></div>
        </aside>
        <section class="auth-content" aria-labelledby="form-title">
            <nav class="auth-top"><a href="{{ url('/') }}">←
                    {{ __('auth-page.back') }}</a><span>@yield('switch')</span></nav>
            <div class="auth-form-wrap" id="auth-form" tabindex="-1">
                <div class="eyebrow">@yield('kicker')</div>
                <h2 id="form-title">@yield('title')</h2>
                <p class="form-intro">@yield('intro')</p>
                @if (session('status'))
                    <div class="status-message" role="status">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div class="error-summary" role="alert">
                        <p>{{ __('auth-page.errors') }}</p>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('form')
            </div>
            <footer class="auth-help"><a
                    href="{{ route('account-recovery.create') }}">{{ __('auth-page.recovery') }}</a><span
                    aria-hidden="true">·</span><a
                    href="{{ route('frontend.secrecy') }}">{{ __('auth-page.privacy') }}</a></footer>
        </section>
    </main>
    <script>
        document.querySelectorAll('[data-password-toggle]').forEach(button => {
            button.addEventListener('click', () => {
                const input = document.getElementById(button.dataset.passwordToggle);
                const visible = input.type === 'password';
                input.type = visible ? 'text' : 'password';
                button.textContent = visible ? button.dataset.hide : button.dataset.show;
                button.setAttribute('aria-pressed', String(visible));
            });
        });
    </script>
</body>

</html>
