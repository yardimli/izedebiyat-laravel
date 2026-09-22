<?php

	namespace App\Http\Middleware;

	use Closure;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\App;
	use Symfony\Component\HttpFoundation\Response;

	class LanguageManager
	{
		/**
		 * Handle an incoming request.
		 *
		 * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
		 */
		public function handle(Request $request, Closure $next): Response
		{
			if (session()->has('locale')) {
				App::setLocale(session()->get('locale'));
			} else
			{
				App::setLocale(config('app.locale', 'tr_TR'));
			}
			// Share the JSON and PHP catalogs with legacy regional locale names.
			$locale = ['tr_TR' => 'tr', 'en_US' => 'en'][App::getLocale()] ?? App::getLocale();
			App::setLocale($locale);
			return $next($request);
		}
	}
