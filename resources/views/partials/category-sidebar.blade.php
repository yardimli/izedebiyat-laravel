<div class="sidebar-widget latest-tpl-4">
	<h5 class="spanborder widget-title">
		<span>Son Eklenenler</span>
	</h5>
	<ol>
		@php
			$counter = 0;
			$authors = [];
			$author_counts = [];
			$previous_author = null;
		@endphp
		
		@foreach($sidebarTexts as $text)
			@php
				$current_author = $text->yazar_id;
				if (!isset($author_counts[$current_author])) {
						$author_counts[$current_author] = 0;
				}
			@endphp
			
			@if($author_counts[$current_author] < 3 && $current_author !== $previous_author && $counter < 20)
				@php
					$author_counts[$current_author]++;
					$previous_author = $current_author;
					$counter++;
				@endphp
				
				<li class="d-flex">
					<div class="post-count">{{ str_pad($counter, 2, "0", STR_PAD_LEFT) }}</div>
					<div class="post-content">
						<h6 class="entry-title mb-1">
							<a href="{{ url('/yapit/' . $text->slug) }}">{{ \App\Helpers\MyHelper::replaceAscii($text->baslik) }}</a>
						</h6>
						<div class="entry-meta align-items-center">
							<a href="{{ url('/yazar/' . $text->yazar_slug) }}">{{ $text->yazar_ad }}</a><br>
							<span>{{ \App\Helpers\MyHelper::timeElapsedString($text->katilma_tarihi) }}</span>
							<span class="middotDivider"></span>
							<span class="readingTime" title="{{ \App\Helpers\MyHelper::estimatedReadingTime($text->yazi) }}">
                                {{ \App\Helpers\MyHelper::estimatedReadingTime($text->yazi) }}
                            </span>
						</div>
					</div>
				</li>
			@endif
		@endforeach
	</ol>
</div>
