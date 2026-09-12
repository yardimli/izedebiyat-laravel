<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Writer\Models\AiCall;
use App\Writer\Models\Book;
use App\Writer\Services\DemoBudget;
use App\Writer\Services\Manuscript;
use App\Writer\Services\ManuscriptHtml;
use App\Writer\Services\MigrateArticles;
use App\Writer\Services\OpenRouter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\WriterTestCase;

class WriterIntegrationTest extends WriterTestCase
{
    public function test_schema_migration_seeds_existing_accounts_from_configuration_and_rolls_back_without_losing_articles(): void
    {
        $user = User::factory()->create();
        $book = $this->book($user);
        $migration = require database_path('migrations/2026_09_12_100000_add_total_author_workspace.php');
        $migration->down();
        $this->assertSame('İlk metin', DB::table('articles')->find($book->id)->main_text);
        config(['writer.demo_limit' => 2.25]);
        $migration->up();
        $this->assertEquals(2.25, $user->fresh()->demo_limit);
        $this->assertEquals(2.25, $user->fresh()->demo_allowance);
        $this->assertEquals(100, DemoBudget::percentage($user->fresh()));
        $this->artisan('writer:migrate-articles', ['--dry-run' => true])->assertSuccessful();
        $this->assertNull(DB::table('articles')->find($book->id)->document);
        (require database_path('migrations/2026_09_12_100001_convert_articles_to_writer_documents.php'))->up();
        $this->assertSame('İlk metin', Book::find($book->id)->manuscript);
    }

    public function test_new_draft_creation_uses_existing_hashed_edit_url_and_keeps_public_route_handlers(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('/eserlerim', ['title' => 'Yeni eser']);
        $book = Book::firstOrFail();
        $response->assertRedirect(route('articles.edit', \App\Helpers\IdHasher::encode($book->id)));
        $this->assertFalse($book->is_published);
        $this->assertSame('App\\Http\\Controllers\\FrontendController@article', app('router')->getRoutes()->getByName('article')->getActionName());
        $this->assertSame('App\\Http\\Controllers\\ArticleController@recordRead', app('router')->getRoutes()->getByName('article.read')->getActionName());
        $this->assertSame('App\\Http\\Controllers\\CommentController@store', app('router')->getRoutes()->getByName('comments.store')->getActionName());
    }

    private function book(User $user): Book
    {
        return Book::create(['user_id' => $user->id, 'title' => 'Türkçe eser', 'document' => Manuscript::fromText('İlk metin'), 'codex_types' => ['People']]);
    }

    public function test_conversion_preserves_public_identity_content_relationships_and_timestamps_and_is_resumable(): void
    {
        $user = User::factory()->create();
        $text = "# Başlık\n\nİstanbul **güzel** ve *sessiz*.\n\n- Birinci\n- İkinci";
        $article = Article::create(['user_id' => $user->id, 'title' => 'Eski eser', 'slug' => 'kalici-adres', 'main_text' => $text, 'read_count' => 123, 'approved' => 1, 'is_published' => 1, 'category_id' => 1, 'featured_image' => 'old.jpg', 'subheading' => 'Giriş', 'subtitle' => 'Alt başlık']);
        DB::table('comments')->insert(['article_id' => $article->id, 'user_id' => $user->id, 'content' => 'Yorum']);
        DB::table('article_reads')->insert(['article_id' => $article->id]);
        $before = (array) DB::table('articles')->find($article->id);
        $this->assertSame(1, app(MigrateArticles::class)->run(true));
        $this->assertNull(DB::table('articles')->find($article->id)->document);
        $this->assertSame(1, app(MigrateArticles::class)->run());
        $book = Book::findOrFail($article->id);
        $this->assertSame($text, $book->main_text);
        $this->assertSame($text, $book->writer_original_text);
        $this->assertStringContainsString('<strong>güzel</strong>', ManuscriptHtml::html($book->document));
        foreach (['id', 'slug', 'read_count', 'approved', 'is_published', 'category_id', 'featured_image', 'subheading', 'subtitle', 'created_at', 'updated_at'] as $key) {
            $this->assertEquals($before[$key], $book->getRawOriginal($key), $key);
        }
        $this->assertCount(1, $book->comments);
        $this->assertSame(1, DB::table('article_reads')->count());
        $this->actingAs($user)->patchJson('/yazi-atolyesi/api/books/'.$book->id, ['revision' => 1, 'document' => Manuscript::fromText('Yeni yazı')])->assertOk();
        $this->assertSame(0, app(MigrateArticles::class)->run());
        $this->assertSame('Yeni yazı', $book->fresh()->main_text);
        $this->assertSame($text, $book->fresh()->writer_original_text);
    }

