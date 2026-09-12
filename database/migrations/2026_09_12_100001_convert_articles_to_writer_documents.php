<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        app(\App\Writer\Services\MigrateArticles::class)->run();
    }

    public function down(): void
    { /* main_text remains usable by the original editor; never overwrite newer writing. */
    }
};
