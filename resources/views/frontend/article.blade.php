@extends('layouts.app-frontend')

@section('title', 'İzEdebiyat - ' . $user->name . ' - ' . $article->title . ' - ' . $article->category_name)

@section('body-class', 'home single')

@section('content')
	{{ \App\Helpers\MyHelper::initializeCategoryImages() }}
	
	<main id="content">
		<div class="container">
			<div class="entry-header">
				<div class="mb-5">
					<h1 class="entry-title mb-2">
						{{ \App\Helpers\MyHelper::replaceAscii($article->title) }}
					</h1>
					<div class="excerpt mb-4">
						<p>{{ $article->subheading }}</p>
					</div>
					
					<div class="entry-meta align-items-center divider pb-4" style="margin-top: 10px;
    margin-bottom: 0px;">
						<a class="user-avatar" href="{{ url('/yazar/' . $user->slug) }}">
							{!! \App\Helpers\MyHelper::generateInitialsAvatar($user->avatar, $user->name,'width:40px; height:40px;') !!}
						</a>
						<a href="{{ url('/yazar/' . $user->slug) }}">{{ $user->name }}</a><br>
						<span class="readingTime">{{ \App\Helpers\MyHelper::estimatedReadingTime($article->main_text) }}</span>
						<span class="middotDivider"></span>
						<span>{{ \App\Helpers\MyHelper::timeElapsedString($article->created_at) }}</span>
						<span class="middotDivider"></span>
						<a
							href="{{ url('/kume/' . $article->parent_category_slug . '/' . $article->category_slug) }}">{{ $article->category_name }}</a>
					</div>
					<div class="entry-meta align-items-center divider pb-2" style="margin-top: 10px;
    margin-bottom: 10px;">
						<button id="clap" class="clap">
  <span>
    <!--  SVG Created by Luis Durazo from the Noun Project  -->
    <svg id="clap--icon" xmlns="http://www.w3.org/2000/svg" viewBox="-549 338 100.1 125">
  <path
	  d="M-471.2 366.8c1.2 1.1 1.9 2.6 2.3 4.1.4-.3.8-.5 1.2-.7 1-1.9.7-4.3-1-5.9-2-1.9-5.2-1.9-7.2.1l-.2.2c1.8.1 3.6.9 4.9 2.2zm-28.8 14c.4.9.7 1.9.8 3.1l16.5-16.9c.6-.6 1.4-1.1 2.1-1.5 1-1.9.7-4.4-.9-6-2-1.9-5.2-1.9-7.2.1l-15.5 15.9c2.3 2.2 3.1 3 4.2 5.3zm-38.9 39.7c-.1-8.9 3.2-17.2 9.4-23.6l18.6-19c.7-2 .5-4.1-.1-5.3-.8-1.8-1.3-2.3-3.6-4.5l-20.9 21.4c-10.6 10.8-11.2 27.6-2.3 39.3-.6-2.6-1-5.4-1.1-8.3z"/>
  <path
	  d="M-527.2 399.1l20.9-21.4c2.2 2.2 2.7 2.6 3.5 4.5.8 1.8 1 5.4-1.6 8l-11.8 12.2c-.5.5-.4 1.2 0 1.7.5.5 1.2.5 1.7 0l34-35c1.9-2 5.2-2.1 7.2-.1 2 1.9 2 5.2.1 7.2l-24.7 25.3c-.5.5-.4 1.2 0 1.7.5.5 1.2.5 1.7 0l28.5-29.3c2-2 5.2-2 7.1-.1 2 1.9 2 5.1.1 7.1l-28.5 29.3c-.5.5-.4 1.2 0 1.7.5.5 1.2.4 1.7 0l24.7-25.3c1.9-2 5.1-2.1 7.1-.1 2 1.9 2 5.2.1 7.2l-24.7 25.3c-.5.5-.4 1.2 0 1.7.5.5 1.2.5 1.7 0l14.6-15c2-2 5.2-2 7.2-.1 2 2 2.1 5.2.1 7.2l-27.6 28.4c-11.6 11.9-30.6 12.2-42.5.6-12-11.7-12.2-30.8-.6-42.7m18.1-48.4l-.7 4.9-2.2-4.4m7.6.9l-3.7 3.4 1.2-4.8m5.5 4.7l-4.8 1.6 3.1-3.9"/>
