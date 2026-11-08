<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('llm_settings', function (Blueprint $table) {
            $table->string('frontend_model_fallback_1')->nullable();
            $table->string('frontend_model_fallback_2')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('llm_settings', function (Blueprint $table) {
            $table->dropColumn(['frontend_model_fallback_1', 'frontend_model_fallback_2']);
        });
    }
};
