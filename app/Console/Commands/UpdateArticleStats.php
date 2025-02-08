<?php

	namespace App\Console\Commands;

	use App\Models\Article;
	use Illuminate\Console\Command;
	use Illuminate\Support\Facades\DB;

	class UpdateArticleStats extends Command
	{
		protected $signature = 'articles:update-stats';
		protected $description = 'Update article statistics (comments, favorites, followers, claps)';

		public function handle()
		{
			$this->info('Starting to update article statistics...');

			// Update all articles with a single query for better performance
			$articles = Article::select('id', 'user_id')
				->where('deleted', 0)
				->where('approved', 1)
				->chunk(500, function ($articles) {
					$this->info('Updating ' . count($articles) . ' articles...');
					foreach ($articles as $article) {
						// Get counts
						$commentCount = DB::table('comments')
							->where('article_id', $article->id)
							->where('is_approved', 1)
							->count();

						$favoritesCount = DB::table('article_favorites')
							->where('article_id', $article->id)
							->count();

						$authorFollowersCount = DB::table('user_follows')
							->where('following_id', $article->user_id)
							->count();

						$clapCount = DB::table('claps')
							->where('article_id', $article->id)
							->sum('count');

						$authorStats = Article::where('user_id', $article->user_id)
							->where('approved', 1)
							->where('is_published', 1)
							->where('deleted', 0)
							->select(
								DB::raw('AVG(read_count) as avg_reads'),
								DB::raw('COUNT(*) as total_articles')
							)
							->first();

						if (!$authorStats->total_articles) {
							$qualityScore = 1;
						} else
						{
							// Calculate author quality score based on average reads and total articles
							$qualityScore = log10(max($authorStats->avg_reads, 1)) *
								log10(max($authorStats->total_articles, 1));

							$qualityScore = min($qualityScore, 10); // Cap at 10
						}

						// Update article
						DB::table('articles')
							->where('id', $article->id)
							->update([
								'comment_count' => $commentCount,
								'favorites_count' => $favoritesCount,
								'author_followers_count' => $authorFollowersCount,
								'clap_count' => $clapCount,
								'author_quality_score' => $qualityScore,
								'updated_at' => now(),
							]);
					}
				});

			$this->info('Article statistics update completed!');
		}
	}
