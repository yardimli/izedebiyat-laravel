<?php

	use App\Helpers\MyHelper;
	use App\Http\Controllers\ChatController;
	use App\Http\Controllers\FrontendController;
	use App\Http\Controllers\ImageGenController;
	use App\Http\Controllers\LangController;
	use App\Http\Controllers\LoginWithGoogleController;
	use App\Http\Controllers\StaticPagesController;
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

	Route::get('/maintenance/update-yazilar-table', function () {
		ob_start();
		MyHelper::updateYaziTable();
		$output = ob_get_clean();
		return response($output)->header('Content-Type', 'text/plain');
	})->name('maintenance.check-moderation');

	//--- OLD IZED ROUTES -----------------------------------------------------
	// Static pages
	Route::get('404', [FrontendController::class, 'page_404'])->name('frontend-404');
	Route::get('katilim', [FrontendController::class, 'page_about'])->name('frontend-katilim');
	Route::get('yasallik', [FrontendController::class, 'page_legal'])->name('frontend-yasallik');
	Route::get('gizlilik', [FrontendController::class, 'page_privacy'])->name('frontend-gizlilik');
	Route::get('yayin-ilkeleri', [FrontendController::class, 'page_sign_up_step_2'])->name('frontend-yayin-ilkeleri');
	Route::get('izedebiyat', [FrontendController::class, 'page_izedebiyat'])->name('frontend-izedebiyat');
	Route::get('sorular', [FrontendController::class, 'page_faq'])->name('frontend-sorular');
	Route::get('kunye', [FrontendController::class, 'page_about'])->name('frontend-kunye');


	Route::get('arabul', function () {
		return require __DIR__ . '/../public/search.php';
	})->name('arabul');

	Route::get('/', [FrontendController::class, 'index'])->name('frontend-index');
	Route::get('/ana-sayfa', [FrontendController::class, 'index'])->name('frontend-ana-sayfa');

	Route::get('/son-eklenenler/{slug}', [FrontendController::class, 'recentTextsByCategory'])->name('recentTextsByCategory');

	Route::get('/son-eklenenler', [FrontendController::class, 'recentTexts'])->name('recentTexts');

	// Replace the old category routes with:
	Route::get('kume/{slug}', [FrontendController::class, 'category'])->name('category');
	Route::get('kume/{slug}/sayfa/{page}', [FrontendController::class, 'category'])->name('category.page');


	// Category specific routes
	$categories = ['siir', 'oyku', 'roman', 'deneme', 'elestiri', 'inceleme', 'bilimsel'];

	foreach ($categories as $category) {
		Route::get($category, [FrontendController::class, 'category'])->name('category');

		Route::get($category, [FrontendController::class, 'category'])->name('category');

		Route::get("$category/{altKategori}", function ($altKategori) use ($category) {
			$_GET['ust_kategori_slug'] = $category;
			return require __DIR__ . '/../public/sub_category.php';
		})->name("$category.alt");
	}

	// Replace the old subcategory routes with:
	Route::get('kume/{categorySlug}/{subcategorySlug}', [FrontendController::class, 'subcategory'])->name('subcategory');
	Route::get('kume/{categorySlug}/{subcategorySlug}/sayfa/{page}', [FrontendController::class, 'subcategory'])->name('subcategory.page');

	Route::get('yazar/{slug}', [FrontendController::class, 'author'])->name('author');
	Route::get('yazar/{slug}/sayfa/{page}', [FrontendController::class, 'author'])->name('author.page');

	Route::get('/yazarlar', [FrontendController::class, 'authors'])->name('authors');
	Route::get('/yazarlar/harf/{filter}', [FrontendController::class, 'authors'])->name('authors.harf');
	Route::get('/yazarlar/harf/{filter}/sayfa/{page}', [FrontendController::class, 'authors'])->name('authors.harf.sayfa');

	Route::get('/etiket/{slug}', [FrontendController::class, 'articlesByKeyword'])->name('articles-by-keyword');
	Route::get('/etiket/{slug}/sayfa/{page}', [FrontendController::class, 'articlesByKeyword'])->name('articles-by-keyword.page');

	Route::get('yapit/{slug}', [FrontendController::class, 'article'])->name('yapit');



	//-------------------------------------------------------------------------
	Route::get('/landing-page', [StaticPagesController::class, 'landing'])->name('landing-page');

	Route::get('/lang/home', [LangController::class, 'index']);
	Route::get('/lang/change', [LangController::class, 'change'])->name('changeLang');

	Route::get('login/google', [LoginWithGoogleController::class, 'redirectToGoogle']);
	Route::get('login/google/callback', [LoginWithGoogleController::class, 'handleGoogleCallback']);

	Route::get('/logout', [LoginWithGoogleController::class, 'logout']);

	Route::get('/verify-thank-you', [VerifyThankYouController::class, 'index'])->name('verify-thank-you')->middleware('verified');
	Route::get('/verify-thank-you-zh_TW', [VerifyThankYouController::class, 'index_zh_TW'])->name('verify-thank-you-zh_TW')->middleware('verified');

	Route::get('/blog', [StaticPagesController::class, 'blog'])->name('blog-page');
	Route::get('/privacy', [StaticPagesController::class, 'privacy'])->name('privacy-page');
	Route::get('/terms', [StaticPagesController::class, 'terms'])->name('terms-page');
	Route::get('/help', [StaticPagesController::class, 'help'])->name('help-page');
	Route::get('/help/{topic}', [StaticPagesController::class, 'helpDetails'])->name('help-details');
	Route::get('/about', [StaticPagesController::class, 'about'])->name('about-page');
	Route::get('/contact', [StaticPagesController::class, 'contact'])->name('contact-page');
	Route::get('/onboarding', [StaticPagesController::class, 'onboarding'])->name('onboarding-page');
	Route::get('/change-log', [StaticPagesController::class, 'changeLog'])->name('change-log-page');
	Route::get('/buy-packages', [UserSettingsController::class, 'buyPackages'])->name('buy-packages');

	Route::get('/help', [StaticPagesController::class, 'help'])->name('help-page');

	//-------------------------------------------------------------------------

	Route::get('/buy-packages', [UserSettingsController::class, 'buyPackages'])->name('buy-packages');

	Route::get('/buy-credits-test/{id}', [PayPalController::class, 'beginTransaction'])->name('beginTransaction');
	Route::get('/buy-credits/{id}', [PayPalController::class, 'processTransaction'])->name('processTransaction');
	Route::get('/success-transaction', [PayPalController::class, 'successTransaction'])->name('successTransaction');
	Route::get('/cancel-transaction', [PayPalController::class, 'cancelTransaction'])->name('cancelTransaction');

	Route::get('/user-profile/{username}', [StaticPagesController::class, 'userProfile'])->name('user-profile');


	//-------------------------------------------------------------------------
	Route::middleware(['auth'])->group(function () {

		Route::get('/check-llms-json', [ChatController::class, 'checkLLMsJson']);


		Route::get('/chat/sessions', [ChatController::class, 'getChatSessions']);
		Route::get('/chat/{session_id?}', [ChatController::class, 'index'])->name('chat');
		Route::post('/create-session', [ChatController::class, 'createSession'])->name('chat.create-session');
		Route::get('/chat/messages/{sessionId}', [ChatController::class, 'getChatMessages']);
		Route::post('/send-llm-prompt', [ChatController::class, 'sendLlmPrompt'])->name('send-llm-prompt');
		Route::delete('/chat/{sessionId}', [ChatController::class, 'destroy'])->name('chat.destroy');


		Route::get('/image-gen/sessions', [ImageGenController::class, 'getImageGenSessions'])->name('image-gen-sessions');
		Route::get('/image-gen/{session_id?}', [ImageGenController::class, 'index'])->name('image-gen');
		Route::post('/image-gen', [ImageGenController::class, 'makeImage'])->name('send-image-gen-prompt');
		Route::delete('/image-gen/{session_id}', [ImageGenController::class, 'destroy'])->name('image-gen.destroy');


		Route::get('/settings', [UserSettingsController::class, 'editSettings'])->name('my-settings');
		Route::post('/settings', [UserSettingsController::class, 'updateSettings'])->name('settings-update');

		Route::post('/settings/password', [UserSettingsController::class, 'updatePassword'])->name('settings-password-update');
		Route::post('/settings/api-keys', [UserSettingsController::class, 'updateApiKeys'])->name('settings-update-api-keys');

		Route::get('/users', [UserController::class, 'index'])->name('users-index');
		Route::post('/login-as', [UserController::class, 'loginAs'])->name('users-login-as');

		Route::post('/settings/password', [UserSettingsController::class, 'updatePassword'])->name('settings-password-update');

	});

	//-------------------------------------------------------------------------

	Auth::routes();
	Auth::routes(['verify' => true]);
