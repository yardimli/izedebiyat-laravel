<?php

	namespace App\Models;

	use Illuminate\Database\Eloquent\Model;

	class Keyword extends Model
	{
		protected $table = 'keywords';

		protected $fillable = [
			'keyword',
			'keyword_slug',
			'count'
		];

		public function articles()
		{
			return $this->belongsToMany(Article::class, 'article_keyword', 'keyword_id', 'article_id');
		}
	}
