<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('llm_settings', function (Blueprint $table) {
            $table->id();
            $table->string('frontend_model')->default('anthropic/claude-3.5-haiku:beta');
            $table->string('backend_model')->default('openai/gpt-5.6-luna');
            $table->string('cron_model')->default('openai/gpt-5.6-luna');
            $table->json('allowed_frontend_models')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('llm_settings');
    }
};
