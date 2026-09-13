<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Fresh SQLite databases receive this relationship in the creation migration.
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $foreignKeys = DB::select(
            'SELECT k.CONSTRAINT_NAME AS name, k.REFERENCED_TABLE_NAME AS target_table,
                    k.REFERENCED_COLUMN_NAME AS target_column, r.DELETE_RULE AS delete_rule
             FROM information_schema.KEY_COLUMN_USAGE k
             JOIN information_schema.REFERENTIAL_CONSTRAINTS r
               ON r.CONSTRAINT_SCHEMA = k.CONSTRAINT_SCHEMA
              AND r.TABLE_NAME = k.TABLE_NAME AND r.CONSTRAINT_NAME = k.CONSTRAINT_NAME
             WHERE k.TABLE_SCHEMA = DATABASE() AND k.TABLE_NAME = ? AND k.COLUMN_NAME = ?',
            ['writer_ai_calls', 'book_id']
        );
        $column = DB::selectOne(
            'SELECT IS_NULLABLE AS nullable FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            ['writer_ai_calls', 'book_id']
        );

        if ($column?->nullable === 'YES' && count($foreignKeys) === 1
            && $foreignKeys[0]->target_table === 'articles'
            && $foreignKeys[0]->target_column === 'id'
            && $foreignKeys[0]->delete_rule === 'SET NULL') {
            return;
        }

        // Separate statements avoid MariaDB's duplicate constraint-name error.
        // Inspecting the schema first also permits retry after any completed step.
        foreach ($foreignKeys as $foreignKey) {
            $name = str_replace(chr(96), chr(96).chr(96), $foreignKey->name);
            DB::statement('ALTER TABLE writer_ai_calls DROP FOREIGN KEY '.chr(96).$name.chr(96));
        }
        DB::statement('ALTER TABLE writer_ai_calls MODIFY book_id BIGINT UNSIGNED NULL');
        // Let the database choose an available name instead of reusing the old symbol.
        DB::statement('ALTER TABLE writer_ai_calls ADD FOREIGN KEY (book_id) REFERENCES articles(id) ON DELETE SET NULL');
    }

    public function down(): void
    {
        // Retain the nullable link so detached billing records remain intact.
    }
};
