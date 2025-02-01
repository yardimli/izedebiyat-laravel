<?php

	namespace App\Models;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Relations\BelongsTo;
	use Illuminate\Database\Eloquent\Relations\HasMany;

	class Image extends Model
	{
		protected $fillable = [
			'user_id',
			'image_type',
			'image_guid',
			'image_alt',
			'user_prompt',
			'llm_prompt',
			'image_prompt',
			'llm',
			'prompt_tokens',
			'completion_tokens',
			'image_original_filename',
			'image_large_filename',
			'image_medium_filename',
			'image_small_filename'
		];

		public function user(): BelongsTo
		{
			return $this->belongsTo(User::class);
		}

		public function articles(): HasMany
		{
			return $this->hasMany(Article::class, 'featured_image');
		}
	}
