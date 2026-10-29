<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use App\Writer\Models\AiCall;
use Tests\WriterTestCase;

class AdminLlmUsageTest extends WriterTestCase
{
    private function recordCall(User $user, array $attributes = []): AiCall
    {
        return AiCall::create(array_replace([
            'user_id' => $user->id, 'model' => 'vendor/model-a', 'stage' => 'chat',
            'funding' => 'demo', 'status' => 'settled', 'prompt_tokens' => 100,
            'completion_tokens' => 20, 'total_tokens' => 120,
            'created_at' => '2026-10-20 12:00:00',
        ], $attributes));
    }

    public function test_only_admins_can_access_usage(): void
    {
        $this->get(route('admin.llm-usage.index'))->assertRedirect(route('register'));
        $this->actingAs(User::factory()->create())->get(route('admin.llm-usage.index'))->assertForbidden();
        $this->actingAs(User::factory()->create(['member_type' => 1]))
            ->get(route('admin.llm-usage.index'))->assertOk()->assertSee('LLM Kullanımı')
            ->assertSee('Bu filtrelere uygun LLM kullanım kaydı bulunamadı.');
    }

    public function test_shows_users_articles_models_and_recorded_totals_without_payloads(): void
    {
        $admin = User::factory()->create(['member_type' => 1]);
        $user = User::factory()->create(['slug' => 'writer-profile']);
        $article = Article::create(['user_id' => $user->id, 'title' => 'Example work', 'slug' => 'example-work', 'is_published' => 1, 'approved' => 1, 'deleted' => 0]);
        $this->recordCall($user, ['book_id' => $article->id, 'total_tokens' => null, 'request_payload' => ['secret' => 'private-prompt'], 'response_body' => 'private-response']);
        $this->recordCall($user, ['status' => 'pending', 'prompt_tokens' => null, 'completion_tokens' => null, 'total_tokens' => null]);
        $response = $this->actingAs($admin)->get(route('admin.llm-usage.index'));
        $response->assertOk()->assertSee($user->email)->assertSee('Example work')->assertSee('vendor/model-a')
            ->assertSee('20.10.2026 12:00:00')->assertSee(route('article', $article->slug))
            ->assertSee('Eser silinmiş veya ilişkilendirilmemiş')->assertDontSee('private-prompt')->assertDontSee('private-response')
            ->assertViewHas('summary', fn ($s) => (int) $s->calls === 2 && (int) $s->recorded === 1 && (int) $s->total_tokens === 120);
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
    }

    public function test_combined_filters_and_inclusive_dates_apply_to_rows_and_totals(): void
    {
        $user = User::factory()->create(['name' => 'Matching Writer']);
        $other = User::factory()->create();
        $article = Article::create(['user_id' => $user->id, 'title' => 'Specific work', 'slug' => 'specific-work']);
        $wanted = $this->recordCall($user, ['book_id' => $article->id, 'created_at' => '2026-10-20 23:59:59']);
        $this->recordCall($user, ['book_id' => $article->id, 'created_at' => '2026-10-21 00:00:00']);
        $this->recordCall($user, ['book_id' => $article->id, 'model' => 'vendor/model-b']);
        $this->recordCall($other, ['book_id' => $article->id]);
        $this->recordCall($user);
        $this->actingAs(User::factory()->create(['member_type' => 1]))
            ->get(route('admin.llm-usage.index', ['user' => 'Matching Writer', 'article' => 'Specific work', 'model' => 'vendor/model-a', 'from' => '2026-10-20', 'to' => '2026-10-20']))
            ->assertOk()->assertViewHas('calls', fn ($calls) => $calls->pluck('id')->all() === [$wanted->id])
            ->assertViewHas('summary', fn ($s) => (int) $s->calls === 1 && (int) $s->total_tokens === 120);
        $this->get(route('admin.llm-usage.index', ['from' => '2026-10-21', 'to' => '2026-10-20']))->assertSessionHasErrors('to');
        $this->get(route('admin.llm-usage.index', ['from' => 'invalid']))->assertSessionHasErrors('from');
    }

    public function test_pagination_keeps_filters_and_summary_covers_all_pages(): void
    {
        $user = User::factory()->create();
        for ($i = 0; $i < 51; $i++) {
            $this->recordCall($user);
        }
        $this->actingAs(User::factory()->create(['member_type' => 1]))
            ->get(route('admin.llm-usage.index', ['model' => 'vendor/model-a', 'page' => 2]))
            ->assertOk()->assertViewHas('calls', fn ($calls) => $calls->count() === 1 && str_contains($calls->url(1), 'model=vendor%2Fmodel-a'))
            ->assertViewHas('summary', fn ($s) => (int) $s->calls === 51 && (int) $s->total_tokens === 6120);
    }
}
