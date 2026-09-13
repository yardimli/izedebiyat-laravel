<?php

// Uses a new, randomly named local test database. Never migrates application data.
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$original = config('database.default');
$connection = config('database.connections.'.$original);
if (($connection['driver'] ?? '') !== 'mysql' || ! in_array($connection['host'] ?? '', ['127.0.0.1', 'localhost', '::1'], true) || ! empty($connection['url'])) {
    throw new RuntimeException('This check requires a local MySQL/MariaDB connection without a URL override.');
}
$database = 'writer_billing_test_'.bin2hex(random_bytes(6));
$pdo = DB::connection($original)->getPdo();
$created = false;
$expect = function ($condition, $message) {
    if (! $condition) throw new RuntimeException($message);
};
try {
    $pdo->exec('CREATE DATABASE '.$database);
    $created = true;
    config(['database.connections.writer_billing_test' => array_replace($connection, ['database' => $database]), 'database.default' => 'writer_billing_test']);
    DB::purge('writer_billing_test');
    DB::statement('CREATE TABLE articles (id BIGINT UNSIGNED PRIMARY KEY) ENGINE=InnoDB');
    DB::statement('CREATE TABLE writer_ai_calls (id BIGINT UNSIGNED PRIMARY KEY, book_id BIGINT UNSIGNED NOT NULL, cost DECIMAL(16,8), CONSTRAINT writer_ai_calls_book_id_foreign FOREIGN KEY (book_id) REFERENCES articles(id) ON DELETE CASCADE) ENGINE=InnoDB');
    DB::statement('INSERT INTO articles VALUES (1)');
    DB::statement('INSERT INTO writer_ai_calls VALUES (1, 1, 0.25)');
    $migration = require database_path('migrations/2026_09_13_100000_preserve_writer_billing_on_work_deletion.php');
    $migration->up();
    $migration->up();
    $expect((float) DB::table('writer_ai_calls')->value('cost') === 0.25, 'Spending changed during migration.');
    DB::table('articles')->where('id', 1)->delete();
    $expect(DB::table('writer_ai_calls')->count() === 1 && DB::table('writer_ai_calls')->value('book_id') === null, 'Billing was not retained on work deletion.');
    // Simulate interruption after dropping the old foreign key.
    $key = DB::selectOne('SELECT CONSTRAINT_NAME AS name FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL', ['writer_ai_calls', 'book_id']);
    DB::statement('ALTER TABLE writer_ai_calls DROP FOREIGN KEY '.chr(96).str_replace(chr(96), chr(96).chr(96), $key->name).chr(96));
    $migration->up();
    $migration->up();
    DB::statement('INSERT INTO articles VALUES (2)');
    DB::statement('INSERT INTO writer_ai_calls VALUES (2, 2, 0.10)');
    DB::table('articles')->where('id', 2)->delete();
    $expect(DB::table('writer_ai_calls')->whereNull('book_id')->count() === 2, 'Interrupted migration did not resume correctly.');
    echo 'PASS on '.$pdo->getAttribute(PDO::ATTR_SERVER_VERSION).': old schema upgraded, repeat runs skipped, missing foreign key restored, billing rows and spending preserved.'.PHP_EOL;
} finally {
    config(['database.default' => $original]);
    DB::purge('writer_billing_test');
    if ($created && preg_match('/^writer_billing_test_[a-f0-9]{12}$/D', $database)) {
        $pdo->exec('DROP DATABASE '.$database);
    }
}
