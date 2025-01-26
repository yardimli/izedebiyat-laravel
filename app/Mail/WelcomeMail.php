<?php

	namespace App\Mail;

	use Illuminate\Bus\Queueable;
	use Illuminate\Contracts\Queue\ShouldQueue;
	use Illuminate\Mail\Mailable;
	use Illuminate\Mail\Mailables\Content;
	use Illuminate\Mail\Mailables\Envelope;
	use Illuminate\Queue\SerializesModels;

	class WelcomeMail extends Mailable
	{
		use Queueable, SerializesModels;

		public $name;
		public $email;

		public function __construct($name, $email)
		{
			$this->name = $name;
			$this->email = $email;
		}

		public function build()
		{
			$locale = \App::getLocale() ?: config('app.fallback_locale', 'tr_TR');

			$subject = 'İzEdebiyat\'a Hoşgeldiniz! Yolculuğunuz burada başlıyor.';
			$email_view = 'emails.welcome-tr_TR';


			return $this->from(env('MAIL_FROM_ADDRESS','yazisma@izedebiyat.com'), env('MAIL_FROM_NAME', 'İzEdebiyat Yazışma'))
				->subject($subject)
				->view($email_view)
				->with(['name' => $this->name, 'email' => $this->email]);
		}
	}
