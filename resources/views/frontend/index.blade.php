@extends('layouts.app-frontend')

@section('title', 'İzEdebiyat - Anasayfa')
@section('body-class', 'home')

@section('content')
	{{ \App\Helpers\MyHelper::initializeCategoryImages() }}
	
	<section class="home">
		<main id="content">
			@foreach($categories as $category)
				<div class="section-featured featured-style-1 pt-2 mb-4 {{ $loop->iteration % 2 == 0 ? 'alternate_background' : '' }}">
					<div class="container">
						<div class="row">
							<!--begin featured-->
							<div class="col-sm-12 col-md-9 col-xl-9">
								<h2 class="spanborder h4">
									<span>{!! $category->kategori_ad !!}</span>
								</h2>
								<div class="row">
									@php
										$counter = 0;
										$authors = [];
									@endphp
									
									@foreach($category->yazilar as $story)
										
										@if(!in_array($story->yazar_id, $authors))
											@php
												$counter++;
												$authors[] = $story->yazar_id;
											@endphp
											
											@if($counter === 1)
												<div class="col-sm-12 col-md-6">
													<article class="first mb-4">
														<figure>
															<a href="/yapit/{{ $story->slug }}">
																{!! \App\Helpers\MyHelper::getImage($story->yazi_ana_resim ?? '', $story->kategori_id, '', 'width: 100%') !!}
															</a>
														</figure>
														<h3 class="entry-title mb-2">
															<a href="/yapit/{{ $story->slug }}">{{ \App\Helpers\MyHelper::replaceAscii($story->baslik) }}</a>
														</h3>
														<div class="entry-excerpt">
															<p class="mb-1">
																@if($story->ust_kategori_slug === "siir")
																	{!! \App\Helpers\MyHelper::getWords(\App\Helpers\MyHelper::replaceAscii($story->yazi), 16, false) !!}
																@else
																	{!! \App\Helpers\MyHelper::getWords(\App\Helpers\MyHelper::replaceAscii($story->tanitim), 16) !!}
																@endif
															</p>
														</div>
														<div class="entry-meta align-items-center">
															<a href="/yazar/{{ $story->yazar_slug }}">{{ $story->yazar_ad }}</a> -
															<a href="/kume/{{ $story->ust_kategori_slug }}/{{ $story->kategori_slug }}">{!! $story->kategori_ad !!}</a><br>
															<span>{{ \App\Helpers\MyHelper::timeElapsedString($story->katilma_tarihi) }}</span>
															<span class="middotDivider"></span>
															<span class="readingTime" title="{{ \App\Helpers\MyHelper::estimatedReadingTime($story->yazi) }}">{{ \App\Helpers\MyHelper::estimatedReadingTime($story->yazi) }}</span>
														</div>
													</article>
												</div>
												<div class="col-sm-12 col-md-6">
													@endif
													
													@if($counter > 1 && $counter <= 5)
														<article>
															<div class="mb-3 d-flex row">
																<figure class="col-4 col-md-4">
																	<a href="/yapit/{{ $story->slug }}">
																		{!! \App\Helpers\MyHelper::getImage($story->yazi_ana_resim ?? '',$story->kategori_id, '', 'width: 100%') !!}
																	</a>
																</figure>
																<div class="entry-content col-8 col-md-8 pl-md-0">
																	<h5 class="entry-title mb-2">
																		<a href="/yapit/{{ $story->slug }}">{{ \App\Helpers\MyHelper::replaceAscii($story->baslik) }}</a>
																	</h5>
																	<div class="entry-meta align-items-center">
																		<a href="/yazar/{{ $story->yazar_slug }}">{{ $story->yazar_ad }}</a> -
																		<a href="/kume/{{ $story->ust_kategori_slug }}/{{ $story->kategori_slug }}">{!! $story->kategori_ad !!}</a><br>
																		<span>{{ \App\Helpers\MyHelper::timeElapsedString($story->katilma_tarihi) }}</span>
																		<span class="middotDivider"></span>
																		<span class="readingTime" title="{{ \App\Helpers\MyHelper::estimatedReadingTime($story->yazi) }}">{{ \App\Helpers\MyHelper::estimatedReadingTime($story->yazi) }}</span>
																	</div>
																</div>
															</div>
														</article>
													@endif
													
													@if($counter === 5)
												</div>
											@endif
										@endif
									@endforeach
								</div>
							</div>
							<!--end featured-->
							
							<!--begin Trending-->
							<div class="col-sm-12 col-md-3 col-xl-3">
								<div class="sidebar-widget latest-tpl-4">
									<h4 class="spanborder">
										<span>Yeni</span>
									</h4>
									<ol>
										@php
											$counter = 0;
											$authors = [];
										@endphp
										
										@foreach($category->yeni_yazilar as $story)
											
											@if(!in_array($story->yazar_id, $authors) && $counter < 5)
												@php
													$counter++;
													$authors[] = $story->yazar_id;
												@endphp
												<li class="d-flex">
													<div class="post-count">{{ str_pad($counter, 2, "0", STR_PAD_LEFT) }}</div>
													<div class="post-content">
														<h5 class="entry-title mb-2">
															<a href="/yapit/{{ $story->slug }}">{{ \App\Helpers\MyHelper::replaceAscii($story->baslik) }}</a>
														</h5>
														<div class="entry-meta align-items-center">
															<a href="/yazar/{{ $story->yazar_slug }}">{{ $story->yazar_ad }}</a> -
															<a href="/kume/{{ $story->ust_kategori_slug }}/{{ $story->kategori_slug }}">{!! $story->kategori_ad !!}</a><br>
															<span>{{ \App\Helpers\MyHelper::timeElapsedString($story->katilma_tarihi) }}</span>
															<span class="middotDivider"></span>
															<span class="readingTime" title="{{ \App\Helpers\MyHelper::estimatedReadingTime($story->yazi) }}">{{ \App\Helpers\MyHelper::estimatedReadingTime($story->yazi) }}</span>
														</div>
													</div>
												</li>
											@endif
										@endforeach
									</ol>
								</div>
							</div>
							<!--end Trending-->
						</div>
					</div>
				</div>
			@endforeach
			
			<div class="content-widget">
				<div class="container">
					<div class="sidebar-widget ads">
						<a href="#"><img src="/frontend/assets/images/ads/ads-2.png" alt="ads" style="max-width:80%;"></a>
					</div>
					<div class="hr"></div>
				</div>
			</div>
		</main>
	</section>
@endsection
