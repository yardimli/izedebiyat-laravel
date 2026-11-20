<?php

namespace App\Console\Commands;

use App\View\Composers\MainMenuComposer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RefreshCategoryCache extends Command
{
    protected $signature = 'categories:refresh-cache';
    protected $description = 'Refresh saved category statistics and the hourly menu cache';

    public function handle(MainMenuComposer $menu): int
    {
        $lock = Cache::lock('categories:refresh-cache', 3600);
        if (!$lock->get()) {
            $this->info('Category refresh is already running.');
            return self::SUCCESS;
        }

        try {
            $totals = [];
            // Two grouped scans replace three aggregate queries per category.
            foreach (['category_id', 'parent_category_id'] as $column) {
                $query = DB::table('articles')->where('approved', 1)
                    ->where('is_published', 1)->where('deleted', 0);
                if ($column === 'parent_category_id') {
                    // Preserve the old OR semantics: an article counts once per category.
                    $query->where(fn ($q) => $q->whereNull('category_id')
                        ->orWhereColumn('category_id', '<>', 'parent_category_id'));
                }
                $rows = $query->select($column)
                    ->selectRaw('SUM(CASE WHEN moderation_flagged = 0 THEN 1 ELSE 0 END) AS total_articles')
                    ->selectRaw('SUM(CASE WHEN moderation_flagged = 0 AND created_at >= ? THEN 1 ELSE 0 END) AS new_articles', [now()->subDays(30)])
                    ->selectRaw('COALESCE(SUM(read_count), 0) AS read_count')
                    ->groupBy($column)->get();
                foreach ($rows as $row) {
                    foreach (['total_articles', 'new_articles', 'read_count'] as $field) {
                        $totals[$row->$column][$field] = ($totals[$row->$column][$field] ?? 0) + (int) $row->$field;
                    }
                }
            }

            DB::transaction(function () use ($totals) {
                foreach (DB::table('categories')->pluck('id') as $id) {
                    DB::table('categories')->where('id', $id)->update(($totals[$id] ?? [
                        'total_articles' => 0, 'new_articles' => 0, 'read_count' => 0,
                    ]) + ['updated_at' => now()]);
                }
            });
            $menu->refresh();
            $this->info('Category statistics and menu cache refreshed.');
            return self::SUCCESS;
        } finally {
            $lock->release();
        }
    }
}
