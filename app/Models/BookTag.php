<?php

	namespace App\Models;

	use Illuminate\Database\Eloquent\Factories\HasFactory;
	use Illuminate\Database\Eloquent\Model;

	class BookTag extends Model
	{
		use HasFactory;

		protected $fillable = ['name', 'slug'];
		public $timestamps = false;

		public function bookReviews()
		{
			return $this->belongsToMany(BookReview::class, 'book_review_tag');
		}
	}
