<?php

	namespace App\Http\Controllers;

	use App\Helpers\MyHelper;
	use App\Models\Kategori;
	use App\Models\Keyword;
	use App\Models\Yazar;
	use App\Models\Yazi;
	use Carbon\Carbon;
	use GuzzleHttp\Client;
	use Illuminate\Http\Request;
	use App\Models\User;
	use Illuminate\Pagination\LengthAwarePaginator;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Facades\File;
	use Illuminate\Support\Facades\Log;
	use Illuminate\Support\Facades\Storage;
	use Illuminate\Support\Str;
	use Illuminate\Support\Facades\Validator;


	class FrontendController extends Controller
	{

		protected $mainMenuCategories;

		public function __construct()
		{
			// Get main menu categories with their last update time
			$firstCategory = Kategori::orderBy('updated_at')->first();

			// Check if first category exists and if it was updated more than 2 minutes ago
			if ($firstCategory && $firstCategory->updated_at->diffInMinutes(now()) > 60) {
				Log::info('Updating category statistics...');

				// Get all categories (including main and sub categories)
				$allCategories = Kategori::all();

				foreach ($allCategories as $category) {
					// Count total approved texts
					$totalTexts = Yazi::where(function($query) use ($category) {
						$query->where('kategori_id', $category->id)
							->orWhere('ust_kategori_id', $category->id);
					})
						->where('onay', 1)
						->where('silindi', 0)
						->where('bad_critical', '<', 4)
						->where('religious_moderation_value', '<', 3)
						->where('respect_moderation_value', '>=', 3)
						->where('moderation_flagged', 0)
						->count();

					// Count new texts (last 30 days)
					$newTexts = Yazi::where(function($query) use ($category) {
						$query->where('kategori_id', $category->id)
							->orWhere('ust_kategori_id', $category->id);
					})
						->where('onay', 1)
						->where('silindi', 0)
						->where('bad_critical', '<', 4)
						->where('religious_moderation_value', '<', 3)
						->where('respect_moderation_value', '>=', 3)
						->where('moderation_flagged', 0)
						->where('katilma_tarihi', '>=', now()->subDays(30))
						->count();

					// Get total views/reads
					$totalReads = Yazi::where(function($query) use ($category) {
						$query->where('kategori_id', $category->id)
							->orWhere('ust_kategori_id', $category->id);
					})
						->where('onay', 1)
						->where('silindi', 0)
						->sum('sayac');

					// Update category
					$category->update([
						'kac_yazi' => $totalTexts,
						'kac_yeni_yazi' => $newTexts,
						'okuma_sayac' => $totalReads,
						'updated_at' => now()
					]);
				}
			}

			// Get main menu categories for the view
			$this->mainMenuCategories = Kategori::where('ust_kategori_id', 0)
				->orderBy('slug')
				->with('subCategories')
				->get();

			// Share with all views
			view()->share('mainMenuCategories', $this->mainMenuCategories);
		}


		public function index()
		{
			$categories = Kategori::where('ust_kategori_id', 0)
				->orderBy('kategori_ad')
				->get();

			foreach ($categories as $category) {
				$category->yazilar = Yazi::where('ust_kategori_id', $category->id)
					->where('onay', 1)
					->where('silindi', 0)
					->where('bad_critical', '<', 4)
					->where('religious_moderation_value', '<', 3)
					->where('respect_moderation_value', '>=', 3)
					->where('moderation_flagged', 0)
					->orderBy('formul_ekim', 'DESC')
					->limit(50)
					->get();

				$category->yeni_yazilar = Yazi::where('ust_kategori_id', $category->id)
					->where('onay', 1)
					->where('silindi', 0)
					->where('bad_critical', '<', 3)
					->where('religious_moderation_value', '<', 3)
					->where('respect_moderation_value', '>=', 3)
					->where('moderation_flagged', 0)
					->orderBy('id', 'DESC')
					->limit(100)
					->get();
			}

			//dd($categories);

			return view('frontend.index', compact('categories'));
		}


		public function recentTexts()
		{
			$texts = Yazi::where('onay', 1)
				->where('silindi', 0)
				->where('bad_critical', '<', 4)
				->where('religious_moderation_value', '<', 3)
				->where('respect_moderation_value', '>=', 3)
				->where('moderation_flagged', 0)
				->where('katilma_tarihi', '>=', Carbon::now()->subDays(30))
				->orderBy('katilma_tarihi', 'DESC')
				->limit(100)
				->get();

			$categories = Kategori::where('ust_kategori_id', 0)
				->orderBy('kategori_ad')
				->get()
				->map(function ($category) {
					$category->new_count = Yazi::where('ust_kategori_id', $category->id)
						->where('onay', 1)
						->where('silindi', 0)
						->where('bad_critical', '<', 4)
						->where('religious_moderation_value', '<', 3)
						->where('respect_moderation_value', '>=', 3)
						->where('moderation_flagged', 0)
						->where('katilma_tarihi', '>=', Carbon::now()->subDays(30))
						->count();
					return $category;
				});

			return view('frontend.recent-texts', compact('texts', 'categories'));
		}

		public function recentTextsByCategory($slug)
		{
			$category = Kategori::where('slug', $slug)->firstOrFail();

			$texts = Yazi::where('ust_kategori_id', $category->id)
				->where('onay', 1)
				->where('silindi', 0)
				->where('bad_critical', '<', 4)
				->where('religious_moderation_value', '<', 3)
				->where('respect_moderation_value', '>=', 3)
				->where('moderation_flagged', 0)
				->where('katilma_tarihi', '>=', Carbon::now()->subDays(30))
				->orderBy('katilma_tarihi', 'DESC')
				->limit(100)
				->get();

			$categories = Kategori::where('ust_kategori_id', 0)
				->orderBy('kategori_ad')
				->get()
				->map(function ($cat) {
					$cat->new_count = Yazi::where('ust_kategori_id', $cat->id)
						->where('onay', 1)
						->where('silindi', 0)
						->where('bad_critical', '<', 4)
						->where('religious_moderation_value', '<', 3)
						->where('respect_moderation_value', '>=', 3)
						->where('moderation_flagged', 0)
						->where('katilma_tarihi', '>=', Carbon::now()->subDays(30))
						->count();
					return $cat;
				});

			return view('frontend.recent-texts', compact('texts', 'categories', 'category'));
		}

		public function category($slug, $page = 1)
		{
			$category = Kategori::where('ust_kategori_id', 0)
				->where('slug', $slug)
				->firstOrFail();

			// Get the base query
			$query = Yazi::where('ust_kategori_id', $category->id)
				->where('onay', 1)
				->where('silindi', 0)
				->where('bad_critical', '<', 4)
				->where('religious_moderation_value', '<', 3)
				->where('respect_moderation_value', '>=', 3)
				->where('moderation_flagged', 0);

			// Get total count for pagination
			$total = $query->count();

			// Get the items for current page
			$items = $query->orderBy('formul_ekim', 'DESC')
				->skip(($page-1) * 20)
				->take(50)
				->get();

			// Create custom pagination
			$texts = new LengthAwarePaginator(
				$items,
				$total,
				20,
				$page,
				[
					'path' => request()->url(),
					'query' => request()->query()
				]
			);

			$sidebarTexts = Yazi::where('ust_kategori_id', $category->id)
				->where('onay', 1)
				->where('silindi', 0)
				->where('bad_critical', '<', 4)
				->where('religious_moderation_value', '<', 3)
				->where('respect_moderation_value', '>=', 3)
				->where('moderation_flagged', 0)
				->orderBy('katilma_tarihi', 'DESC')
				->skip(($page-1) * 10)
				->limit(100)
				->get();

			if ($texts->currentPage() > $texts->lastPage()) {
				abort(404);
			}

			return view('frontend.category', compact('category', 'texts', 'sidebarTexts'));
		}

		public function subcategory($categorySlug, $subcategorySlug, $page = 1)
		{
			// Get main category
			$category = Kategori::where('slug', $categorySlug)
				->where('ust_kategori_id', 0)
				->firstOrFail();

			// Get subcategory
			$subCategory = Kategori::where('slug', $subcategorySlug)
				->where('ust_kategori_id', $category->id)
				->firstOrFail();

			// Get the base query
			$query = Yazi::where('kategori_id', $subCategory->id)
				->where('onay', 1)
				->where('silindi', 0)
				->where('bad_critical', '<', 4)
				->where('religious_moderation_value', '<', 3)
				->where('respect_moderation_value', '>=', 3)
				->where('moderation_flagged', 0);

			// Get total count for pagination
			$total = $query->count();

			// Get the items for current page
			$items = $query->orderBy('formul_ekim', 'DESC')
				->skip(($page-1) * 20)
				->take(50)
				->get();

			// Create custom pagination
			$texts = new LengthAwarePaginator(
				$items,
				$total,
				20,
				$page,
				[
					'path' => request()->url(),
					'query' => request()->query()
				]
			);

			$sidebarTexts = Yazi::where('kategori_id', $subCategory->id)
				->where('onay', 1)
				->where('silindi', 0)
				->where('bad_critical', '<', 3)
				->where('religious_moderation_value', '<', 3)
				->where('respect_moderation_value', '>=', 3)
				->where('moderation_flagged', 0)
				->orderBy('katilma_tarihi', 'DESC')
				->skip(($page-1) * 10)
				->limit(100)
				->get();

			if ($texts->currentPage() > $texts->lastPage()) {
				abort(404);
			}

			return view('frontend.subcategory', compact('category', 'subCategory', 'texts', 'sidebarTexts'));
		}

		public function author($slug, $page = 1)
		{
			$author = Yazar::where('slug', $slug)->firstOrFail();

			// Get the base query for texts
			$query = Yazi::where('yazar_id', $author->id)
				->where('onay', 1)
				->where('silindi', 0);

			// Get total count for pagination
			$total = $query->count();

			// Get the items for current page
			$items = $query->orderBy('katilma_tarihi', 'DESC')
				->skip(($page-1) * 20)
				->take(20)
				->get();

			// Create custom pagination
			$texts = new LengthAwarePaginator(
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
			$sidebarTexts = Yazi::where('yazar_id', $author->id)
				->where('onay', 1)
				->where('silindi', 0)
				->orderBy('formul_ekim', 'DESC')
				->limit(20)
				->get();

			return view('frontend.author', compact('author', 'texts', 'sidebarTexts'));
		}


		public function authors($filter = 'tumu', $page = 1)
		{
			// Base query
			$query = Yazar::whereExists(function ($query) {
				$query->select(DB::raw(1))
					->from('yazilar')
					->whereColumn('yazilar.yazar_id', 'yazar.id')
					->where('yazilar.onay', 1)
					->where('yazilar.silindi', 0);
			});

			// Apply filter if not 'all'
			if ($filter !== 'tumu' && mb_strlen($filter, 'UTF-8') === 1) {
				$query->where('yazar_ad', 'LIKE', $filter . '%');
			}

			// Get total count for pagination
			$total = $query->count();

			// Get items for current page
			$authors = $query->orderBy('yazar_ad')
				->skip(($page-1) * 24)
				->take(24)
				->get();

			// Create custom pagination
			$authors = new LengthAwarePaginator(
				$authors,
				$total,
				24,
				$page,
				[
					'path' => request()->url(),
					'query' => request()->query()
				]
			);

			return view('frontend.authors', compact('authors', 'filter'));
		}


		public function articlesByKeyword($slug, $page = 1)
		{
			// Get keyword information
			$keyword = Keyword::where('keyword_slug', $slug)->firstOrFail();

			// Get the base query
			$query = Yazi::whereHas('keywords', function($q) use ($keyword) {
				$q->where('keywords.id', $keyword->id);
			})
				->where('onay', 1)
				->where('silindi', 0)
				->where('bad_critical', '<', 4);

			// Get total count for pagination
			$total = $query->count();

			// Get items for current page
			$items = $query->orderBy('formul_ekim', 'DESC')
				->skip(($page-1) * 21)
				->take(21)
				->get();

			// Create custom pagination
			$texts = new LengthAwarePaginator(
				$items,
				$total,
				21,
				$page,
				[
					'path' => request()->url(),
					'query' => request()->query()
				]
			);

			return view('frontend.articles-by-keyword', compact('texts', 'keyword'));
		}

		public function article($slug)
		{
			// Get the article
			$article = Yazi::where('slug', $slug)
				->where('onay', 1)
				->where('silindi', 0)
				->firstOrFail();

			// Get the author
			$author = Yazar::findOrFail($article->yazar_id);

			// Get keywords for this article
			$keywords = $article->keywords()
				->where('count', '>', 1)
				->get();

			// Get related posts (you may want to customize this query)
			$sameAuthorAndCategory = Yazi::where('kategori_id', $article->kategori_id)
				->where('yazar_id', $article->yazar_id)
				->where('id', '!=', $article->id)
				->where('onay', 1)
				->where('silindi', 0)
				->where('bad_critical', '<', 3)
				->orderBy('katilma_tarihi', 'DESC')
				->limit(3)
				->get();

			$sameAuthorAndMainCategory = \App\Models\Yazi::where('ust_kategori_id', $article->ust_kategori_id)
				->where('yazar_id', $article->yazar_id)
				->where('id', '!=', $article->id)
				->whereNotIn('id', $sameAuthorAndCategory->pluck('id'))
				->where('onay', 1)
				->where('silindi', 0)
				->where('bad_critical', '<', 3)
				->orderBy('katilma_tarihi', 'DESC')
				->limit(3)
				->get();

			$otherAuthorArticles = \App\Models\Yazi::where('yazar_id', $article->yazar_id)
				->where('id', '!=', $article->id)
				->whereNotIn('id', $sameAuthorAndCategory->pluck('id'))
				->whereNotIn('id', $sameAuthorAndMainCategory->pluck('id'))
				->where('onay', 1)
				->where('silindi', 0)
				->where('bad_critical', '<', 3)
				->orderBy('katilma_tarihi', 'DESC')
				->limit(6)
				->get();

			return view('frontend.article', compact('article', 'author', 'keywords', 'sameAuthorAndCategory', 'sameAuthorAndMainCategory', 'otherAuthorArticles'));
		}

	}
