@if($sameUserAndCategory->count() > 0)
	<div class="related-posts mb-5">
		<h4 class="spanborder text-center mb-1">
			<span>Yazarın {{ $article->category_name }} kümesinde bulunan diğer yazıları...</span>
		</h4>
		<div class="row">
			<div class="divider-2 mb-1"></div>
			@foreach($sameUserAndCategory as $post)
				<article class="col-md-4">
					<div class="mb-2 d-flex row">
						<figure class="col-md-5">
							<a href="{{ url('/yapit/' . $post->slug) }}">
								{!! \App\Helpers\MyHelper::getImage($post->featured_image, $post->category_id, '', 'width: 100%') !!}
							</a>
						</figure>
						<div class="entry-content col-md-7 pl-md-0">
							<h5 class="entry-title mb-2">
								<a href="{{ url('/yapit/' . $post->slug) }}">
									{{ \App\Helpers\MyHelper::replaceAscii($post->title) }}
								</a>
							</h5>
							<div class="entry-meta align-items-center">
								<a href="{{ url('/yazar/' . $post->name_slug) }}">{{ $post->name }}</a> -
								<a href="{{ url('/kume/' . $post->parent_category_slug . '/' . $post->category_slug) }}">
									{{ $post->parent_category_name }}<br>{{ $post->category_name }}
								</a><br>
								<span>{{ \App\Helpers\MyHelper::timeElapsedString($post->created_at) }}</span>
								<span class="middotDivider"></span>
								<span class="readingTime">{{ \App\Helpers\MyHelper::estimatedReadingTime($post->main_text) }}</span>
							</div>
						</div>
					</div>
				</article>
			@endforeach
		</div>
	</div>
@endif

@if($sameUserAndMainCategory->count() > 0)
	<div class="related-posts mb-5">
		<h4 class="spanborder text-center mb-1">
			<span>Yazarın {{ $article->parent_category_name }} kümesinde bulunan diğer yazıları...</span>
		</h4>
		<div class="row">
			<div class="divider-2 mb-1"></div>
			@foreach($sameUserAndMainCategory as $post)
				<article class="col-md-4">
					<div class="mb-2 d-flex row">
						<figure class="col-md-5">
							<a href="{{ url('/yapit/' . $post->slug) }}">
								{!! \App\Helpers\MyHelper::getImage($post->featured_image, $post->category_id, '', 'width: 100%') !!}
							</a>
						</figure>
						<div class="entry-content col-md-7 pl-md-0">
							<h5 class="entry-title mb-2">
								<a href="{{ url('/yapit/' . $post->slug) }}">
									{{ \App\Helpers\MyHelper::replaceAscii($post->title) }}
								</a>
							</h5>
							<div class="entry-meta align-items-center">
								<a href="{{ url('/yazar/' . $post->name_slug) }}">{{ $post->name }}</a> -
								<a href="{{ url('/kume/' . $post->parent_category_slug . '/' . $post->category_slug) }}">
									{{ $post->parent_category_name }}<br>{{ $post->category_name }}
								</a><br>
								<span>{{ \App\Helpers\MyHelper::timeElapsedString($post->created_at) }}</span>
								<span class="middotDivider"></span>
								<span class="readingTime">{{ \App\Helpers\MyHelper::estimatedReadingTime($post->main_text) }}</span>
							</div>
						</div>
					</div>
				</article>
			@endforeach
		</div>
	</div>
@endif

@if($otherUserArticles->count() > 0)
	<div class="related-posts mb-5">
		<h4 class="spanborder text-center mb-1">
			<span>Yazarın diğer ana kümelerde yazmış olduğu yazılar...</span>
		</h4>
		<div class="row">
			<div class="divider-2 mb-1"></div>
			@foreach($otherUserArticles as $post)
				<article class="col-md-4">
					<div class="mb-2 d-flex row">
						<figure class="col-md-5">
							<a href="{{ url('/yapit/' . $post->slug) }}">
								{!! \App\Helpers\MyHelper::getImage($post->featured_image, $post->category_id, '', 'width: 100%') !!}
							</a>
						</figure>
						<div class="entry-content col-md-7 pl-md-0">
							<h5 class="entry-title mb-2">
								<a href="{{ url('/yapit/' . $post->slug) }}">
									{{ \App\Helpers\MyHelper::replaceAscii($post->title) }}
								</a>
							</h5>
							<div class="entry-meta align-items-center">
								<a href="{{ url('/yazar/' . $post->name_slug) }}">{{ $post->name }}</a> -
								<a href="{{ url('/kume/' . $post->parent_category_slug . '/' . $post->category_slug) }}">
									{{ $post->parent_category_name }}<br>{{ $post->category_name }}
								</a><br>
								<span>{{ \App\Helpers\MyHelper::timeElapsedString($post->created_at) }}</span>
								<span class="middotDivider"></span>
								<span class="readingTime">{{ \App\Helpers\MyHelper::estimatedReadingTime($post->main_text) }}</span>
							</div>
						</div>
					</div>
				</article>
			@endforeach
		</div>
	</div>
@endif
