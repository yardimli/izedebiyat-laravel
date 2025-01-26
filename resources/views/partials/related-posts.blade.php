@if($sameAuthorAndCategory->count() > 0)
	<div class="related-posts mb-5">
		<h4 class="spanborder text-center mb-1">
			<span>Yazarın {{ $article->kategori_ad }} kümesinde bulunan diğer yazıları...</span>
		</h4>
		<div class="row">
			<div class="divider-2 mb-1"></div>
			@foreach($sameAuthorAndCategory as $post)
				<article class="col-md-4">
					<div class="mb-2 d-flex row">
						<figure class="col-md-5">
							<a href="{{ url('/yapit/' . $post->slug) }}">
								{!! \App\Helpers\MyHelper::getImage($post->yazi_ana_resim, $post->kategori_id, '', 'width: 100%') !!}
							</a>
						</figure>
						<div class="entry-content col-md-7 pl-md-0">
							<h5 class="entry-title mb-2">
								<a href="{{ url('/yapit/' . $post->slug) }}">
									{{ \App\Helpers\MyHelper::replaceAscii($post->baslik) }}
								</a>
							</h5>
							<div class="entry-meta align-items-center">
								<a href="{{ url('/yazar/' . $post->name_slug) }}">{{ $post->name }}</a> -
								<a href="{{ url('/kume/' . $post->ust_kategori_slug . '/' . $post->kategori_slug) }}">
									{{ $post->ust_kategori_ad }}<br>{{ $post->kategori_ad }}
								</a><br>
								<span>{{ \App\Helpers\MyHelper::timeElapsedString($post->katilma_tarihi) }}</span>
								<span class="middotDivider"></span>
								<span class="readingTime">{{ \App\Helpers\MyHelper::estimatedReadingTime($post->yazi) }}</span>
							</div>
						</div>
					</div>
				</article>
			@endforeach
		</div>
	</div>
@endif

@if($sameAuthorAndMainCategory->count() > 0)
	<div class="related-posts mb-5">
		<h4 class="spanborder text-center mb-1">
			<span>Yazarın {{ $article->ust_kategori_ad }} kümesinde bulunan diğer yazıları...</span>
		</h4>
		<div class="row">
			<div class="divider-2 mb-1"></div>
			@foreach($sameAuthorAndMainCategory as $post)
				<article class="col-md-4">
					<div class="mb-2 d-flex row">
						<figure class="col-md-5">
							<a href="{{ url('/yapit/' . $post->slug) }}">
								{!! \App\Helpers\MyHelper::getImage($post->yazi_ana_resim, $post->kategori_id, '', 'width: 100%') !!}
							</a>
						</figure>
						<div class="entry-content col-md-7 pl-md-0">
							<h5 class="entry-title mb-2">
								<a href="{{ url('/yapit/' . $post->slug) }}">
									{{ \App\Helpers\MyHelper::replaceAscii($post->baslik) }}
								</a>
							</h5>
							<div class="entry-meta align-items-center">
								<a href="{{ url('/yazar/' . $post->name_slug) }}">{{ $post->name }}</a> -
								<a href="{{ url('/kume/' . $post->ust_kategori_slug . '/' . $post->kategori_slug) }}">
									{{ $post->ust_kategori_ad }}<br>{{ $post->kategori_ad }}
								</a><br>
								<span>{{ \App\Helpers\MyHelper::timeElapsedString($post->katilma_tarihi) }}</span>
								<span class="middotDivider"></span>
								<span class="readingTime">{{ \App\Helpers\MyHelper::estimatedReadingTime($post->yazi) }}</span>
							</div>
						</div>
					</div>
				</article>
			@endforeach
		</div>
	</div>
@endif

@if($otherAuthorArticles->count() > 0)
	<div class="related-posts mb-5">
		<h4 class="spanborder text-center mb-1">
			<span>Yazarın diğer ana kümelerde yazmış olduğu yazılar...</span>
		</h4>
		<div class="row">
			<div class="divider-2 mb-1"></div>
			@foreach($otherAuthorArticles as $post)
				<article class="col-md-4">
					<div class="mb-2 d-flex row">
						<figure class="col-md-5">
							<a href="{{ url('/yapit/' . $post->slug) }}">
								{!! \App\Helpers\MyHelper::getImage($post->yazi_ana_resim, $post->kategori_id, '', 'width: 100%') !!}
							</a>
						</figure>
						<div class="entry-content col-md-7 pl-md-0">
							<h5 class="entry-title mb-2">
								<a href="{{ url('/yapit/' . $post->slug) }}">
									{{ \App\Helpers\MyHelper::replaceAscii($post->baslik) }}
								</a>
							</h5>
							<div class="entry-meta align-items-center">
								<a href="{{ url('/yazar/' . $post->name_slug) }}">{{ $post->name }}</a> -
								<a href="{{ url('/kume/' . $post->ust_kategori_slug . '/' . $post->kategori_slug) }}">
									{{ $post->ust_kategori_ad }}<br>{{ $post->kategori_ad }}
								</a><br>
								<span>{{ \App\Helpers\MyHelper::timeElapsedString($post->katilma_tarihi) }}</span>
								<span class="middotDivider"></span>
								<span class="readingTime">{{ \App\Helpers\MyHelper::estimatedReadingTime($post->yazi) }}</span>
							</div>
						</div>
					</div>
				</article>
			@endforeach
		</div>
	</div>
@endif
