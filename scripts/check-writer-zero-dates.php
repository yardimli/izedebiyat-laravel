<?php

// This check uses connection-local temporary tables only; existing records are never changed.
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Writer\Services\LegacyDates;
use Illuminate\Support\Facades\DB;

if (DB::connection()->getDriverName() !== 'mysql') {
    throw new RuntimeException('This regression check requires a MySQL/MariaDB connection.');
}
$pdo = DB::connection()->getPdo();
$original = (string) $pdo->query('SELECT @@SESSION.sql_mode')->fetchColumn();
$table = 'writer_zero_dates_'.bin2hex(random_bytes(6));
$expect = function (bool $condition, string $message): void {
    if (! $condition) {
        throw new RuntimeException($message);
    }
};
try {
    $pdo->exec("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,NO_ENGINE_SUBSTITUTION'");
    $strict = (string) $pdo->query('SELECT @@SESSION.sql_mode')->fetchColumn();
    $pdo->exec("CREATE TEMPORARY TABLE `$table` (id INT PRIMARY KEY, created_at DATETIME NULL, updated_at DATETIME NULL)");
    LegacyDates::run(function () use ($pdo, $table) {
        $pdo->exec("INSERT INTO `$table` VALUES (1, '0000-00-00 00:00:00', '0000-00-00 00:00:00'), (2, '2020-00-15 00:00:00', '2020-00-15 00:00:00')");
    });
    $failed = false;
    try {
        $pdo->exec("ALTER TABLE `$table` ADD rejected_probe JSON NULL, ALGORITHM=COPY");
    } catch (PDOException $error) {
        $expect(in_array($error->errorInfo[1], [1292, 1067]), 'Unexpected ALTER TABLE error: '.$error->getMessage());
        $failed = true;
    }
    $expect($failed, 'The strict-mode zero-date failure was not reproduced.');
    LegacyDates::run(function () use ($pdo, $table, $expect) {
        $mode = (string) $pdo->query('SELECT @@SESSION.sql_mode')->fetchColumn();
        $expect(str_contains($mode, 'STRICT_TRANS_TABLES'), 'Strict mode was unexpectedly disabled.');
        $pdo->exec("ALTER TABLE `$table` ADD document JSON NULL, ALGORITHM=COPY");
        $pdo->exec("UPDATE `$table` SET document = '{}', updated_at = '0000-00-00 00:00:00' WHERE id = 1");
    });
    $expect($strict === $pdo->query('SELECT @@SESSION.sql_mode')->fetchColumn(), 'SQL mode was not restored after success.');
    $rows = $pdo->query("SELECT created_at, updated_at FROM `$table` ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
    $expect($rows[0]['created_at'] === '0000-00-00 00:00:00' && $rows[0]['updated_at'] === '0000-00-00 00:00:00', 'Original zero timestamps were changed.');
    $expect($rows[1]['created_at'] === '2020-00-15 00:00:00', 'Partial-zero date was changed.');
    try {
        LegacyDates::run(function () {
            throw new RuntimeException('Expected failure');
        });
        throw new LogicException('The callback exception was swallowed.');
    } catch (RuntimeException $error) {
        $expect($error->getMessage() === 'Expected failure', 'Unexpected callback failure.');
    }
    $expect($strict === $pdo->query('SELECT @@SESSION.sql_mode')->fetchColumn(), 'SQL mode was not restored after failure.');
    echo "PASS: strict ALTER failure reproduced; schema/data updates preserve zero dates; strict mode stays enabled; session restored after success and failure.\n";
} finally {
    try {
        $pdo->exec("DROP TEMPORARY TABLE IF EXISTS `$table`");
    } finally {
        $pdo->exec('SET SESSION sql_mode = '.$pdo->quote($original));
    }
}
