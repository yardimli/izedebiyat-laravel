<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;
class RecaptchaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
	    Validator::extend('recaptcha', function ($attribute, $value, $parameters, $validator) {
		    $client = new Client();

		    $response = $client->post('https://www.google.com/recaptcha/api/siteverify',
			    ['form_params'=>
				    [
					    'secret' => config('services.recaptcha.secret_key'),
					    'response' => $value,
					    'remoteip' => request()->ip()
				    ]
			    ]
		    );

		    $body = json_decode((string)$response->getBody());
		    return $body->success;
	    });

	    Validator::replacer('recaptcha', function ($message, $attribute, $rule, $parameters) {
		    return 'The recaptcha verification failed. Please try again.';
	    });
    }
}