    public function test_publication_metadata_tags_and_original_article_are_updated_together(): void
    {
        $user = User::factory()->create();
        $book = $this->book($user);
        $parent = Category::create(['category_name' => 'Edebiyat', 'slug' => 'edebiyat']);
        $category = Category::create(['category_name' => 'Öykü', 'slug' => 'oyku', 'parent_category_id' => $parent->id]);
        $this->actingAs($user)->patchJson('/yazi-atolyesi/api/books/'.$book->id, ['revision' => 1, 'title' => 'Yeni başlık', 'subtitle' => 'Alt başlık', 'subheading' => 'Giriş yazısı', 'category_id' => $category->id, 'keywords_string' => 'şiir, kısa öykü', 'is_published' => true])->assertOk();
        $article = Article::findOrFail($book->id);
        $this->assertSame('Giriş yazısı', $article->subheading);
        $this->assertEquals(1, $article->is_published);
        $this->assertSame('Öykü', $article->category_name);
        $this->assertSame('edebiyat', $article->parent_category_slug);
        $this->assertCount(2, $article->keywords);
        $this->assertSame($book->slug, $article->slug);
        $this->patchJson('/yazi-atolyesi/api/books/'.$book->id, ['revision' => 1, 'subtitle' => 'Stale'])->assertStatus(409);
        $this->patchJson('/yazi-atolyesi/api/books/'.$book->id, ['revision' => 2, 'is_published' => false, 'category_id' => null, 'keywords_string' => ''])->assertOk();
        $this->assertCount(0, $article->fresh()->keywords);
    }

    public function test_hashed_routes_and_every_workspace_surface_are_owner_scoped(): void
    {
        $owner = User::factory()->create();
        $book = $this->book($owner);
        $other = User::factory()->create();
        $hash = \App\Helpers\IdHasher::encode($book->id);
        $this->actingAs($other)->get('/eserlerim/'.$hash.'/duzenle')->assertNotFound();
        $this->delete('/eserlerim/'.$hash)->assertNotFound();
        $this->post('/yazi-atolyesi/books/'.$book->id.'/recover')->assertNotFound();
        $this->get('/yazi-atolyesi/admin/budgets')->assertForbidden();
        $this->post('/yazi-atolyesi/admin/budgets/'.$owner->id.'/reset')->assertForbidden();
        $this->actingAs($owner)->get('/eserlerim')->assertOk()->assertSee('Türkçe eser');
        Http::fake(['*/models' => Http::response(['data' => []])]);
        $this->get('/eserlerim/'.$hash.'/duzenle')->assertOk()->assertSee('featured-image-upload')->assertSee('keywords_string');
        $this->getJson('/yazi-atolyesi/api/books/'.$book->id)->assertOk()->assertJsonStructure(['revisions', 'usage' => ['demo_percentage']])->assertJsonMissingPath('book.writer_original_text');
    }

