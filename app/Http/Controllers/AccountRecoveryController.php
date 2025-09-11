<?php

	namespace App\Http\Controllers;

	// ADDED: Necessary imports for the constructor logic
	use App\Models\Article;
	use App\Models\Category;
	use App\Models\AccountRecoveryRequest;
	use App\Models\User;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Cache;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\Log;
	use Illuminate\Support\Facades\Mail;
	use Illuminate\Support\Facades\Storage;
	use Illuminate\Support\Str;
	use App\Mail\NewPasswordMail;

	class AccountRecoveryController extends Controller
	{
		// ADDED: Constructor to provide $mainMenuCategories to the view layout
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
				'contact_email' => 'required|email|max:255',
				'id_document' => 'required|image|mimes:jpeg,png,jpg|max:2048',
			]);

			$path = $request->file('id_document')->store('id_documents', 'public');

			AccountRecoveryRequest::create([
				'real_name' => $request->real_name,
				'remembered_emails' => $request->remembered_emails,
				'contact_email' => $request->contact_email,
				'id_document_path' => $path,
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
				abort(403, 'Unauthorized action.');
			}

			$requests = AccountRecoveryRequest::orderBy('created_at', 'desc')->paginate(20);
			return view('backend.account_recovery_index', compact('requests'));
		}

		/**
		 * Display the specified account recovery request for admins.
		 */
		public function show($id)
		{
			if (Auth::user()->member_type !== 1) {
				abort(403, 'Unauthorized action.');
			}

			$request = AccountRecoveryRequest::findOrFail($id);
			return view('backend.account_recovery_show', compact('request'));
		}

		/**
		 * Approve the specified account recovery request.
		 */
		public function approve(Request $request, $id)
		{
			if (Auth::user()->member_type !== 1) {
				abort(403, 'Unauthorized action.');
			}

			$recoveryRequest = AccountRecoveryRequest::findOrFail($id);
			$user = User::find($request->input('user_id'));

			if (!$user) {
				return back()->with('error', 'Kullanıcı bulunamadı. Lütfen geçerli bir kullanıcı seçin.');
			}

			$newPassword = Str::random(12);
			$user->password = Hash::make($newPassword);
			$user->save();

			// Send email with the new password
			Mail::to($recoveryRequest->contact_email)->send(new NewPasswordMail($newPassword));

			$recoveryRequest->status = 'approved';
			$recoveryRequest->user_id = $user->id;
			$recoveryRequest->notes = $request->input('notes');
			$recoveryRequest->save();

			return redirect()->route('admin.account-recovery.index')->with('success', 'İstek onaylandı ve yeni şifre kullanıcıya gönderildi.');
		}

		/**
		 * Reject the specified account recovery request.
		 */
		public function reject(Request $request, $id)
		{
			if (Auth::user()->member_type !== 1) {
				abort(403, 'Unauthorized action.');
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
