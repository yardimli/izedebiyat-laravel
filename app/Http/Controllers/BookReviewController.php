<?php

	namespace App\Http\Controllers;

	use App\Models\BookAuthor; // ADDED: Import BookAuthor model
	use App\Models\BookReview;
	use App\Models\BookCategory;
	use App\Models\BookTag;
	use Carbon\Carbon; // ADDED: Import Carbon for date parsing
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Storage; // ADDED: For file handling
	use Illuminate\Support\Facades\Validator; // ADDED: For custom validation
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
				// MODIFIED: Allow ingest method to bypass auth middleware
				if ($request->route()->getName() === 'book-reviews.ingest') {
					return $next($request);
				}
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
			$bookReviews = BookReview::with('user', 'bookAuthor')->latest()->paginate(20); // MODIFIED: Eager load bookAuthor
			return view('backend.book_reviews.index', compact('bookReviews'));
		}

		/**
		 * Show the form for creating a new resource.
		 *
		 * @return \Illuminate\Http\Response
		 */
		public function create()
		{
			// ADDED: Fetch authors for the dropdown
			$authors = BookAuthor::orderBy('name')->get();
			return view('backend.book_reviews.create', compact('authors'));
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
			// ADDED: Fetch authors for the dropdown
			$authors = BookAuthor::orderBy('name')->get();
			return view('backend.book_reviews.edit', compact('bookReview', 'authors'));
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
				'author' => 'required_without:book_author_id|nullable|string|max:255', // MODIFIED: Manual author is only required if no author is selected from DB
				'book_author_id' => 'nullable|exists:book_authors,id', // ADDED: Validate the selected author
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

		// ADDED: New method to handle book ingestion from a script.
		/**
		 * Store a new book review from an automated script.
		 *
		 * @param \Illuminate\Http\Request $request
		 * @return \Illuminate\Http\JsonResponse
		 */
		public function ingest(Request $request)
		{
			// 1. Validate incoming data
			$validator = Validator::make($request->all(), [
				'title' => 'required|string|max:255',
				'author' => 'required|string|max:255',
				'description' => 'required|string',
				'publication_info' => 'nullable|string',
				'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
			]);

			if ($validator->fails()) {
				return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
			}

			// 2. Check if the book already exists
			$existingBook = BookReview::where('title', $request->input('title'))
				->where('author', $request->input('author'))
				->exists();

			if ($existingBook) {
				return response()->json(['status' => 'skipped', 'message' => 'Book already exists'], 200);
			}

			// 3. Process and store the new book
			try {
				$data = [
					'title' => $request->input('title'),
					'author' => $request->input('author'),
					'slug' => Str::slug($request->input('title')),
					'review_content' => $request->input('description'),
					'is_published' => false, // Set as not published by default
					'user_id' => 1, // Assign to a default admin user (ID 1)
				];

				// 3a. Parse publication info
				if ($request->has('publication_info')) {
					$parsedInfo = $this->parsePublicationInfo($request->input('publication_info'));
					$data['publisher'] = $parsedInfo['publisher'];
					$data['publication_date'] = $parsedInfo['date'];
				}

				// 3b. Handle file upload
				if ($request->hasFile('cover_image')) {
					$path = $request->file('cover_image')->store('public/book_covers');
					$data['cover_image'] = Storage::url($path);
				}

				BookReview::create($data);

				return response()->json(['status' => 'success', 'message' => 'Book review created successfully'], 201);
			} catch (\Exception $e) {
				// Log the error for debugging
				\Illuminate\Support\Facades\Log::error('Book ingestion failed: ' . $e->getMessage());
				return response()->json(['status' => 'error', 'message' => 'An internal error occurred.'], 500);
			}
		}

		// ADDED: Helper function to parse publication string.
		/**
		 * Parses the publication info string from Goodreads.
		 * E.g., "October 1, 2003 by Yapı Kredi Yayınları"
		 *
		 * @param string $info
		 * @return array
		 */
		private function parsePublicationInfo(string $info): array
		{
			$publisher = null;
			$date = null;

			// Split the string by " by " to separate date and publisher
			$parts = explode(' by ', $info);

			if (count($parts) === 2) {
				$publisher = trim($parts[1]);
				try {
					// Carbon can parse many date formats
					$date = Carbon::parse(trim($parts[0]))->toDateString();
				} catch (\Exception $e) {
					$date = null; // Set to null if parsing fails
				}
			}

			return [
				'publisher' => $publisher,
				'date' => $date
			];
		}
	}
