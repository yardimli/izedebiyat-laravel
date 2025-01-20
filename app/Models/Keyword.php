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

		public function yazilar()
		{
			return $this->belongsToMany(Yazi::class, 'yazi_keyword', 'keyword_id', 'yazi_id');
		}
	}
