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
			Schema::create('users', function (Blueprint $table) {
				$table->id();
				$table->string('name');
				$table->string('slug');
				$table->string('email')->unique();
				$table->timestamp('email_verified_at')->nullable();
				$table->string('password');
				$table->string('google_id')->nullable();
				$table->string('username')->nullable();
				$table->string('avatar')->nullable();
				$table->string('picture')->nullable();
				$table->string('background_image')->nullable();
				$table->string('page_title')->nullable();
				$table->string('personal_url')->nullable();
				$table->text('about_me')->nullable();
				$table->integer('member_status')->default(1);
				$table->integer('member_type')->default(1);
				$table->dateTime('last_login')->default(now())->nullable();
				$table->string('last_ip')->nullable();


				$table->rememberToken();
				$table->timestamps();
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void
		{
			Schema::dropIfExists('users');
		}
	};
