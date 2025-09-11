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

		/**
		 * Create a new message instance.
		 */
		public function __construct(string $newPassword)
		{
			$this->newPassword = $newPassword;
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
