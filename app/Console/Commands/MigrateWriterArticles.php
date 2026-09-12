<?php

namespace App\Console\Commands;

use App\Writer\Services\MigrateArticles;
use Illuminate\Console\Command;

class MigrateWriterArticles extends Command
{
    protected $signature = 'writer:migrate-articles {--dry-run : Validate conversion without writing any records}';

    protected $description = 'Convert legacy articles to Total Author documents, preserving original text and article identity';

    public function handle(MigrateArticles $migration): int
    {
        $count = $migration->run((bool) $this->option('dry-run'));
        $this->info($count.' articles '.($this->option('dry-run') ? 'validated.' : 'converted.'));

        return self::SUCCESS;
    }
}
