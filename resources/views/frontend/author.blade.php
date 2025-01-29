@extends('layouts.app-frontend')

@section('title', 'İzEdebiyat - ' . $user->name)
@section('body-class', 'archive')

@section('content')
	{{ \App\Helpers\MyHelper::initializeCategoryImages() }}
	
	<main id="content">
		<div class="content-widget">
			<div class="container">
				<div class="row">
					<div class="col-md-8 col-12">
						
						<h4 class="spanborder pt-4">
							<span>{{ $user->name }}</span>
						</h4>
						
						
						@php
							$counter = 0;
						@endphp
						
						@foreach($articles as $article)
							@php
								$counter++;
							@endphp
							
							@if ($counter <=20)
								<article class="row justify-content-between mb-5 mr-0">
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
											{!! \App\Helpers\MyHelper::getImage($article->featured_image ?? '', $article->category_id, 'bgcover2', '') !!}
										</a>
									</div>
								</article>
							@endif
						@endforeach
					</div>
					
					<div class="col-md-4 pl-md-5 sticky-sidebar">
						<h4 class="spanborder pt-4">
							<span>{{$user->page_title ?? 'Tanıtım' }}</span>
						</h4>
						
						<div class="text-center" style="position:relative;">
							<div>
								<a href="{!! $user->personal_url_link !!}" target="_blank">{!! $user->personal_url !!}</a>
							</div>
							{!! \App\Helpers\MyHelper::generateInitialsAvatar($user->avatar, $user->name, 'border-radius: 10px; width:60%; min-width:150px; min-height:150px;','mt-2 mb-2', 'art-author-picture-2') !!}
						</div>
						
						@php
							$about_me = $user->about_me;
							
							// First clean up specific strings
							$about_me = str_replace([
									"<a href='http://'>http://</a><br>",
									"<a href='http:/'>http:/</a><br>",
									"<a href='http://'>http://</a>",
									"<a href='http:/'>http:/</a>",
							], '', $about_me);
							
							// Remove sections with empty paragraphs
							$about_me = preg_replace('/<h5>[^<]*<\/h5>\s*<p>\s*<\/p>/', '', $about_me);
							
							// Remove sections with paragraphs that only contain whitespace
							$about_me = preg_replace('/<h5>[^<]*<\/h5>\s*<p>\s*<\/p>/', '', $about_me);
							
							$about_me = str_replace('',"'", $about_me);
							
							$about_me = preg_replace('/(?<!<br>)\n/', '<br>', $about_me);

							
							$about_me = str_replace( "<a href='www", "<a href='//www",$about_me);
							$about_me = str_replace( '<a href="www', '<a href="//www',$about_me);
							$about_me = str_replace( '<a ', '<a target="_blank" ',$about_me);

							echo $about_me;
						@endphp
						@include('partials.author-sidebar')
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
										   href="{{ url('/yazar/' . $user->slug . '/sayfa/' . ($currentPage - 1)) }}">
											<i class="icon-left-open-big"></i>
										</a>
									</li>
								@endif
								
								@for($i = max(1, $currentPage - 2); $i <= min($lastPage, $currentPage + 2); $i++)
									<li>
										<a class="page-numbers {{ $i == $currentPage ? 'current' : '' }}"
										   href="{{ url('/yazar/' . $user->slug . '/sayfa/' . $i) }}">
											{{ $i }}
										</a>
									</li>
								@endfor
								
								@if($currentPage < $lastPage)
									<li>
										<a class="next page-numbers"
										   href="{{ url('/yazar/' . $user->slug . '/sayfa/' . ($currentPage + 1)) }}">
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
					<a href="#"><img src="{{ asset('/frontend/assets/images/ads/ads-2.png') }}" alt="ads"
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
		});
	</script>
@endpush

@push('styles')
	<style>
	</style>
@endpush
