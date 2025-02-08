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

			// Get all required counts in single queries
			$commentCounts = DB::table('comments')
				->where('is_approved', 1)
				->select('article_id', DB::raw('COUNT(*) as count'))
				->groupBy('article_id');

			$favoriteCounts = DB::table('article_favorites')
				->select('article_id', DB::raw('COUNT(*) as count'))
				->groupBy('article_id');

			$clapCounts = DB::table('claps')
				->select('article_id', DB::raw('SUM(count) as count'))
				->groupBy('article_id');

			// Get author stats in a single query
			$authorStats = DB::table('articles')
				->where('approved', 1)
				->where('is_published', 1)
				->where('deleted', 0)
				->select(
					'user_id',
					DB::raw('AVG(read_count) as avg_reads'),
					DB::raw('COUNT(*) as total_articles')
				)
				->groupBy('user_id');

			// Get follower counts for all authors at once
			$followerCounts = DB::table('user_follows')
				->select('following_id', DB::raw('COUNT(*) as count'))
				->groupBy('following_id');

			// Update articles in chunks
			Article::where('deleted', 0)
				->where('approved', 1)
				->select('id', 'user_id')
				->orderBy('created_at', 'desc')
				->chunk(1000, function ($articles) use ($commentCounts, $favoriteCounts, $clapCounts, $authorStats, $followerCounts) {
					$this->info('Processing ' . count($articles) . ' articles...');

					// Convert to subqueries
					$updates = DB::table('articles')
						->joinSub($commentCounts, 'comment_counts', function ($join) {
							$join->on('articles.id', '=', 'comment_counts.article_id');
						})
						->joinSub($favoriteCounts, 'favorite_counts', function ($join) {
							$join->on('articles.id', '=', 'favorite_counts.article_id');
						})
						->joinSub($clapCounts, 'clap_counts', function ($join) {
							$join->on('articles.id', '=', 'clap_counts.article_id');
						})
						->joinSub($authorStats, 'author_stats', function ($join) {
							$join->on('articles.user_id', '=', 'author_stats.user_id');
						})
						->joinSub($followerCounts, 'follower_counts', function ($join) {
							$join->on('articles.user_id', '=', 'follower_counts.following_id');
						})
						->whereIn('articles.id', $articles->pluck('id'))
						->update([
							'comment_count' => DB::raw('COALESCE(comment_counts.count, 0)'),
							'favorites_count' => DB::raw('COALESCE(favorite_counts.count, 0)'),
							'author_followers_count' => DB::raw('COALESCE(follower_counts.count, 0)'),
							'clap_count' => DB::raw('COALESCE(clap_counts.count, 0)'),
							'author_quality_score' => DB::raw('LEAST(LOG10(GREATEST(author_stats.avg_reads, 1)) * LOG10(GREATEST(author_stats.total_articles, 1)), 10)'),
							'updated_at' => now(),
						]);
				});

			$this->info('Article statistics update completed!');
		}
	}
