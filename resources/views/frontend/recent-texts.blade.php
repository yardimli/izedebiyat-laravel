@extends('layouts.app-frontend')

@section('title', 'Son Eklenen Yazılar - İzEdebiyat')
@section('body-class', 'home')

@section('content')
	{{ \App\Helpers\MyHelper::initializeCategoryImages() }}
	
	<section class="home recent-texts">
		<main id="content">
			<div class="container-lg">
				
				<div class="row">
					<div class="col-md-9">
						<h2 class="spanborder h4 mb-4">
				    <span>
				        @if(isset($category))
						    {!! $category->category_name  !!} - Son 30 Günde Eklenen Yazılar
					    @else
						    Son 30 Günde Eklenen Yazılar
					    @endif
				    </span>
						</h2>
						@foreach($articles as $article)
							<article class="mb-4">
								<div class="row">
									<div class="col-md-3">
										<figure>
											<a href="/yapit/{{ $article->slug }}">
												{!! \App\Helpers\MyHelper::getImage($article->featured_image ?? '', $article->category_id, '', 'width: 100%') !!}
											</a>
										</figure>
									</div>
									<div class="col-md-9">
										<h3 class="entry-title mb-2">
											<a href="/yapit/{{ $article->slug }}">{{ \App\Helpers\MyHelper::replaceAscii($article->title) }}</a>
										</h3>
										<div class="entry-excerpt mb-2">
											<p>
												@if($article->parent_category_slug === "siir")
													{!! \App\Helpers\MyHelper::getWords(\App\Helpers\MyHelper::replaceAscii($article->main_text), 25, false) !!}
												@else
													{!! \App\Helpers\MyHelper::getWords(\App\Helpers\MyHelper::replaceAscii($article->subheading), 25) !!}
												@endif
											</p>
										</div>
										<div class="entry-meta">
											<a href="/yazar/{{ $article->name_slug }}">{{ $article->name }}</a> -
											<a
												href="/kume/{{ $article->parent_category_slug }}/{{ $article->category_slug }}">{!! $article->category_name !!} </a>
											<br>
											<span>{{ \App\Helpers\MyHelper::timeElapsedString($article->created_at) }}</span>
											<span class="middotDivider"></span>
											<span class="readingTime" title="{{ \App\Helpers\MyHelper::estimatedReadingTime($article->main_text) }}">
                                        {{ \App\Helpers\MyHelper::estimatedReadingTime($article->main_text) }}
                                    </span>
										</div>
									</div>
								</div>
							</article>
						@endforeach
					
					
					</div>
					
					<div class="col-md-3">
						<div class="sidebar-widget">
							<h4 class="spanborder">
								<span>Kümeler</span>
							</h4>
							<ul class="list-unstyled">
								@foreach($categories as $category)
									<li>
										<a href="{{ url('/son-eklenenler/' . $category->slug) }}">
											<span>{!! $category->category_name !!}</span>
											@if($category->new_count > 0)
												<span>({{ $category->new_count }})</span>
											@endif
										</a>
									</li>
								@endforeach
							</ul>
						</div>
					</div>
				</div>
			</div>
		</main>
	</section>
@endsection
