<?php

	namespace App\Http\Controllers;

	use App\Helpers\MyHelper;
	use App\Models\Category;
	use App\Models\Keyword;
	use App\Models\User;
	use App\Models\Article;
	use Carbon\Carbon;
	use GuzzleHttp\Client;
	use Illuminate\Http\Request;
	use Illuminate\Pagination\LengthAwarePaginator;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Cache;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Facades\File;
	use Illuminate\Support\Facades\Log;
	use Illuminate\Support\Facades\Storage;
	use Illuminate\Support\Str;
	use Illuminate\Support\Facades\Validator;
	use League\CommonMark\CommonMarkConverter;


	class FrontendController extends Controller
	{

		protected $mainMenuCategories;

		public function __construct()
		{
			if (Cache::lock('updating_category_stats', 3600)->get()) {
				// Get main menu categories with their last update time
				$firstCategory = Category::orderBy('updated_at')->first();

				// Check if first category exists and if it was updated more than 2 minutes ago
				if ($firstCategory && $firstCategory->updated_at->diffInMinutes(now()) > 60) {
					Log::info('Updating category statistics...');

					// Get all categories (including main and sub categories)
					$allCategories = Category::all();

					foreach ($allCategories as $category) {
						// Count total approved texts
						$totalTexts = Article::where(function ($query) use ($category) {
							$query->where('category_id', $category->id)
								->orWhere('parent_category_id', $category->id);
						})
							->where('approved', 1)
							->where('is_published', 1)
							->where('deleted', 0)
							->where('moderation_flagged', 0)
							->count();

						// Count new texts (last 30 days)
						$newTexts = Article::where(function ($query) use ($category) {
							$query->where('category_id', $category->id)
								->orWhere('parent_category_id', $category->id);
						})
							->where('approved', 1)
							->where('is_published', 1)
							->where('deleted', 0)
							->where('moderation_flagged', 0)
							->where('created_at', '>=', now()->subDays(30))
							->count();

						// Get total views/reads
						$totalReads = Article::where(function ($query) use ($category) {
							$query->where('category_id', $category->id)
								->orWhere('parent_category_id', $category->id);
						})
							->where('approved', 1)
							->where('is_published', 1)
							->where('deleted', 0)
							->sum('read_count');

						// Update category
						$category->update([
							'total_articles' => $totalTexts,
							'new_articles' => $newTexts,
							'read_count' => $totalReads,
							'updated_at' => now()
						]);
					}
				}
				Cache::lock('updating_category_stats')->release();
			}

			// Get main menu categories for the view
			$this->mainMenuCategories = Category::where('parent_category_id', 0)
				->orderBy('slug')
				->with('subCategories')
				->get();

			// Share with all views
			view()->share('mainMenuCategories', $this->mainMenuCategories);
		}

		public function page_legal()
		{
			return view('frontend.page-legal');
		}

		public function page_privacy()
		{
			return view('frontend.page-privacy');
		}

		public function page_sign_up_step_1()
		{
			return view('frontend.page-sign-up-step-1');
		}

		public function page_sign_up_step_2()
		{
			return view('frontend.page-sign-up-step-2');
		}

		public function page_izedebiyat()
		{
			return view('frontend.page-izedebiyat');
		}

		public function page_faq()
		{
			return view('frontend.page-faq');
		}

		public function page_about()
		{
			return view('frontend.page-about');
		}

		public function index()
		{
			$categories = Category::where('parent_category_id', 0)
				->orderBy('category_name')
				->get();

			$category_order_slug_array = ['oyku', 'elestiri', 'siir', 'deneme', 'bilimsel', 'roman', 'inceleme'];

			// Sort categories
			$categories = $categories->sort(function ($a, $b) use ($category_order_slug_array) {
				$posA = array_search($a->slug, $category_order_slug_array);
				$posB = array_search($b->slug, $category_order_slug_array);
				$posA = $posA !== false ? $posA : count($category_order_slug_array);
				$posB = $posB !== false ? $posB : count($category_order_slug_array);
				return $posA - $posB;
			})->values();

			$seenUserIds = [];
			$userArticleCount = []; // Track count of articles per user

			$seenUserIdsInNew = [];
			$userNewArticleCount = []; // Track count of new articles per user

			$limit_formul_ekim = 350;
			$limit_yeni = 50;

			foreach ($categories as $category) {
				if ($category->slug === 'deneme' || $category->slug === 'bilimsel') {
					$seenUserIds = [];
					$userArticleCount = []; // Track count of articles per user

					$seenUserIdsInNew = [];
					$userNewArticleCount = []; // Track count of new articles per user
				}

				// Get articles and remove duplicates by user_id
				$articles = Article::where('parent_category_id', $category->id)
					->where('approved', 1)
					->where('deleted', 0)
					->where('is_published', 1)
					->where('moderation_flagged', 0)
					->orderBy('formul_ekim', 'DESC')
					->limit($limit_formul_ekim)
					->get();

				// Remove duplicate user_ids keeping first occurrence
				$uniqueArticles = collect();

				foreach ($articles as $article) {
					// Initialize count if not exists
					if (!isset($userArticleCount[$article->user_id])) {
						$userArticleCount[$article->user_id] = 0;
					}

					if ((!in_array($article->user_id, $seenUserIds) &&
						$userArticleCount[$article->user_id] < 2)) { // Limit to 2 articles per user
						$seenUserIds[] = $article->user_id;
						$userArticleCount[$article->user_id]++;
						$uniqueArticles->push($article);
					}
				}

				$category->articles = $uniqueArticles;

				// Do the same for yeni_articles
				$yeniArticles = Article::where('parent_category_id', $category->id)
					->where('approved', 1)
					->where('deleted', 0)
					->where('is_published', 1)
					->where('moderation_flagged', 0)
					->orderBy('created_at', 'DESC')
					->limit($limit_yeni)
					->get();

				// Remove duplicate user_ids keeping first occurrence
				$uniqueYeniArticles = collect();

				foreach ($yeniArticles as $article) {
					// Initialize count if not exists
					if (!isset($userNewArticleCount[$article->user_id])) {
						$userNewArticleCount[$article->user_id] = 0;
					}

					if ((!in_array($article->user_id, $seenUserIdsInNew) &&
						$userNewArticleCount[$article->user_id] < 2)) { // Limit to 2 articles per user
						$seenUserIdsInNew[] = $article->user_id;
						$userNewArticleCount[$article->user_id]++;
						$uniqueYeniArticles->push($article);
					}
				}

				$category->yeni_articles = $uniqueYeniArticles;
			}

			return view('frontend.index', compact('categories'));
		}

		public function search(Request $request, $page = 1)
		{
			$query = $request->input('q');

			if (strlen($query) < 3) {
				return view('frontend.search', [
					'error' => 'Arama sorgusu en az 3 karakter olmalıdır.',
					'query' => $query
				]);
			}

			// Get the base query
			$baseQuery = Article::where('approved', 1)
				->where('deleted', 0)
				->where('is_published', 1)
				->where(function ($q) use ($query) {
					$q->where('title', 'LIKE', '%' . $query . '%')
						->orWhere('name', 'LIKE', '%' . $query . '%');
				});

			// Get total count for pagination
			$total = $baseQuery->count();

			$page = (int)$page;

			// Get items for current page
			$items = $baseQuery->orderBy('created_at', 'DESC')
				->skip(($page - 1) * 20)
				->take(20)
				->get();

			// Create custom pagination
			$articles = new LengthAwarePaginator(
				$items,
				$total,
				20,
				$page,
				[
					'path' => request()->url(),
					'query' => request()->query()
				]
			);

			return view('frontend.search', compact('articles', 'query'));
		}


		public function recentTexts()
		{
			$articles = Article::where('approved', 1)
				->where('deleted', 0)
				->where('is_published', 1)
				->where('moderation_flagged', 0)
				->where('created_at', '>=', Carbon::now()->subDays(30))
				->orderBy('created_at', 'DESC')
				->limit(100)
				->get();

			$categories = Category::where('parent_category_id', 0)
				->orderBy('category_name')
				->get()
				->map(function ($category) {
					$category->new_count = Article::where('parent_category_id', $category->id)
						->where('approved', 1)
						->where('deleted', 0)
						->where('is_published', 1)
						->where('moderation_flagged', 0)
						->where('created_at', '>=', Carbon::now()->subDays(30))
						->count();
					return $category;
				});

			return view('frontend.recent-texts', compact('articles', 'categories'));
		}

		public function recentTextsByCategory($slug)
		{
			$category = Category::where('slug', $slug)->firstOrFail();

			$articles = Article::where('parent_category_id', $category->id)
				->where('approved', 1)
				->where('deleted', 0)
				->where('is_published', 1)
				->where('moderation_flagged', 0)
				->where('created_at', '>=', Carbon::now()->subDays(30))
				->orderBy('created_at', 'DESC')
				->limit(100)
				->get();

			$categories = Category::where('parent_category_id', 0)
				->orderBy('category_name')
				->get()
				->map(function ($cat) {
					$cat->new_count = Article::where('parent_category_id', $cat->id)
						->where('approved', 1)
						->where('deleted', 0)
						->where('is_published', 1)
						->where('moderation_flagged', 0)
						->where('created_at', '>=', Carbon::now()->subDays(30))
						->count();
					return $cat;
				});

			return view('frontend.recent-texts', compact('articles', 'categories', 'category'));
		}

		public function category($slug, $page = 1)
		{
			$category = Category::where('parent_category_id', 0)
				->where('slug', $slug)
				->firstOrFail();

			// Get the base query
			$query = Article::where('parent_category_id', $category->id)
				->where('approved', 1)
				->where('deleted', 0)
				->where('is_published', 1)
				->where('moderation_flagged', 0);

			// Get total count for pagination
			$total = $query->count();

			$page = (int)$page;

			// Get the items for current page
			$items = $query->orderBy('formul_ekim', 'DESC')
				->skip(($page - 1) * 20)
				->take(200)
				->get();

			// Create custom pagination
			$articles = new LengthAwarePaginator(
				$items,
				$total,
				20,
				$page,
				[
					'path' => request()->url(),
					'query' => request()->query()
				]
			);

			$sidebarTexts = Article::where('parent_category_id', $category->id)
				->where('approved', 1)
				->where('deleted', 0)
				->where('is_published', 1)
				->where('moderation_flagged', 0)
				->orderBy('created_at', 'DESC')
				->skip(($page - 1) * 10)
				->limit(100)
				->get();

			if ($articles->currentPage() > $articles->lastPage()) {
				abort(404);
			}

			return view('frontend.category', compact('category', 'articles', 'sidebarTexts'));
		}

		public function subcategory($categorySlug, $subcategorySlug, $page = 1)
		{
			// Get main category
			$category = Category::where('slug', $categorySlug)
				->where('parent_category_id', 0)
				->firstOrFail();

			// Get subcategory
			$subCategory = Category::where('slug', $subcategorySlug)
				->where('parent_category_id', $category->id)
				->firstOrFail();

			// Get the base query
			$query = Article::where('category_id', $subCategory->id)
				->where('approved', 1)
				->where('deleted', 0)
				->where('is_published', 1)
				->where('moderation_flagged', 0);

			// Get total count for pagination
			$total = $query->count();

			$page = (int)$page;

			// Get the items for current page
			$items = $query->orderBy('formul_ekim', 'DESC')
				->skip(($page - 1) * 20)
				->take(150)
				->get();

			// Create custom pagination
			$articles = new LengthAwarePaginator(
				$items,
				$total,
				20,
				$page,
				[
					'path' => request()->url(),
					'query' => request()->query()
				]
			);

			$sidebarTexts = Article::where('category_id', $subCategory->id)
				->where('approved', 1)
				->where('deleted', 0)
				->where('is_published', 1)
				->where('moderation_flagged', 0)
				->orderBy('created_at', 'DESC')
				->skip(($page - 1) * 10)
				->limit(100)
				->get();

			if ($articles->currentPage() > $articles->lastPage()) {
				abort(404);
			}

			return view('frontend.subcategory', compact('category', 'subCategory', 'articles', 'sidebarTexts'));
		}

		public function user($slug, $page = 1)
		{
			$user = User::where('slug', $slug)
				->with('following.following')
				->firstOrFail();

			$converter = new CommonMarkConverter([
				'html_input' => 'strip',
				'allow_unsafe_links' => false,
			]);

			$user->about_me = $converter->convertToHtml($user->about_me);
//			$about_me = $user->about_me;
//			$about_me = explode('\n', $about_me);
//			remove lines with only <br> tags if above line starts with <p or <h
//			foreach ($about_me as $key => $line) {
//				if (preg_match('/^<br>$/', $line) && ($key == 0 || preg_match('/^<p|^<h/', $about_me[$key - 1]))) {
//					unset($about_me[$key]);
//				}
//			}
//			$user->about_me = implode('\n', $about_me);


			// Get the base query for texts
			$query = Article::where('user_id', $user->id)
				->where('approved', 1)
				->where('is_published', 1)
				->where('deleted', 0);

			// Get total count for pagination
			$total = $query->count();

			//make $page int
			$page = (int)$page;

			// Get the items for current page
			$items = $query->orderBy('created_at', 'DESC')
				->skip(($page - 1) * 20)
				->take(20)
				->get();

			// Create custom pagination
			$articles = new LengthAwarePaginator(
				$items,
				$total,
				20,
				$page,
				[
					'path' => request()->url(),
					'query' => request()->query()
				]
			);

			// Get sidebar texts
			$sidebarTexts = Article::where('user_id', $user->id)
				->where('approved', 1)
				->where('deleted', 0)
				->where('is_published', 1)
				->orderBy('formul_ekim', 'DESC')
				->limit(20)
				->get();

			//check if $user->personal_url is not blank and if it is missing http add it
			$user->personal_url_link = $user->personal_url ?? '';
			if ($user->personal_url_link && !Str::startsWith($user->personal_url_link, ['http://', 'https://'])) {
				$user->personal_url_link = 'http://' . $user->personal_url;
			}

			return view('frontend.user', compact('user', 'articles', 'sidebarTexts'));
		}


		public function users($filter = 'yeni', $page = 1)
		{
			// Base query
			$query = User::whereExists(function ($query) {
				$query->select(DB::raw(1))
					->from('articles')
					->whereColumn('articles.user_id', 'users.id')
					->where('articles.approved', 1)
					->where('articles.deleted', 0);
			});

			// Apply filter
			if ($filter === 'yeni') {
				// Get users ordered by their latest text publication date
				$query->addSelect(['latest_text_date' => Article::select('created_at')
					->whereColumn('user_id', 'users.id')
					->where('approved', 1)
					->where('deleted', 0)
					->where('is_published', 1)
					->latest('created_at')
					->limit(1)
				])
					->orderBy('latest_text_date', 'desc');
			} elseif ($filter !== 'tumu' && mb_strlen($filter, 'UTF-8') === 1) {
				$query->where('name', 'LIKE', $filter . '%');
				$query->orderBy('name');
			}

			// Get total count for pagination
			$total = $query->count();

			$page = (int)$page;

			// Get items for current page
			$users = $query->skip(($page - 1) * 24)
				->take(24)
				->get();

			// Create custom pagination
			$users = new LengthAwarePaginator(
				$users,
				$total,
				24,
				$page,
				[
					'path' => request()->url(),
					'query' => request()->query()
				]
			);

			return view('frontend.authors', compact('users', 'filter'));
		}


		public function articlesByKeyword($slug, $page = 1)
		{
			// Get keyword information
			$keyword = Keyword::where('keyword_slug', $slug)->firstOrFail();

			// Get the base query
			$query = Article::whereHas('keywords', function ($q) use ($keyword) {
				$q->where('keywords.id', $keyword->id);
			})
				->where('approved', 1)
				->where('deleted', 0)
				->where('is_published', 1);

			// Get total count for pagination
			$total = $query->count();

			$page = (int)$page;

			// Get items for current page
			$items = $query->orderBy('formul_ekim', 'DESC')
				->skip(($page - 1) * 21)
				->take(21)
				->get();

			// Create custom pagination
			$articles = new LengthAwarePaginator(
				$items,
				$total,
				21,
				$page,
				[
					'path' => request()->url(),
					'query' => request()->query()
				]
			);

			return view('frontend.articles-by-keyword', compact('articles', 'keyword'));
		}

		public function article($slug)
		{
			Log::debug('Article page accessed: ' . $slug);
			// Get the article
			$article = Article::where('slug', $slug)
				->where('approved', 1)
				->where('deleted', 0)
				->where('is_published', 1)
				->firstOrFail();
			Log::debug('Article found: ' . $article->id);

			$converter = new CommonMarkConverter([
				'html_input' => 'strip',
				'allow_unsafe_links' => false,
			]);

			Log::debug('Converting article text to HTML...');
			try {
				$article->main_text = $converter->convertToHtml($article->main_text);
			} catch (\Exception $e) {
				Log::error('Error converting article text to HTML: ' . $e->getMessage());
				$article->main_text = str_replace("\n", '<br>', $article->main_text);
			}
			Log::debug('Article text converted to HTML');

			// Get the user
			Log::debug('Finding user... with id: ' . $article->user_id);
			$user = User::findOrFail($article->user_id);
			Log::debug('User found: ' . $user->id);

			// Get keywords for this article
			$keywords = $article->keywords()
				->where('count', '>', 1)
				->get();
			Log::debug('Keywords found: ' . $keywords->count());

			// Get related posts (you may want to customize this query)
			$sameUserAndCategory = Article::where('category_id', $article->category_id)
				->where('user_id', $article->user_id)
				->where('id', '!=', $article->id)
				->where('approved', 1)
				->where('deleted', 0)
				->where('is_published', 1)
				->orderBy('created_at', 'DESC')
				->limit(3)
				->get();

			$sameUserAndMainCategory = \App\Models\Article::where('parent_category_id', $article->parent_category_id)
				->where('user_id', $article->user_id)
				->where('id', '!=', $article->id)
				->whereNotIn('id', $sameUserAndCategory->pluck('id'))
				->where('approved', 1)
				->where('deleted', 0)
				->where('is_published', 1)
				->orderBy('created_at', 'DESC')
				->limit(3)
				->get();

			$otherUserArticles = \App\Models\Article::where('user_id', $article->user_id)
				->where('id', '!=', $article->id)
				->whereNotIn('id', $sameUserAndCategory->pluck('id'))
				->whereNotIn('id', $sameUserAndMainCategory->pluck('id'))
				->where('approved', 1)
				->where('deleted', 0)
				->where('is_published', 1)
				->orderBy('created_at', 'DESC')
				->limit(6)
				->get();

			return view('frontend.article', compact('article', 'user', 'keywords', 'sameUserAndCategory', 'sameUserAndMainCategory', 'otherUserArticles'));
		}
	}
