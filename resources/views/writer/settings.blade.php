@extends('writer.layouts.portal')
@section('title', __('Account · ') . config('app.name'))
@section('portal-content')
    <main class="settings-page">
        <div class="eyebrow">{{ __('YOUR WRITING PRACTICE') }}</div>
        <h1>{{ __('Account & AI allowance') }}</h1>
        <section class="profile-settings">@include('writer.partials.profile-settings', ['user' => auth()->user()])</section>
        <section class="panel account-images" aria-labelledby="account-images-title">
            <div class="account-images-heading">
                <h2 id="account-images-title"><a href="{{ route('backend.images') }}">Görsellerim</a></h2><a
                    href="{{ route('backend.images') }}">Tüm görselleri yönet ↗</a>
            </div>
            <div class="account-image-stats"><span><strong>{{ number_format($imageCounts->sum()) }}</strong> toplam
                    görsel</span><span><strong>{{ number_format($imageCounts->get('upload', 0)) }}</strong>
                    yüklenen</span><span><strong>{{ number_format($imageCounts->get('generated', 0)) }}</strong> zekai ile
                    oluşturulan</span></div>
            @if ($latestImages->isNotEmpty())
                <h3>Son görseller</h3>
                <div class="account-image-grid">
                    @foreach ($latestImages as $image)
                        <a href="{{ route('backend.images') }}" class="account-image-item"><img
                                src="{{ asset('storage/' . ($image->image_type === 'upload' ? 'upload-images' : 'ai-images') . '/medium/' . $image->image_medium_filename) }}"
                                alt="{{ $image->image_alt ?: 'Görsel' }}"
                                loading="lazy"><span>{{ $image->image_type === 'upload' ? 'Yüklenen' : 'Zekai' }} ·
                                {{ $image->created_at?->format('d.m.Y') }}</span></a>
                    @endforeach
                </div>
            @else
                <p class="muted">Henüz görseliniz yok. Görsellerim sayfasından görsel yükleyebilir veya zekai ile
                    oluşturabilirsiniz.</p>
            @endif
        </section>
        @if (auth()->user()->isAdmin())
            <section class="panel">
                <h2>Yönetim</h2><a class="toolbar-link" href="{{ route('writer.budgets.index') }}">Zekai kotaları ↗</a>
            </section>
        @endif
        <div class="settings-grid">
            <section class="panel">
                <h2>{{ __('Your OpenRouter key') }}</h2>
                <p class="muted">
                    {{ __('Use your own key for continued AI assistance. It is encrypted at rest and never returned to your browser.') }}
                </p>
                <p>{{ auth()->user()->openrouter_key ? __('A personal key is saved.') : __('Using the demo allowance when available.') }}
                </p>
                <form method="post" action="{{ route('writer.settings.update') }}">@csrf @method('PATCH')<label
                        for="api-key">{{ __('Personal API key') }}</label><input id="api-key" type="password"
                        name="openrouter_key" autocomplete="off" placeholder="{{ __('Enter a key to save or replace') }}"
                        required><button class="primary">{{ __('Save API key') }}</button></form>
                <form method="post" action="{{ route('writer.settings.update') }}">@csrf @method('PATCH')<input
                        type="hidden" name="openrouter_key" value=""><button
                        class="quiet">{{ __('Remove saved key') }}</button></form>
            </section>
            <section class="panel">
                <h2>{{ __('Demo allowance') }}</h2>
                <div class="large-number">{{ \App\Writer\Services\DemoBudget::percentage(auth()->user()) }}%</div><progress
                    max="100" value="{{ \App\Writer\Services\DemoBudget::percentage(auth()->user()) }}"></progress>
                <p>{{ __('Remaining AI allowance') }}</p>
                <small>{{ __('Classification and writing both count. Uncertain provider charges remain reserved until reconciled.') }}</small>
            </section>
        </div>
        <p><a href="{{ route('backend.close-account') }}">Hesabı kapat</a></p>
        <form class="account-sign-out" action="{{ route('logout') }}" method="post">@csrf<button
                type="submit">{{ __('Sign out') }}</button></form>
    </main>
@endsection
