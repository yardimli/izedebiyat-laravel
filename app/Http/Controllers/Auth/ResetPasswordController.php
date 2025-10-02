<?php

	namespace App\Http\Controllers\Auth;

	use App\Http\Controllers\Controller;
	use Illuminate\Foundation\Auth\ResetsPasswords;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Str;

	class ResetPasswordController extends Controller
	{
		use ResetsPasswords;

		/**
		 * Where to redirect users after resetting their password.
		 *
		 * @var string
		 */
		// MODIFIED: This is kept for reference but will be overridden by our custom response logic.
		protected $redirectTo = '/sahne-arkasi';

		/**
		 * Reset the given user's password.
		 *
		 * @param \Illuminate\Contracts\Auth\CanResetPassword $user
		 * @param string $password
		 * @return void
		 */
		// MODIFIED: Overriding this method to prevent automatic login after a password reset.
		protected function resetPassword($user, $password)
		{
			$user->password = Hash::make($password);
			$user->setRememberToken(Str::random(60));
			$user->save();
			// NOTE: The default behavior logs the user in. We have removed that line
			// to ensure the user is redirected to the login page to sign in again.
		}

		/**
		 * Get the response for a successful password reset.
		 *
		 * @param \Illuminate\Http\Request $request
		 * @param string $response
		 * @return \Illuminate\Http\RedirectResponse
		 */
		// ADDED: A new method to override the default redirect behavior.
		protected function sendResetResponse(Request $request, $response)
		{
			// MODIFIED: Redirect to the 'login' route instead of the dashboard.
			return redirect()->route('login')
				// MODIFIED: Add a specific, localized success message to the session.
				->with('status', 'Şifreniz başarıyla değiştirildi. Lütfen yeni şifrenizle giriş yapın.');
		}

		/**
		 * Display the password reset view for the given token.
		 *
		 * If no token is present, display the link request form.
		 *
		 * @param \Illuminate\Http\Request $request
		 * @param string|null $token
		 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
		 */
		public function showResetPasswordForm(Request $request, $token = null)
		{
			return view('auth.passwords.reset')->with(['token' => $token, 'email' => $request->email]);
		}
	}
