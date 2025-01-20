<?php

	namespace App\Models;

	use Illuminate\Database\Eloquent\Model;

	class Kategori extends Model
	{
		protected $table = 'kategoriler';

		protected $fillable = [
			'kategori_ad',
			'ust_kategori_id',
			'kac_yazi',
			'kac_yeni_yazi',
			'okuma_sayac',
			'english',
			'picture',
			'slug_tr',
			'slug_en'
		];

		public function parentCategory()
		{
			return $this->belongsTo(Kategori::class, 'ust_kategori_id');
		}

		public function subCategories()
		{
			return $this->hasMany(Kategori::class, 'ust_kategori_id');
		}

		public function yazilar()
		{
			return $this->hasMany(Yazi::class, 'kategori_id');
		}
	}
