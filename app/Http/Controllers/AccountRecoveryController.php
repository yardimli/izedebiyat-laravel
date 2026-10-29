<?php

	namespace App\Http\Controllers;

	use App\Models\Article;
	use App\Models\Category;
	use App\Models\AccountRecoveryRequest;
	use App\Models\User;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Cache;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Facades\Password;
	use Illuminate\Validation\Rule;
	use Illuminate\Validation\ValidationException;
	use App\Notifications\AccountRecovered;
	use Illuminate\Support\Facades\Log;
	use Illuminate\Support\Facades\Storage;
	use Illuminate\Support\Str;

	class AccountRecoveryController extends Controller
	{
		public function __construct()
		{
			if (Cache::lock('updating_category_stats', 3600)->get()) {
				// Get main menu categories with their last update time
				$firstCategory = Category::orderBy('updated_at')->first();

				// Check if first category exists and if it was updated more than 60 minutes ago
				if ($firstCategory && $firstCategory->updated_at->diffInMinutes(now()) > 60) {
					Log::info('Updating category statistics...');

					// Get all categories (including main and sub categories)
					$allCategories = Category::all();

					foreach ($allCategories as $category) {
						// Count total approved texts
						$totalTexts = Article::where(function ($query) use ($category) {
							$query->where('category_id', $category->id)
								->orWhere('parent_category_id', $category->id);
						})
							->where('approved', 1)
							->where('is_published', 1)
							->where('deleted', 0)
							->where('moderation_flagged', 0)
							->count();

						// Count new texts (last 30 days)
						$newTexts = Article::where(function ($query) use ($category) {
							$query->where('category_id', $category->id)
								->orWhere('parent_category_id', $category->id);
						})
							->where('approved', 1)
							->where('is_published', 1)
							->where('deleted', 0)
							->where('moderation_flagged', 0)
							->where('created_at', '>=', now()->subDays(30))
							->count();

						// Get total views/reads
						$totalReads = Article::where(function ($query) use ($category) {
							$query->where('category_id', $category->id)
								->orWhere('parent_category_id', $category->id);
						})
							->where('approved', 1)
							->where('is_published', 1)
							->where('deleted', 0)
							->sum('read_count');

						// Update category
						$category->update([
							'total_articles' => $totalTexts,
							'new_articles' => $newTexts,
							'read_count' => $totalReads,
							'updated_at' => now()
						]);
					}
				}
				Cache::lock('updating_category_stats')->release();
			}

			// Get main menu categories for the view
			$mainMenuCategories = Category::where('parent_category_id', 0)
				->orderBy('slug')
				->with('subCategories')
				->get();

			// Share with all views
			view()->share('mainMenuCategories', $mainMenuCategories);
		}

		/**
		 * Show the form for creating a new account recovery request.
		 */
		public function create()
		{
			return view('frontend.account_recovery_create');
		}

		/**
		 * Store a newly created account recovery request in storage.
		 */
		public function store(Request $request)
		{
			$request->validate([
				'real_name' => 'required|string|max:255',
				'remembered_emails' => 'required|string',
				'profile_url' => 'nullable|url|max:255',
				'contact_email' => 'required|email|max:255',
				'id_document' => 'required|image|mimes:jpeg,png,jpg|max:2048',
				'delete_account' => 'nullable|boolean',
			]);

			$path = $request->file('id_document')->store('id_documents', 'public');

			AccountRecoveryRequest::create([
				'real_name' => $request->real_name,
				'remembered_emails' => $request->remembered_emails,
				'profile_url' => $request->profile_url,
				'contact_email' => $request->contact_email,
				'id_document_path' => $path,
				'delete_account' => $request->boolean('delete_account'),
			]);

			return redirect()->route('account-recovery.create')
				->with('success', 'Talebiniz başarıyla alındı. En kısa sürede inceleyip size geri dönüş yapacağız.');
		}

		/**
		 * Display a listing of the account recovery requests for admins.
		 */
		public function index()
		{
			if (Auth::user()->member_type !== 1) {
				abort(403, __('default.Unauthorized action.'));
			}

			$requests = AccountRecoveryRequest::orderBy('created_at', 'desc')->paginate(20);
			return view('backend.account_recovery_index', compact('requests'));
		}

		/**
		 * Display the specified account recovery request for admins.
		 */
		public function show($id, Request $request)
		{
			if (Auth::user()->member_type !== 1) {
				abort(403, __('default.Unauthorized action.'));
			}

			$recoveryRequest = AccountRecoveryRequest::findOrFail($id);
			$request->validate(['search' => 'nullable|string|max:255']);
			$searchQuery = trim($request->input('search', ''));
			$possible_users = collect();

			if ($searchQuery) {
				if (str_contains($searchQuery, '/') || str_contains($searchQuery, '://')) {
					$url = str_starts_with($searchQuery, '/') ? $searchQuery : (preg_match('~^https?://~i', $searchQuery) ? $searchQuery : 'https://' . $searchQuery);
					$host = strtolower(parse_url($url, PHP_URL_HOST) ?? '');
					$allowedHosts = ['izedebiyat.com', 'www.izedebiyat.com', strtolower(parse_url(config('app.url'), PHP_URL_HOST) ?? '')];
					if (($host === '' || in_array($host, $allowedHosts, true)) && preg_match('~^/yazar/([^/]+)/?$~u', rawurldecode(parse_url($url, PHP_URL_PATH) ?? ''), $matches)) {
						$possible_users = User::where('slug', $matches[1])->get();
					}
				} else {
					$possible_users = User::where('name', 'like', '%' . $searchQuery . '%')
						->orWhere('email', 'like', '%' . $searchQuery . '%')
						->orWhere('slug', $searchQuery)->get();
				}
			}

			return view('backend.account_recovery_show', [
				'request' => $recoveryRequest,
				'possible_users' => $possible_users,
				'searchQuery' => $searchQuery,
			]);
		}

		/**
		 * Approve the specified account recovery request.
		 */
		public function confirmation(Request $request, $id)
		{
			abort_unless(Auth::user()->member_type === 1, 403);
			$recoveryRequest = AccountRecoveryRequest::findOrFail($id);
			$snapshot = $request->session()->get('account_recovery_confirmation.' . $id);
			abort_unless($snapshot && $recoveryRequest->status === 'pending', 409, 'Lütfen hesabı yeniden seçin.');
			$user = User::findOrFail($snapshot['user_id']);
			abort_unless($user->email === $snapshot['email'] && $user->slug === $snapshot['slug']
				&& (bool) $recoveryRequest->delete_account === $snapshot['delete_account'], 409, 'Hesap değişti. Lütfen yeniden inceleyin.');
			return view('backend.account_recovery_confirm', [
				'request' => $recoveryRequest, 'user' => $user, 'token' => $snapshot['token'],
				'newEmail' => $snapshot['new_email'], 'notes' => $snapshot['notes'],
			]);
		}

		public function approve(Request $request, $id)
		{
			if (Auth::user()->member_type !== 1) {
				abort(403, __('default.Unauthorized action.'));
			}

			$recoveryRequest = AccountRecoveryRequest::findOrFail($id);
			abort_unless($recoveryRequest->status === 'pending', 409, 'Bu talep daha önce işleme alınmış.');
			$request->validate(['user_id' => 'required|integer|exists:users,id', 'notes' => 'nullable|string|max:5000']);
			$user = User::findOrFail($request->input('user_id'));
			if (!$recoveryRequest->delete_account) {
				$request->merge(['new_email' => trim($request->input('new_email', ''))]);
				$request->validate(['new_email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)]]);
			}

			$key = 'account_recovery_confirmation.' . $id;
			if (!$request->filled('confirmation_token')) {
				$token = Str::random(40);
				$request->session()->put($key, [
					'token' => $token, 'user_id' => $user->id, 'email' => $user->email,
					'slug' => $user->slug, 'delete_account' => (bool) $recoveryRequest->delete_account,
					'new_email' => $request->input('new_email'), 'notes' => $request->input('notes'),
				]);
				return redirect()->route('admin.account-recovery.confirm', $id);
			}

			$request->validate(['confirmed' => 'accepted', 'confirmation_token' => 'required|string']);
			$snapshot = $request->session()->get($key);
			abort_unless($snapshot && hash_equals($snapshot['token'], $request->input('confirmation_token')), 409, 'Lütfen hesabı yeniden seçip onaylayın.');
			try {
				DB::transaction(function () use ($request, $id, $snapshot) {
					$recoveryRequest = AccountRecoveryRequest::lockForUpdate()->findOrFail($id);
					$user = User::lockForUpdate()->findOrFail($request->input('user_id'));
					abort_unless($recoveryRequest->status === 'pending' && $user->id === $snapshot['user_id']
						&& $user->email === $snapshot['email'] && $user->slug === $snapshot['slug']
						&& (bool) $recoveryRequest->delete_account === $snapshot['delete_account'], 409, 'Hesap veya talep değişti. Lütfen yeniden inceleyin.');
					$recoveryRequest->user_id = $user->id;
					$recoveryRequest->notes = $request->input('notes');
					if ($recoveryRequest->delete_account) {
						Article::where('user_id', $user->id)->delete();
						$user->delete();
						// The schema permits approved; delete_account identifies deletion approvals.
						$recoveryRequest->status = 'approved';
					} else {
						$request->validate(['new_email' => [Rule::unique('users', 'email')->ignore($user->id)]]);
						Password::broker()->deleteToken($user);
						$user->email = $request->input('new_email');
						$user->save();
						$resetToken = Password::broker()->createToken($user);
						$user->notify(new AccountRecovered($resetToken));
						$recoveryRequest->status = 'approved';
					}
					$recoveryRequest->save();
				});
			} catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface | ValidationException $e) {
				throw $e;
			} catch (\Throwable $e) {
				report($e);
				return redirect()->route('admin.account-recovery.show', ['id' => $id, 'search' => $user->slug])
					->withInput()->with('error', 'İşlem tamamlanamadı. Hesap değişiklikleri kaydedilmedi. Lütfen yeniden deneyin.');
			}
			$request->session()->forget($key);
			return redirect()->route('admin.account-recovery.index')->with('success', $recoveryRequest->delete_account
				? 'Hesap ve yazıları kalıcı olarak silindi.'
				: 'E-posta güncellendi; şifre belirleme bağlantısı ve giriş bilgileri yeni adrese gönderildi.');
		}

		/**
		 * Reject the specified account recovery request.
		 */
		public function reject(Request $request, $id)
		{
			if (Auth::user()->member_type !== 1) {
				abort(403, __('default.Unauthorized action.'));
			}

			$recoveryRequest = AccountRecoveryRequest::findOrFail($id);
			$recoveryRequest->status = 'rejected';
			$recoveryRequest->notes = $request->input('notes');
			$recoveryRequest->save();

			// Optionally, send a rejection email
			// Mail::to($recoveryRequest->contact_email)->send(new RecoveryRequestRejectedMail());

			return redirect()->route('admin.account-recovery.index')->with('success', 'İstek reddedildi.');
		}
	}
