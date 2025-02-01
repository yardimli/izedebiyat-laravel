<?php
// app/Models/UserFollow.php
	namespace App\Models;

	use Illuminate\Database\Eloquent\Model;

	class UserFollow extends Model
	{
		protected $fillable = ['follower_id', 'following_id'];

		public function follower()
		{
			return $this->belongsTo(User::class, 'follower_id');
		}

		public function following()
		{
			return $this->belongsTo(User::class, 'following_id');
		}
	}
