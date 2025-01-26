@extends('layouts.app-frontend')

@section('title', 'İzEdebiyat - ' . $category->kategori_ad . ' - ' . $subCategory->kategori_ad)

@section('body-class', 'home')

@section('content')
	{{ \App\Helpers\MyHelper::initializeCategoryImages() }}

	<section class="archive">
		<main id="content">
			<div class="content-widget">
				<div class="container">
					<div class="row">
						<div class="col-md-8 col-12">
							<h4 class="spanborder">
                            <span style="text-transform: uppercase;">
                                <a href="{{ url('/kume/' . $category->slug) }}">{{ $category->kategori_ad }}</a>
                                @if($subCategory)
		                            > {{ $subCategory->kategori_ad }}
	                            @endif
                            </span>
							</h4>
							
							@php
								$counter = 0;
								$authors = [];
								$author_counts = [];
								$previous_author = null;
							@endphp
							
							@foreach($texts as $text)
								@php
									$current_author = $text->user_id;
									if (!isset($author_counts[$current_author])) {
											$author_counts[$current_author] = 0;
									}
								@endphp
								
								@if ($counter === 20)
									@break
								@endif
								
								@if($author_counts[$current_author] < 3 && $current_author !== $previous_author)
									@php
										$author_counts[$current_author]++;
										$previous_author = $current_author;
										$counter++;
									@endphp
									
									<article class="row justify-content-between mb-5 mr-0">
										<div class="col-md-9">
											<div class="align-self-center" style="min-height: 200px;">
												<div class="capsSubtle mb-2">{{ $text->sentiment }}</div>
												<h3 class="entry-title mb-2">
													<a href="{{ url('/yapit/' . $text->slug) }}">
														{{ \App\Helpers\MyHelper::replaceAscii($text->baslik) }}
													</a>
												</h3>
												<div class="entry-excerpt">
													<p class="mb-1">
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
										</div>
										<div class="col-md-3">
											<a href="{{ url('/yapit/' . $text->slug) }}">
												{!! \App\Helpers\MyHelper::getImage($text->yazi_ana_resim ?? '', $text->kategori_id, 'bgcover2', '') !!}
											</a>
										</div>
									</article>
								@endif
							@endforeach
						</div>
						
						<div class="col-md-4 pl-md-5 sticky-sidebar">
							@include('partials.category-sidebar')
						</div>
					</div>
					
					<!-- Pagination -->
					@php
						$currentPage = $texts->currentPage();
						$lastPage = $texts->lastPage();
						$totalItems = $texts->total();
						$perPage = $texts->perPage();
					@endphp
					
					@if($lastPage > 1)
						<div class="pagination-container">
							<div class="pagination">
								<div class="pagination-info">
									<span>{{ $totalItems }} yazı içinden {{ ($currentPage - 1) * $perPage + 1 }}-{{ min($currentPage * $perPage, $totalItems) }} arası görüntüleniyor</span>
								</div>
								<ul class="page-numbers">
									@if($currentPage > 1)
										<li>
											<a class="prev page-numbers"
											   href="{{ url('/kume/' . $category->slug . '/' . $subCategory->slug .'/sayfa/' . ($currentPage - 1)) }}">
												<i class="icon-left-open-big"></i>
											</a>
										</li>
									@endif
									
									@for($i = max(1, $currentPage - 2); $i <= min($lastPage, $currentPage + 2); $i++)
										<li>
											<a class="page-numbers {{ $i == $currentPage ? 'current' : '' }}"
											   href="{{ url('/kume/' . $category->slug . '/' . $subCategory->slug . '/sayfa/' . $i) }}">
												{{ $i }}
											</a>
										</li>
									@endfor
									
									@if($currentPage < $lastPage)
										<li>
											<a class="next page-numbers"
											   href="{{ url('/kume/' . $category->slug . '/' . $subCategory->slug .'/sayfa/' . ($currentPage + 1)) }}">
												<i class="icon-right-open-big"></i>
											</a>
										</li>
									@endif
								</ul>
							</div>
						</div>
					@endif
					<!-- End Pagination -->
				</div>
			</div>
			
			<div class="content-widget">
				<div class="container">
					<div class="sidebar-widget ads">
						<a href="#"><img src="{{ asset('/frontend/assets/images/ads/ads-2.png') }}" alt="ads" style="max-width:80%;"></a>
					</div>
					<div class="hr"></div>
				</div>
			</div>
		</main>
	</section>
@endsection

@push('styles')
	<style>
      /* Add any necessary styles */
	</style>
@endpush
