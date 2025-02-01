<?php

	namespace App\Models;

	use Illuminate\Database\Eloquent\Factories\HasFactory;
	use Illuminate\Database\Eloquent\Model;

	class Clap extends Model
	{
		use HasFactory;

		protected $fillable = ['user_id', 'article_id', 'count'];

		public function user()
		{
			return $this->belongsTo(User::class);
		}

		public function article()
		{
			return $this->belongsTo(Article::class);
		}
	}
