<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Existing deployments use MySQL. Fresh databases receive the nullable
        // relationship directly in the workspace creation migration.
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE writer_ai_calls
                DROP FOREIGN KEY writer_ai_calls_book_id_foreign,
                MODIFY book_id BIGINT UNSIGNED NULL,
                ADD CONSTRAINT writer_ai_calls_book_id_foreign FOREIGN KEY (book_id) REFERENCES articles(id) ON DELETE SET NULL');
        }
    }

    public function down(): void
    {
        // Retain the nullable link so detached billing records remain intact.
    }
};
