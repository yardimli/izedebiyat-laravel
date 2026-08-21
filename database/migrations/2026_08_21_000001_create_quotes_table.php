<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->text('quote');
            $table->string('author', 150);
            $table->unsignedTinyInteger('day')->nullable();
            $table->unsignedTinyInteger('month')->nullable();
            $table->timestamps();

            $table->index(['month', 'day']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
