<?php

	namespace App\View\Composers;

	use Illuminate\View\View;
	use App\Helpers\MyHelper; // <-- Import your helper
	use App\Models\Quote;

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
			$quoteRecord = Quote::forToday();
			$defaultQuote = "Kelimelerin g\u{00FC}c\u{00FC}yle d\u{00FC}nyalar\u{0131} de\u{011F}i\u{015F}tirin.";
			$view->with('inspirationalQuote', $quoteRecord
				? $quoteRecord->quote." \u{2014} ".$quoteRecord->author
				: $defaultQuote);
			return;

			$quoteRecord = Quote::forToday();
			$view->with('inspirationalQuote', $quoteRecord
				? $quoteRecord->quote.' - '.$quoteRecord->author
				: 'Kelimelerin gcyle dnyalari degistirin.');
			return;
			// Call the helper function to get the quote (cached or newly generated)
			$quote = MyHelper::generateInspirationalQuote();

			// Share the quote with the view under the variable name 'inspirationalQuote'
			$view->with('inspirationalQuote', $quote);
		}
	}
