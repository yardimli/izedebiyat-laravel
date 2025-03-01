<?php

	namespace App\Models;

	use Illuminate\Database\Eloquent\Model;

	class Comment extends Model
	{
		protected $fillable = [
			'user_id',
			'article_id',
			'parent_id',
			'content',
			'sender_name',
			'sender_email',
			'is_approved',
			'created_at',
			'updated_at'
		];

		public function user()
		{
			return $this->belongsTo(User::class);
		}

		public function article()
		{
			return $this->belongsTo(Article::class);
		}

		public function replies()
		{
			return $this->hasMany(Comment::class, 'parent_id');
		}

		public function parent()
		{
			return $this->belongsTo(Comment::class, 'parent_id');
		}
	}
