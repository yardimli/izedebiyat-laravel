<div class="sidebar-widget latest-tpl-4">
	<h5 class="spanborder widget-title">
		<span>POPÜLER</span>
	</h5>
	<ol>
		@php
			$counter = 0;
		@endphp
		
		@foreach($sidebarTexts as $text)
			@php $counter++; @endphp
			<li class="d-flex">
				<div class="post-count">{{ str_pad($counter, 2, "0", STR_PAD_LEFT) }}</div>
				<div class="post-content">
					<h5 class="entry-title mb-1">
						<a href="{{ url('/yapit/' . $text->slug) }}">
							{{ \App\Helpers\MyHelper::replaceAscii($text->baslik) }}
						</a>
					</h5>
					<div class="entry-meta align-items-center">
						{{ $text->name }}<br>
						<span>{{ \App\Helpers\MyHelper::timeElapsedString($text->katilma_tarihi) }}</span>
						<span class="middotDivider"></span>
						<span class="readingTime">
                            {{ \App\Helpers\MyHelper::estimatedReadingTime($text->yazi) }}
                        </span>
					</div>
				</div>
			</li>
		@endforeach
	</ol>
</div>
