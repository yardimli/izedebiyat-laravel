<?php

	namespace App\Models;

	use Illuminate\Database\Eloquent\Model;

	class ArticleKeyword extends Model
	{
		protected $table = 'article_keyword';

		protected $fillable = [
			'article_id',
			'keyword_id'
		];

		public function article()
		{
			return $this->belongsTo(Article::class, 'article_id');
		}

		public function keyword()
		{
			return $this->belongsTo(Keyword::class, 'keyword_id');
		}
	}
