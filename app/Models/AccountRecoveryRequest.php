<?php

	namespace App\Models;

	use Illuminate\Database\Eloquent\Factories\HasFactory;
	use Illuminate\Database\Eloquent\Model;

	class AccountRecoveryRequest extends Model
	{
		use HasFactory;

		/**
		 * The attributes that are mass assignable.
		 *
		 * @var array<int, string>
		 */
		protected $fillable = [
			'real_name',
			'remembered_emails',
			'profile_url',
			'contact_email',
			'id_document_path',
			'status',
			'user_id',
			'notes',
			'delete_account', // ADDED: Added delete_account to fillable attributes.
		];

		/**
		 * Get the user associated with the recovery request.
		 */
		public function user()
		{
			return $this->belongsTo(User::class);
		}
	}