    public function test_reset_preserves_spending_and_pending_charges_and_restores_a_full_configured_allowance(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->create(['member_type' => 1]);
        $book = $this->book($user);
        $user->forceFill(['demo_spent' => 0.8, 'demo_reserved' => 0.2, 'demo_limit' => 1, 'demo_allowance' => 1])->save();
        $call = AiCall::create(['user_id' => $user->id, 'book_id' => $book->id, 'funding' => 'demo', 'model' => 'test', 'stage' => 'execution', 'status' => 'pending', 'reserved' => 0.2]);
        config(['writer.demo_limit' => 2.5]);
        $this->actingAs($admin)->post('/yazi-atolyesi/admin/budgets/'.$user->id.'/reset')->assertRedirect();
        $user->refresh();
        $this->assertEquals(3.5, $user->demo_limit);
        $this->assertEquals(0.8, $user->demo_spent);
        $this->assertEquals(0.2, $user->demo_reserved);
        $this->assertEquals(100, DemoBudget::percentage($user));
        $this->assertEquals(2.5, DemoBudget::remaining($user));
        app(OpenRouter::class)->settle($call, 0.2);
        $user->refresh();
        $this->assertEquals(1, $user->demo_spent);
        $this->assertEquals(2.5, DemoBudget::remaining($user));
        app(OpenRouter::class)->settle($call, 0.2);
        $this->assertEquals(1, $user->fresh()->demo_spent);
        DemoBudget::reset($user, $admin);
        $this->assertEquals(3.5, $user->fresh()->demo_limit);
        $this->assertSame(2, DB::table('writer_budget_resets')->count());
        $this->get('/yazi-atolyesi/admin/budgets')->assertOk()->assertSee($user->email);
    }

    public function test_personal_key_is_encrypted_hidden_and_does_not_consume_demo_credit(): void
    {
        $user = User::factory()->create();
        $book = $this->book($user);
        $this->actingAs($user)->patchJson('/yazi-atolyesi/account', ['openrouter_key' => 'personal-secret'])->assertOk();
        $user->refresh();
        $this->assertSame('personal-secret', $user->openrouter_key);
        $this->assertNotSame('personal-secret', $user->getRawOriginal('openrouter_key'));
        $this->assertArrayNotHasKey('openrouter_key', $user->toArray());
        $user->forceFill(['demo_limit' => 1, 'demo_allowance' => 1])->save();
        config(['writer.demo_limit' => 0]);
        $model = ['id' => 'test', 'context_length' => 100000, 'pricing' => ['prompt' => 0.000001, 'completion' => 0.000002]];
        $call = app(OpenRouter::class)->reserve($user, $book, $model, [], 'execution');
        $this->assertSame('personal', $call->funding);
        app(OpenRouter::class)->settle($call, 2);
        $this->assertEquals(0, $user->fresh()->demo_spent);
        $this->get('/yazi-atolyesi/account')->assertOk()->assertSee('100%')->assertDontSee('personal-secret');
    }

    public function test_featured_image_upload_and_removal_use_the_existing_article_image_paths(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $book = $this->book($user);
        $response = $this->actingAs($user)->postJson('/yazi-atolyesi/api/books/'.$book->id.'/featured-image', ['image' => UploadedFile::fake()->image('cover.jpg')])->assertOk();
        $filename = $response->json('filename');
        foreach (['original', 'large', 'medium', 'small'] as $size) {
            Storage::disk('public')->assertExists('upload-images/'.$size.'/'.$filename);
        }
        $this->patchJson('/yazi-atolyesi/api/books/'.$book->id, ['revision' => 1, 'featured_image' => $filename])->assertOk();
        $this->assertStringEndsWith($filename, Article::find($book->id)->getLargeUrl());
        $this->patchJson('/yazi-atolyesi/api/books/'.$book->id, ['revision' => 2, 'featured_image' => '../foreign.jpg'])->assertUnprocessable();
        $this->patchJson('/yazi-atolyesi/api/books/'.$book->id, ['revision' => 2, 'featured_image' => null])->assertOk();
        $this->assertNull($book->fresh()->featured_image);
    }
}
