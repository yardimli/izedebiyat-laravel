<?php

namespace Tests\Feature;

use App\Http\Middleware\LanguageManager;
use Illuminate\Http\Request;
use Tests\TestCase;

class TurkishFallbackMessagesTest extends TestCase
{
    public function test_legacy_turkish_session_resolves_json_and_php_messages(): void
    {
        config(['app.locale' => 'en']);
        session()->put('locale', 'tr_TR');

        (new LanguageManager)->handle(Request::create('/'), function () {
            $this->assertSame('tr', app()->getLocale());
            $this->assertSame('Bu sayfa, ilk başlığının doğmasını bekliyor.', __('This page is still waiting for its first title.'));
            $this->assertSame('Yazarın kendi öyküsü, şimdilik satır aralarında dinleniyor.', __('The author’s own story is still resting between the lines.'));
            $this->assertSame('Henüz yorum yok', __('default.No comments yet'));
            $this->assertSame('Kategori görseli bulunamadı. Kategori numarası: 42', __('Category image not found for category id: :id', ['id' => 42]));
            $this->assertSame('Görsel yüklenemedi. Lütfen tekrar deneyin.', __('Image upload failed. Please try again.'));
            return response('ok');
        });
    }

    public function test_default_locale_comes_from_configuration_without_a_session_preference(): void
    {
        session()->forget('locale');
        config(['app.locale' => 'tr_TR']);

        (new LanguageManager)->handle(Request::create('/'), function () {
            $this->assertSame('tr', app()->getLocale());
            $this->assertSame('Oturum bulunamadı', __('Session not found'));
            return response('ok');
        });
    }

    public function test_english_preference_is_preserved(): void
    {
        session()->put('locale', 'en_US');

        (new LanguageManager)->handle(Request::create('/'), function () {
            $this->assertSame('en', app()->getLocale());
            $this->assertSame('This page is still waiting for its first title.', __('This page is still waiting for its first title.'));
            return response('ok');
        });
    }
}
