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
			Schema::create('book_review_tag', function (Blueprint $table) {
				$table->foreignId('book_review_id')->constrained()->onDelete('cascade');
				$table->foreignId('book_tag_id')->constrained()->onDelete('cascade');
				$table->primary(['book_review_id', 'book_tag_id']);
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void
		{
			Schema::dropIfExists('book_review_tag');
		}
	};
