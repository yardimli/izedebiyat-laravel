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
        Schema::create('claps', function (Blueprint $table) {
	        $table->id();
	        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
	        $table->foreignId('article_id')->constrained('articles')->onDelete('cascade');
	        $table->integer('count')->default(0);
	        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claps');
    }
};
