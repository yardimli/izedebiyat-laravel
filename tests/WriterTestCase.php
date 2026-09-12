<?php

namespace Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

/** Legacy production schema fixture; never runs historical import migrations. */
abstract class WriterTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array', 'session.driver' => 'array', 'hashing.bcrypt.rounds' => 10, 'writer.demo_limit' => 1.0, 'writer.openrouter_key' => 'test-demo-key']);
        DB::purge('sqlite');
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());
        Http::preventStrayRequests();
        $this->withoutVite();
        $this->withSession(['locale' => 'en_US']);
        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('email')->unique();
            $t->string('password');
            $t->string('slug')->nullable();
            $t->integer('member_type')->default(0);
            $t->string('avatar')->nullable();
            $t->timestamp('email_verified_at')->nullable();
            $t->rememberToken();
            $t->timestamps();
        });
        Schema::create('articles', function (Blueprint $t) {
            $t->id();
            $t->integer('user_id');
            $t->string('title');
            $t->string('slug')->unique();
            foreach (['subtitle', 'subheading', 'featured_image', 'keywords_string', 'name', 'name_slug', 'category_name', 'category_slug', 'parent_category_name', 'parent_category_slug'] as $name) {
                $t->string($name)->nullable();
            }
            $t->longText('main_text')->default('');
            $t->boolean('markdown')->default(1);
            $t->boolean('is_published')->default(0);
            $t->boolean('approved')->default(1);
            $t->boolean('deleted')->default(0);
            $t->integer('category_id')->nullable();
            $t->integer('parent_category_id')->default(0);
            $t->integer('read_count')->default(0);
            $t->boolean('has_changed')->default(0);
            $t->timestamps();
        });
        Schema::create('categories', function (Blueprint $t) {
            $t->id();
            $t->string('category_name');
            $t->string('slug');
            $t->integer('parent_category_id')->default(0);
            $t->timestamps();
        });
        Schema::create('keywords', function (Blueprint $t) {
            $t->id();
            $t->string('keyword')->unique();
            $t->string('keyword_slug');
            $t->integer('count')->default(0);
            $t->timestamps();
        });
        Schema::create('article_keyword', function (Blueprint $t) {
            $t->integer('article_id');
            $t->integer('keyword_id');
            $t->unique(['article_id', 'keyword_id']);
        });
        Schema::create('comments', function (Blueprint $t) {
            $t->id();
            $t->integer('article_id');
            $t->integer('user_id');
            $t->text('content');
            $t->timestamps();
        });
        Schema::create('article_reads', function (Blueprint $t) {
            $t->id();
            $t->integer('article_id');
            $t->timestamps();
        });
        Schema::create('images', function (Blueprint $t) {
            $t->id();
            $t->integer('user_id');
            foreach (['image_type', 'image_guid', 'image_alt', 'image_original_filename', 'image_large_filename', 'image_medium_filename', 'image_small_filename'] as $name) {
                $t->string($name);
            }$t->timestamps();
        });
        (require database_path('migrations/2026_09_12_100000_add_total_author_workspace.php'))->up();
        (require database_path('migrations/2026_09_13_100000_preserve_writer_billing_on_work_deletion.php'))->up();
    }
}
