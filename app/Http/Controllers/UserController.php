<?php

	namespace App\Http\Controllers;

	use App\Helpers\MyHelper;
	use App\Models\ChatBody;
	use App\Models\ChatHeader;
	use App\Models\Article;
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
            abort_unless($request->user()->isAdmin(), 403);
            $columns = ['id'=>'users.id', 'name'=>'users.name', 'email'=>'users.email', 'story_count'=>'story_count', 'last_story_date'=>'last_story_date', 'created_at'=>'users.created_at'];
            $data = $request->validate(['search'=>'nullable|string|max:200', 'sort'=>'sometimes|in:'.implode(',', array_keys($columns)), 'direction'=>'sometimes|in:asc,desc']);
            $sort = $data['sort'] ?? 'id';
            $direction = $data['direction'] ?? 'desc';
            $search = trim($data['search'] ?? '');
            $query = User::query()->select('users.*')
                ->selectRaw('(SELECT COUNT(*) FROM articles WHERE articles.user_id = users.id AND articles.approved = 1 AND articles.deleted = 0) as story_count')
                ->selectRaw('(SELECT MAX(created_at) FROM articles WHERE articles.user_id = users.id AND articles.approved = 1 AND articles.deleted = 0) as last_story_date');
            if ($search !== '') {
                $pattern = '%'.str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $search).'%';
                $query->where(fn ($q) => $q->whereRaw("users.name LIKE ? ESCAPE '!'", [$pattern])->orWhereRaw("users.email LIKE ? ESCAPE '!'", [$pattern]));
            }
            $query->orderBy($columns[$sort], $direction);
            if ($sort !== 'id') $query->orderByDesc('users.id');
            $users = $query->paginate(100)->withQueryString();
            return view('backend.users', compact('users', 'sort', 'direction', 'search'));
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



		public function destroy(User $user)
		{
			if (Auth::user()->member_type !== 1) {
				abort(403, 'Unauthorized action.');
			}

			// Delete all articles by the user
			Article::where('user_id', $user->id)->delete();

			// Delete the user
			$user->delete();

			return redirect()->route('admin-users-index')->with('success', 'User and all their articles have been deleted successfully.');
		}
	}
