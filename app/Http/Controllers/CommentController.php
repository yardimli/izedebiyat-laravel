<?php

	namespace App\Http\Controllers;

	use App\Models\Comment;
	use App\Models\Article;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Auth;

	class CommentController extends Controller
	{
		public function index(Article $article)
		{
			$comments = Comment::with(['user', 'replies.user'])
				->where('article_id', $article->id)
				->whereNull('parent_id')
				->where('is_approved', true)
				->orderBy('created_at', 'desc')
				->get();

			return response()->json($comments);
		}

		public function store(Request $request, Article $article)
		{
			$request->validate([
				'content' => 'required|string|max:1000',
				'parent_id' => 'nullable|exists:comments,id'
			]);

			$comment = Comment::create([
				'user_id' => Auth::id(),
				'article_id' => $article->id,
				'parent_id' => $request->parent_id,
				'content' => $request->content,
				'is_approved' => true
			]);

			$comment = Comment::with(['user', 'replies.user'])
				->find($comment->id);

			// Initialize empty replies array if there are none
			$comment->replies = $comment->replies ?? collect([]);

			return response()->json($comment);
		}

		public function destroy(Comment $comment)
		{
			if (Auth::id() !== $comment->user_id && !Auth::user()->isAdmin()) {
				return response()->json(['message' => 'Unauthorized'], 403);
			}

			$comment->delete();
			return response()->json(['message' => 'Comment deleted successfully']);
		}
	}
