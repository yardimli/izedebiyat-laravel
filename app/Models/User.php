<?php

	namespace App\Models;

	use Illuminate\Contracts\Auth\MustVerifyEmail;
	use Illuminate\Database\Eloquent\Factories\HasFactory;
	use Illuminate\Foundation\Auth\User as Authenticatable;
	use Illuminate\Notifications\Notifiable;
	use Illuminate\Contracts\Auth\CanResetPassword;


//use Laravel\Fortify\TwoFactorAuthenticatable;
//use Laravel\Jetstream\HasProfilePhoto;
//use Laravel\Jetstream\HasTeams;
	use Laravel\Sanctum\HasApiTokens;

	use App\Notifications\CustomVerifyEmail;


	class User extends Authenticatable implements MustVerifyEmail, CanResetPassword
	{
		use HasApiTokens;
		use HasFactory;

//	use HasProfilePhoto;
//	use HasTeams;
		use Notifiable;

//	use TwoFactorAuthenticatable;


		/**
		 * The attributes that are mass assignable.
		 *
		 * @var array<int, string>
		 */
		protected $fillable = [
			'name',
			'slug',
			'email',
			'password',
			'google_id',
			'line_id',
			'facebook_id',
			'avatar',
			'picture',
			'username',
			'page_title',
			'personal_url',
			'about_me',
			'member_status',
			'member_type',
			'last_login',
			'last_ip',
			'background_image',
			'email_verified_at',


		];

		/**
		 * The attributes that should be hidden for serialization.
		 *
		 * @var array<int, string>
		 */
		protected $hidden = [
			'password',
			'remember_token',
			'two_factor_recovery_codes',
			'two_factor_secret',
		];

		/**
		 * The attributes that should be cast.
		 *
		 * @var array<string, string>
		 */
		protected $casts = [
			'email_verified_at' => 'datetime',
			'password' => 'hashed',
		];

		protected $appends = [
			'profile_photo_url',
		];

		public function sendEmailVerificationNotification()
		{
			$this->notify(new CustomVerifyEmail());
		}


		public function isAdmin()
		{
			return $this->member_type === 1;
		}

		public function articles()
		{
			return $this->hasMany(Article::class, 'user_id');
		}

		public function bookReviews()
		{
			return $this->hasMany(BookReview::class, 'user_id');
		}

		public function followers()
		{
			return $this->hasMany(UserFollow::class, 'following_id')->orWhere(function($query) {
				$query->whereRaw('1 = 0'); // This ensures an empty collection is returned instead of null
			});
		}

		public function following()
		{
			return $this->hasMany(UserFollow::class, 'follower_id')->orWhere(function($query) {
				$query->whereRaw('1 = 0'); // This ensures an empty collection is returned instead of null
			});
		}

		public function favorites()
		{
			return $this->hasMany(ArticleFavorite::class)->orWhere(function($query) {
				$query->whereRaw('1 = 0'); // This ensures an empty collection is returned instead of null
			});
		}

		public function getProfilePhotoUrlAttribute()
		{
			if ($this->avatar) {
				// If avatar exists, return its URL
				//remove /public from the path
				$avatar = str_replace('public/', '', $this->avatar);
				return asset('storage/' . $avatar);
			}

			// If no avatar, generate initials avatar
			$name = $this->name;
			$words = explode(' ', $name);
			$initials = '';

			foreach ($words as $w) {
				$initials .= mb_substr($w, 0, 1);
			}

			// Return a URL to generate an avatar with initials
			return 'https://ui-avatars.com/api/?name=' . urlencode($initials) . '&color=7F9CF5&background=EBF4FF';
		}


	}
