<?php

	use App\Helpers\MyHelper;
	use App\Http\Controllers\ArticleController;
	use App\Http\Controllers\CategoryController;
	use App\Http\Controllers\ChatController;
	use App\Http\Controllers\FollowController;
	use App\Http\Controllers\FrontendController;
	use App\Http\Controllers\ImageController;
	use App\Http\Controllers\LangController;
	use App\Http\Controllers\LoginWithGoogleController;
	use App\Http\Controllers\UserController;
	use App\Http\Controllers\UserSettingsController;
	use App\Http\Controllers\VerifyThankYouController;
	use App\Mail\WelcomeMail;
	use App\Models\Article;
	use App\Models\Category;
	use App\Models\User;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Mail;
	use Illuminate\Support\Facades\Route;


	/*
	|--------------------------------------------------------------------------
	| Web Routes
	|--------------------------------------------------------------------------
	|
	| Here is where you can register web routes for your application. These
	| routes are loaded by the RouteServiceProvider and all of them will
	| be assigned to the "web" middleware group. Make something great!
	|
	*/

	// Handle legacy ASP URLs
	Route::get('yazi.asp', function(Request $request) {
		$id = $request->query('id');

		if ($id) {
			// Look up the article by ID
			$article = Article::where('id', $id)
				->where('approved', 1)
				->where('deleted', 0)
				->first();

			if ($article) {
				// Redirect to the new slug-based URL with 301 (permanent) redirect
				return redirect()->route('article', ['slug' => $article->slug], 301);
			}
		}

		// If article not found or no ID provided, redirect to homepage
		return redirect()->route('frontend.index', 301);
	});

	Route::get('kume.asp', function(Request $request) {
		$id = $request->query('id');

		if ($id) {
			// Look up the article by ID
			$category = Category::where('id', $id)
				->first();

			if ($category) {
				// Redirect to the new slug-based URL with 301 (permanent) redirect
				return redirect()->route('frontend.category', ['slug' => $category->slug], 301);
			}
		}

		// If article not found or no ID provided, redirect to homepage
		return redirect()->route('frontend.index', 301);
	});

	Route::get('list.asp', function(Request $request) {
		$id = $request->query('id');

		if ($id) {
			// Look up the article by ID
			$category = Category::where('id', $id)
				->first();

			$parentCategory = Category::where('id', $category->parent_category_id)
				->first();

			if ($category) {
				// Redirect to the new slug-based URL with 301 (permanent) redirect
				return redirect()->route('frontend.subcategory', ['categorySlug' => $parentCategory->slug, 'subcategorySlug' => $category->slug], 301);
			}
		}

		// If article not found or no ID provided, redirect to homepage
		return redirect()->route('frontend.index', 301);
	});

	Route::get('yazar.asp', function (Request $request) {
		// Retrieve the user ID from the query string
		$userId = $request->query('id');

		// Find the user by ID
		$user = User::find($userId);

		// Check if the user exists
		if ($user) {
			// Redirect permanently to the URL based on the user's slug
			return redirect()->route('user', ['slug' => $user->slug], 301);
		}

		// If no user found, return a 404 response
		abort(404);
	})->where('id', '[0-9]+');

	Route::get('arsiv.asp', function (Request $request) {
			return redirect()->route('frontend.recent-articles', [], 301);
	});

	Route::get('yazarlar.asp', function (Request $request) {
		return redirect()->route('users', [], 301);
	});

	// Static pages
	Route::get('404', [FrontendController::class, 'page_404'])->name('frontend.404');
	Route::get('katilim', [FrontendController::class, 'page_sign_up_step_1'])->name('frontend.join');
	Route::get('yasallik', [FrontendController::class, 'page_legal'])->name('frontend.legal');
	Route::get('gizlilik', [FrontendController::class, 'page_privacy'])->name('frontend.secrecy');
	Route::get('yayin-ilkeleri', [FrontendController::class, 'page_sign_up_step_2'])->name('frontend.principles');
	Route::get('izedebiyat', [FrontendController::class, 'page_izedebiyat'])->name('frontend.izedebiyat');
	Route::get('sorular', [FrontendController::class, 'page_faq'])->name('frontend.faq');
	Route::get('kunye', [FrontendController::class, 'page_about'])->name('frontend.who-we-are');


	Route::get('/', [FrontendController::class, 'index'])->name('frontend.index');
	Route::get('/ana-sayfa', [FrontendController::class, 'index'])->name('frontend.home-page');

