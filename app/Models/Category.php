<?php

	namespace App\Models;

	use Illuminate\Database\Eloquent\Model;

	class Category extends Model
	{
		protected $table = 'categories';
		public $timestamps = true;

		protected $fillable = [
			'category_name',
			'parent_category_id',
			'total_articles',
			'new_articles',
			'read_count',
			'english',
			'picture',
			'slug',
		];

		public function parentCategory()
		{
			return $this->belongsTo(Category::class, 'parent_category_id');
		}

		public function subCategories()
		{
			return $this->hasMany(Category::class, 'parent_category_id');
		}

		public function articles()
		{
			return $this->hasMany(Article::class, 'category_id');
		}
	}
