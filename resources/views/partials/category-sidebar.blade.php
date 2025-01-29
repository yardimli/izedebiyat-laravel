<div class="sidebar-widget latest-tpl-4">
	
	<h5 class="spanborder widget-title" style="margin-bottom: 8px;">
		<span>Kümeler</span>
	</h5>
	@foreach($category->subCategories as $index => $subCategory)
		<a href="{{ url('/kume/' . $category->slug . '/' . $subCategory->slug) }}"
		   alt="{{ $subCategory->category_name . ' ' . $subCategory->new_articles }}">
			<div class="btn btn-sm btn-outline-info mb-1" style=" font-size: 14px;
					@if($subCategory->new_articles > 0)
					font-weight: bold;
					@endif

		">
				{!! $subCategory->category_name !!}
			</div>
		</a>
	@endforeach
	
	
	<h5 class="spanborder widget-title mt-3">
		<span>Son Eklenenler</span>
	</h5>
	<ol>
		@php
			$counter = 0;
			$users = [];
			$user_counts = [];
			$previous_user = null;
		@endphp
		
		@foreach($sidebarTexts as $article)
			@php
				$current_user = $article->user_id;
				if (!isset($user_counts[$current_user])) {
						$user_counts[$current_user] = 0;
				}
			@endphp
			
			@if($user_counts[$current_user] < 3 && $current_user !== $previous_user && $counter < 20)
				@php
					$user_counts[$current_user]++;
					$previous_user = $current_user;
					$counter++;
				@endphp
				
				<li class="d-flex">
					<div class="post-count">{{ str_pad($counter, 2, "0", STR_PAD_LEFT) }}</div>
					<div class="post-content">
						<h6 class="entry-title mb-1">
							<a href="{{ url('/yapit/' . $article->slug) }}">{{ \App\Helpers\MyHelper::replaceAscii($article->title) }}</a>
						</h6>
						<div class="entry-meta align-items-center">
							<a href="{{ url('/yazar/' . $article->name_slug) }}">{{ $article->name }}</a><br>
							<span>{{ \App\Helpers\MyHelper::timeElapsedString($article->created_at) }}</span>
							<span class="middotDivider"></span>
							<span class="readingTime" title="{{ \App\Helpers\MyHelper::estimatedReadingTime($article->main_text) }}">
                                {{ \App\Helpers\MyHelper::estimatedReadingTime($article->main_text) }}
                            </span>
						</div>
					</div>
				</li>
			@endif
		@endforeach
	</ol>
</div>
