<?php

	namespace App\Http\Controllers;

	use App\Helpers\MyHelper;
	use App\Models\Kategori;
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

					// Count new texts (last 60 days)
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
						->where('katilma_tarihi', '>=', now()->subDays(60))
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
					->where('bad_critical', '<', 3)
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
				->where('bad_critical', '<', 3)
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
				->where('bad_critical', '<', 3)
				->where('religious_moderation_value', '<', 3)
				->where('respect_moderation_value', '>=', 3)
				->where('moderation_flagged', 0)
				->orderBy('katilma_tarihi', 'DESC')
				->skip(($page-1) * 10)
				->limit(300)
				->get();

			if ($texts->currentPage() > $texts->lastPage()) {
				abort(404);
			}

			return view('frontend.category', compact('category', 'texts', 'sidebarTexts'));
		}

	}
