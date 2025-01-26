@extends('layouts.app-frontend')

@section('title', 'İzEdebiyat - ' . $author->name)
@section('body-class', 'archive')

@section('content')
	{{ \App\Helpers\MyHelper::initializeCategoryImages() }}
	
	<main id="content">
		<div class="content-widget">
			<div class="container">
				<div class="row">
					<div class="col-md-8 col-12">
						
						<h4 class="spanborder pt-4">
							<span>{{ $author->name }}</span>
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
												<a href="{{ url('/yazar/' . $text->name_slug) }}">{{ $text->name }}</a><br>
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
						<h4 class="spanborder pt-4">
							<span>{{$author->page_title ?? 'Tanıtım' }}</span>
						</h4>
						
						<div class="text-center" style="position:relative;">
							<div>
								<a href="{!! $author->personal_url_link !!}" target="_blank">{!! $author->personal_url !!}</a>
							</div>
							{!! \App\Helpers\MyHelper::generateInitialsAvatar($author->picture, $author->name, 'border-radius: 10px; width:60%; min-width:150px; min-height:150px;','mt-2 mb-2', 'yz-yazar-resim-2') !!}
						</div>
						
						@php
							$about_me = $author->about_me;
							
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
