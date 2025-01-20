<?php

	namespace App\Models;

	use Illuminate\Database\Eloquent\Model;

	class YaziKeyword extends Model
	{
		protected $table = 'yazi_keyword';

		protected $fillable = [
			'yazi_id',
			'keyword_id'
		];

		public function yazi()
		{
			return $this->belongsTo(Yazi::class, 'yazi_id');
		}

		public function keyword()
		{
			return $this->belongsTo(Keyword::class, 'keyword_id');
		}
	}
