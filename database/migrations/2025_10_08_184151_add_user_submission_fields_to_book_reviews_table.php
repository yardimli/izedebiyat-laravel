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
				// ADDED: Flag to identify if the review was submitted by a user.
				// Placed after 'is_published' for logical grouping.
				$table->boolean('is_user_submitted')->default(false)->after('is_published');

				// ADDED: Foreign key to link the submission to the user who submitted it.
				// Placed after 'is_user_submitted'.
				$table->foreignId('submitted_by_user_id')->nullable()->after('is_user_submitted')->constrained('users')->onDelete('set null');
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void
		{
			Schema::table('book_reviews', function (Blueprint $table) {
				// ADDED: Logic to correctly drop the foreign key and columns on rollback.
				$table->dropForeign(['submitted_by_user_id']);
				$table->dropColumn(['is_user_submitted', 'submitted_by_user_id']);
			});
		}
	};
