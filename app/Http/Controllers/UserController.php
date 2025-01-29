<?php

	namespace App\Http\Controllers;

	use App\Helpers\MyHelper;
	use App\Models\ChatBody;
	use App\Models\ChatHeader;
	use Illuminate\Http\Request;
	use App\Models\User;
	use Illuminate\Pagination\LengthAwarePaginator;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Facades\Log;
	use Illuminate\Support\Facades\Storage;
	use Illuminate\Support\Str;
	use Illuminate\Support\Facades\Validator;


	class UserController extends Controller
	{
		public function index(Request $request)
		{
			if (Auth::user()->member_type === 1) {
				$query = User::query()
					->select('users.*')
					->selectRaw('(SELECT COUNT(*) FROM articles WHERE articles.user_id = users.id AND articles.approved = 1 AND articles.deleted = 0) as story_count')
					->selectRaw('(SELECT MAX(created_at) FROM articles WHERE articles.user_id = users.id AND articles.approved = 1 AND articles.deleted = 0) as last_story_date');

				if ($request->has('search')) {
					$query->where('name', 'like', "%{$request->search}%")
						->orWhere('email', 'like', "%{$request->search}%");
				}

				$users = $query->orderBy('id', 'desc')->get();

				$page = LengthAwarePaginator::resolveCurrentPage() ?: 1;
				$items = $users->forPage($page, 100);

				$users = new LengthAwarePaginator(
					$items,
					$users->count(),
					100,
					$page,
					['path' => LengthAwarePaginator::resolveCurrentPath()]
				);

				return view('backend.users', compact('users'));
			} else {
				abort(403, 'Unauthorized action.');
			}
		}

		public function loginAs(Request $request)
		{
			if (Auth::user()->member_type === 1) {
				Auth::loginUsingId($request->user_id);
				return redirect()->route('articles.index');
			} else {
				abort(403, 'Unauthorized action.');
			}
		}



	}