// Replace the old search route
	Route::get('/arabul', [FrontendController::class, 'search'])->name('search');
	Route::get('/arabul/sayfa/{page}', [FrontendController::class, 'search'])->name('search.page');

	Route::get('/son-eklenenler/{slug}', [FrontendController::class, 'recentTextsByCategory'])->name('frontend.recent-articles-by-category');

	Route::get('/son-eklenenler', [FrontendController::class, 'recentTexts'])->name('frontend.recent-articles');

	// Replace the old category routes with:
	Route::get('kume/{slug}', [FrontendController::class, 'category'])->name('frontend.category');
	Route::get('kume/{slug}/sayfa/{page}', [FrontendController::class, 'category'])->name('frontend.category.page');

	// Replace the old subcategory routes with:
	Route::get('kume/{categorySlug}/{subcategorySlug}', [FrontendController::class, 'subcategory'])->name('frontend.subcategory');
	Route::get('kume/{categorySlug}/{subcategorySlug}/sayfa/{page}', [FrontendController::class, 'subcategory'])->name('frontend.subcategory.page');

	Route::get('yazar/{slug}', [FrontendController::class, 'user'])->name('user');
	Route::get('yazar/{slug}/sayfa/{page}', [FrontendController::class, 'user'])->name('user.page');

	Route::get('/yazarlar', [FrontendController::class, 'users'])->name('users');
	Route::get('/yazarlar/harf/{filter}', [FrontendController::class, 'users'])->name('users.letter');
	Route::get('/yazarlar/harf/{filter}/sayfa/{page}', [FrontendController::class, 'users'])->name('users.letter.page');

	Route::get('/etiket/{slug}', [FrontendController::class, 'articlesByKeyword'])->name('articles-by-keyword');
	Route::get('/etiket/{slug}/sayfa/{page}', [FrontendController::class, 'articlesByKeyword'])->name('articles-by-keyword.page');

	Route::get('yapit/{slug}', [FrontendController::class, 'article'])->name('article');



	//-------------------------------------------------------------------------
	Route::get('/lang/home', [LangController::class, 'index']);
	Route::get('/lang/change', [LangController::class, 'change'])->name('changeLang');

	Route::get('login/google', [LoginWithGoogleController::class, 'redirectToGoogle']);
	Route::get('login/google/callback', [LoginWithGoogleController::class, 'handleGoogleCallback']);

	Route::get('/logout', [LoginWithGoogleController::class, 'logout']);

	Route::get('/verify-thank-you-tr_TR', [VerifyThankYouController::class, 'index'])->name('verify-thank-you-tr_TR')->middleware('verified');



	//-------------------------------------------------------------------------
	Route::middleware(['auth'])->group(function () {

		// New maintenance routes
		Route::get('/maintenance/check-harmful', function () {
			ob_start();
			MyHelper::returnIsHarmful();
			$output = ob_get_clean();
			return response($output)->header('Content-Type', 'text/plain');
		})->name('maintenance.check-harmful');

		Route::get('/maintenance/check-religious', function () {
			ob_start();
			MyHelper::returnReligiousReason();
			$output = ob_get_clean();
			return response($output)->header('Content-Type', 'text/plain');
		})->name('maintenance.check-religious');

		Route::get('/maintenance/check-keywords', function () {
			ob_start();
			MyHelper::returnKeywords();
			$output = ob_get_clean();
			return response($output)->header('Content-Type', 'text/plain');
		})->name('maintenance.check-keywords');

		Route::get('/maintenance/check-moderation', function () {
			ob_start();
			MyHelper::returnModeration();
			$output = ob_get_clean();
			return response($output)->header('Content-Type', 'text/plain');
		})->name('maintenance.check-moderation');

		Route::get('/maintenance/update-articles-table', function () {
			ob_start();
			MyHelper::updateArticleTable();
			$output = ob_get_clean();
			return response($output)->header('Content-Type', 'text/plain');
		})->name('maintenance.check-moderation');

		Route::get('/maintenance/check-markdown', function () {
			ob_start();
			MyHelper::returnMarkdown();
			$output = ob_get_clean();
			return response($output)->header('Content-Type', 'text/plain');
		})->name('maintenance.check-markdown');


		Route::get('/check-llms-json', [ChatController::class, 'checkLLMsJson']);

		Route::get('/upload-images', [ImageController::class, 'index'])->name('upload-images.index');
		Route::post('/upload-images', [ImageController::class, 'store'])->name('upload-images.store');
		Route::put('/upload-images/{id}', [ImageController::class, 'update'])->name('upload-images.update');
		Route::delete('/upload-images/{id}', [ImageController::class, 'destroy'])->name('upload-images.destroy');


		Route::post('/image-gen', [ImageController::class, 'makeImage'])->name('send-image-gen-prompt');
		Route::delete('/image-gen/{session_id}', [ImageController::class, 'destroyGenImage'])->name('image-gen.destroy');


		Route::prefix('eserlerim')->group(function () {
			Route::get('/', [ArticleController::class, 'index'])->name('articles.index');
			Route::get('/create', [ArticleController::class, 'create'])->name('articles.create');
			Route::post('/', [ArticleController::class, 'store'])->name('articles.store');
			Route::get('/{hashedId}/edit', [ArticleController::class, 'edit'])->name('articles.edit');
			Route::put('/{hashedId}', [ArticleController::class, 'update'])->name('articles.update');
			Route::delete('/{hashedId}', [ArticleController::class, 'destroy'])->name('articles.destroy');
			Route::get('/get-images', [ArticleController::class, 'getImages'])->name('articles.get-images');

			Route::get('/keywords/search', [ArticleController::class, 'searchKeywords'])->name('keywords.search');

			Route::post('/upload-article-images', [ArticleController::class, 'storeArticleImage'])->name('upload-article-images.store');

			Route::post('/generate-category', [ChatController::class, 'generateCategory'])->name('articles.generate-category');
			Route::post('/generate-description', [ChatController::class, 'generateDescription'])->name('articles.generate-description');
			Route::post('/generate-keywords', [ChatController::class, 'generateKeywords'])->name('articles.generate-keywords');

		});

		Route::get('/chat/sessions', [ChatController::class, 'getChatSessions']);
		Route::get('/chat/{session_id?}', [ChatController::class, 'index'])->name('chat');
		Route::post('/create-session', [ChatController::class, 'createSession'])->name('chat.create-session');
		Route::get('/chat/messages/{sessionId}', [ChatController::class, 'getChatMessages']);
		Route::post('/send-llm-prompt', [ChatController::class, 'sendLlmPrompt'])->name('send-llm-prompt');
		Route::delete('/chat/{sessionId}', [ChatController::class, 'destroy'])->name('chat.destroy');



		Route::post('/sahne-arkasi', [UserSettingsController::class, 'updateSettings'])->name('backend.update');
		Route::get('/sahne-arkasi/hesap', [UserSettingsController::class, 'account'])->name('backend.account');

		Route::get('/sahne-arkasi/images', [UserSettingsController::class, 'images'])->name('backend.images');

		Route::get('/sahne-arkasi/close-account', [UserSettingsController::class, 'closeAccount'])->name('backend.close-account');

		Route::post('/sahne-arkasi/password', [UserSettingsController::class, 'updatePassword'])->name('backend.sifre-guncelle');

		Route::post('/favori/yazar/{user}', [FollowController::class, 'toggleFollow'])->name('follow.user');
		Route::post('/favori/eser/{article}', [FollowController::class, 'toggleFavorite'])->name('follow.article');
		Route::get('/favorilerim', [FollowController::class, 'following'])->name('backend.following');
		Route::post('/article/{article}/clap', [ArticleController::class, 'toggleClap'])->name('article.clap');

		Route::get('/users', [UserController::class, 'index'])->name('admin-users-index');
		Route::post('/login-as', [UserController::class, 'loginAs'])->name('users-login-as');


	});

	//-------------------------------------------------------------------------

	Auth::routes();
	Auth::routes(['verify' => true]);
