<?php

	namespace App\Console\Commands;

	use App\Models\Article;
	use Illuminate\Console\Command;
	use Illuminate\Support\Facades\DB;

	class UpdateArticleRankings extends Command
	{
		protected $signature = 'articles:update-rankings';
		protected $description = 'Update article rankings based on various metrics';

		public function handle()
		{
			$this->info('Starting article ranking updates...');

			$query = "
            UPDATE articles
            SET formul_ekim = LEAST(
                GREATEST(
                    (
                        (
                            (8 * (1 / (1 + SQRT(DATEDIFF(NOW(), created_at) / 365)))) +
                            (2.5 * LOG10(GREATEST(read_count, 1))) +
                            (1.5 * LOG10(GREATEST(author_followers_count, 1))) +
                            (2.5 * LOG10(GREATEST(
                                (favorites_count * 2) +
                                (clap_count * 0.5) +
                                (comment_count * 3),
                                1
                            ))) +
                            (1.5 * author_quality_score)
                        ) * 
                        CASE 
                            WHEN religious_moderation_value < 3 THEN 1.5 
                            ELSE 1 
                        END *
                        -- Age multiplier: starts at 1 and decreases to 0.2 over 25 years
                        GREATEST(0.2, (1 - (LEAST(DATEDIFF(NOW(), created_at) / 365, 25) * 0.032))) *
                        -- Recent articles boost (last 30 days)
                        CASE 
                            WHEN DATEDIFF(NOW(), created_at) <= 60 THEN 2.5 
                            ELSE 1 
                        END
                    ),
                    0
                ),
                1000
            )
            WHERE approved = 1 
            AND is_published = 1 
            AND deleted = 0
            AND id IN (?)
        ";

			// Process in larger chunks
			Article::where('approved', 1)
				->where('is_published', 1)
				->where('deleted', 0)
				->select('id')
				->orderBy('id')
				->chunk(5000, function ($articles) use ($query) {
					$ids = $articles->pluck('id')->toArray();
					DB::statement(str_replace('?', implode(',', $ids), $query));
					$this->info('Processed ' . count($ids) . ' articles');
				});

			$this->info('Article rankings updated successfully!');
		}
	}
