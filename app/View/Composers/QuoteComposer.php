<?php

	namespace App\View\Composers;

	use Illuminate\View\View;
	use App\Helpers\MyHelper; // <-- Import your helper

	class QuoteComposer
	{
		/**
		 * Bind data to the view.
		 *
		 * @param  \Illuminate\View\View  $view
		 * @return void
		 */
		public function compose(View $view)
		{
			// Call the helper function to get the quote (cached or newly generated)
			$quote = MyHelper::generateInspirationalQuote();

			// Share the quote with the view under the variable name 'inspirationalQuote'
			$view->with('inspirationalQuote', $quote);
		}
	}
