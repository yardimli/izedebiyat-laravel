<?php

namespace Tests\Feature;

use App\Models\LlmSetting;
use App\Models\User;
use App\Writer\Services\DefaultModel;
use App\Writer\Services\ModelCatalog;
use App\Writer\Services\OpenRouter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Tests\WriterTestCase;

class WriterModelPolicyTest extends WriterTestCase
{
    private function catalog(array $allowed = ['p/1', 'p/2', 'p/3']): void
    {
        LlmSetting::updateOrCreate(['id' => 1], ['allowed_frontend_models' => $allowed]);
        $models = [];
        foreach ([4, 1, 3, 2] as $price) {
            $models[] = ['id' => 'p/'.$price, 'name' => 'Model '.$price,
                'architecture' => ['output_modalities' => ['text']],
                'pricing' => ['prompt' => $price / 1000000, 'completion' => $price / 1000000]];
        }
        Cache::forever('openrouter.catalog', ['data' => $models, 'refreshed_at' => null]);
    }

    public function test_picker_returns_only_approved_models_and_middle_price_default(): void
    {
        $this->catalog();
        $this->actingAs(User::factory()->create())->getJson('/yazi-atolyesi/api/models')
            ->assertOk()->assertJsonCount(3, 'data')->assertJsonPath('default_model', 'p/2');
        $this->assertSame(['p/1', 'p/2', 'p/3'], array_column(app(ModelCatalog::class)->get()['data'], 'id'));
        LlmSetting::first()->update(['allowed_frontend_models' => ['p/1', 'p/3']]);
        $this->assertSame('p/1', app(ModelCatalog::class)->get()['default_model']);
    }

    public function test_unapproved_models_cannot_be_saved_or_resolved_for_chat(): void
    {
        $this->catalog();
        $this->actingAs(User::factory()->create())->patchJson('/yazi-atolyesi/hesap', ['selected_model' => 'p/4'])->assertUnprocessable();
        Http::fake();
        try {
            app(OpenRouter::class)->model('p/4');
            $this->fail('Unapproved model was accepted.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('model', $e->errors());
        }
        Http::assertNothingSent();
    }

    public function test_default_replaces_removed_selection_and_preserves_approved_manual_choice(): void
    {
        $this->catalog();
        $user = User::factory()->create(['selected_model' => 'p/4', 'model_selected_at' => now()]);
        $request = Request::create('/');
        $request->setLaravelSession(app('session.store'));
        $request->setUserResolver(fn () => $user);
        app(DefaultModel::class)->initialize($request, app(ModelCatalog::class));
        $this->assertSame('p/2', $user->fresh()->selected_model);
        $user->forceFill(['selected_model' => 'p/3', 'model_selected_at' => now()])->save();
        app(DefaultModel::class)->initialize($request, app(ModelCatalog::class));
        $this->assertSame('p/3', $user->fresh()->selected_model);
    }

    public function test_no_admin_selection_exposes_no_models(): void
    {
        $this->catalog([]);
        $result = app(ModelCatalog::class)->get();
        $this->assertSame([], $result['data']);
        $this->assertNull($result['default_model']);
    }
}
