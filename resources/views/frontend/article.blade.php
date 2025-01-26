@extends('layouts.app-frontend')

@section('title', 'İzEdebiyat - ' . $author->name . ' - ' . $article->baslik . ' - ' . $article->kategori_ad)

@section('body-class', 'home single')

@section('content')
	{{ \App\Helpers\MyHelper::initializeCategoryImages() }}
	
	<main id="content">
		<div class="container">
			<div class="entry-header">
				<div class="mb-5">
					<h1 class="entry-title mb-2">
						{{ \App\Helpers\MyHelper::replaceAscii($article->baslik) }}
					</h1>
					<div class="entry-meta align-items-center">
						<a class="author-avatar" href="{{ url('/yazar/' . $author->slug) }}">
							{!! \App\Helpers\MyHelper::generateInitialsAvatar($author->picture, $author->name) !!}
						</a>
						<a href="{{ url('/yazar/' . $author->slug) }}">{{ $author->name }}</a><br>
						<a href="{{ url('/kume/' . $article->ust_kategori_slug . '/' . $article->kategori_slug) }}">
							{{ $article->ust_kategori_ad }}<span class="middotDivider"></span>{{ $article->kategori_ad }}
						</a><br>
						<span>{{ $article->katilma_tarihi }}</span>
						<span class="middotDivider"></span>
						<span class="readingTime">{{ \App\Helpers\MyHelper::estimatedReadingTime($article->yazi) }}</span>
					</div>
				</div>
			</div>
			
			<div class="bar-long"></div>
			
			<article class="entry-wrapper mb-5">
				<div class="entry-left-col">
					<div class="social-sticky">
						<a href="#"><i class="icon-heart"></i></a>
					</div>
				</div>
				
				<figure class="image zoom mb-5">
					{!! \App\Helpers\MyHelper::getImage($article->yazi_ana_resim ?? '', $article->kategori_id, '', 'width: 100%') !!}
				</figure>
				
				<div class="excerpt mb-4">
					<p>{{ $article->tanitim }}</p>
				</div>
				
				<div class="entry-main-content">
					<p>{!! nl2br(
                    preg_replace('/\[\[.*?\]\]/', ' ',
                        str_ireplace(
                            ['[[I]]', '[[K]]', '[[/I]]', '[[/K]]'],
                            ['<i>', '<b>', '</i>', '</b>'],
                            str_ireplace("\n", "</p><p>", $article->yazi)
                        )
                    )
                ) !!}</p>
				</div>
				
				<div class="entry-bottom">
					<div class="tags-wrap heading">
						@foreach($keywords as $keyword)
							<a href="{{ url('/etiket/' . $keyword->keyword_slug) }}">{{ $keyword->keyword }}</a>
						@endforeach
					</div>
				</div>
				
				@include('partials.author-box', ['author' => $author])
				@include('partials.subscription-box')
			</article>
			
			@include('partials.related-posts', ['sameAuthorAndCategory' => $sameAuthorAndCategory,'sameAuthorAndMainCategory' => $sameAuthorAndMainCategory, 'otherAuthorArticles' => $otherAuthorArticles])
		</div>
	</main>
@endsection

@push('styles')
	<style>
      .bar-long {
          height: 3px;
          background-color: #CCC;
          width: 0px;
          z-index: 1000;
          position: fixed;
          top: 100px;
          left: 0;
      }
</style>
@endpush

@push('scripts')
	<script>
		$(window).scroll(function() {
			const element = document.querySelector('#main-menu');
			const computedStyle = window.getComputedStyle(element);
			var main_menu_height = $("#main-menu").outerHeight();
			
			if (computedStyle.display === 'block') {
				$('.bar-long').css("top", (main_menu_height) + "px");
			} else {
				$('.bar-long').css("top", ($(".sticky-header").outerHeight()) + "px");
			}
			
			var scrollwin = $(window).scrollTop();
			var articleheight = $('.entry-main-content').outerHeight(true);
			var windowWidth = $(window).width();
			
			if (scrollwin >= ($('.entry-main-content').offset().top - 100)) {
				if (scrollwin <= (($('.entry-main-content').offset().top - 100) + (articleheight + 100))) {
					$('.bar-long').css('width', ((scrollwin - ($('.entry-main-content').offset().top - 100)) / (articleheight + 100)) * 100 + "%");
				} else {
					$('.bar-long').css('width', "100%");
				}
			} else {
				$('.bar-long').css('width', "0px");
			}
		});
	</script>
@endpush
