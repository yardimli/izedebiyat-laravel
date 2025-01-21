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
	Route::get('/maintenance/check-harmful', function() {
		ob_start();
		MyHelper::returnIsHarmful();
		$output = ob_get_clean();
		return response($output)->header('Content-Type', 'text/plain');
	})->name('maintenance.check-harmful');

	Route::get('/maintenance/check-religious', function() {
		ob_start();
		MyHelper::returnReligiousReason();
		$output = ob_get_clean();
		return response($output)->header('Content-Type', 'text/plain');
	})->name('maintenance.check-religious');

	Route::get('/maintenance/check-keywords', function() {
		ob_start();
		MyHelper::returnKeywords();
		$output = ob_get_clean();
		return response($output)->header('Content-Type', 'text/plain');
	})->name('maintenance.check-keywords');

	Route::get('/maintenance/check-moderation', function() {
		ob_start();
		MyHelper::returnModeration();
		$output = ob_get_clean();
		return response($output)->header('Content-Type', 'text/plain');
	})->name('maintenance.check-moderation');

	Route::get('/maintenance/update-yazilar-table', function() {
		ob_start();
		MyHelper::updateYaziTable();
		$output = ob_get_clean();
		return response($output)->header('Content-Type', 'text/plain');
	})->name('maintenance.check-moderation');

	//--- OLD IZED ROUTES -----------------------------------------------------
	// Static pages
	Route::get('404', function() {
		return require __DIR__. '/../public/404.php';
	})->name('404');

	Route::get('yazarlar', function() {
		return require __DIR__. '/../public/page-authors.php';
	})->name('yazarlar');

	Route::get('katilim', function() {
		return require __DIR__. '/../public/page-sign-up-step-1.php';
	})->name('katilim');

	Route::get('yasallik', function() {
		return require __DIR__. '/../public/page-legal.php';
	})->name('yasallik');

	Route::get('gizlilik', function() {
		return require __DIR__. '/../public/page-privacy.php';
	})->name('gizlilik');

	Route::get('yayin-ilkeleri', function() {
		return require __DIR__. '/../public/page-sign-up-step-2.php';
	})->name('yayin-ilkeleri');

	Route::get('izedebiyat', function() {
		return require __DIR__. '/../public/page-izedebiyat.php';
	})->name('izedebiyat');

	Route::get('sorular', function() {
		return require __DIR__. '/../public/page-faq.php';
	})->name('sorular');

	Route::get('kunye', function() {
		return require __DIR__. '/../public/page-about.php';
	})->name('kunye');

	Route::get('arabul', function() {
		return require __DIR__. '/../public/search.php';
	})->name('arabul');

// Dynamic routes
	Route::get('etiket/{slug}/sayfa/{page}', function($slug, $page) {
		$_GET['etiket_slug'] = $slug;
		$_GET['sayfa'] = $page;
		return require __DIR__. '/../public/articles-by-keyword.php';
	})->name('etiket.sayfa');

	Route::get('etiket/{slug}', function($slug) {
		$_GET['etiket_slug'] = $slug;
		return require __DIR__. '/../public/articles-by-keyword.php';
	})->name('etiket');

	Route::get('kume/{ustKategori}/{altKategori}/sayfa/{page}', function($ustKategori, $altKategori, $page) {
		$_GET['ust_kategori_slug'] = $ustKategori;
		$_GET['alt_kategori_slug'] = $altKategori;
		$_GET['sayfa'] = $page;
		return require __DIR__. '/../public/sub_category.php';
	})->name('kume.alt.sayfa');

	Route::get('yazar/{slug}/sayfa/{page}', function($slug, $page) {
		$_GET['yazar_slug'] = $slug;
		$_GET['sayfa'] = $page;
		return require __DIR__. '/../public/author.php';
	})->name('yazar.sayfa');

	Route::get('kume/{ustKategori}/{altKategori}', function($ustKategori, $altKategori) {
		$_GET['ust_kategori_slug'] = $ustKategori;
		$_GET['alt_kategori_slug'] = $altKategori;
		$_GET['sayfa'] = 1;
		return require __DIR__. '/../public/sub_category.php';
	})->name('kume.alt');

	Route::get('yapit/{slug}', function($slug) {
		$_GET['slug'] = $slug;
		return require __DIR__. '/../public/single.php';
	})->name('yapit');

	Route::get('yazar/{slug}/sayfa/{page}', function($slug) {
		$_GET['slug'] = $slug;
		$_GET['sayfa'] = $page;
		return require __DIR__. '/../public/author.php';
	})->name('yazar');

	Route::get('yazar/{slug}', function($slug) {
		$_GET['slug'] = $slug;
		return require __DIR__. '/../public/author.php';
	})->name('yazar');


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

		Route::get("$category/{altKategori}", function($altKategori) use ($category) {
			$_GET['ust_kategori_slug'] = $category;
			return require __DIR__. '/../public/sub_category.php';
		})->name("$category.alt");
	}

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
