<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	return new class extends Migration {
		/**
		 * Run the migrations.
		 */
		public function up(): void
		{
			Schema::create('comments', function (Blueprint $table) {
				$table->id();
				$table->integer('user_id')->unsigned()->index();
				$table->integer('article_id')->unsigned()->index();
				$table->integer('parent_id')->unsigned()->default(0)->index();
				$table->text('content');
				$table->string('sender_name')->nullable();
				$table->string('sender_email')->nullable();
				$table->boolean('is_approved')->default(true)->index();
				$table->timestamps();
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void
		{
			Schema::dropIfExists('comments');
		}
	};
