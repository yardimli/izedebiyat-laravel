<?php

	use App\Helpers\MyHelper;
	use App\Http\Controllers\AccountRecoveryController;
	use App\Http\Controllers\ArticleController;
	use App\Http\Controllers\BookAuthorController;
	use App\Http\Controllers\CategoryController;
	use App\Http\Controllers\ChatController;
	use App\Http\Controllers\CommentController;
	use App\Http\Controllers\FollowController;
	use App\Http\Controllers\FrontendController;
	use App\Http\Controllers\ImageController;
	use App\Http\Controllers\LangController;
	use App\Http\Controllers\ForumController;
	use App\Http\Controllers\AdminForumController;
	use App\Http\Controllers\AdminQuoteController;
	use App\Http\Controllers\AdminLlmSettingController;
	use App\Http\Controllers\LoginWithGoogleController;
	use App\Http\Controllers\UserController;
	use App\Http\Controllers\UserSettingsController;
	use App\Http\Controllers\VerifyThankYouController;
	use App\Http\Controllers\BookReviewController;
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
				->where('is_published', 1)
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

	// Forum pages are public to read.
	Route::prefix('forum')->name('forum.')->group(function () {
		Route::get('/', [ForumController::class, 'index'])->name('index');
		Route::get('/etiketler', [ForumController::class, 'tags'])->name('tags');
		Route::get('/etiket/{tag}', [ForumController::class, 'index'])->name('tag');
		Route::get('/uye/{user}', [ForumController::class, 'profile'])->name('profile');
		Route::get('/tartisma/{discussion}', [ForumController::class, 'show'])->name('show');

		Route::middleware('auth')->group(function () {
			Route::get('/yeni', [ForumController::class, 'create'])->name('create');
			Route::post('/', [ForumController::class, 'store'])->name('store');
			Route::post('/tartisma/{discussion}/yanit', [ForumController::class, 'reply'])->name('reply');
			Route::post('/ileti/{post}/begen', [ForumController::class, 'like'])->name('like');
			Route::post('/ileti/{post}/bildir', [ForumController::class, 'flag'])->name('flag');
		});
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

	Route::get('/hesap-kurtarma', [AccountRecoveryController::class, 'create'])->name('account-recovery.create');
	Route::post('/hesap-kurtarma', [AccountRecoveryController::class, 'store'])->name('account-recovery.store');

	Route::get('/', [FrontendController::class, 'index'])->name('frontend.index');
	Route::get('/ana-sayfa', [FrontendController::class, 'index'])->name('frontend.home-page');

	// Book Review Frontend Routes
	Route::get('/kitap-izleri', [FrontendController::class, 'bookReviews'])->name('frontend.book-reviews.index');
	Route::get('/kitap-izleri/yazarlar', [FrontendController::class, 'listBookAuthors'])->name('frontend.book-reviews.authors');
	Route::get('/kitap-izleri/yazar/{slug}', [FrontendController::class, 'showBookAuthor'])->name('frontend.book-reviews.author');
	Route::get('/kitap-izleri/kumeler', [FrontendController::class, 'listBookCategories'])->name('frontend.book-reviews.categories');
	Route::get('/kitap-izleri/kume/{slug}', [FrontendController::class, 'showBookReviewsByCategory'])->name('frontend.book-reviews.show-by-category');
	Route::get('/kitap-izleri/etiketler', [FrontendController::class, 'listBookTags'])->name('frontend.book-reviews.tags');
	Route::get('/kitap-izleri/etiket/{slug}', [FrontendController::class, 'showBookReviewsByTag'])->name('frontend.book-reviews.show-by-tag');
	Route::get('/kitap-izleri/{slug}', [FrontendController::class, 'showBookReview'])->name('frontend.book-review.show');
	Route::get('/kitap-gonder', [FrontendController::class, 'createBookSubmission'])->name('frontend.book-reviews.create-submission');
	Route::post('/kitap-gonder', [FrontendController::class, 'storeBookSubmission'])->name('frontend.book-reviews.store-submission');

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

	Route::get('yazar/{slug}/pdf', [FrontendController::class, 'userPdf'])->name('user.pdf');
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

	Route::post('/yapit/{article}/read', [ArticleController::class, 'recordRead'])->name('article.read');



	Route::get('/maintenance/all', function () {
//		echo "Checking harmful content...<br>\n";
//		MyHelper::returnIsHarmful();
		echo "<br>Checking religious content...<br>\n";
		MyHelper::returnReligiousReason();
		echo "<br>Checking keywords...<br>\n";
		MyHelper::returnKeywords();
		echo "<br>Checking moderation...<br>\n";
		MyHelper::updateArticleTable();
		echo "<br>Checking markdown...<br>\n";
		MyHelper::returnMarkdown();
	})->name('maintenance.all');

	Route::get('/migrate-old-comments', [CommentController::class, 'migrateOldComments'])
		->middleware(['auth'])
		->name('migrate-old-comments');

	Route::get('/articles/{article}/comments', [CommentController::class, 'index'])->name('comments.index');

	//-------------------------------------------------------------------------
	Route::post('/api/book-reviews/ingest', [BookReviewController::class, 'ingest'])->name('book-reviews.ingest');

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
		})->name('maintenance.update-articles-table');

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


		Route::prefix('eserlerim')->middleware(\App\Writer\Http\Middleware\WriterLocale::class)->group(function () {
			Route::get('/', [\App\Writer\Http\Controllers\BookController::class, 'index'])->name('articles.index');
			Route::get('/create', [\App\Writer\Http\Controllers\BookController::class, 'create'])->name('articles.create');
			Route::post('/', [\App\Writer\Http\Controllers\BookController::class, 'store'])->name('articles.store');
			Route::get('/{hashedId}/duzenle', [\App\Writer\Http\Controllers\BookController::class, 'edit'])->name('articles.edit');
			Route::put('/{hashedId}', [\App\Writer\Http\Controllers\BookController::class, 'updateArticle'])->name('articles.update');
			Route::delete('/{hashedId}', [\App\Writer\Http\Controllers\BookController::class, 'destroyArticle'])->name('articles.destroy');
			Route::get('/get-images', [ArticleController::class, 'getImages'])->name('articles.get-images');

			Route::get('/keywords/search', [ArticleController::class, 'searchKeywords'])->name('keywords.search');

			Route::post('/upload-article-images', [ArticleController::class, 'storeArticleImage'])->name('upload-article-images.store');

			Route::post('/generate-category', [ChatController::class, 'generateCategory'])->name('articles.generate-category');
			Route::post('/generate-description', [ChatController::class, 'generateDescription'])->name('articles.generate-description');
			Route::post('/generate-keywords', [ChatController::class, 'generateKeywords'])->name('articles.generate-keywords');

		});

		Route::post('/articles/{article}/comments', [CommentController::class, 'store'])->name('comments.store');
		Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');


		Route::get('/sohbet/oturumlar', [ChatController::class, 'getChatSessions']);
		Route::get('/sohbet/{session_id?}', [ChatController::class, 'index'])->name('chat');
		Route::post('/sohbet-oturum-ac', [ChatController::class, 'createSession'])->name('chat.create-session');
		Route::get('/sohbet/mesajlar/{sessionId}', [ChatController::class, 'getChatMessages']);
		Route::post('/send-llm-prompt', [ChatController::class, 'sendLlmPrompt'])->name('send-llm-prompt');
		Route::delete('/sohbet/{sessionId}', [ChatController::class, 'destroy'])->name('chat.destroy');


		Route::get('/sahne-arkasi', [UserSettingsController::class, 'account']);
		Route::post('/sahne-arkasi', [UserSettingsController::class, 'updateSettings'])->name('backend.update');
		Route::get('/sahne-arkasi/hesap', [UserSettingsController::class, 'account'])->name('backend.account');

		Route::get('/yazi-atolyesi/gorseller', [UserSettingsController::class, 'images'])->name('backend.images');

		Route::get('/yazi-atolyesi/hesabi-kapat', [UserSettingsController::class, 'closeAccount'])->name('backend.close-account');

		Route::post('/sahne-arkasi/password', [UserSettingsController::class, 'updatePassword'])->name('backend.sifre-guncelle');

		Route::post('/favori/yazar/{user}', [FollowController::class, 'toggleFollow'])->name('follow.user');
		Route::post('/favori/eser/{article}', [FollowController::class, 'toggleFavorite'])->name('follow.article');
		Route::get('/favorilerim', [FollowController::class, 'following'])->name('backend.following');
		Route::post('/yapit/{article}/clap', [ArticleController::class, 'toggleClap'])->name('article.clap');

		Route::get('/admin/kullanicilar', [UserController::class, 'index'])->name('admin-users-index');
        Route::get('/users', function (\Illuminate\Http\Request $request) {
            abort_unless($request->user()->isAdmin(), 403);
            return redirect()->route('admin-users-index', $request->query());
        });
		Route::post('/login-as', [UserController::class, 'loginAs'])->name('users-login-as');
        Route::post('/yoneticiye-don', [UserController::class, 'stopImpersonating'])->name('users-stop-impersonating');
		Route::delete('/admin/kullanicilar/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
		Route::get('/admin/eserler', [ArticleController::class, 'adminIndex'])->name('admin.articles.index');
		Route::post('/admin/eserler/toplu-guncelle', [ArticleController::class, 'adminBulkUpdate'])->name('admin.articles.bulk-update');
		Route::patch('/admin/eserler/{article}/durumlar', [ArticleController::class, 'adminUpdateFlags'])->name('admin.articles.flags');
		Route::delete('/admin/eserler/{article}', [ArticleController::class, 'adminDestroy'])->name('admin.articles.destroy');
		Route::get('/admin/okuma-kayitlari', [ArticleController::class, 'adminReadCleanup'])->name('admin.read-cleanup.index');
		Route::delete('/admin/okuma-kayitlari', [ArticleController::class, 'adminReadCleanupDestroy'])->name('admin.read-cleanup.destroy');

		// Admin Account Recovery Routes
		Route::get('/admin/hesap-kurtarma', [AccountRecoveryController::class, 'index'])->name('admin.account-recovery.index');
		Route::get('/admin/hesap-kurtarma/{id}', [AccountRecoveryController::class, 'show'])->name('admin.account-recovery.show');
		Route::post('/admin/hesap-kurtarma/{id}/onayla', [AccountRecoveryController::class, 'approve'])->name('admin.account-recovery.approve');
		Route::post('/admin/hesap-kurtarma/{id}/reddet', [AccountRecoveryController::class, 'reject'])->name('admin.account-recovery.reject');

		// Admin routes for Book Reviews
		Route::resourceVerbs(['create' => 'ekle', 'edit' => 'duzenle']);
        Route::resource('admin/kitap-incelemeleri', BookReviewController::class, ['except' => ['show']])->names('book-reviews')->parameters(['kitap-incelemeleri' => 'book_review']);
		Route::post('/book-reviews/generate-category', [ChatController::class, 'generateBookCategory'])->name('book-reviews.generate-category');
		Route::post('/book-reviews/generate-keywords', [ChatController::class, 'generateBookKeywords'])->name('book-reviews.generate-keywords');
		Route::resource('admin/kitap-yazarlari', BookAuthorController::class)->names('book-authors')->parameters(['kitap-yazarlari' => 'book_author']);
		Route::resource('admin/alintilar', AdminQuoteController::class)->except('show')->names('admin.quotes')->parameters(['alintilar' => 'quote']);
        Route::resourceVerbs(['create' => 'create', 'edit' => 'edit']);
		Route::post('/admin/alintilar/olustur', [AdminQuoteController::class, 'generate'])->name('admin.quotes.generate');
		Route::get('/admin/yapay-zeka-ayarlari', [AdminLlmSettingController::class, 'edit'])->name('admin.llm-settings.edit');
		Route::put('/admin/yapay-zeka-ayarlari', [AdminLlmSettingController::class, 'update'])->name('admin.llm-settings.update');


		Route::prefix('admin/forum')->name('admin.forum.')->group(function () {
			Route::get('/', [AdminForumController::class, 'index'])->name('index');
			Route::get('/tartismalar/{discussion}/duzenle', [AdminForumController::class, 'editDiscussion'])->name('discussions.edit');
			Route::put('/tartismalar/{discussion}', [AdminForumController::class, 'updateDiscussion'])->name('discussions.update');
			Route::delete('/tartismalar/{discussion}', [AdminForumController::class, 'destroyDiscussion'])->name('discussions.destroy');
			Route::get('/iletiler/{post}/duzenle', [AdminForumController::class, 'editPost'])->name('posts.edit');
			Route::put('/iletiler/{post}', [AdminForumController::class, 'updatePost'])->name('posts.update');
			Route::delete('/iletiler/{post}', [AdminForumController::class, 'destroyPost'])->name('posts.destroy');
			Route::patch('/bildirimler/{flag}', [AdminForumController::class, 'updateFlag'])->name('flags.update');
		});

	});

	//-------------------------------------------------------------------------


	Auth::routes(['verify' => true]);

