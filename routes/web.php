<?php

	use App\Helpers\MyHelper;
	use App\Http\Controllers\ArticleController;
	use App\Http\Controllers\CategoryController;
	use App\Http\Controllers\ChatController;
	use App\Http\Controllers\FrontendController;
	use App\Http\Controllers\ImageController;
	use App\Http\Controllers\LangController;
	use App\Http\Controllers\LoginWithGoogleController;
	use App\Http\Controllers\UserController;
	use App\Http\Controllers\UserSettingsController;
	use App\Http\Controllers\VerifyThankYouController;
	use App\Mail\WelcomeMail;
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


	// Static pages
	Route::get('404', [FrontendController::class, 'page_404'])->name('frontend-404');
	Route::get('katilim', [FrontendController::class, 'page_sign_up_step_1'])->name('frontend-katilim');
	Route::get('yasallik', [FrontendController::class, 'page_legal'])->name('frontend-yasallik');
	Route::get('gizlilik', [FrontendController::class, 'page_privacy'])->name('frontend-gizlilik');
	Route::get('yayin-ilkeleri', [FrontendController::class, 'page_sign_up_step_2'])->name('frontend-yayin-ilkeleri');
	Route::get('izedebiyat', [FrontendController::class, 'page_izedebiyat'])->name('frontend-izedebiyat');
	Route::get('sorular', [FrontendController::class, 'page_faq'])->name('frontend-sorular');
	Route::get('kunye', [FrontendController::class, 'page_about'])->name('frontend-kunye');


	Route::get('/', [FrontendController::class, 'index'])->name('frontend-index');
	Route::get('/ana-sayfa', [FrontendController::class, 'index'])->name('frontend-ana-sayfa');

// Replace the old search route
	Route::get('/arabul', [FrontendController::class, 'search'])->name('search');
	Route::get('/arabul/sayfa/{page}', [FrontendController::class, 'search'])->name('search.page');

	Route::get('/son-eklenenler/{slug}', [FrontendController::class, 'recentTextsByCategory'])->name('recentTextsByCategory');

	Route::get('/son-eklenenler', [FrontendController::class, 'recentTexts'])->name('recentTexts');

	// Replace the old category routes with:
	Route::get('kume/{slug}', [FrontendController::class, 'category'])->name('category');
	Route::get('kume/{slug}/sayfa/{page}', [FrontendController::class, 'category'])->name('category.page');

	// Replace the old subcategory routes with:
	Route::get('kume/{categorySlug}/{subcategorySlug}', [FrontendController::class, 'subcategory'])->name('subcategory');
	Route::get('kume/{categorySlug}/{subcategorySlug}/sayfa/{page}', [FrontendController::class, 'subcategory'])->name('subcategory.page');

	Route::get('yazar/{slug}', [FrontendController::class, 'user'])->name('user');
	Route::get('yazar/{slug}/sayfa/{page}', [FrontendController::class, 'user'])->name('author.page');

	Route::get('/yazarlar', [FrontendController::class, 'users'])->name('users');
	Route::get('/yazarlar/harf/{filter}', [FrontendController::class, 'users'])->name('users.harf');
	Route::get('/yazarlar/harf/{filter}/sayfa/{page}', [FrontendController::class, 'users'])->name('users.harf.sayfa');

	Route::get('/etiket/{slug}', [FrontendController::class, 'articlesByKeyword'])->name('articles-by-keyword');
	Route::get('/etiket/{slug}/sayfa/{page}', [FrontendController::class, 'articlesByKeyword'])->name('articles-by-keyword.page');

	Route::get('yapit/{slug}', [FrontendController::class, 'article'])->name('yapit');



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

		});

		Route::get('/chat/sessions', [ChatController::class, 'getChatSessions']);
		Route::get('/chat/{session_id?}', [ChatController::class, 'index'])->name('chat');
		Route::post('/create-session', [ChatController::class, 'createSession'])->name('chat.create-session');
		Route::get('/chat/messages/{sessionId}', [ChatController::class, 'getChatMessages']);
		Route::post('/send-llm-prompt', [ChatController::class, 'sendLlmPrompt'])->name('send-llm-prompt');
		Route::delete('/chat/{sessionId}', [ChatController::class, 'destroy'])->name('chat.destroy');



		Route::post('/settings', [UserSettingsController::class, 'updateSettings'])->name('sahne-arkasi.update');
		Route::get('/settings/account', [UserSettingsController::class, 'account'])->name('sahne-arkasi.account');

		Route::get('/sahne-arkasi/images', [UserSettingsController::class, 'images'])->name('sahne-arkasi.images');

		Route::get('/sahne-arkasi/close-account', [UserSettingsController::class, 'closeAccount'])->name('sahne-arkasi.close-account');

		Route::post('/sahne-arkasi/password', [UserSettingsController::class, 'updatePassword'])->name('sahne-arkasi.sifre-guncelle');


		Route::get('/users', [UserController::class, 'index'])->name('users-index');
		Route::post('/login-as', [UserController::class, 'loginAs'])->name('users-login-as');


	});

	//-------------------------------------------------------------------------

	Auth::routes();
	Auth::routes(['verify' => true]);
