@extends('auth.layout')
@section('title', __('auth-page.login_title'))
@section('story-title'){{ __('auth-page.login_story') }} <em>{{ __('auth-page.login_story_em') }}</em>@endsection
@section('story-copy', __('auth-page.login_copy'))
@section('kicker', __('auth-page.welcome'))
@section('intro', __('auth-page.login_intro'))
@section('switch'){{ __('auth-page.new_here') }} <a href="{{ route('register') }}">{{ __('default.Sign up') }}
    ↗</a>@endsection
@section('form')
    <a href="{{ url('login/google') }}" class="google-button"><img src="{{ asset('assets/v2/images/png_icons/google.png') }}"
            width="20" height="20" alt="">{{ __('default.Log in with Google') }}</a>
    <div class="auth-divider"><span>{{ __('default.Or') }}</span></div>
    <form method="POST" action="{{ route('login') }}" class="auth-form">
        @csrf<input type="hidden" name="login2" value="true">
        @include('auth.partials.field', [
            'name' => 'email',
            'label' => __('default.Email address'),
            'type' => 'email',
            'autocomplete' => 'email',
        ])
        @include('auth.partials.field', [
            'name' => 'password',
            'label' => __('default.Password'),
            'type' => 'password',
            'autocomplete' => 'current-password',
        ])
        <div class="form-options"><label class="check-label"><input type="checkbox" name="remember" value="1"
                    @checked(old('remember'))>{{ __('default.Remember me?') }}</label><a
                href="{{ url('/password/reset') }}">{{ __('default.Forgot password?') }}</a></div>
        <button class="auth-submit" type="submit">{{ __('default.Login') }} <span aria-hidden="true">↗</span></button>
    </form>
    <p class="auth-switch">{{ __('default.Not a member yet?') }} <a
            href="{{ route('register') }}">{{ __('default.Sign up') }}</a></p>
@endsection
