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
				$table->string('profile_url')->nullable()->after('remembered_emails');
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void
		{
			Schema::table('account_recovery_requests', function (Blueprint $table) {
				$table->dropColumn('profile_url');
			});
		}
	};
