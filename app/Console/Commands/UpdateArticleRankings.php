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
				->chunk(100, function ($articles) use ($chunk_counter) {
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

			// Age decay factor (newer articles rank higher)
			$ageInDays = Carbon::now()->diffInDays($article->created_at);
			$ageFactor = 1 / (1 + ($ageInDays / 30)); // Decay over months

			// Read count factor (logarithmic scaling to prevent dominance of very popular articles)
			$readFactor = log10(max($article->read_count, 1));

			// Author metrics
			$author = $article->user;
			$authorFollowersCount = $author->followers()->count();
			$authorFollowersFactor = log10(max($authorFollowersCount, 1));

			// Article engagement metrics
			$favoritesCount = $article->favorites()->count();
			$clapsCount = $article->claps()->sum('count');
			$commentsCount = $article->comments()->count();

			// Calculate engagement factor
			$engagementFactor = log10(
				max(
					($favoritesCount * 2) +
					($clapsCount * 0.5) +
					($commentsCount * 3),
					1
				)
			);

			// Author's overall article quality
			$authorQualityScore = $this->getAuthorQualityScore($author);

			// Respect moderation boost
			$moderationBoost = 1;
			if ($article->religious_moderation_value < 3) {
				$moderationBoost = 1.5;
			}

			// Combine all factors
			$score = (
					$ageFactor * 10 +
					$readFactor * 2 +
					$authorFollowersFactor * 1.5 +
					$engagementFactor * 2 +
					$authorQualityScore * 1.2
				) * $moderationBoost;

			// Normalize the score (0-1000 range)
			$score = min(max($score, 0), 1000);

			return $score;
		}

		private function getAuthorQualityScore($author)
		{
			// Get average metrics for author's other articles
			$authorStats = Article::where('user_id', $author->id)
				->where('approved', 1)
				->where('is_published', 1)
				->where('deleted', 0)
				->select(
					DB::raw('AVG(read_count) as avg_reads'),
					DB::raw('COUNT(*) as total_articles')
				)
				->first();

			if (!$authorStats->total_articles) {
				return 1;
			}

			// Calculate author quality score based on average reads and total articles
			$qualityScore = log10(max($authorStats->avg_reads, 1)) *
				log10(max($authorStats->total_articles, 1));

			return min($qualityScore, 10); // Cap at 10
		}
	}
