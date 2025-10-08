<?php

	namespace App\Models;

	use Illuminate\Database\Eloquent\Factories\HasFactory;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Support\Str;

	class BookAuthor extends Model
	{
		use HasFactory;

		/**
		 * The attributes that are mass assignable.
		 *
		 * @var array<int, string>
		 */
		protected $fillable = [
			'name',
			'slug',
			'picture',
			'biography',
			'bibliography',
		];

		/**
		 * Boot the model.
		 */
		protected static function boot()
		{
			parent::boot();

			static::creating(function ($author) {
				if (empty($author->slug)) {
					$author->slug = Str::slug($author->name);
				}
			});

			static::updating(function ($author) {
				$author->slug = Str::slug($author->name);
			});
		}

		/**
		 * Get the book reviews for the author.
		 */
		public function bookReviews()
		{
			return $this->hasMany(BookReview::class);
		}
	}
