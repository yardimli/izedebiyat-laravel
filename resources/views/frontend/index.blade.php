@extends('layouts.app-frontend')

@section('title', 'İzEdebiyat - Anasayfa')
@section('body-class', 'home')

@section('content')
	{{ \App\Helpers\MyHelper::initializeCategoryImages() }}
	
	<section class="home">
		<main id="content">
			@php
				$counter_section=0;
			@endphp
			@foreach($categories as $category)
				@php
					$counter_section++;
				@endphp
				<div
					class="section-featured featured-style-1 pt-2 mb-4 {{ $loop->iteration % 2 == 0 ? 'alternate_background' : '' }}">
					<div class="container-lg">
						<div class="row">
							<!--begin featured-->
							<div class="col-sm-12 col-md-9 col-xl-9">
								<h2 class="spanborder h4">
									<span><a href="/kume/{{ $category->slug }}">{{ $category->category_name }}</a></span>
								</h2>
								<div class="row">
									@php
										$counter = 0;
										$users = [];
									@endphp
									
									@foreach($category->articles as $story)
										
										@if(!in_array($story->user_id, $users))
											@php
												$counter++;
												$users[] = $story->user_id;
											@endphp
											
											@if($counter === 1)
												<div class="col-sm-12 col-md-6">
													<article class="first mb-4">
														<figure>
															<a href="/yapit/{{ $story->slug }}">
																{!! \App\Helpers\MyHelper::getImage($story->featured_image ?? '', $story->category_id, '', 'width: 100%','medium_landscape') !!}
															</a>
														</figure>
														<h3 class="entry-title mb-2">
															<a
																href="/yapit/{{ $story->slug }}">{{ \App\Helpers\MyHelper::replaceAscii($story->title) }}</a>
														</h3>
														<div class="entry-excerpt">
															<p class="mb-1">
																@if($story->parent_category_slug === "siir")
																	{!! \App\Helpers\MyHelper::getWords(\App\Helpers\MyHelper::replaceAscii($story->main_text), 16, false) !!}
																@else
																	{!! \App\Helpers\MyHelper::getWords(\App\Helpers\MyHelper::replaceAscii($story->subheading), 16) !!}
																@endif
															</p>
														</div>
														<div class="entry-meta align-items-center">
															<a href="/yazar/{{ $story->name_slug }}">{{ $story->name }}</a> -
															<a
																href="/kume/{{ $story->parent_category_slug }}/{{ $story->category_slug }}">{!! $story->category_name !!}</a><br>
															<span>{{ \App\Helpers\MyHelper::timeElapsedString($story->created_at) }}</span>
															<span class="middotDivider"></span>
															<span class="readingTime"
															      title="{{ \App\Helpers\MyHelper::estimatedReadingTime($story->main_text) }}">{{ \App\Helpers\MyHelper::estimatedReadingTime($story->main_text) }}</span>
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
																		{!! \App\Helpers\MyHelper::getImage($story->featured_image ?? '',$story->category_id, '', 'width: 100%', 'small_landscape') !!}
																	</a>
																</figure>
																<div class="entry-content col-8 col-md-8 pl-md-0">
																	<h5 class="entry-title mb-2">
																		<a
																			href="/yapit/{{ $story->slug }}">{{ \App\Helpers\MyHelper::replaceAscii($story->title) }}</a>
																	</h5>
																	<div class="entry-meta align-items-center">
																		<a href="/yazar/{{ $story->name_slug }}">{{ $story->name }}</a> -
																		<a
																			href="/kume/{{ $story->parent_category_slug }}/{{ $story->category_slug }}">{!! $story->category_name !!}</a><br>
																		<span>{{ \App\Helpers\MyHelper::timeElapsedString($story->created_at) }}</span>
																		<span class="middotDivider"></span>
																		<span class="readingTime"
																		      title="{{ \App\Helpers\MyHelper::estimatedReadingTime($story->main_text) }}">{{ \App\Helpers\MyHelper::estimatedReadingTime($story->main_text) }}</span>
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
											$users = [];
										@endphp
										
										@foreach($category->yeni_articles as $story)
											
											@if(!in_array($story->user_id, $users) && $counter < 5)
												@php
													$counter++;
													$users[] = $story->user_id;
												@endphp
												<li class="d-flex">
													<div class="post-count">{{ str_pad($counter, 2, "0", STR_PAD_LEFT) }}</div>
													<div class="post-content">
														<h5 class="entry-title mb-2">
															<a
																href="/yapit/{{ $story->slug }}">{{ \App\Helpers\MyHelper::replaceAscii($story->title) }}</a>
														</h5>
														<div class="entry-meta align-items-center">
															<a href="/yazar/{{ $story->name_slug }}">{{ $story->name }}</a> -
															<a
																href="/kume/{{ $story->parent_category_slug }}/{{ $story->category_slug }}">{!! $story->category_name !!}</a><br>
															<span>{{ \App\Helpers\MyHelper::timeElapsedString($story->created_at) }}</span>
															<span class="middotDivider"></span>
															<span class="readingTime"
															      title="{{ \App\Helpers\MyHelper::estimatedReadingTime($story->main_text) }}">{{ \App\Helpers\MyHelper::estimatedReadingTime($story->main_text) }}</span>
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
					
					@if ($counter_section === 1 || $counter_section === 3)
						<div class="container-lg" style="text-align: center;">
								<a href="https://herkesyazar.app">
									<img src="{{ asset('/images/herkesyazar.jpg') }}"
									     class="desktop-image"
									     alt="herkes yazar"
									     style="max-width:100%;">
									<img src="{{ asset('/images/herkesyazar_dar.jpg') }}"
									     class="mobile-image"
									     alt="herkes yazar"
									     style="max-width:100%;">
								</a>
						</div>
					@endif
				
				</div>
			@endforeach
			
			<div class="container-lg" style="text-align: center;">
				<a href="https://herkesyazar.app">
					<img src="{{ asset('/images/herkesyazar.jpg') }}"
					     class="desktop-image"
					     alt="herkes yazar"
					     style="max-width:100%;">
					<img src="{{ asset('/images/herkesyazar_dar.jpg') }}"
					     class="mobile-image"
					     alt="herkes yazar"
					     style="max-width:100%;">
				</a>
			</div>
		</main>
	</section>
@endsection

@push('styles')
	<style>
      .mobile-image {
          display: none;
      }

      @media only screen and (max-width: 768px) {
          .desktop-image {
              display: none;
          }

          .mobile-image {
              display: block;
          }
      }
	</style>
@endpush
