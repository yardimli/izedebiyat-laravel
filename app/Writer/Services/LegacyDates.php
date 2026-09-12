<?php

namespace App\Writer\Services;

use Illuminate\Support\Facades\DB;

class LegacyDates
{
    /** Preserve imported zero dates while migrating, without changing global SQL settings. */
    public static function run(callable $operation): mixed
    {
        $connection = DB::connection();
        if ($connection->getDriverName() !== 'mysql') {
            return $operation();
        }

        // Use the write PDO for both reading and changing its session mode.
        $pdo = $connection->getPdo();
        $original = (string) $pdo->query('SELECT @@SESSION.sql_mode')->fetchColumn();
        $modes = array_filter(explode(',', $original), fn ($mode) => ! in_array(strtoupper(trim($mode)), ['NO_ZERO_DATE', 'NO_ZERO_IN_DATE'], true));
        $compatible = implode(',', $modes);
        if ($compatible === $original) {
            return $operation();
        }

        $pdo->exec('SET SESSION sql_mode = '.$pdo->quote($compatible));
        try {
            return $operation();
        } finally {
            $pdo->exec('SET SESSION sql_mode = '.$pdo->quote($original));
        }
    }
}
