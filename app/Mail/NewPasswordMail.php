<?php

	namespace App\Mail;

	use Illuminate\Bus\Queueable;
	use Illuminate\Mail\Mailable;
	use Illuminate\Mail\Mailables\Content;
	use Illuminate\Mail\Mailables\Envelope;
	use Illuminate\Queue\SerializesModels;

	class NewPasswordMail extends Mailable
	{
		use Queueable;
		use SerializesModels;

		public string $newPassword;
		public string $newEmail; // ADDED: Public property for the new email

		/**
		 * Create a new message instance.
		 * @param string $newPassword
		 * @param string $newEmail
		 */
		public function __construct(string $newPassword, string $newEmail) // MODIFIED: Constructor now accepts the new email
		{
			$this->newPassword = $newPassword;
			$this->newEmail = $newEmail; // ADDED: Assign the new email to the property
		}

		/**
		 * Get the message envelope.
		 */
		public function envelope(): Envelope
		{
			return new Envelope(
				subject: 'İzEdebiyat Hesap Kurtarma - Yeni Şifreniz',
			);
		}

		/**
		 * Get the message content definition.
		 */
		public function content(): Content
		{
			return new Content(
				view: 'emails.new_password',
			);
		}
	}
