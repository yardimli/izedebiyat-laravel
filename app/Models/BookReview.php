<?php

	namespace App\Models;

	use Illuminate\Database\Eloquent\Factories\HasFactory;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Support\Str;

	class BookReview extends Model
	{
		use HasFactory;

		protected $fillable = [
			'title',
			'slug',
			'author',
			'cover_image',
			'review_content',
			'user_id',
			'is_published',
			'published_at',
			// MODIFIED: Added new optional fields
			'publisher',
			'publication_date',
			'publication_place',
			'buy_url',
			'book_author_id', // ADDED: Foreign key for BookAuthor
		];

		protected $casts = [
			'is_published' => 'boolean',
			'published_at' => 'datetime',
		];

		/**
		 * Boot the model.
		 */
		protected static function boot()
		{
			parent::boot();

			static::creating(function ($bookReview) {
				$bookReview->slug = Str::slug($bookReview->title);
			});
		}

		/**
		 * Get the user who wrote the review.
		 */
		public function user()
		{
			return $this->belongsTo(User::class);
		}

		/**
		 * The categories that belong to the book review.
		 */
		public function categories()
		{
			return $this->belongsToMany(BookCategory::class, 'book_review_category');
		}

		/**
		 * The tags that belong to the book review.
		 */
		public function tags()
		{
			return $this->belongsToMany(BookTag::class, 'book_review_tag');
		}

		/**
		 * Get the author of the book.
		 * ADDED: Relationship to BookAuthor
		 */
		public function bookAuthor()
		{
			return $this->belongsTo(BookAuthor::class);
		}

		/**
		 * Get the display author name, prioritizing the relationship.
		 * ADDED: Accessor for author name
		 */
		public function getDisplayAuthorAttribute()
		{
			return $this->bookAuthor->name ?? $this->author;
		}
	}
