<?php

	namespace App\Models;

	use Illuminate\Database\Eloquent\Model;

	class Yazar extends Model
	{
		protected $table = 'yazar';

		protected $fillable = [
			'yazar_ad',
			'slug',
			'nick',
			'sifre',
			'eposta',
			'sayfa_baslik',
			'site_adres',
			'yazar_tanitim',
			// Add other fillable fields as needed
		];

		protected $dates = [
			'katilma_tarih'
		];

		public function yazilar()
		{
			return $this->hasMany(Yazi::class, 'yazar_id');
		}
	}
