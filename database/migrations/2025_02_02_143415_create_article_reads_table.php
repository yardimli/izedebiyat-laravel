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
        Schema::create('article_reads', function (Blueprint $table) {
	        $table->id();
	        $table->foreignId('article_id')->constrained('articles');
	        $table->unsignedBigInteger('user_id')->default(0);
	        $table->string('ip_address', 45);
	        $table->timestamps();

	        // Add an index for better query performance
	        $table->index(['article_id', 'user_id', 'ip_address']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_reads');
    }
};
