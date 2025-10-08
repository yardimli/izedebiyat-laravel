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
			Schema::table('book_reviews', function (Blueprint $table) {
				// We add the foreign key column.
				// It's nullable because a review might have a manually entered author name.
				// 'after' places the column after the 'author' column for better organization.
				// 'constrained' sets up the foreign key relationship to the 'book_authors' table.
				// 'onDelete('set null')' means if an author is deleted, this field in the review will become NULL,
				// which prevents the review from being deleted and preserves the manually entered author name.
				$table->foreignId('book_author_id')
					->nullable()
					->after('author')
					->constrained('book_authors')
					->onDelete('set null');
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void
		{
			Schema::table('book_reviews', function (Blueprint $table) {
				// This ensures the migration can be safely rolled back.
				$table->dropForeign(['book_author_id']);
				$table->dropColumn('book_author_id');
			});
		}
	};
