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
								@foreach($texts as $text)
									<article class="col-md-4">
										<div class="mb-3 d-flex row">
											<figure class="col-md-5">
												<a href="{{ url('/yapit/' . $text->slug) }}">
													{!! \App\Helpers\MyHelper::getImage($text->yazi_ana_resim ?? '', $text->kategori_id, '', 'width:100%;') !!}
												</a>
											</figure>
											<div class="entry-content col-md-7 pl-md-0">
												<h5 class="entry-title mb-3">
													<a href="{{ url('/yapit/' . $text->slug) }}">
														{{ \App\Helpers\MyHelper::replaceAscii($text->baslik) }}
													</a>
												</h5>
												<div class="entry-meta align-items-center">
													<a href="{{ url('/yazar/' . $text->name_slug) }}">{{ $text->name }}</a>
													<br>
													<a href="{{ url('/kume/' . $text->ust_kategori_slug . '/' . $text->kategori_slug) }}">
														{{ $text->ust_kategori_ad }}
														<span class="middotDivider"></span>
														{{ $text->kategori_ad }}
													</a>
													<br>
													<span>{{ \App\Helpers\MyHelper::timeElapsedString($text->katilma_tarihi) }}</span>
													<span class="middotDivider"></span>
													<span class="readingTime" title="{{ \App\Helpers\MyHelper::estimatedReadingTime($text->yazi) }}">
                                                    {{ \App\Helpers\MyHelper::estimatedReadingTime($text->yazi) }}
                                                </span>
												</div>
											</div>
										</div>
									</article>
								@endforeach
							</div>
							
							{{-- Pagination --}}
							@if($texts->lastPage() > 1)
								<div class="pagination-container">
									<div class="pagination">
										<div class="pagination-info">
                                        <span>
                                            {{ $texts->total() }} yazı içinden
                                            {{ ($texts->currentPage() - 1) * $texts->perPage() + 1 }}-{{ min($texts->currentPage() * $texts->perPage(), $texts->total()) }}
                                            arası görüntüleniyor
                                        </span>
										</div>
										<ul class="page-numbers">
											@if($texts->currentPage() > 1)
												<li>
													<a class="prev page-numbers" href="{{ url('/etiket/' . $keyword->keyword_slug . '/sayfa/' . ($texts->currentPage() - 1)) }}">
														<i class="icon-left-open-big"></i>
													</a>
												</li>
											@endif
											
											@for($i = max(1, $texts->currentPage() - 2); $i <= min($texts->lastPage(), $texts->currentPage() + 2); $i++)
												<li>
													<a class="page-numbers {{ $i == $texts->currentPage() ? 'current' : '' }}"
													   href="{{ url('/etiket/' . $keyword->keyword_slug . '/sayfa/' . $i) }}">
														{{ $i }}
													</a>
												</li>
											@endfor
											
											@if($texts->currentPage() < $texts->lastPage())
												<li>
													<a class="next page-numbers" href="{{ url('/etiket/' . $keyword->keyword_slug . '/sayfa/' . ($texts->currentPage() + 1)) }}">
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
