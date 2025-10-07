<?php

	namespace App\Models;

	use Illuminate\Database\Eloquent\Factories\HasFactory;
	use Illuminate\Database\Eloquent\Model;

	class BookCategory extends Model
	{
		use HasFactory;

		protected $fillable = ['name', 'slug'];
		public $timestamps = false;

		public function bookReviews()
		{
			return $this->belongsToMany(BookReview::class, 'book_review_category');
		}
	}
