<?php

namespace App\Writer\Services;

use Illuminate\Support\Facades\DB;

class MigrateArticles
{
    public function run(bool $dryRun = false, ?callable $progress = null): int
    {
        return LegacyDates::run(fn () => $this->convert($dryRun, $progress));
    }

    private function convert(bool $dryRun, ?callable $progress): int
    {
        $count = 0;
        DB::table('articles')->whereNull('document')->orderBy('id')->chunkById(100, function ($articles) use ($dryRun, $progress, &$count) {
            foreach ($articles as $article) {
                try {
                    $document = LegacyManuscript::convert((string) $article->main_text, (bool) ($article->markdown ?? true));
                } catch (\Throwable $error) {
                    throw new \RuntimeException('Unable to convert article '.$article->id.'. Previously converted articles are safe; correct this article and rerun.', 0, $error);
                }
                if (! $dryRun) {
                    DB::transaction(function () use ($article, $document) {
                        // Recheck under lock so reruns never overwrite an edited manuscript.
                        $current = DB::table('articles')->where('id', $article->id)->lockForUpdate()->first();
                        if ($current->document !== null) {
                            return;
                        }
                        if ($current->main_text !== $article->main_text) {
                            $document = LegacyManuscript::convert((string) $current->main_text, (bool) ($current->markdown ?? true));
                        }
                        DB::table('articles')->where('id', $article->id)->update([
                            'document' => json_encode($document, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                            'manuscript' => Manuscript::text($document), 'metadata' => json_encode(['synopsis' => $current->subheading ?? '', 'genre' => $current->category_name ?? '']),
                            'codex_types' => json_encode(['People', 'Places', 'Items', 'Organizations', 'Events', 'Lore']),
                            'updated_at' => $current->updated_at, 'writer_original_text' => $current->main_text, 'writer_original_markdown' => $current->markdown ?? true, 'writer_migrated_at' => now(),
                        ]);
                    });
                }
                $count++;
                if ($progress) {
                    $progress($article->id);
                }
            }
        });

        return $count;
    }
}
