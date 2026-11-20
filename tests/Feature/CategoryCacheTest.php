<?php

namespace Tests\Feature;

use App\Http\Controllers\AccountRecoveryController;
use App\View\Composers\MainMenuComposer;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CategoryCacheTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array']);
        DB::purge('sqlite');
        Schema::create('categories', function (Blueprint $t) {
            $t->id();
            $t->integer('parent_category_id')->default(0);
            $t->string('slug')->default('category');
            $t->integer('total_articles')->default(0);
            $t->integer('new_articles')->default(0);
            $t->integer('read_count')->default(0);
            $t->timestamps();
        });
        Schema::create('articles', function (Blueprint $t) {
            $t->id();
            $t->integer('category_id')->nullable();
            $t->integer('parent_category_id')->nullable();
            $t->integer('approved')->default(1);
            $t->integer('is_published')->default(1);
            $t->integer('deleted')->default(0);
            $t->integer('moderation_flagged')->default(0);
            $t->integer('read_count')->default(10);
            $t->timestamps();
        });
    }

    public function test_refresh_preserves_count_filters_and_does_not_double_count(): void
    {
        DB::table('categories')->insert([['id' => 1], ['id' => 2], ['id' => 3]]);
        foreach ([[], ['category_id' => 1], ['moderation_flagged' => 1], ['deleted' => 1], ['created_at' => now()->subDays(40)]] as $overrides) {
            DB::table('articles')->insert($overrides + ['category_id' => 2, 'parent_category_id' => 1, 'created_at' => now()]);
        }
        $this->mock(MainMenuComposer::class)->shouldReceive('refresh')->once();
        $this->artisan('categories:refresh-cache')->assertSuccessful();
        $parent = DB::table('categories')->find(1);
        $this->assertSame(3, $parent->total_articles);
        $this->assertSame(2, $parent->new_articles);
        $this->assertSame(40, $parent->read_count);
        $this->assertSame(2, DB::table('categories')->find(2)->total_articles);
        $this->assertSame(0, DB::table('categories')->find(3)->total_articles);
        $lock = Cache::lock('categories:refresh-cache', 3600);
        $this->assertTrue($lock->get());
        $lock->release();
    }

    public function test_controller_construction_and_cold_menu_do_not_query_articles(): void
    {
        DB::table('categories')->insert(['id' => 1, 'total_articles' => 7]);
        DB::enableQueryLog();
        new AccountRecoveryController();
        $this->assertSame([], DB::getQueryLog());
        $view = view('frontend.account_recovery_create');
        (new MainMenuComposer())->compose($view);
        $this->assertSame(7, $view->getData()['mainMenuCategories']->first()->menu_total);
        foreach (DB::getQueryLog() as $query) {
            $this->assertStringNotContainsString('articles', $query['query']);
        }
    }

    public function test_cached_menu_stays_available_past_an_hour_without_queries(): void
    {
        $menu = array_fill_keys(['categories', 'recentArticles', 'topArticles', 'forumTags', 'recentDiscussions', 'recentBookReviews'], collect());
        Cache::forever(MainMenuComposer::CACHE_KEY, $menu);
        $this->travel(2)->hours();
        DB::enableQueryLog();
        (new MainMenuComposer())->compose(view('frontend.account_recovery_create'));
        $this->assertSame([], DB::getQueryLog());
    }

    public function test_refresh_lock_is_released_after_failure_and_old_cache_survives(): void
    {
        Cache::forever(MainMenuComposer::CACHE_KEY, ['previous' => true]);
        $this->mock(MainMenuComposer::class)->shouldReceive('refresh')->once()->andThrow(new \RuntimeException('refresh failed'));
        try {
            $this->artisan('categories:refresh-cache')->run();
            $this->fail('Expected refresh failure');
        } catch (\RuntimeException $exception) {
            $this->assertSame('refresh failed', $exception->getMessage());
        }
        $this->assertSame(['previous' => true], Cache::get(MainMenuComposer::CACHE_KEY));
        $lock = Cache::lock('categories:refresh-cache', 3600);
        $this->assertTrue($lock->get());
        $lock->release();
    }
}