</svg>
  </span>
							<span id="clap--count" class="clap--count"></span>
							<span id="clap--count-total" class="clap--count-total"></span>
						</button>
					</div>
				</div>
			</div>
			
			<div class="bar-long"></div>
			
			<article class="entry-wrapper mb-5">
				<figure class="image zoom mb-5">
					{!! \App\Helpers\MyHelper::getImage($article->featured_image ?? '', $article->category_id, '', 'width: 100%') !!}
				</figure>
				
				<div class="entry-main-content">
					<p>{!! nl2br(
                    preg_replace('/\[\[.*?\]\]/', ' ',
                        str_ireplace(
                            ['[[I]]', '[[K]]', '[[/I]]', '[[/K]]'],
                            ['<i>', '<b>', '</i>', '</b>'],
                            str_ireplace("\n", "</p><p>", $article->main_text)
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
				
				@include('partials.author-box', ['user' => $user])
				@include('partials.subscription-box')
			</article>
			
			@include('partials.related-posts', ['sameUserAndCategory' => $sameUserAndCategory,'sameUserAndMainCategory' => $sameUserAndMainCategory, 'otherUserArticles' => $otherUserArticles])
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


      /* clap */
      .clap {
          position: relative;
          outline: 1px solid transparent;
          border-radius: 50%;
          border: 1px solid #bdc3c7;
          width: 34px;
          height: 34px;
          background: none;
      }

      [data-bs-theme=dark] .clap {
          border: 1px solid #4a4a4a !important;
      }

      .clap:after {
          content: "";
          position: absolute;
          top: 0;
          left: 0;
          display: block;
          border-radius: 50%;
          width: 33px;
          height: 33px;
      }

      .clap:hover {
          cursor: pointer;
          border: 1px solid #27ae60;
          transition: border-color 0.3s ease-in;
      }

      [data-bs-theme=dark] .clap:hover {
          border: 1px solid #ccc !important;
          background: #2c3e50 !important;
      }

      .clap:hover:after {
          animation: shockwave 1s ease-in infinite;
      }

      [data-bs-theme=dark] .clap:hover:after {
          animation: shockwave-dark 1s ease-in infinite;
      }

      .clap svg {
          width: 20px;
          fill: none;
          stroke: #333;
          stroke-width: 2px;
      }

      [data-bs-theme=dark] .clap svg {
          stroke: #ccc !important;
      }

      .clap svg.checked {
          fill: #27ae60;
          stroke: #fff;
          stroke-width: 1px;
      }

      [data-bs-theme=dark] .clap svg.checked {
          fill: #333 !important;
          stroke: #fff;
      }

      .clap .clap--count {
          position: absolute;
          top: -25px;
          left: 0px;
          font-size: 0.8rem;
          color: white;
          background: #27ae60;
          border-radius: 50%;
          height: 30px;
          width: 30px;
          line-height: 30px;
      }

      [data-bs-theme=dark] .clap .clap--count {
          background: #9b59b6 !important;
      }

      .clap .clap--count-total {
          position: absolute;
          font-size: 0.8rem;
          width: 40px;
          text-align: center;
          left: 50px;
          top: 15px;
          color: #bdc3c7;
      }

      [data-bs-theme=dark] .clap .clap--count-total {
          color: #7f8c8d;
      }

      @keyframes shockwave {
          0% {
              transform: scale(1);
              box-shadow: 0 0 2px #27ae60;
              opacity: 1;
          }
          100% {
              transform: scale(1);
              opacity: 0;
              box-shadow: 0 0 40px #145b32, inset 0 0 10px #27ae60;
          }
      }

      @keyframes shockwave-dark {
          0% {
              transform: scale(1);
              box-shadow: 0 0 2px #9b59b6;
              opacity: 1;
          }
          100% {
              transform: scale(1);
              opacity: 0;
              box-shadow: 0 0 50px #8e44ad, inset 0 0 10px #9b59b6;
          }
      }
	
	
	</style>
@endpush


@push('scripts')
	<script src="/js/mo.min.js"></script>
	<script>
		$(window).scroll(function () {
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
		
		
		$(document).ready(function () {
			//clap
			
			const clap = document.getElementById('clap')
			const clapIcon = document.getElementById('clap--icon')
			const clapCount = document.getElementById('clap--count')
			const clapTotalCount = document.getElementById('clap--count-total')
			const initialNumberOfClaps = generateRandomNumber(500, 10000);
			const btnDimension = 40
			const tlDuration = 300
			let numberOfClaps = 0
			let clapHold;
			
			const triangleBurst = new mojs.Burst({
				parent: clap,
				radius: {25: 48},
				count: 5,
				angle: 30,
				children: {
					shape: 'polygon',
					radius: {6: 0},
					scale: 1,
					stroke: 'rgba(211,84,0 ,0.5)',
					strokeWidth: 2,
					angle: 210,
					delay: 30,
					speed: 0.2,
					easing: mojs.easing.bezier(0.1, 1, 0.3, 1),
					duration: tlDuration
				}
			})
			const circleBurst = new mojs.Burst({
				parent: clap,
				radius: {25: 37},
				angle: 25,
				duration: tlDuration,
				children: {
					shape: 'circle',
					fill: 'rgba(149,165,166 ,0.5)',
					delay: 30,
					speed: 0.2,
					radius: {3: 0},
					easing: mojs.easing.bezier(0.1, 1, 0.3, 1),
				}
			})
			const countAnimation = new mojs.Html({
				el: '#clap--count',
				isShowStart: false,
				isShowEnd: true,
				y: {0: -30},
				opacity: {0: 1},
				duration: tlDuration
			}).then({
				opacity: {1: 0},
				y: -40,
				delay: tlDuration / 2
			})
			const countTotalAnimation = new mojs.Html({
				el: '#clap--count-total',
				isShowStart: false,
				isShowEnd: true,
				opacity: {0: 1},
				delay: 3 * (tlDuration) / 2,
				duration: tlDuration,
				y: {0: -3}
			})
			const scaleButton = new mojs.Html({
				el: '#clap',
				duration: tlDuration,
				scale: {1.3: 1},
				easing: mojs.easing.out
			})
			clap.style.transform = "scale(1, 1)" /*Bug1 fix*/
			
			const animationTimeline = new mojs.Timeline()
			animationTimeline.add([
				triangleBurst,
				circleBurst,
				countAnimation,
				countTotalAnimation,
				scaleButton
			])
			
			
			clap.addEventListener('click', function () {
				repeatClapping();
			})
			
			clap.addEventListener('mousedown', function () {
				clapHold = setInterval(function () {
					repeatClapping();
				}, 400)
			})
			
			clap.addEventListener('mouseup', function () {
				clearInterval(clapHold);
			})
			
			
			function repeatClapping() {
				updateNumberOfClaps()
				animationTimeline.replay()
				clapIcon.classList.add('checked')
			}
			
			function updateNumberOfClaps() {
				numberOfClaps < 50 ? numberOfClaps++ : null
				clapCount.innerHTML = "+" + numberOfClaps
				clapTotalCount.innerHTML = initialNumberOfClaps + numberOfClaps
			}
			
			function generateRandomNumber(min, max) {
				return Math.floor(Math.random() * (max - min + 1) + min);
			}
		});
	
	
	</script>
@endpush
