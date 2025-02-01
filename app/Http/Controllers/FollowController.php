<?php
	// app/Http/Controllers/FollowController.php
	namespace App\Http\Controllers;

	use App\Models\User;
	use App\Models\Article;
	use App\Models\UserFollow;
	use App\Models\ArticleFavorite;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Auth;

	class FollowController extends Controller
	{
		public function toggleFollow(User $user)
		{
			$follow = UserFollow::where('follower_id', Auth::id())
				->where('following_id', $user->id)
				->first();

			if ($follow) {
				$follow->delete();
				return response()->json(['following' => false]);
			}

			UserFollow::create([
				'follower_id' => Auth::id(),
				'following_id' => $user->id
			]);

			return response()->json(['following' => true]);
		}

		public function toggleFavorite(Article $article)
		{
			$favorite = ArticleFavorite::where('user_id', Auth::id())
				->where('article_id', $article->id)
				->first();

			if ($favorite) {
				$favorite->delete();
				return response()->json(['favorited' => false]);
			}

			ArticleFavorite::create([
				'user_id' => Auth::id(),
				'article_id' => $article->id
			]);

			return response()->json(['favorited' => true]);
		}

		public function following()
		{
			$following = Auth::user()->following()
				->with('following')
				->paginate(10);

			$favorites = Auth::user()->favorites()
				->with('article')
				->paginate(10);

			return view('backend.following', compact('following', 'favorites'));
		}
	}
