@extends('layouts.app-frontend')

@section('title', 'İzEdebiyat - Arama Sonuçları')
@section('body-class', 'home archive')

@section('content')
	<main id="content">
		<div class="content-widget">
			<div class="container">
				<!--Begin Archive Header-->
				<div class="row">
					<div class="col-12 archive-header text-center pt-3 pb-0">
						<h3 class="mb-0">Arama Sonuçları</h3>
					</div>
				</div>
				<div class="divider"></div>
				<!--End Archive Header-->
				
				<div class="row">
					@if(isset($error))
						<div class="col-12">
							<h3 class="text-center">{{ $error }}</h3>
						</div>
					@else
						@foreach($articles as $article)
							<article class="col-12 col-xl-3 col-md-4 justify-content-between mb-5 mr-0">
								<a href="{{ url('/yapit/' . $article->slug) }}">
									{!! \App\Helpers\MyHelper::getImage($article->featured_image ?? '', $article->category_id, 'mb-2', 'width:100%; max-height:178px; object-fit: cover') !!}
								</a>
								<div class="align-self-center" style="min-height: 200px;">
									<h3 class="entry-title mb-3">
										<a href="{{ url('/yapit/' . $article->slug) }}">
											{{ \App\Helpers\MyHelper::replaceAscii($article->title) }}
										</a>
									</h3>
									<div class="capsSubtle mb-2">
										<a href="{{ url('/kume/' . $article->parent_category_slug . '/' . $article->category_slug) }}">
											{{ $article->parent_category_name . ' - ' . $article->category_name }}
										</a>
									</div>
									<div class="entry-excerpt">
										<p>
											@if($article->parent_category_slug === "siir")
												{!! \App\Helpers\MyHelper::getWords(\App\Helpers\MyHelper::replaceAscii($article->main_text), 16, false) !!}
											@else
												{!! \App\Helpers\MyHelper::getWords(\App\Helpers\MyHelper::replaceAscii($article->subheading), 48) !!}
											@endif
										</p>
									</div>
									<div class="entry-meta align-items-center">
										<a href="{{ url('/yazar/' . $article->name_slug) }}">{{ $article->name }}</a><br>
										<span>{{ \App\Helpers\MyHelper::timeElapsedString($article->created_at) }}</span>
										<span class="middotDivider"></span>
										<span class="readingTime">
                                        {{ \App\Helpers\MyHelper::estimatedReadingTime($article->main_text) }}
                                    </span>
									</div>
								</div>
							</article>
						@endforeach
					@endif
				</div>
				
				@if(isset($articles) && $articles->lastPage() > 1)
					<div class="pagination-container">
						<div class="pagination">
							<div class="pagination-info">
                            <span>
                                {{ $articles->total() }} yazı içinden
                                {{ ($articles->currentPage() - 1) * $articles->perPage() + 1 }}-{{ min($articles->currentPage() * $articles->perPage(), $articles->total()) }}
                                arası görüntüleniyor
                            </span>
							</div>
							<ul class="page-numbers">
								@if($articles->currentPage() > 1)
									<li>
										<a class="prev page-numbers" href="{{ $articles->url($articles->currentPage() - 1) . '&q=' . $query }}">
											<i class="icon-left-open-big"></i>
										</a>
									</li>
								@endif
								
								@for($i = max(1, $articles->currentPage() - 2); $i <= min($articles->lastPage(), $articles->currentPage() + 2); $i++)
									<li>
										<a class="page-numbers {{ $i == $articles->currentPage() ? 'current' : '' }}"
										   href="{{ $articles->url($i) . '&q=' . $query }}">
											{{ $i }}
										</a>
									</li>
								@endfor
								
								@if($articles->currentPage() < $articles->lastPage())
									<li>
										<a class="next page-numbers" href="{{ $articles->url($articles->currentPage() + 1) . '&q=' . $query }}">
											<i class="icon-right-open-big"></i>
										</a>
									</li>
								@endif
							</ul>
						</div>
					</div>
				@endif
			</div>
		</div>
	</main>
@endsection
