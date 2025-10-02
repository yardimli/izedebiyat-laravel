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
			Schema::table('account_recovery_requests', function (Blueprint $table) {
				// Add a boolean column to flag account deletion requests.
				// This column is placed after 'id_document_path' for logical grouping.
				// It defaults to 'false' as most requests will be for recovery, not deletion.
				$table->boolean('delete_account')
					->default(false)
					->after('id_document_path')
					->comment('Flag to indicate if the user requested account deletion instead of recovery.');
			});
		}

		/**
		 * Reverse the migrations.
		 *
		 * @return void
		 */
		public function down(): void
		{
			Schema::table('account_recovery_requests', function (Blueprint $table) {
				// Drop the column if the migration is rolled back.
				$table->dropColumn('delete_account');
			});
		}
	};
