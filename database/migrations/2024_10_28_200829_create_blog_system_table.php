<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

	    Schema::create('images', function (Blueprint $table) {
        $table->engine('InnoDB');
		    $table->id();
		    $table->integer('user_id')->index()->default(0);
		    $table->string('image_type')->default('upload');
		    $table->uuid('image_guid')->unique();
		    $table->string('image_alt')->nullable();
		    $table->text('user_prompt')->nullable();
		    $table->text('llm_prompt')->nullable();
		    $table->text('image_prompt')->nullable();
		    $table->string('llm')->nullable();
		    $table->integer('prompt_tokens')->default(0);
		    $table->integer('completion_tokens')->default(0);
		    $table->string('image_original_filename');
		    $table->string('image_large_filename');
		    $table->string('image_medium_filename');
		    $table->string('image_small_filename');
		    $table->timestamps();
	    });

	    Schema::create('categories', function (Blueprint $table) {
        $table->engine('InnoDB');
		    $table->id();
		    $table->integer('user_id')->index()->default(0);
		    $table->string('category_name');
		    $table->string('category_slug')->unique();
		    $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('set null');
		    $table->text('category_description')->nullable();
		    $table->timestamps();
	    });

	    Schema::create('articles', function (Blueprint $table) {
        $table->engine('InnoDB');
		    $table->id();
		    $table->integer('user_id')->index()->default(0);
		    $table->string('chat_session_id')->nullable();
		    $table->string('title');
		    $table->string('subtitle')->nullable();
		    $table->string('slug')->unique();
		    $table->boolean('is_published')->default(false);
		    $table->timestamp('posted_at')->nullable();
		    $table->longText('body');  // for markdown content
		    $table->text('short_description')->nullable();
		    $table->foreignId('featured_image_id')->nullable()->constrained('images')->onDelete('set null');
		    $table->timestamps();
	    });

	    Schema::create('article_category', function (Blueprint $table) {
        $table->engine('InnoDB');
		    $table->id();
		    $table->foreignId('article_id')->constrained()->onDelete('cascade');
		    $table->foreignId('category_id')->constrained()->onDelete('cascade');
		    $table->timestamps();
	    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_category');
				Schema::dropIfExists('articles');
				Schema::dropIfExists('categories');
				Schema::dropIfExists('images');
    }
};
