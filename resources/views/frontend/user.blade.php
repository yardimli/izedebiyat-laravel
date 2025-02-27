@extends('layouts.app-frontend')

@section('title', 'İzEdebiyat - ' . $user->name)
@section('body-class', 'archive')

@section('content')
	{{ \App\Helpers\MyHelper::initializeCategoryImages() }}
	
	<main id="content">
		<div class="content-widget">
			<div class="container-lg">
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
											{!! \App\Helpers\MyHelper::getImage($article->featured_image ?? '', $article->category_id, 'bgcover2', '', 'small_landscape') !!}
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
							{!! \App\Helpers\MyHelper::generateInitialsAvatar($user->avatar, $user->email, 'border-radius: 10px; width:60%; min-width:150px; min-height:150px;','mt-2 mb-2') !!}
						</div>
						
						@php
							$about_me = $user->about_me;
							
							$about_me = str_replace( '<a href="http://">http://</a>', '',$about_me);
							$about_me = str_replace( '<a href="http:/">http:/</a>', '',$about_me);
							$about_me = str_replace( '<a href="https://">https://</a>', '',$about_me);
							$about_me = str_replace( '<a href="https:/">https:/</a>', '',$about_me);
							$about_me = str_replace( "<a href='www", "<a href='//www",$about_me);
							$about_me = str_replace( '<a href="www', '<a href="//www',$about_me);
							$about_me = str_replace( '<a ', '<a target="_blank" ',$about_me);

							echo $about_me;
						@endphp
						
						@php
							$followingUsers = $user->following()->with('following')->get();
						@endphp
						
						@if($followingUsers->count() > 0)
							<div class="following-users mt-4 mb-4">
								<h5 class="spanborder" style="margin-bottom: 5px;">
									<span>{{__('default.Following')}}</span>
								</h5>
								<div>
									@foreach($followingUsers as $follow)
										@if ($follow->following)
										<div class="entry-meta align-items-center pb-1" style="margin-top: 10px;
    margin-bottom: 0px;">
											<a class="user-avatar" href="{{ url('/yazar/' . $follow->following->slug) }}">
												{!! \App\Helpers\MyHelper::generateInitialsAvatar($follow->following->avatar, $follow->following->email,'width:20px; height:20px; border-radius:50%;','') !!}
											</a>
											<a href="{{ url('/yazar/' . $follow->following->slug) }}">{{ $follow->following->name }}</a>
										</div>
										@endif
									@endforeach
								</div>
							</div>
						@endif
						
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
			<div class="container-lg">
{{--				<div class="sidebar-widget ads">--}}
{{--					<a href="https://herkesyazar.app"><img src="{{ asset('/images/herkesyazar.jpg') }}" alt="herkes yazar"--}}
{{--					                                       style="max-width:80%;"></a>--}}
{{--				</div>--}}
{{--				<div class="hr"></div>--}}
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
