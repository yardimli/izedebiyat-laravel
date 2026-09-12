@extends('auth.layout')
@section('title', __('auth-page.register_title'))
@section('story-title'){{ __('auth-page.register_story') }} <em>{{ __('auth-page.register_story_em') }}</em>@endsection
@section('story-copy', __('auth-page.register_copy'))
@section('kicker', __('auth-page.join'))
@section('intro', __('auth-page.register_intro'))
@section('switch'){{ __('auth-page.member') }} <a href="{{ route('login') }}">{{ __('default.Login') }} ↗</a>@endsection
@push('head')<script src="https://www.google.com/recaptcha/api.js" async defer></script>@endpush
@section('form')
<form method="POST" action="{{ route('register') }}" class="auth-form">
@csrf
<div class="field-pair">
@include('auth.partials.field', ['name'=>'name', 'label'=>__('auth-page.name'), 'autocomplete'=>'name', 'max'=>60])
@include('auth.partials.field', ['name'=>'username', 'label'=>__('auth-page.username'), 'autocomplete'=>'username', 'max'=>60])
</div>
@include('auth.partials.field', ['name'=>'email', 'label'=>__('default.Email address'), 'type'=>'email', 'autocomplete'=>'email', 'max'=>255])
@include('auth.partials.field', ['name'=>'password', 'label'=>__('default.Password'), 'type'=>'password', 'autocomplete'=>'new-password', 'min'=>8])
@include('auth.partials.field', ['name'=>'password_confirmation', 'label'=>__('default.Confirm Password'), 'type'=>'password', 'autocomplete'=>'new-password', 'min'=>8])
<label class="check-label policy"><input type="checkbox" name="policy" value="1" required @checked(old('policy'))><span>{!! __('default.I Agree with', ['terms_url'=>route('frontend.legal'), 'privacy_url'=>route('frontend.secrecy')]) !!}</span></label>
<div class="captcha-wrap"><div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div></div>
<button class="auth-submit" type="submit">{{ __('default.Create Account') }} <span aria-hidden="true">↗</span></button>
</form>
<p class="auth-switch">{!! __('default.Already Have Account Sign In', ['login_url'=>route('login')]) !!}</p>
@endsection