require __DIR__.'/writer.php';

// Preserve old bookmarks and open forms while publishing Turkish-facing URLs.
$legacyPaths = [
    'yazi-atolyesi/gorseller' => 'sahne-arkasi/images',
    'yazi-atolyesi/hesabi-kapat' => 'sahne-arkasi/close-account',
    'yazi-atolyesi/hesap' => 'yazi-atolyesi/account',
    'yazi-atolyesi/admin/kotalar' => 'yazi-atolyesi/admin/budgets',
    'yazi-atolyesi/eserler' => 'yazi-atolyesi/books',
    'admin/kullanicilar' => 'admin/users', 'admin/eserler' => 'admin/articles',
    'admin/okuma-kayitlari' => 'admin/read-cleanup', 'admin/kitap-incelemeleri' => 'admin/book-reviews',
    'admin/kitap-yazarlari' => 'admin/book-authors', 'admin/alintilar' => 'admin/quotes',
    'admin/yapay-zeka-ayarlari' => 'admin/llm-settings',
];
foreach (array_values(Route::getRoutes()->getRoutes()) as $canonical) {
    foreach ($legacyPaths as $current => $legacy) {
        if ($canonical->uri() !== $current && !str_starts_with($canonical->uri(), $current.'/')) continue;
        $oldUri = $legacy.substr($canonical->uri(), strlen($current));
        $oldUri = strtr($oldUri, ['/ekle'=>'/create', '/duzenle'=>'/edit', '/yapay-zeka-gunlugu'=>'/llm-log', '/disari-aktar'=>'/export', '/yenile'=>'/reset', '/toplu-guncelle'=>'/bulk-update', '/durumlar'=>'/flags', '/olustur'=>'/generate']);
        if (in_array('GET', $canonical->methods())) {
            $destination = $canonical->getName();
            Route::get($oldUri, function (\Illuminate\Http\Request $request) use ($destination) {
                return redirect()->route($destination, array_merge($request->query(), $request->route()->parameters()));
            })->middleware($canonical->getAction('middleware') ?? [])->where($canonical->wheres);
        } else {
            $action = $canonical->getAction(); unset($action['as'], $action['prefix']);
            Route::match($canonical->methods(), $oldUri, $action)->where($canonical->wheres);
        }
        break;
    }
}

// Support forms opened before the recovery action URLs were translated.
Route::post('/admin/hesap-kurtarma/{id}/approve', [AccountRecoveryController::class, 'approve'])->middleware('auth');
Route::post('/admin/hesap-kurtarma/{id}/reject', [AccountRecoveryController::class, 'reject'])->middleware('auth');
