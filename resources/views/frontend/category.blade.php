@extends('layouts.app-frontend')

@section('title', 'İzEdebiyat - ' . $category->kategori_ad)

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
								<span style="text-transform: uppercase;">{{ $category->kategori_ad }}</span>
							</h4>
							
							@php
								$counter = 0;
								$authors = [];
								$author_counts = [];
								$previous_author = null;
								$open_div = false;
							@endphp
							
							@foreach($texts as $text)
								@php
									$current_author = $text->yazar_id;
									if (!isset($author_counts[$current_author])) {
											$author_counts[$current_author] = 0;
									}
								@endphp
								
								@if($author_counts[$current_author] < 3 && $current_author !== $previous_author)
									@php
										$author_counts[$current_author]++;
										$previous_author = $current_author;
										$counter++;
									@endphp
									
									@if($counter === 1)
										<article class="first mb-3">
											<figure>
												<a href="{{ url('/yapit/' . $text->slug) }}">
													{!! \App\Helpers\MyHelper::getImage($text->yazi_ana_resim ?? '', $text->kategori_id, '', 'width: 100%; max-height:120px; object-fit: cover') !!}
												</a>
											</figure>
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
												<a href="{{ url('/yazar/' . $text->yazar_slug) }}">{{ $text->yazar_ad }}</a> -
												<a
													href="{{ url('/kume/' . $text->ust_kategori_slug . '/' . $text->kategori_slug) }}">{{ $text->kategori_ad }}</a><br>
												<span>{{ \App\Helpers\MyHelper::timeElapsedString($text->katilma_tarihi) }}</span>
												<span class="middotDivider"></span>
												<span class="readingTime"
												      title="{{ \App\Helpers\MyHelper::estimatedReadingTime($text->yazi) }}">
                                                {{ \App\Helpers\MyHelper::estimatedReadingTime($text->yazi) }}
                                            </span>
											</div>
										</article>
										<div class="divider"></div>
									@elseif(($counter >= 4 && $counter <= 7) || ($counter >= 14 && $counter <= 17))
										@if($counter === 4 || $counter === 14)
											@php
												$open_div = true;
											@endphp
											<div class="row justify-content-between">
												<div class="divider-2"></div>
												@endif
												
												<article class="col-md-6">
													<div class="mb-3 d-flex row">
														<figure class="col-md-5">
															<a href="{{ url('/yapit/' . $text->slug) }}">
																{!! \App\Helpers\MyHelper::getImage($text->yazi_ana_resim ?? '', $text->kategori_id, '', 'width: 100%;') !!}
															</a>
														</figure>
														<div class="entry-content col-md-7 pl-md-0">
															<h5 class="entry-title mb-3">
																<a
																	href="{{ url('/yapit/' . $text->slug) }}">{{ \App\Helpers\MyHelper::replaceAscii($text->baslik) }}</a>
															</h5>
															<div class="entry-meta align-items-center">
																<a href="{{ url('/yazar/' . $text->yazar_slug) }}">{{ $text->yazar_ad }}</a> -
																<a
																	href="{{ url('/kume/' . $text->ust_kategori_slug . '/' . $text->kategori_slug) }}">{{ $text->kategori_ad }}</a><br>
																<span>{{ \App\Helpers\MyHelper::timeElapsedString($text->katilma_tarihi) }}</span>
																<span class="middotDivider"></span>
																<span class="readingTime"
																      title="{{ \App\Helpers\MyHelper::estimatedReadingTime($text->yazi) }}">
                                                        {{ \App\Helpers\MyHelper::estimatedReadingTime($text->yazi) }}
                                                    </span>
															</div>
														</div>
													</div>
												</article>
												
												@if($counter === 7 || $counter === 17)
													@php
														$open_div = false;
													@endphp
											</div>
										@endif
									@elseif ($counter <=20)
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
								@endif
							@endforeach
							
							@if($open_div)
						</div>
						@endif
					
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
										   href="{{ url('/kume/' . $category->slug . '/sayfa/' . ($currentPage - 1)) }}">
											<i class="icon-left-open-big"></i>
										</a>
									</li>
								@endif
								
								@for($i = max(1, $currentPage - 2); $i <= min($lastPage, $currentPage + 2); $i++)
									<li>
										<a class="page-numbers {{ $i == $currentPage ? 'current' : '' }}"
										   href="{{ url('/kume/' . $category->slug . '/sayfa/' . $i) }}">
											{{ $i }}
										</a>
									</li>
								@endfor
								
								@if($currentPage < $lastPage)
									<li>
										<a class="next page-numbers"
										   href="{{ url('/kume/' . $category->slug . '/sayfa/' . ($currentPage + 1)) }}">
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
			
			
			<div class="content-widget">
				<div class="container">
					<div class="sidebar-widget ads">
						<a href="#"><img src="{{ asset('frontend/assets/images/ads/ads-2.png') }}" alt="ads" style="max-width:80%;"></a>
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
			// Add any necessary JavaScript
		});
	</script>
@endpush

@push('styles')
	<style>
	</style>
@endpush
