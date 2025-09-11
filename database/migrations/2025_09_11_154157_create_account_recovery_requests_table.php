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
			Schema::create('account_recovery_requests', function (Blueprint $table) {
				$table->id();
				$table->string('real_name');
				$table->text('remembered_emails');
				$table->string('contact_email');
				$table->string('id_document_path');
				$table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
				$table->unsignedBigInteger('user_id')->nullable(); // To link to the user if found
				$table->text('notes')->nullable(); // For admin notes
				$table->timestamps();
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void
		{
			Schema::dropIfExists('account_recovery_requests');
		}
	};
