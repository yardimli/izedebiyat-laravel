@extends('layouts.app-frontend')

@section('title', 'İzEdebiyat - ' . $category->category_name . ' - ' . $subCategory->category_name)

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
                            <span style="text-transform: uppercase;">
                                <a href="{{ url('/kume/' . $category->slug) }}">{{ $category->category_name }}</a>
                                @if($subCategory)
		                            > {{ $subCategory->category_name }}
	                            @endif
                            </span>
							</h4>
							
							@php
								$counter = 0;
								$users = [];
								$user_counts = [];
								$previous_user = null;
							@endphp
							
							@foreach($articles as $article)
								@php
									$current_user = $article->user_id;
									if (!isset($user_counts[$current_user])) {
											$user_counts[$current_user] = 0;
									}
								@endphp
								
								@if ($counter === 20)
									@break
								@endif
								
								@if($user_counts[$current_user] < 3 && $current_user !== $previous_user)
									@php
										$user_counts[$current_user]++;
										$previous_user = $current_user;
										$counter++;
									@endphp
									
									<article class="row justify-content-between mb-5 mr-0">
										<div class="col-md-9">
											<div class="align-self-center" style="min-height: 200px;">
												<div class="capsSubtle mb-2">{{ $article->sentiment }}</div>
												<h3 class="entry-title mb-2">
													<a href="{{ url('/yapit/' . $article->slug) }}">
														{{ \App\Helpers\MyHelper::replaceAscii($article->title) }}
													</a>
												</h3>
												<div class="entry-excerpt">
													<p class="mb-1">
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
										</div>
										<div class="col-md-3">
											<a href="{{ url('/yapit/' . $article->slug) }}">
												{!! \App\Helpers\MyHelper::getImage($article->featured_image ?? '', $article->category_id, 'bgcover2', '', 'medium_landscape') !!}
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
				<div class="container-lg">
					<div class="sidebar-widget ads">
						<a href="https://herkesyazar.app"><img src="{{ asset('/images/herkes-yazar.png') }}" alt="herkes yazar"
						                                       style="max-width:80%;"></a>
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
