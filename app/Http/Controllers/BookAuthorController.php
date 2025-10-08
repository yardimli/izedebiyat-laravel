<?php

	namespace App\Http\Controllers;

	use App\Models\BookAuthor;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Storage;
	use Illuminate\Support\Str;

	class BookAuthorController extends Controller
	{
		/**
		 * Create a new controller instance.
		 *
		 * @return void
		 */
		public function __construct()
		{
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
			$authors = BookAuthor::latest()->paginate(20);
			return view('backend.book_authors.index', compact('authors'));
		}

		/**
		 * Show the form for creating a new resource.
		 *
		 * @return \Illuminate\Http\Response
		 */
		public function create()
		{
			return view('backend.book_authors.create');
		}

		/**
		 * Store a newly created resource in storage.
		 *
		 * @param  \Illuminate\Http\Request  $request
		 * @return \Illuminate\Http\Response
		 */
		public function store(Request $request)
		{
			$validated = $request->validate([
				'name' => 'required|string|max:255|unique:book_authors,name',
				'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
				'biography' => 'nullable|string',
				'bibliography' => 'nullable|string',
			]);

			$validated['slug'] = Str::slug($validated['name']);

			if ($request->hasFile('picture')) {
				$path = $request->file('picture')->store('public/book_authors');
				$validated['picture'] = Storage::url($path);
			}

			BookAuthor::create($validated);

			return redirect()->route('book-authors.index')->with('success', 'Yazar başarıyla oluşturuldu.');
		}

		/**
		 * Show the form for editing the specified resource.
		 *
		 * @param  \App\Models\BookAuthor  $bookAuthor
		 * @return \Illuminate\Http\Response
		 */
		public function edit(BookAuthor $bookAuthor)
		{
			return view('backend.book_authors.edit', compact('bookAuthor'));
		}

		/**
		 * Update the specified resource in storage.
		 *
		 * @param  \Illuminate\Http\Request  $request
		 * @param  \App\Models\BookAuthor  $bookAuthor
		 * @return \Illuminate\Http\Response
		 */
		public function update(Request $request, BookAuthor $bookAuthor)
		{
			$validated = $request->validate([
				'name' => 'required|string|max:255|unique:book_authors,name,' . $bookAuthor->id,
				'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
				'biography' => 'nullable|string',
				'bibliography' => 'nullable|string',
			]);

			if ($request->hasFile('picture')) {
				if ($bookAuthor->picture) {
					Storage::delete(str_replace('/storage', 'public', $bookAuthor->picture));
				}
				$path = $request->file('picture')->store('public/book_authors');
				$validated['picture'] = Storage::url($path);
			}

			$bookAuthor->update($validated);

			return redirect()->route('book-authors.index')->with('success', 'Yazar başarıyla güncellendi.');
		}

		/**
		 * Remove the specified resource from storage.
		 *
		 * @param  \App\Models\BookAuthor  $bookAuthor
		 * @return \Illuminate\Http\Response
		 */
		public function destroy(BookAuthor $bookAuthor)
		{
			if ($bookAuthor->picture) {
				Storage::delete(str_replace('/storage', 'public', $bookAuthor->picture));
			}
			$bookAuthor->delete();
			return redirect()->route('book-authors.index')->with('success', 'Yazar başarıyla silindi.');
		}
	}
