<div class="auth-field">
    <label for="{{ $name }}">{{ $label }}</label>
    <div class="input-wrap">
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type ?? 'text' }}"
            autocomplete="{{ $autocomplete ?? $name }}"
            @if (($type ?? 'text') !== 'password') value="{{ old($name) }}" @endif
            @if (isset($max)) maxlength="{{ $max }}" @endif
            @if (isset($min)) minlength="{{ $min }}" @endif required
            @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror>
        @if (($type ?? 'text') === 'password')
            <button class="password-toggle" type="button" data-password-toggle="{{ $name }}"
                data-show="{{ __('auth-page.show') }}" data-hide="{{ __('auth-page.hide') }}"
                aria-controls="{{ $name }}" aria-pressed="false">{{ __('auth-page.show') }}</button>
        @endif
    </div>
    @error($name)
        <small class="field-error" id="{{ $name }}-error">{{ $message }}</small>
    @enderror
</div>
