@extends('layouts.app-frontend')

@section('title', 'İzEdebiyat - Etiket: ' . $keyword->keyword)

@section('body-class', 'home')

@section('content')
	{{ \App\Helpers\MyHelper::initializeCategoryImages() }}
	
	<section class="archive">
		<main id="content">
			<div class="content-widget">
				<div class="container">
					<div class="row">
						<div class="col-12">
							<h4 class="spanborder">
								<span>Etiket: {{ $keyword->keyword }}</span>
							</h4>
							
							<div class="row">
								@foreach($articles as $article)
									<article class="col-md-4">
										<div class="mb-3 d-flex row">
											<figure class="col-md-5">
												<a href="{{ url('/yapit/' . $article->slug) }}">
													{!! \App\Helpers\MyHelper::getImage($article->featured_image ?? '', $article->category_id, '', 'width:100%;') !!}
												</a>
											</figure>
											<div class="entry-content col-md-7 pl-md-0">
												<h5 class="entry-title mb-3">
													<a href="{{ url('/yapit/' . $article->slug) }}">
														{{ \App\Helpers\MyHelper::replaceAscii($article->title) }}
													</a>
												</h5>
												<div class="entry-meta align-items-center">
													<a href="{{ url('/yazar/' . $article->name_slug) }}">{{ $article->name }}</a>
													<br>
													<a href="{{ url('/kume/' . $article->parent_category_slug . '/' . $article->category_slug) }}">
														{{ $article->parent_category_name }}
														<span class="middotDivider"></span>
														{{ $article->category_name }}
													</a>
													<br>
													<span>{{ \App\Helpers\MyHelper::timeElapsedString($article->created_at) }}</span>
													<span class="middotDivider"></span>
													<span class="readingTime" title="{{ \App\Helpers\MyHelper::estimatedReadingTime($article->main_text) }}">
                                                    {{ \App\Helpers\MyHelper::estimatedReadingTime($article->main_text) }}
                                                </span>
												</div>
											</div>
										</div>
									</article>
								@endforeach
							</div>
							
							{{-- Pagination --}}
							@if($articles->lastPage() > 1)
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
													<a class="prev page-numbers" href="{{ url('/etiket/' . $keyword->keyword_slug . '/sayfa/' . ($articles->currentPage() - 1)) }}">
														<i class="icon-left-open-big"></i>
													</a>
												</li>
											@endif
											
											@for($i = max(1, $articles->currentPage() - 2); $i <= min($articles->lastPage(), $articles->currentPage() + 2); $i++)
												<li>
													<a class="page-numbers {{ $i == $articles->currentPage() ? 'current' : '' }}"
													   href="{{ url('/etiket/' . $keyword->keyword_slug . '/sayfa/' . $i) }}">
														{{ $i }}
													</a>
												</li>
											@endfor
											
											@if($articles->currentPage() < $articles->lastPage())
												<li>
													<a class="next page-numbers" href="{{ url('/etiket/' . $keyword->keyword_slug . '/sayfa/' . ($articles->currentPage() + 1)) }}">
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
				</div>
			</div>
		</main>
	</section>
@endsection
