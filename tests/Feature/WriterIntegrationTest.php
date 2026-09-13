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

    public function test_featured_image_paths_are_not_prefixed_twice_and_generated_images_are_owner_scoped(): void
    {
        $user = User::factory()->create();
        $book = $this->book($user);
        $path = '/storage/upload-images/medium/3867c900_medium.jfif';
        $book->featured_image = $path;
        foreach (['getOriginalUrl', 'getLargeUrl', 'getMediumUrl', 'getSmallUrl'] as $method) {
            $this->assertSame(asset(ltrim($path, '/')), $book->$method());
        }
        $book->featured_image = 'https://www.izedebiyat.com'.$path;
        $this->assertSame($book->featured_image, $book->getOriginalUrl());
        $book->featured_image = 'upload.jpg';
        $this->assertSame(asset('storage/upload-images/original/upload.jpg'), $book->getOriginalUrl());
        $image = \App\Models\Image::create(['user_id' => $user->id, 'image_type' => 'generated', 'image_guid' => 'image-guid', 'image_alt' => '', 'image_original_filename' => 'ai.jpg', 'image_large_filename' => 'ai_large.jpg', 'image_medium_filename' => 'ai_medium.jpg', 'image_small_filename' => 'ai_small.jpg']);
        $url = '/yazi-atolyesi/api/books/'.$book->id;
        $this->actingAs($user)->patchJson($url, ['revision' => 1, 'featured_image' => '/storage/ai-images/medium/ai_medium.jpg'])->assertOk();
        $this->assertSame(asset('storage/ai-images/medium/ai_medium.jpg'), $book->fresh()->getOriginalUrl());
        $this->patchJson($url, ['revision' => 2, 'featured_image' => 'https://unrelated.example/storage/ai-images/medium/ai_medium.jpg'])->assertUnprocessable();
        $image->update(['user_id' => User::factory()->create()->id]);
        $this->patchJson($url, ['revision' => 2, 'featured_image' => '/storage/ai-images/large/ai_large.jpg'])->assertUnprocessable();
    }

    public function test_dashboard_publication_requires_category_and_details_save_preserves_publication(): void
    {
        $user = User::factory()->create();
        $book = $this->book($user);
        $url = '/yazi-atolyesi/api/books/'.$book->id;
        $this->actingAs($user)->patchJson($url, ['revision' => 1, 'is_published' => true])->assertUnprocessable();
        $parent = Category::create(['category_name' => 'Edebiyat', 'slug' => 'edebiyat']);
        $category = Category::create(['category_name' => 'Deneme', 'slug' => 'deneme', 'parent_category_id' => $parent->id]);
        $this->patchJson($url, ['revision' => 1, 'category_id' => $category->id])->assertOk();
        $this->patchJson($url, ['revision' => 2, 'is_published' => true])->assertOk();
        $this->patchJson($url, ['revision' => 3, 'subtitle' => 'Yeni alt başlık'])->assertOk();
        $this->assertTrue($book->fresh()->is_published);
        $this->patchJson($url, ['revision' => 3, 'is_published' => false])->assertConflict();
        $this->patchJson($url, ['revision' => 4, 'is_published' => false])->assertOk();
        $this->assertFalse($book->fresh()->is_published);
    }

    public function test_library_search_matches_title_and_short_description_only_for_the_owner(): void
    {
        $owner = User::factory()->create();
        $title = $this->book($owner); $title->update(['title' => 'Deniz feneri']);
        $description = $this->book($owner); $description->update(['title' => 'Bir akşam', 'subheading' => 'Deniz kıyısında bir akşam']);
        $unmatched = $this->book($owner); $unmatched->update(['title' => 'Dağ yolu', 'subtitle' => 'Deniz']);
        $private = $this->book(User::factory()->create()); $private->update(['subheading' => 'Deniz']);
        $deleted = $this->book($owner); $deleted->update(['title' => 'Deniz', 'deleted' => 1]);
        $response = $this->actingAs($owner)->get('/eserlerim?q=Deniz')->assertOk();
        $this->assertEqualsCanonicalizing([$title->id, $description->id], $response->viewData('books')->pluck('id')->all());
        $this->assertSame('Deniz', $response->viewData('search'));
        $this->get('/eserlerim?q=no-match')->assertOk()->assertSee('No matching works.')->assertSee('Clear search');
        $title->update(['title' => '100%_tam']);
        $this->assertSame([$title->id], $this->get('/eserlerim?q='.urlencode('%_'))->assertOk()->viewData('books')->pluck('id')->all());
        $this->assertSame(3, $this->get('/eserlerim?q=%20%20')->assertOk()->viewData('books')->total());
        $this->getJson('/eserlerim?q[]=invalid')->assertUnprocessable();
    }

    public function test_numbered_library_pagination_retains_search_and_has_stable_page_boundaries(): void
    {
        $owner = User::factory()->create();
        $ids = [];
        for ($i = 0; $i < 61; $i++) {
            $book = $this->book($owner);
            $book->update(['title' => 'Match '.$i, 'updated_at' => '2026-09-01 12:00:00']);
            $ids[] = $book->id;
        }
        $this->book($owner)->update(['title' => 'Unrelated']);
        $response = $this->actingAs($owner)->get('/eserlerim?q=Match&page=2')->assertOk();
        $paginator = $response->viewData('books');
        $this->assertSame(61, $paginator->total());
        $this->assertSame(3, $paginator->lastPage());
        $this->assertSame(array_slice(array_reverse($ids), 30, 30), $paginator->pluck('id')->all());
        $response->assertSee('aria-current="page"', false)->assertSee('Page 3')->assertSee('First page')->assertSee('Last page')->assertSee('Showing 31–60 of 61 works');
        parse_str(parse_url($paginator->url(3), PHP_URL_QUERY), $query);
        $this->assertSame(['q' => 'Match', 'page' => '3'], $query);
        $this->get('/eserlerim?q=Match&page=3')->assertOk()->assertSee('Showing 61–61 of 61 works');
    }

    public function test_admin_budget_sorting_matches_displayed_values_and_validates_columns(): void
    {
        $admin = User::factory()->create(['member_type' => 1]);
        $a = User::factory()->create(['name' => 'Budget A', 'email' => 'z@example.com']);
        $b = User::factory()->create(['name' => 'Budget B', 'email' => 'a@example.com']);
        $a->forceFill(['demo_limit' => null, 'demo_allowance' => null, 'demo_spent' => 0.25, 'demo_reserved' => 0.25])->save();
        $b->forceFill(['demo_limit' => 5, 'demo_allowance' => 2, 'demo_spent' => 3, 'demo_reserved' => 0.1])->save();
        AiCall::create(['user_id' => $a->id, 'book_id' => $this->book($a)->id, 'model' => 'test/writer', 'stage' => 'chat', 'funding' => 'demo', 'cost' => 7]);
        $this->actingAs($admin);
        $url = '/yazi-atolyesi/admin/budgets?search=Budget&';
        foreach (['name' => $a->id, 'email' => $b->id, 'total_spent' => $b->id, 'demo_spent' => $a->id, 'pending' => $b->id, 'limit' => $a->id, 'remaining' => $a->id, 'percentage' => $a->id] as $sort => $first) {
            $ascending = $this->get($url.'sort='.$sort.'&direction=asc')->assertOk()->viewData('users');
            $this->assertSame($first, $ascending->first()->id, $sort);
            $descending = $this->get($url.'sort='.$sort.'&direction=desc')->assertOk()->viewData('users');
            $this->assertSame(array_reverse($ascending->pluck('id')->all()), $descending->pluck('id')->all());
            foreach ($ascending as $user) {
                $this->assertEquals(DemoBudget::remaining($user), $user->writer_remaining);
                $this->assertEquals(DemoBudget::percentage($user), $user->writer_percentage);
            }
        }
        $b->forceFill(['demo_limit' => 0, 'demo_allowance' => 0])->save();
        $this->assertSame($b->id, $this->get($url.'sort=percentage')->assertOk()->viewData('users')->first()->id);
        $this->getJson($url.'sort=password')->assertUnprocessable();
        $this->getJson($url.'direction=invalid')->assertUnprocessable();
        $this->getJson($url.'per_page=1000')->assertUnprocessable();
        $this->actingAs($a)->get($url.'sort=total_spent')->assertForbidden();
    }

    public function test_admin_budget_pagination_preserves_search_sort_and_page_size(): void
    {
        $admin = User::factory()->create(['member_type' => 1]);
        for ($i = 0; $i < 27; $i++) User::factory()->create(['name' => 'Pagination member '.str_pad((string) $i, 2, '0', STR_PAD_LEFT)]);
        $response = $this->actingAs($admin)->get('/yazi-atolyesi/admin/budgets?search=Pagination&sort=name&direction=desc&per_page=25&page=2')->assertOk();
        $users = $response->viewData('users');
        $this->assertSame(27, $users->total());
        $this->assertCount(2, $users);
        $this->assertSame('Pagination member 01', $users->first()->name);
        parse_str(parse_url($users->url(1), PHP_URL_QUERY), $query);
        $this->assertSame(['search'=>'Pagination', 'sort'=>'name', 'direction'=>'desc', 'per_page'=>'25', 'page'=>'1'], $query);
        $response->assertSee('Showing 26–27 of 27 members')->assertSee('aria-sort="descending"', false)->assertSee('First page')->assertSee('Last page');
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
