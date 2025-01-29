<div class="sidebar-widget latest-tpl-4">
	<h5 class="spanborder widget-title">
		<span>POPÜLER</span>
	</h5>
	<ol>
		@php
			$counter = 0;
		@endphp
		
		@foreach($sidebarTexts as $article)
			@php $counter++; @endphp
			<li class="d-flex">
				<div class="post-count">{{ str_pad($counter, 2, "0", STR_PAD_LEFT) }}</div>
				<div class="post-content">
					<h5 class="entry-title mb-1">
						<a href="{{ url('/yapit/' . $article->slug) }}">
							{{ \App\Helpers\MyHelper::replaceAscii($article->title) }}
						</a>
					</h5>
					<div class="entry-meta align-items-center">
						{{ $article->name }}<br>
						<span>{{ \App\Helpers\MyHelper::timeElapsedString($article->created_at) }}</span>
						<span class="middotDivider"></span>
						<span class="readingTime">
                            {{ \App\Helpers\MyHelper::estimatedReadingTime($article->main_text) }}
                        </span>
					</div>
				</div>
			</li>
		@endforeach
	</ol>
</div>
