<?php

	namespace App\Models;

	use Illuminate\Database\Eloquent\Model;

	class Article extends Model
	{
		protected $table = 'articles';

		protected $fillable = [
			'user_id',
			'is_published',
			'parent_category_id',
			'category_id',
			'approved',
			'title',
			'slug',
			'subtitle',
			'keywords_string',
			'publishing_date',
			'subheading',
			'main_text',
			'name',
			'name_slug',
			'category_name',
			'category_slug',
			'parent_category_name',
			'parent_category_slug',
			'sentiment',
			'featured_image',
			'deleted',
			'formul_ekim',
			'read_count',
			'has_moderation',
			'moderation_flagged',
			'bad_critical',
			'has_religious_moderation',
			'religious_moderation_value',
			'respect_moderation_value',
			'moderation',
			'critical_reason',
			'religious_reason',
			'article_order',
			'has_changed',
			'created_at',
			'updated_at',
			// Add other fillable fields as needed
		];

		protected $dates = [
			'created_at'
		];

		protected $casts = [
			'created_at' => 'datetime'
		];

		public function user()
		{
			return $this->belongsTo(User::class, 'user_id');
		}

		public function category()
		{
			return $this->belongsTo(Category::class, 'category_id');
		}

		public function parentCategory()
		{
			return $this->belongsTo(Category::class, 'parent_category_id');
		}

		public function keywords()
		{
			return $this->belongsToMany(Keyword::class, 'article_keyword', 'article_id', 'keyword_id');
		}

		public function getOriginalUrl(): string
		{
			if (!$this->featured_image) {
				return asset('images/no-image.png');
			}
			if (stripos($this->featured_image, '00001_') !== false) {
				$articleMainImage = $this->featured_image;
				$articleMainImage = str_ireplace('.png', '.jpg', $articleMainImage);
				$articleMainImage = str_replace('\\', '/', $articleMainImage);

				return asset('storage/yazi_resimler/' . $articleMainImage);
			} else {
				return asset('storage/upload-images/original/' . $this->featured_image);
			}
		}

		public function getLargeUrl(): string
		{
			if (!$this->featured_image) {
				return asset('images/no-image.png');
			}
			if (stripos($this->featured_image, '00001_') !== false) {

				$articleMainImage = $this->featured_image;
				$articleMainImage = str_ireplace('.png', '.jpg', $articleMainImage);
				$articleMainImage = str_replace('\\', '/', $articleMainImage);

				return asset('storage/yazi_resimler/' . $articleMainImage);
			} else {
				return asset('storage/upload-images/large/' . $this->featured_image);
			}
		}

		public function getMediumUrl(): string
		{
			if (!$this->featured_image) {
				return asset('images/no-image.png');
			}
			if (stripos($this->featured_image, '00001_') !== false) {
				$articleMainImage = $this->featured_image;
				$articleMainImage = str_ireplace('.png', '.jpg', $articleMainImage);
				$articleMainImage = str_replace('\\', '/', $articleMainImage);

				return asset('storage/yazi_resimler/' . $articleMainImage);
			} else {
				return asset('storage/upload-images/medium/' . $this->featured_image);
			}
		}

		public function getSmallUrl(): string
		{
			if (!$this->featured_image) {
				return asset('images/no-image.png');
			}
			if (stripos($this->featured_image, '00001_') !== false) {
				$articleMainImage = $this->featured_image;
				$articleMainImage = str_ireplace('.png', '.jpg', $articleMainImage);
				$articleMainImage = str_replace('\\', '/', $articleMainImage);

				return asset('storage/yazi_resimler/' . $articleMainImage);
			} else {
				return asset('storage/upload-images/small/' . $this->featured_image);
			}
		}

		public function favorites()
		{
			return $this->hasMany(ArticleFavorite::class);
		}

		public function claps()
		{
			return $this->hasMany(Clap::class);
		}

	}
