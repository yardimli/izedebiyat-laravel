<?php

	namespace App\Http\Controllers;

	use App\Models\Comment;
	use App\Models\Article;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\DB;

	class CommentController extends Controller
	{
		public function index(Article $article)
		{
			$comments = Comment::with(['user', 'replies.user'])
				->where('article_id', $article->id)
				->where('parent_id', 0)
				->where('is_approved', true)
				->orderBy('created_at', 'desc')
				->get();

			//replace comment content \n with <br>
			foreach ($comments as $comment) {
				$comment->content = nl2br($comment->content);
				foreach ($comment->replies as $reply) {
					$reply->content = nl2br($reply->content);
				}
			}


			return response()->json($comments);
		}

		public function store(Request $request, Article $article)
		{
			$request->validate([
				'content' => 'required|string|max:1000',
				'parent_id' => 'nullable|exists:comments,id'
			]);

			if ($request->parent_id === null || $request->parent_id === '') {
				$request->parent_id = 0;
			}

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

		//------------------------------------------ IMPORT OLD COMMENTS ------
		public function migrateOldComments()
		{
			try {
				// Get all records from yorumlar table
				$oldComments = DB::table('yorumlar')->get();

				$migrated = 0;
				$skipped = 0;

				foreach ($oldComments as $oldComment) {
					// Check if article exists
					$articleExists = DB::table('articles')
						->where('id', $oldComment->yaziID)
						->exists();

					if (!$articleExists) {
						$skipped++;
						continue;
					}

					// Check if user exists
					$userId = 0;
					if ($oldComment->uyeID > 0) {
						$userExists = DB::table('users')
							->where('id', $oldComment->uyeID)
							->exists();

						if ($userExists) {
							$userId = $oldComment->uyeID;
						}
					}

					// Create new comment record
					Comment::create([
						'user_id' => $userId,
						'article_id' => $oldComment->yaziID,
						'content' => $this->cleanContent($oldComment->ybaslik . "\n" . $oldComment->yorum),
						'created_at' => $oldComment->ytarih,
						'updated_at' => $oldComment->ytarih,
						'is_approved' => $oldComment->onayli == 1,
						'parent_id' => 0, // Since old system didn't have nested comments
						'sender_name' => $oldComment->yad,
						'sender_email' => $oldComment->yeposta
					]);

					$migrated++;
				}

				return "Migration completed. Migrated: $migrated, Skipped: $skipped";

			} catch (\Exception $e) {
				return "Error during migration: " . $e->getMessage();
			}
		}

		private function cleanContent($content)
		{
			// Remove any potentially harmful HTML
			$content = strip_tags($content, '<p><br><strong><em><u><i><b>');

			// Convert old Turkish charset to UTF-8 if needed
			if (mb_detect_encoding($content, 'UTF-8', true) === false) {
				$content = mb_convert_encoding($content, 'UTF-8', 'ISO-8859-9');
			}

			$content = str_replace('&#351;', 'ş', $content);
			$content = str_replace('&#305;', 'ı', $content);
			$content = str_replace('&#246;', 'ö', $content);
			$content = str_replace('&#231;', 'ç', $content);
			$content = str_replace('&#252;', 'ü', $content);
			$content = str_replace('&#287;', 'ğ', $content);
			$content = str_replace('&#304;', 'İ', $content);
			$content = str_replace('&#214;', 'Ö', $content);
			$content = str_replace('&#220;', 'Ü', $content);
			$content = str_replace('&#199;', 'Ç', $content);
			$content = str_replace('&#286;', 'Ğ', $content);
			$content = str_replace('&#350;', 'Ş', $content);

			// Trim whitespace
			$content = trim($content);

			return $content;
		}
	}
