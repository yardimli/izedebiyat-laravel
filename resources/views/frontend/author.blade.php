@extends('layouts.app-frontend')

@section('title', 'İzEdebiyat - ' . $author->yazar_ad)
@section('body-class', 'archive')

@section('content')
	{{ \App\Helpers\MyHelper::initializeCategoryImages() }}
	
	<main id="content">
		<div class="content-widget">
			<div class="container">
				<div class="row">
					<div class="col-md-8 col-12">
						<div class="box box-author mb-2">
							<div class="post-author row-flex">
								<div class="author-img">
									{!! \App\Helpers\MyHelper::generateInitialsAvatar($author->yazar_resim, $author->yazar_ad) !!}
								</div>
								<div class="author-content">
									<div class="top-author">
										<h5 class="heading-font">{{ $author->yazar_ad }}</h5>
									</div>
									<p class="d-none d-md-block">
										{!! nl2br(e($author->yazar_tanitim)) !!}
									</p>
									<div class="readmore" data-tid="yazar_hakkinda">
										Devamını göster &gt;
									</div>
									<p class="hide mt-2" id="yazar_hakkinda">
										{!! nl2br(e($author->yazar_gecmis)) !!}
										@if($author->yazar_gecmis)
											<br><br>
										@endif
										{!! nl2br(e($author->yazar_konum)) !!}
										@if($author->yazar_konum)
											<br><br>
										@endif
										{!! nl2br(e($author->yazar_resim_yazi)) !!}
										@if($author->yazar_resim_yazi)
											<br><br>
										@endif
										{!! nl2br(e($author->site_adres)) !!}
										@if($author->site_adres)
											<br><br>
										@endif
										{{ $author->katilma_tarihi }}
									</p>
									<div class="readless hide" data-tid="yazar_hakkinda">
										Daha az &gt;
									</div>
								</div>
							</div>
						</div>
						
						<h4 class="spanborder pt-4">
							<span>Yeni</span>
						</h4>
						
						
						@php
							$counter = 0;
						@endphp
						
						@foreach($texts as $text)
							@php
								$counter++;
							@endphp
							
							@if ($counter <=20)
								<article class="row justify-content-between mb-5 mr-0">
									<div class="col-md-9">
										<div class="align-self-center" style="min-height: 200px;">
											<div class="capsSubtle mb-2">
												<a
													href="{{ url('/kume/' . $text->ust_kategori_slug . '/' . $text->kategori_slug) }}">{{ $text->kategori_ad }}</a>
											</div>
											<h3 class="entry-title mb-3">
												<a
													href="{{ url('/yapit/' . $text->slug) }}">{{ \App\Helpers\MyHelper::replaceAscii($text->baslik) }}</a>
											</h3>
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
												<a href="{{ url('/yazar/' . $text->yazar_slug) }}">{{ $text->yazar_ad }}</a><br>
												<span>{{ \App\Helpers\MyHelper::timeElapsedString($text->katilma_tarihi) }}</span>
												<span class="middotDivider"></span>
												<span class="readingTime"
												      title="{{ \App\Helpers\MyHelper::estimatedReadingTime($text->yazi) }}">
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
						@include('partials.author-sidebar')
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
										   href="{{ url('/yazar/' . $author->slug . '/sayfa/' . ($currentPage - 1)) }}">
											<i class="icon-left-open-big"></i>
										</a>
									</li>
								@endif
								
								@for($i = max(1, $currentPage - 2); $i <= min($lastPage, $currentPage + 2); $i++)
									<li>
										<a class="page-numbers {{ $i == $currentPage ? 'current' : '' }}"
										   href="{{ url('/yazar/' . $author->slug . '/sayfa/' . $i) }}">
											{{ $i }}
										</a>
									</li>
								@endfor
								
								@if($currentPage < $lastPage)
									<li>
										<a class="next page-numbers"
										   href="{{ url('/yazar/' . $author->slug . '/sayfa/' . ($currentPage + 1)) }}">
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
					<a href="#"><img src="{{ asset('frontend/assets/images/ads/ads-2.png') }}" alt="ads"
					                 style="max-width:80%;"></a>
				</div>
				<div class="hr"></div>
			</div>
		</div>
	</main>
	</section>
@endsection

@push('scripts')
	<script>
		$(document).ready(function () {
			$('.readmore').click(function (event) {
				$("#" + $(this).data("tid")).slideDown();
				$(".readmore").hide();
				$(".readless").show();
				event.preventDefault();
			});
			
			$('.readless').click(function (event) {
				$("#" + $(this).data("tid")).slideUp();
				$(".readless").hide();
				$(".readmore").show();
				event.preventDefault();
			});
		});
	</script>
@endpush

@push('styles')
	<style>
      .hide {
          display: none;
      }

      .readmore, .readless {
          text-align: right;
          cursor: pointer;
      }
	</style>
@endpush
