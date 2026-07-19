<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('forum_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80);
            $table->string('slug', 100)->unique();
            $table->string('color', 7)->default('#55779f');
            $table->string('description', 255)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('forum_discussions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('forum_tag_id')->constrained('forum_tags')->restrictOnDelete();
            $table->string('title', 180);
            $table->string('slug')->unique();
            $table->unsignedBigInteger('views_count')->default(0);
            $table->boolean('is_pinned')->default(false)->index();
            $table->boolean('is_locked')->default(false)->index();
            $table->timestamp('last_post_at')->nullable()->index();
            $table->timestamps();

            $table->index(['forum_tag_id', 'last_post_at']);
        });

        Schema::create('forum_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_discussion_id')->constrained('forum_discussions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('forum_posts')->nullOnDelete();
            $table->longText('body');
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['forum_discussion_id', 'created_at']);
        });

        Schema::create('forum_post_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_post_id')->constrained('forum_posts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['forum_post_id', 'user_id']);
        });

        Schema::create('forum_post_flags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_post_id')->constrained('forum_posts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reason', 500);
            $table->string('status', 20)->default('pending')->index();
            $table->timestamps();
            $table->unique(['forum_post_id', 'user_id']);
        });

        $now = now();
        DB::table('forum_tags')->insert([
            ['name' => 'Genel', 'slug' => 'genel', 'color' => '#929292', 'description' => 'Her konuda sohbetler', 'sort_order' => 10, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Duyurular', 'slug' => 'duyurular', 'color' => '#ef4b3e', 'description' => 'Topluluk duyuruları', 'sort_order' => 20, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Destek', 'slug' => 'destek', 'color' => '#3498db', 'description' => 'Yardım ve destek talepleri', 'sort_order' => 30, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Hata Bildirimleri', 'slug' => 'hata-bildirimleri', 'color' => '#c7372a', 'description' => 'Karşılaştığınız sorunlar', 'sort_order' => 40, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Öneriler', 'slug' => 'oneriler', 'color' => '#27ae60', 'description' => 'Yeni fikirler ve öneriler', 'sort_order' => 50, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Konu Dışı', 'slug' => 'konu-disi', 'color' => '#9b59b6', 'description' => 'Serbest sohbet alanı', 'sort_order' => 60, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Rehberler', 'slug' => 'rehberler', 'color' => '#f39c12', 'description' => 'Faydalı anlatımlar', 'sort_order' => 70, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_post_flags');
        Schema::dropIfExists('forum_post_likes');
        Schema::dropIfExists('forum_posts');
        Schema::dropIfExists('forum_discussions');
        Schema::dropIfExists('forum_tags');
    }
};
