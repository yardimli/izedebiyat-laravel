@extends('layouts.app-frontend')

@section('title', 'İzEdebiyat - ' . $category->category_name)

@section('body-class', 'home')

@section('content')
	{{ \App\Helpers\MyHelper::initializeCategoryImages() }}
	
	<section class="archive">
		<main id="content">
			<div class="content-widget">
				<div class="container-lg">
					<div class="row">
						<div class="col-md-8 col-12">
							<h4 class="spanborder">
								<span style="text-transform: uppercase;">{{ $category->category_name }}</span>
							</h4>
							
							@php
								$counter = 0;
								$users = [];
								$user_counts = [];
								$previous_user = null;
								$open_div = false;
							@endphp
							
							@foreach($articles as $article)
								@php
									$current_user = $article->user_id;
									if (!isset($user_counts[$current_user])) {
											$user_counts[$current_user] = 0;
									}
								@endphp
								
								@if($user_counts[$current_user] < 3 && $current_user !== $previous_user)
									@php
										$user_counts[$current_user]++;
										$previous_user = $current_user;
										$counter++;
									@endphp
									
									@if($counter === 1)
										<article class="first mb-3">
											<figure>
												<a href="{{ url('/yapit/' . $article->slug) }}">
													{!! \App\Helpers\MyHelper::getImage($article->featured_image ?? '', $article->category_id, '', 'width: 100%; max-height:150px; object-fit: cover', 'large_landscape') !!}
												</a>
											</figure>
											<h3 class="entry-title mb-3">
												<a
													href="{{ url('/yapit/' . $article->slug) }}">{{ \App\Helpers\MyHelper::replaceAscii($article->title) }}</a>
											</h3>
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
												<a href="{{ url('/yazar/' . $article->name_slug) }}">{{ $article->name }}</a> -
												<a
													href="{{ url('/kume/' . $article->parent_category_slug . '/' . $article->category_slug) }}">{{ $article->category_name }}</a><br>
												<span>{{ \App\Helpers\MyHelper::timeElapsedString($article->created_at) }}</span>
												<span class="middotDivider"></span>
												<span class="readingTime"
												      title="{{ \App\Helpers\MyHelper::estimatedReadingTime($article->main_text) }}">
                                                {{ \App\Helpers\MyHelper::estimatedReadingTime($article->main_text) }}
                                            </span>
											</div>
										</article>
										
										<div class="container-lg mb-2" style="text-align: center;">
											<a href="https://herkesyazar.app">
												<img src="{{ asset('/images/herkesyazar.jpg') }}"
												     class="desktop-image"
												     alt="herkes yazar"
												     style="max-width:100%;">
												<img src="{{ asset('/images/herkesyazar-dar.jpg') }}"
												     class="mobile-image"
												     alt="herkes yazar"
												     style="max-width:100%;">
											</a>
										</div>
									
										<div class="divider"></div>
									@elseif(($counter >= 4 && $counter <= 7) || ($counter >= 14 && $counter <= 17))
										@if($counter === 4 || $counter === 14)
											@php
												$open_div = true;
											@endphp
											<div class="row justify-content-between mb-3">
												<div class="divider-2"></div>
												@endif
												
												<article class="col-md-6">
													<div class="mb-3 d-flex row">
														<figure class="col-md-5">
															<a href="{{ url('/yapit/' . $article->slug) }}">
																{!! \App\Helpers\MyHelper::getImage($article->featured_image ?? '', $article->category_id, '', 'width: 100%;', 'medium_landscape') !!}
															</a>
														</figure>
														<div class="entry-content col-md-7 pl-md-0">
															<h5 class="entry-title mb-3">
																<a
																	href="{{ url('/yapit/' . $article->slug) }}">{{ \App\Helpers\MyHelper::replaceAscii($article->title) }}</a>
															</h5>
															<div class="entry-meta align-items-center">
																<a href="{{ url('/yazar/' . $article->name_slug) }}">{{ $article->name }}</a> -
																<a
																	href="{{ url('/kume/' . $article->parent_category_slug . '/' . $article->category_slug) }}">{{ $article->category_name }}</a><br>
																<span>{{ \App\Helpers\MyHelper::timeElapsedString($article->created_at) }}</span>
																<span class="middotDivider"></span>
																<span class="readingTime"
																      title="{{ \App\Helpers\MyHelper::estimatedReadingTime($article->main_text) }}">
                                                        {{ \App\Helpers\MyHelper::estimatedReadingTime($article->main_text) }}
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
										<article class="row justify-content-between mb-3 mr-0">
											<div class="col-md-9">
												<div class="align-self-center" style="min-height: 200px;">
													<div class="capsSubtle mb-2">
														<a
															href="{{ url('/kume/' . $article->parent_category_slug . '/' . $article->category_slug) }}">{{ $article->category_name }}</a>
													</div>
													<h3 class="entry-title mb-3">
														<a
															href="{{ url('/yapit/' . $article->slug) }}">{{ \App\Helpers\MyHelper::replaceAscii($article->title) }}</a>
													</h3>
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
														<span class="readingTime"
														      title="{{ \App\Helpers\MyHelper::estimatedReadingTime($article->main_text) }}">
                                                        {{ \App\Helpers\MyHelper::estimatedReadingTime($article->main_text) }}
                                                    </span>
													</div>
												</div>
											</div>
											<div class="col-md-3">
												<a href="{{ url('/yapit/' . $article->slug) }}">
													{!! \App\Helpers\MyHelper::getImage($article->featured_image ?? '', $article->category_id, 'bgcover2', '', 'medium_landscape') !!}
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
					$currentPage = $articles->currentPage();
					$lastPage = $articles->lastPage();
					$totalItems = $articles->total();
					$perPage = $articles->perPage();
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
				<div class="container-lg">
					<div class="sidebar-widget ads">
						<a href="https://herkesyazar.app"><img src="{{ asset('/images/herkesyazar.jpg') }}" alt="herkes yazar"
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
			// Add any necessary JavaScript
		});
	</script>
@endpush

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
