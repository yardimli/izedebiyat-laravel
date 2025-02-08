<?php

	namespace App\Console\Commands;

	use App\Models\Article;
	use App\Models\User;
	use Carbon\Carbon;
	use Illuminate\Console\Command;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Facades\Log;

	class UpdateArticleRankings extends Command
	{
		protected $signature = 'articles:update-rankings';
		protected $description = 'Update article rankings based on various metrics';

		public function handle()
		{
			$this->info('Starting article ranking updates...');
			$chunk_counter = 0;
			// Get all published and approved articles
			Article::where('approved', 1)
				->where('is_published', 1)
				->where('deleted', 0)
				->chunk(500, function ($articles) use ($chunk_counter) {
					$chunk_counter++;
					$this->info('Processing chunk ' . $chunk_counter);
					foreach ($articles as $article) {
						$ranking = $this->calculateRanking($article);

						// Update the formul_ekim column
						$article->update(['formul_ekim' => $ranking]);
					}
				});

			$this->info('Article rankings updated successfully!');
		}

		private function calculateRanking($article)
		{
			// Base score starts at 1
			$score = 1;

			// Modified age decay factor
			$ageInDays = Carbon::now()->diffInDays($article->created_at);
			// Slower decay curve using square root and larger denominator
			$ageFactor = 1 / (1 + sqrt($ageInDays / 365)); // Decay over years more gradually

			$readFactor = log10(max($article->read_count, 1));

			$authorFollowersCount = $article->author_followers_count;
			$authorFollowersFactor = log10(max($authorFollowersCount, 1));

			$favoritesCount = $article->favorites_count;
			$clapsCount = $article->clap_count;
			$commentsCount = $article->comment_count;

			$engagementFactor = log10(
				max(
					($favoritesCount * 2) +
					($clapsCount * 0.5) +
					($commentsCount * 3),
					1
				)
			);

			$authorQualityScore = $article->author_quality_score;

			$moderationBoost = 1;
			if ($article->religious_moderation_value < 3) {
				$moderationBoost = 1.5;
			}

			// Adjusted weights to balance age impact
			$score = (
					$ageFactor * 8 +                  // Reduced from 10 to 8
					$readFactor * 2.5 +               // Increased slightly
					$authorFollowersFactor * 1.5 +
					$engagementFactor * 2.5 +         // Increased slightly
					$authorQualityScore * 1.5         // Increased slightly
				) * $moderationBoost;

			// Normalize the score (0-1000 range)
			$score = min(max($score, 0), 1000);

			return $score;
		}
	}
