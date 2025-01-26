<?php

	namespace App\Models;

	use Illuminate\Database\Eloquent\Model;

	class Yazi extends Model
	{
		protected $table = 'yazilar';

		protected $fillable = [
			'baslik',
			'slug',
			'alt_baslik',
			'keywords',
			'yayinlama_tarih',
			'tanitim',
			'yazi',
			'name',
			'name_slug',
			'kategori_ad',
			'kategori_slug',
			'ust_kategori_ad',
			'ust_kategori_slug',
			// Add other fillable fields as needed
		];

		protected $dates = [
			'katilma_tarihi'
		];

		public function yazar()
		{
			return $this->belongsTo(User::class, 'user_id');
		}

		public function kategori()
		{
			return $this->belongsTo(Kategori::class, 'kategori_id');
		}

		public function ustKategori()
		{
			return $this->belongsTo(Kategori::class, 'ust_kategori_id');
		}

		public function keywords()
		{
			return $this->belongsToMany(Keyword::class, 'yazi_keyword', 'yazi_id', 'keyword_id');
		}
	}
