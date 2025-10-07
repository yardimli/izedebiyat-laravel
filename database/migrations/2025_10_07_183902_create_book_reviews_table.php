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
			Schema::create('book_reviews', function (Blueprint $table) {
				$table->id();
				$table->foreignId('user_id')->constrained()->onDelete('cascade');
				$table->string('title');
				$table->string('slug')->unique();
				$table->string('author');
				$table->string('cover_image')->nullable();
				$table->text('review_content');
				$table->boolean('is_published')->default(false);
				$table->timestamp('published_at')->nullable();
				$table->timestamps();
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void
		{
			Schema::dropIfExists('book_reviews');
		}
	};
