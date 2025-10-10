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
				$table->boolean('is_user_submitted')->default(false)->after('is_published');

				$table->foreignId('submitted_by_user_id')->nullable()->after('is_user_submitted')->constrained('users')->onDelete('set null');
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void
		{
			Schema::table('book_reviews', function (Blueprint $table) {
				$table->dropForeign(['submitted_by_user_id']);
				$table->dropColumn(['is_user_submitted', 'submitted_by_user_id']);
			});
		}
	};
