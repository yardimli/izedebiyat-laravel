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
			Schema::table('account_recovery_requests', function (Blueprint $table) {
				// ADDED: Column to store the profile URL provided by the user during recovery request.
				$table->string('profile_url')->nullable()->after('remembered_emails');
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void
		{
			Schema::table('account_recovery_requests', function (Blueprint $table) {
				// ADDED: Reverts the migration by dropping the profile_url column.
				$table->dropColumn('profile_url');
			});
		}
	};
