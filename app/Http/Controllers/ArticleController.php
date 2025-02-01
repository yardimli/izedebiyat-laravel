<?php

	namespace App\Http\Controllers;

	use App\Models\Clap;
	use App\Models\Image;
	use App\Models\Category;
	use App\Models\Article;
	use App\Models\Keyword;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Str;
	use App\Helpers\IdHasher;

	class ArticleController extends Controller
	{
		public function index(Request $request)
		{
			$query = Article::where('user_id', Auth::id())
				->where('approved', 1)
				->where('deleted', 0);

			// Apply filters
			if ($request->has('search')) {
				$query->where('title', 'like', '%' . $request->search . '%');
			}

			if ($request->has('status')) {
				if ($request->status === 'published') {
					$query->where('is_published', 1);
				} elseif ($request->status === 'draft') {
					$query->where('is_published', 0);
				}
			}

			if ($request->has('sort')) {
				switch ($request->sort) {
					case 'read_count':
						$query->orderBy('read_count', 'desc');
						break;
					case 'newest':
						$query->orderBy('created_at', 'desc');
						break;
					case 'oldest':
						$query->orderBy('created_at', 'asc');
						break;
				}
			} else {
				$query->orderBy('id', 'desc');
			}

			$articles = $query->paginate(10);

			foreach ($articles as $article) {
				$article->title = strip_tags($article->title);
				$article->subtitle = strip_tags($article->subtitle);
				$article->subheading = strip_tags($article->subheading);
			}

			return view('backend.articles', compact('articles'));
		}

		public function create()
		{
			$categories = Category::where('parent_category_id', 0)
				->with('subCategories')
				->get();
			return view('backend.article', compact('categories'));
		}

		public function edit($hashedId)
		{
			$id = IdHasher::decode($hashedId);
			if (!$id) {
				abort(404);
			}

			$article = Article::with('keywords')->findOrFail($id);

			$categories = Category::where('parent_category_id', 0)
				->with('subCategories')
				->get();

			$article->title = strip_tags($article->title);
			$article->subtitle = strip_tags($article->subtitle);
			$article->subheading = strip_tags($article->subheading);

			$article->main_text = str_replace('<br>', "\n", $article->main_text);
			$article->main_text = str_replace('<br/>', "\n", $article->main_text);
			$article->main_text = str_replace('<br />', "\n", $article->main_text);
			$article->main_text = str_replace('<p>', '\n', $article->main_text);
			$article->main_text = str_replace('</p>', '\n', $article->main_text);

			//change keywords to json ['value' => 'keyword']
			$keywords = $article->keywords->map(function ($keyword) {
				return ['value' => $keyword->keyword];
			});

			return view('backend.article', compact('article', 'categories'));
		}

		public function store(Request $request)
		{
		if (!Auth::check()) {
			return redirect()->route('frontend.join');
		}

			$validated = $request->validate([
				'title' => 'required|max:255',
				'subtitle' => 'nullable|max:255',
				'main_text' => 'required',
				'subheading' => 'nullable|max:500',
				'featured_image' => 'nullable|exists:images,id',
				'category_id' => 'nullable|exists:categories,id', // Update this line
				'is_published' => 'boolean',
				'keywords' => 'nullable|string|max:255',
			]);

			$validated['is_published'] = $request->has('is_published');
			$validated['user_id'] = Auth::id();
			$validated['approved'] = 1;
			$validated['slug'] = Str::slug($validated['title']);

			$article = Article::create($validated);

			if ($request->has('keywords')) {
				$keywordsData = json_decode($request->keywords, true);
				$keywords = collect($keywordsData)->pluck('value')->join(', ');

				$article->keywords_string = $keywords;
				$article->save();

				$this->syncKeywords($article, $keywords);
			}

			return redirect()->route('articles.index')
				->with('success', __('default.Article created successfully.'));
		}

		public function update(Request $request, $hashedId)
		{
			$id = IdHasher::decode($hashedId);
			if (!$id) {
				abort(404);
			}

			$article = Article::findOrFail($id);

			$validated = $request->validate([
				'title' => 'required|max:255',
				'subtitle' => 'nullable|max:255',
				'main_text' => 'required',
				'subheading' => 'nullable|max:500',
				'featured_image' => 'nullable|exists:images,id',
				'category_id' => 'nullable|exists:categories,id', // Update this line
				'is_published' => 'boolean',
				'keywords' => 'nullable|string|max:255',
			]);


			// Set is_published based on checkbox
			$validated['is_published'] = $request->has('is_published');

			if ($request->has('keywords')) {
				$keywordsData = json_decode($request->keywords, true);
				$keywords = collect($keywordsData)->pluck('value')->join(', ');

				$validated['keywords_string'] = $keywords;

				$this->syncKeywords($article, $keywords);
			} else
			{
				$validated['keywords_string'] = '';
			}

			$article->update($validated);

			return redirect()->route('articles.index')
				->with('success', __('default.Text updated successfully.'));
		}

		public function destroy($hashedId)
		{
			$id = IdHasher::decode($hashedId);
			if (!$id) {
				abort(404);
			}

			$article = Article::findOrFail($id);
			$article->categories()->detach();
			$article->delete();

			return redirect()->route('articles.index')
				->with('success', __('default.Text deleted successfully.'));
		}

		// Add method to handle image loading for the modal
		public function getImages()
		{
			$images = Image::where('user_id', auth()->id())
				->orderBy('created_at', 'desc')
				->paginate(9);  // 9 images per page

			return response()->json([
				'images' => $images,
				'pagination' => [
					'current_page' => $images->currentPage(),
					'last_page' => $images->lastPage(),
					'per_page' => $images->perPage(),
					'total' => $images->total()
				]
			]);
		}

		public function storeArticleImage(Request $request)
		{
			$request->validate([
				'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
			]);

			try {
				$image = new Image();
				$file = $request->file('image');

				// Generate unique filename
				$filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

				// Store original image
				$file->storeAs('public/yresimler/original', $filename);

				return response()->json([
					'success' => true,
					'url' => asset('storage/yresimler/original/' . $filename),
					'id' => $image->id
				]);
			} catch (\Exception $e) {
				return response()->json([
					'success' => false,
					'message' => __('default.Upload failed') . ': ' . $e->getMessage()
				], 500);
			}
		}

		public function searchKeywords(Request $request)
		{
			$search = $request->get('q');

			$keywords = Keyword::where('keyword', 'like', '' . $search . '%')
				->select('keyword') // Only select what we need
				->limit(10)
				->orderBy('keyword')
				->get()
				->pluck('keyword'); // Get just the keyword values

			return response()->json($keywords);
		}

		private function syncKeywords($article, $keywordString)
		{
			$keywordArray = array_map('trim', preg_split('/[,\s]+/', $keywordString));
			$keywordArray = array_filter($keywordArray); // Remove empty values

			$keywordIds = [];
			foreach ($keywordArray as $keywordText) {
				$keywordText = substr($keywordText, 0, 16); // Limit to 16 characters
				if (empty($keywordText)) continue;

				// Find or create keyword
				$keyword = Keyword::firstOrCreate(
					['keyword' => $keywordText],
					['keyword_slug' => Str::slug($keywordText)]
				);

				$keywordIds[] = $keyword->id;
			}

			// Sync keywords with article
			$article->keywords()->sync($keywordIds);

			return implode(', ', $keywordArray);
		}

	public function toggleClap(Request $request, Article $article)
	{
		if (!Auth::check()) {
			return redirect()->route('frontend.join');
		}

		$clap = Clap::firstOrNew([
			'user_id' => Auth::id(),
			'article_id' => $article->id
		]);

		if ($clap->count < 50) {
			$clap->count++;
			$clap->save();
		}

		$totalClaps = Clap::where('article_id', $article->id)->sum('count');

		return response()->json(['claps' => $totalClaps]);
	}
	}
