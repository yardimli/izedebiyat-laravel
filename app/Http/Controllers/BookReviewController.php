<?php

	namespace App\Http\Controllers;

	use App\Models\BookReview;
	use App\Models\BookCategory;
	use App\Models\BookTag;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Storage; // ADDED: For file handling
	use Illuminate\Support\Str;

	class BookReviewController extends Controller
	{
		/**
		 * Create a new controller instance.
		 *
		 * @return void
		 */
		public function __construct()
		{
			// Middleware to ensure only admins can access these methods
			$this->middleware(function ($request, $next) {
				if (Auth::check() && Auth::user()->isAdmin()) {
					return $next($request);
				}
				abort(403, 'Unauthorized action.');
			});
		}

		/**
		 * Display a listing of the resource.
		 *
		 * @return \Illuminate\Http\Response
		 */
		public function index()
		{
			$bookReviews = BookReview::with('user')->latest()->paginate(20);
			return view('backend.book_reviews.index', compact('bookReviews'));
		}

		/**
		 * Show the form for creating a new resource.
		 *
		 * @return \Illuminate\Http\Response
		 */
		public function create()
		{
			return view('backend.book_reviews.create');
		}

		/**
		 * Store a newly created resource in storage.
		 *
		 * @param \Illuminate\Http\Request $request
		 * @return \Illuminate\Http\Response
		 */
		public function store(Request $request)
		{
			$validated = $this->validateRequest($request);
			$validated['user_id'] = Auth::id();
			$validated['slug'] = Str::slug($validated['title']);

			// MODIFIED: Handle file upload for cover image
			if ($request->hasFile('cover_image')) {
				$path = $request->file('cover_image')->store('public/book_covers');
				$validated['cover_image'] = Storage::url($path); // Store the public URL
			}

			$bookReview = BookReview::create($validated);
			$this->syncCategoriesAndTags($bookReview, $request);

			return redirect()->route('book-reviews.index')->with('success', __('default.Book review created successfully.'));
		}

		/**
		 * Show the form for editing the specified resource.
		 *
		 * @param \App\Models\BookReview $bookReview
		 * @return \Illuminate\Http\Response
		 */
		public function edit(BookReview $bookReview)
		{
			return view('backend.book_reviews.edit', compact('bookReview'));
		}

		/**
		 * Update the specified resource in storage.
		 *
		 * @param \Illuminate\Http\Request $request
		 * @param \App\Models\BookReview $bookReview
		 * @return \Illuminate\Http\Response
		 */
		public function update(Request $request, BookReview $bookReview)
		{
			$validated = $this->validateRequest($request);
			$validated['slug'] = Str::slug($validated['title']);

			// MODIFIED: Handle file upload for update
			if ($request->hasFile('cover_image')) {
				// Delete old image if it exists
				if ($bookReview->cover_image) {
					$oldPath = str_replace('/storage', 'public', $bookReview->cover_image);
					Storage::delete($oldPath);
				}
				// Store new image
				$path = $request->file('cover_image')->store('public/book_covers');
				$validated['cover_image'] = Storage::url($path);
			}

			$bookReview->update($validated);
			$this->syncCategoriesAndTags($bookReview, $request);

			return redirect()->route('book-reviews.index')->with('success', __('default.Book review updated successfully.'));
		}

		/**
		 * Remove the specified resource from storage.
		 *
		 * @param \App\Models\BookReview $bookReview
		 * @return \Illuminate\Http\Response
		 */
		public function destroy(BookReview $bookReview)
		{
			// ADDED: Delete cover image from storage
			if ($bookReview->cover_image) {
				// Convert public URL back to storage path
				$imagePath = str_replace('/storage', 'public', $bookReview->cover_image);
				Storage::delete($imagePath);
			}

			$bookReview->delete();
			return redirect()->route('book-reviews.index')->with('success', __('default.Book review deleted successfully.'));
		}

		/**
		 * Validate the request for store and update.
		 *
		 * @param Request $request
		 * @return array
		 */
		private function validateRequest(Request $request)
		{
			// MODIFIED: Updated validation rules for file upload and new fields
			return $request->validate([
				'title' => 'required|string|max:255',
				'author' => 'required|string|max:255',
				'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
				'review_content' => 'required|string',
				'is_published' => 'boolean',
				'categories' => 'nullable|string',
				'tags' => 'nullable|string',
				'publisher' => 'nullable|string|max:255',
				'publication_date' => 'nullable|date',
				'publication_place' => 'nullable|string|max:255',
				'buy_url' => 'nullable|url|max:255',
			]);
		}

		/**
		 * Sync categories and tags for the book review.
		 *
		 * @param BookReview $bookReview
		 * @param Request $request
		 */
		private function syncCategoriesAndTags(BookReview $bookReview, Request $request)
		{
			// Sync Categories
			$categoryIds = [];
			if ($request->has('categories')) {
				$categoriesData = json_decode($request->categories, true);
				$categoryNames = collect($categoriesData)->pluck('value');
				foreach ($categoryNames as $name) {
					$category = BookCategory::firstOrCreate(
						['name' => trim($name)],
						['slug' => Str::slug(trim($name))]
					);
					$categoryIds[] = $category->id;
				}
			}
			$bookReview->categories()->sync($categoryIds);

			// Sync Tags
			$tagIds = [];
			if ($request->has('tags')) {
				$tagsData = json_decode($request->tags, true);
				$tagNames = collect($tagsData)->pluck('value');
				foreach ($tagNames as $name) {
					$tag = BookTag::firstOrCreate(
						['name' => trim($name)],
						['slug' => Str::slug(trim($name))]
					);
					$tagIds[] = $tag->id;
				}
			}
			$bookReview->tags()->sync($tagIds);
		}
	}
