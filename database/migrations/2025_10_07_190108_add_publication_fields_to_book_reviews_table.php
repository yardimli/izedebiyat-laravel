<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	return new class extends Migration
	{
		/**
		 * Run the migrations.
		 *
		 * @return void
		 */
		public function up(): void
		{
			// Use Schema::table() to modify an existing table.
			Schema::table('book_reviews', function (Blueprint $table) {
				$table->string('publisher')->nullable()->after('review_content');
				$table->date('publication_date')->nullable()->after('publisher');
				$table->string('publication_place')->nullable()->after('publication_date');
				$table->string('buy_url')->nullable()->after('publication_place');
			});
		}

		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down(): void
		{
			// The down method defines how to revert the changes made in the up() method.
			Schema::table('book_reviews', function (Blueprint $table) {
				$table->dropColumn([
					'publisher',
					'publication_date',
					'publication_place',
					'buy_url'
				]);
			});
		}
	};
