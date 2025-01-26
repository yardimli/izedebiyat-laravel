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
						@foreach($texts as $text)
							<article class="col-12 col-xl-3 col-md-4 justify-content-between mb-5 mr-0">
								<a href="{{ url('/yapit/' . $text->slug) }}">
									{!! \App\Helpers\MyHelper::getImage($text->yazi_ana_resim ?? '', $text->kategori_id, 'mb-2', 'width:100%; max-height:178px; object-fit: cover') !!}
								</a>
								<div class="align-self-center" style="min-height: 200px;">
									<h3 class="entry-title mb-3">
										<a href="{{ url('/yapit/' . $text->slug) }}">
											{{ \App\Helpers\MyHelper::replaceAscii($text->baslik) }}
										</a>
									</h3>
									<div class="capsSubtle mb-2">
										<a href="{{ url('/kume/' . $text->ust_kategori_slug . '/' . $text->kategori_slug) }}">
											{{ $text->ust_kategori_ad . ' - ' . $text->kategori_ad }}
										</a>
									</div>
									<div class="entry-excerpt">
										<p>
											@if($text->ust_kategori_slug === "siir")
												{!! \App\Helpers\MyHelper::getWords(\App\Helpers\MyHelper::replaceAscii($text->yazi), 16, false) !!}
											@else
												{!! \App\Helpers\MyHelper::getWords(\App\Helpers\MyHelper::replaceAscii($text->tanitim), 48) !!}
											@endif
										</p>
									</div>
									<div class="entry-meta align-items-center">
										<a href="{{ url('/yazar/' . $text->name_slug) }}">{{ $text->name }}</a><br>
										<span>{{ \App\Helpers\MyHelper::timeElapsedString($text->katilma_tarihi) }}</span>
										<span class="middotDivider"></span>
										<span class="readingTime">
                                        {{ \App\Helpers\MyHelper::estimatedReadingTime($text->yazi) }}
                                    </span>
									</div>
								</div>
							</article>
						@endforeach
					@endif
				</div>
				
				@if(isset($texts) && $texts->lastPage() > 1)
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
										<a class="prev page-numbers" href="{{ $texts->url($texts->currentPage() - 1) . '&q=' . $query }}">
											<i class="icon-left-open-big"></i>
										</a>
									</li>
								@endif
								
								@for($i = max(1, $texts->currentPage() - 2); $i <= min($texts->lastPage(), $texts->currentPage() + 2); $i++)
									<li>
										<a class="page-numbers {{ $i == $texts->currentPage() ? 'current' : '' }}"
										   href="{{ $texts->url($i) . '&q=' . $query }}">
											{{ $i }}
										</a>
									</li>
								@endfor
								
								@if($texts->currentPage() < $texts->lastPage())
									<li>
										<a class="next page-numbers" href="{{ $texts->url($texts->currentPage() + 1) . '&q=' . $query }}">
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
