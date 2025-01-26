@extends('layouts.app-frontend')

@section('title', 'Son Eklenen Yazılar - İzEdebiyat')
@section('body-class', 'home')

@section('content')
	{{ \App\Helpers\MyHelper::initializeCategoryImages() }}
	
	<section class="home recent-texts">
		<main id="content">
			<div class="container">
				
				<div class="row">
					<div class="col-md-9">
						<h2 class="spanborder h4 mb-4">
				    <span>
				        @if(isset($category))
						    {!! $category->kategori_ad  !!} - Son 30 Günde Eklenen Yazılar
					    @else
						    Son 30 Günde Eklenen Yazılar
					    @endif
				    </span>
						</h2>
						@foreach($texts as $text)
							<article class="mb-4">
								<div class="row">
									<div class="col-md-3">
										<figure>
											<a href="/yapit/{{ $text->slug }}">
												{!! \App\Helpers\MyHelper::getImage($text->yazi_ana_resim ?? '', $text->kategori_id, '', 'width: 100%') !!}
											</a>
										</figure>
									</div>
									<div class="col-md-9">
										<h3 class="entry-title mb-2">
											<a href="/yapit/{{ $text->slug }}">{{ \App\Helpers\MyHelper::replaceAscii($text->baslik) }}</a>
										</h3>
										<div class="entry-excerpt mb-2">
											<p>
												@if($text->ust_kategori_slug === "siir")
													{!! \App\Helpers\MyHelper::getWords(\App\Helpers\MyHelper::replaceAscii($text->yazi), 25, false) !!}
												@else
													{!! \App\Helpers\MyHelper::getWords(\App\Helpers\MyHelper::replaceAscii($text->tanitim), 25) !!}
												@endif
											</p>
										</div>
										<div class="entry-meta">
											<a href="/yazar/{{ $text->name_slug }}">{{ $text->name }}</a> -
											<a
												href="/kume/{{ $text->ust_kategori_slug }}/{{ $text->kategori_slug }}">{!! $text->kategori_ad !!} </a>
											<br>
											<span>{{ \App\Helpers\MyHelper::timeElapsedString($text->katilma_tarihi) }}</span>
											<span class="middotDivider"></span>
											<span class="readingTime" title="{{ \App\Helpers\MyHelper::estimatedReadingTime($text->yazi) }}">
                                        {{ \App\Helpers\MyHelper::estimatedReadingTime($text->yazi) }}
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
											<span>{!! $category->kategori_ad !!}</span>
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
