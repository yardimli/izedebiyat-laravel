@extends('layouts.app-frontend')

@section('title', 'İzEdebiyat - ' . $user->name . ' - ' . $article->title . ' - ' . $article->category_name)

@section('body-class', 'home single')

@section('content')
	{{ \App\Helpers\MyHelper::initializeCategoryImages() }}
	
	<main id="content">
		<div class="container-lg">
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
							{!! \App\Helpers\MyHelper::generateInitialsAvatar($user->avatar, $user->email,'width:50px; height:50px; border-radius:50%;','') !!}
						</a>
						<a href="{{ url('/yazar/' . $user->slug) }}">{{ $user->name }}</a>
						<span class="middotDivider"></span>
						<div class="d-inline-block follow-text" data-user-id="{{ $user->id }}"
						     onclick="toggleFollow({{ $user->id }})">
							{{ Auth::check() && $user->followers->contains('follower_id', Auth::id()) ? __('default.Following') : __('default.Follow') }}
						</div>
						<br>
						<a
							href="{{ url('/kume/' . $article->parent_category_slug . '/' . $article->category_slug) }}">{{ $article->category_name }}</a>
						<span class="middotDivider"></span>
						<span class="readingTime">{{ \App\Helpers\MyHelper::estimatedReadingTime($article->main_text) }}</span>
						<span class="middotDivider"></span>
						<span>{{ \App\Helpers\MyHelper::timeElapsedString($article->created_at) }}</span>
					</div>
					
					<div class="entry-meta align-items-center divider pb-2" style="margin-top: 10px;
    margin-bottom: 10px;">
						<button id="clap" class="clap" data-article-id="{{ $article->id }}">
							<i class="bi bi-star fs-5"></i>
							<span id="clap--count" class="clap--count"></span>
							<span id="clap--count-total" class="clap--count-total">{{ $article->claps()->sum('count') }}</span>
						</button>
						
						<div class="comment-btn d-inline-block" onclick="toggleCommentPanel()" style="margin-left: 40px;">
							<i class="bi bi-chat-text fs-5"></i>
							<span style="margin-left: 4px;" id="comment-count">{{ $article->comments()->count() }}</span>
						</div>
						
						<div class="share-btn d-inline-block" onclick="showShareOptions()" style="float:right;">
							<i class="bi bi-share fs-5"></i>
						</div>
						<div class="favorite-btn d-inline-block" data-article-id="{{ $article->id }}"
						     onclick="toggleFavorite({{ $article->id }})" style="float:right; margin-right: 10px;">
							{!! Auth::check() && $article->favorites->contains('follower_id', Auth::id()) ? '<i class="bi bi-bookmark-fill fs-5"></i>' : '<i class="bi bi-bookmark fs-5"></i>'  !!}
						</div>
						
						<div id="share-options" class="share-options" style="display: none;">
							<a href="https://twitter.com/share?url={{ urlencode(url()->current()) }}"
							   target="_blank">{{__('default.Share on X')}}</a>
							<a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
							   target="_blank">{{__('default.Share on Facebook')}}</a>
							<a href="#" onclick="copyShareLink()">{{__('default.Copy Link')}}</a>
						</div>
					
					</div>
				
				</div>
			</div>
			
			<div class="bar-long"></div>
			
			<article class="entry-wrapper mb-5">
				<figure class="image zoom mb-5">
					{!! \App\Helpers\MyHelper::getImage($article->featured_image ?? '', $article->category_id, '', 'width: 100%', 'large_landscape') !!}
				</figure>
				
				<div class="entry-main-content article-text-color">
					{!! $article->main_text !!}
				</div>
				
				<div class="entry-bottom">
					<div class="tags-wrap heading">
						@foreach($keywords as $keyword)
							<a href="{{ url('/etiket/' . $keyword->keyword_slug) }}">{{ $keyword->keyword }}</a>
						@endforeach
					</div>
				</div>
				
				<div class="container-lg" style="text-align: center;">
					<a href="https://herkesyazar.app">
						<img src="{{ asset('/images/herkes-yazar.png') }}"
						     class="desktop-image"
						     alt="herkes yazar"
						     style="max-width:100%;">
						<img src="{{ asset('/images/herkes-yazar-mobile.png') }}"
						     class="mobile-image"
						     alt="herkes yazar"
						     style="max-width:100%;">
					</a>
				</div>
				
{{--				@include('partials.author-box', ['user' => $user])--}}
				{{--				@include('partials.subscription-box')--}}
			</article>
			
			@include('partials.related-posts', ['sameUserAndCategory' => $sameUserAndCategory,'sameUserAndMainCategory' => $sameUserAndMainCategory, 'otherUserArticles' => $otherUserArticles])
		</div>
	</main>
	
	<!-- Login Required Modal -->
	<div class="modal fade" id="loginRequiredModal" tabindex="-1" aria-labelledby="loginRequiredModalLabel"
	     aria-hidden="true" style="z-index: 21000;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="loginRequiredModalLabel">{{ __('default.Login Required') }}</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					{{ __('default.You need to login to use this feature.') }}
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('default.Close') }}</button>
					<a href="{{ route('login') }}" class="btn btn-primary">{{ __('default.Login') }}</a>
				</div>
			</div>
		</div>
	</div>
	
	<!-- CSRF Error Modal -->
	<div class="modal fade" id="csrfErrorModal" tabindex="-1" aria-labelledby="csrfErrorModalLabel" aria-hidden="true"
	     style="z-index: 21000;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="csrfErrorModalLabel">{{ __('default.Session Expired') }}</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					{{ __('default.Your session has expired. Please refresh the page and try again.') }}
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('default.Close') }}</button>
					<button type="button" class="btn btn-primary"
					        onclick="window.location.reload()">{{ __('default.Refresh Page') }}</button>
				</div>
			</div>
		</div>
	</div>
	
	<!-- Comment Panel -->
	<div id="comment-panel" class="comment-panel">
		<div class="comment-panel-header">
			<h3>{{__('default.Responses')}}</h3>
			<button class="close-btn" onclick="toggleCommentPanel()">
				<i class="material-icons">close</i>
			</button>
		</div>
		<div class="comment-panel-content">
			<div class="comment-form mb-4">
				<textarea id="comment-content" class="form-control" rows="3"
				          placeholder="{{ __('default.Write a response...') }}"></textarea>
				<button class="btn btn-secondary btn-sm mt-2" onclick="submitComment()">{{ __('default.Submit') }}</button>
			</div>
			<div id="comments-container"></div>
		</div>
	</div>
	<div id="comment-panel-overlay" class="comment-panel-overlay" onclick="toggleCommentPanel()"></div>

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
          color: #ccc !important;
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
          color: rgb(209, 205, 199) !important;
      }

      .clap:hover:after {
          animation: shockwave 1s ease-in infinite;
      }

      [data-bs-theme=dark] .clap:hover:after {
          animation: shockwave-dark 1s ease-in infinite;
      }

      .clap .clap--count {
          position: absolute;
          top: -25px;
          left: 0px;
          font-size: 0.8rem;
          color: rgb(209, 205, 199);
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
          text-align: left;
          left: 40px;
          top: 10px;
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

      .follow-text {
          cursor: pointer;
          foht-weight: 600;
          color: #333;
      }

      .follow-text:hover {
          text-decoration: underline;
      }

      [data-bs-theme=dark] .follow-text {
          color: #99FF99 !important;
      }

      .share-options {
          display: none;
          position: absolute;
          top: 40px;
          right: 0;
          background-color: #fff;
          border: 1px solid #ccc;
          border-radius: 5px;
          box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
          padding: 10px;
          z-index: 1000;
      }

      [data-bs-theme=dark] .share-options {
          background-color: #333 !important;
          border: 1px solid #ccc !important;
      }

      .share-options a {
          display: block;
          margin-bottom: 5px;
          color: inherit;
          text-decoration: none;
      }

      .share-options a:hover {
          text-decoration: underline;
      }

      .favorite-btn {
          cursor: pointer;
      }

      .share-btn {
          cursor: pointer;
      }


      /* Comment Panel Styles */
      .comment-btn {
          cursor: pointer;
      }

      .comment-panel-overlay {
          display: none;
          position: fixed;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background: rgba(0, 0, 0, 0.5);
          z-index: 1000;
      }

      .comment-panel {
          display: none;
          position: fixed;
          background: var(--bs-body-bg);
          z-index: 21001;
          transition: transform 0.3s ease-in-out;
      }

      .comment-panel-header {
          padding: 20px;
          border-bottom: 1px solid var(--bs-border-color);
          display: flex;
          justify-content: space-between;
          align-items: center;
      }

      .comment-panel-header h3 {
          margin: 0;
          font-size: 1.25rem;
      }

      .comment-panel-header .close-btn {
          background: none;
          border: none;
          cursor: pointer;
          padding: 5px;
      }

      .comment-panel-content {
          padding: 20px;
          height: calc(100% - 70px);
          overflow-y: auto;
      }

      /* Desktop styles */
      @media (min-width: 768px) {
          .comment-panel {
              top: 0;
              right: 0;
              width: 400px;
              height: 100%;
              transform: translateX(100%);
          }

          .comment-panel.active {
              transform: translateX(0);
          }
      }

      /* Mobile styles */
      @media (max-width: 767px) {
          .comment-panel {
              left: 0;
              right: 0;
              bottom: 0;
              height: 80vh;
              transform: translateY(100%);
          }

          .comment-panel.active {
              transform: translateY(0);
          }
      }

      [data-bs-theme=dark] .comment-panel {
          background: #222;
          color: rgb(209, 205, 199);
      }

      [data-bs-theme=dark] .comment-panel-header {
          border-bottom-color: #444;
      }

      [data-bs-theme=dark] .comment-panel-header .close-btn {
          color: rgb(209, 205, 199);
      }

      .comment {
          border-bottom: 1px solid var(--bs-border-color);
          padding: 0px;
          font-size: 14px;

      }

      #comment-content {
          min-height: 100px;
          padding: 5px;
      }

      .comment:last-child {
          border-bottom: none;
      }

      .comment-header {
          display: flex;
          align-items: flex-start;
          margin-bottom: 8px;
      }

      .comment-avatar {
          flex-shrink: 0;
      }

      .small-user-avatar {
          width: 40px;
		      max-width: 40px;
          height: 40px;
		      max-height: 40px;
          border-radius: 50%;
          object-fit: cover;
      }

      .comment-meta {
          display: flex;
          flex-direction: column;
      }

      .comment-author {
          font-weight: 500;
          line-height: 1.2;
      }

      .comment-date {
          font-size: 0.85em;
      }

      .comment-body {
          font-size: 14px;
          margin-bottom: 8px;
      }

      .comment-body p {
          margin-bottom: 0;
          word-break: break-word;
      }

      .comment-actions {
          font-size: 0.9em;
      }

      .replies {
          margin-left: 15px;
          border-left: 2px solid var(--bs-border-color);
          margin-top: 10px;
      }

      .reply-form {
          margin: 10px 0;
      }

      .reply-button-text {
          cursor: pointer;
          foht-weight: 400;
          color: #333;
          font-size: 13px;
          text-decoration: underline;
      }

      [data-bs-theme=dark] .reply-button-text {
          color: #99FF99 !important;
      }

      .delete-comment-button-text {
		      margin-left:8px;
          cursor: pointer;
          foht-weight: 400;
          color: #FF3333;
          font-size: 13px;
          text-decoration: underline;
      }
      
      [data-bs-theme=dark] .delete-comment-button-text {
					color: #FF3333 !important;
			}

      .comment-form textarea,
      .reply-form textarea {
          resize: vertical;
          min-height: 60px;
      }

      [data-bs-theme=dark] .replies {
          border-left-color: #444;
      }


      [data-bs-theme=dark] .modal-content {
          background-color: #222;
          color: rgb(209, 205, 199);
      }

      [data-bs-theme=dark] .modal-header {
          border-bottom-color: #444;
      }

      [data-bs-theme=dark] .modal-footer {
          border-top-color: #444;
      }

      [data-bs-theme=dark] .btn-close {
          filter: invert(1) grayscale(100%) brightness(200%);
      }

      [data-bs-theme=dark] textarea.form-control {
          background-color: #333;
          color: rgb(209, 205, 199);
          border-color: #444;
      }

      [data-bs-theme=dark] textarea.form-control:focus {
          background-color: #444;
          color: rgb(209, 205, 199);
          border-color: #666;
          box-shadow: 0 0 0 0.25rem rgba(66, 70, 73, 0.5);
      }

      [data-bs-theme=dark] textarea.form-control::placeholder {
          color: #aaa;
      }
	</style>
@endpush


@push('scripts')
	<script src="/js/mo.min.js"></script>
	<script>
		let hasRecordedRead = false;
		let currentArticleId = '{{ $article->id }}';
		let clap;
		let clapCount;
		let clapTotalCount;
		let initialNumberOfClaps = {{ $article->claps()->sum('count') }};
		let btnDimension = 40;
		let tlDuration = 300;
		let numberOfClaps = 0;
		let clapHold;
		
		function recordRead() {
			if (!hasRecordedRead) {
				const articleId = '{{ $article->id }}';
				$.ajax({
					url: `/yapit/${articleId}/read`,
					method: 'POST',
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function (response) {
						if (response.success) {
							hasRecordedRead = true;
						}
					}
				});
			}
		}
		
		function toggleFollow(userId) {
			$.ajax({
				url: '/favori/yazar/' + userId,
				type: 'POST',
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				success: function (response) {
					const btn = $(`.follow-text[data-user-id="${userId}"]`);
					if (response.following) {
						btn.html('{{__('default.Following')}}');
					} else {
						btn.html('{{__('default.Follow')}}');
					}
				},
				error: function (xhr) {
					if (xhr.status === 401) {
						showLoginModal();
					} else if (xhr.status === 419) {
						showCsrfErrorModal();
					}
				}
			});
		}
		
		function toggleFavorite(articleId) {
			$.ajax({
				url: '/favori/eser/' + articleId,
				type: 'POST',
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				success: function (response) {
					const btn = $(`.favorite-btn[data-article-id="${articleId}"]`);
					if (response.favorited) {
						btn.html('<i class="bi bi-bookmark-fill fs-5"></i>');
					} else {
						btn.html('<i class="bi bi-bookmark fs-5"></i>');
					}
				},
				error: function (xhr) {
					if (xhr.status === 401) {
						showLoginModal();
					} else if (xhr.status === 419) {
						showCsrfErrorModal();
					}
				}
			});
		}
		
		function showShareOptions() {
			const shareOptions = document.getElementById('share-options');
			shareOptions.style.display = shareOptions.style.display === 'none' ? 'block' : 'none';
		}
		
		function copyShareLink() {
			const dummy = document.createElement('input');
			const text = window.location.href;
			
			document.body.appendChild(dummy);
			dummy.value = text;
			dummy.select();
			document.execCommand('copy');
			document.body.removeChild(dummy);
			
			alert('{{__('default.Link copied to clipboard!')}}');
		}
		
		function clapArticle(articleId) {
			$.ajax({
				url: `/yapit/${articleId}/clap`,
				method: 'POST',
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				success: function (response) {
					$('#clap--count-total').text(response.claps);
				},
				error: function (xhr) {
					if (xhr.status === 401) {
						showLoginModal();
					} else if (xhr.status === 419) {
						showCsrfErrorModal();
					}
				}
			});
		}
		
		// Add after existing scripts
		function toggleCommentPanel() {
			const panel = document.getElementById('comment-panel');
			const overlay = document.getElementById('comment-panel-overlay');
			
			if (panel.style.display === 'none' || !panel.style.display) {
				panel.style.display = 'block';
				overlay.style.display = 'block';
				// Use setTimeout to ensure the display change has taken effect
				setTimeout(() => {
					panel.classList.add('active');
					loadComments();
				}, 10);
				// Prevent body scrolling
				document.body.style.overflow = 'hidden';
			} else {
				panel.classList.remove('active');
				// Wait for animation to complete before hiding
				setTimeout(() => {
					panel.style.display = 'none';
					overlay.style.display = 'none';
				}, 300);
				// Restore body scrolling
				document.body.style.overflow = '';
			}
		}
		
		function loadComments() {
			$.get(`/articles/${currentArticleId}/comments`, function (response) {
				displayComments(response);
			});
		}
		
		function displayComments(comments) {
			const container = $('#comments-container');
			container.empty();
			
			comments.forEach(comment => {
				container.append(createCommentHTML(comment));
			});
		}
		
		function createCommentHTML(comment) {
			// Format the date
			const date = new Date(comment.created_at);
			const formattedDate = date.toLocaleDateString('tr-TR', {
				day: 'numeric',
				month: 'short',
				year: 'numeric'
			});
			
			// Handle user avatar and name link
			let userAvatarSrc;
			let userNameHTML;
			
			if (comment.user_id === 0) {
				// For guest comments
				userAvatarSrc = '/assets/images/avatar/placeholder.jpg';
				userNameHTML = `<div class="comment-author">${comment.sender_name || '{{ __("default.Guest") }}'}</div>`;
			} else {
				// For registered users
				userAvatarSrc = comment.user.avatar
					? comment.user.profile_photo_url
					: '/assets/images/avatar/placeholder.jpg';
				userNameHTML = `<div class="comment-author"><a href="/yazar/${comment.user.slug}">${comment.user.name}</a></div>`;
			}
			
			// Ensure replies exists and is an array
			const replies = (comment.replies || []).map(reply => createCommentHTML(reply)).join('');
			
			return `
        <div class="comment" data-comment-id="${comment.id}">
            <div class="comment-header d-flex align-items-start">
                <div class="comment-avatar">
                    <img class="small-user-avatar" src="${userAvatarSrc}" alt="${comment.user_id === 0 ? '{{ __("default.Guest") }}' : comment.user.name}'s avatar">
                </div>
                <div class="comment-meta ms-2 flex-grow-1">
                    ${userNameHTML}
                    <div class="comment-date text-muted">${formattedDate}</div>
                </div>
            </div>
            <div class="comment-body">
                <p>${comment.content}</p>
            </div>
            <div class="comment-actions">
                <span class="reply-button-text" onclick="showReplyForm(${comment.id})">
                    {{ __('default.Reply') }}
			</span>
${comment.user_id === {{ Auth::id() ?? 'null' }} ?
				`<span class="delete-comment-button-text" onclick="deleteComment(${comment.id})">
                        {{ __('default.Delete') }}
				</span>` : ''
			}
            </div>
            <div id="reply-form-${comment.id}" class="reply-form mt-2" style="display: none;">
                <textarea class="form-control" style="min-height:100px; padding:5px;" rows="2"></textarea>
                <button class="btn btn-sm btn-primary mt-2" onclick="submitReply(${comment.id})">
                    {{ __('default.Submit Reply') }}
			</button>
	</div>
	<div class="replies ml-4">
${replies}
            </div>
        </div>
    `;
		}
		
		function submitComment(parentId = null) {
			const content = parentId ?
				$(`#reply-form-${parentId} textarea`).val() :
				$('#comment-content').val();
			
			if (!content.trim()) {
				alert('{{ __("default.Please enter a comment") }}');
				return;
			}
			
			$.ajax({
				url: `/articles/${currentArticleId}/comments`,
				method: 'POST',
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				data: {
					content: content,
					parent_id: parentId
				},
				success: function (response) {
					// Instead of reloading all comments, just reload if it's a top-level comment
					if (!parentId) {
						loadComments();
					} else {
						// If it's a reply, just append it to the existing replies
						const parentComment = $(`.comment[data-comment-id="${parentId}"]`);
						const repliesContainer = parentComment.find('.replies').first();
						repliesContainer.append(createCommentHTML(response));
						
						// Hide the reply form and clear it
						$(`#reply-form-${parentId}`).hide();
						$(`#reply-form-${parentId} textarea`).val('');
					}
					
					// Clear the main comment form if it was a top-level comment
					if (!parentId) {
						$('#comment-content').val('');
					}
				},
				error: function (xhr) {
					if (xhr.status === 401) {
						showLoginModal();
					} else if (xhr.status === 419) {
						showCsrfErrorModal();
					} else {
						alert('{{ __("default.Error posting comment") }}');
					}
				}
			});
		}
		
		function showReplyForm(commentId) {
			$(`#reply-form-${commentId}`).toggle();
		}
		
		function submitReply(commentId) {
			submitComment(commentId);
		}
		
		function deleteComment(commentId) {
			if (confirm('{{ __('default.Are you sure you want to delete this comment?') }}')) {
				$.ajax({
					url: `/comments/${commentId}`,
					method: 'DELETE',
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function () {
						loadComments();
					}
				});
			}
		}
		
		function showLoginModal() {
			$("#loginRequiredModal").modal('show');
		}
		
		function showCsrfErrorModal() {
			$("#csrfErrorModal").modal('show');
		}
		
		
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
			
			const entryContent = $('.entry-main-content');
			if (entryContent.length) {
				const contentTop = entryContent.offset().top;
				const contentHeight = entryContent.height();
				const scrollPosition = $(window).scrollTop();
				const windowHeight = $(window).height();
				
				// Calculate the middle point of the content
				const middlePoint = contentTop + (contentHeight / 2);
				
				// Check if user has scrolled past the middle point
				if (scrollPosition + (windowHeight / 2) > middlePoint) {
					recordRead();
				}
			}
		});
		
		
		$(document).ready(function () {
			//comment panel
			
			// Close panel on escape key
			document.addEventListener('keydown', function (event) {
				if (event.key === 'Escape') {
					const panel = document.getElementById('comment-panel');
					if (panel.style.display === 'block') {
						toggleCommentPanel();
					}
				}
			});
			
			//clap
			clap = document.getElementById('clap');
			clapCount = document.getElementById('clap--count');
			clapTotalCount = document.getElementById('clap--count-total');
			
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
			
			$("#clap--count-total").css({"opacity": 1, "transform": "scale(1, 1)"});
			
			
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
			}
			
			function updateNumberOfClaps() {
				numberOfClaps < 50 ? numberOfClaps++ : null
				clapCount.innerHTML = "+" + numberOfClaps
				clapTotalCount.innerHTML = initialNumberOfClaps + numberOfClaps
			}
			
			function generateRandomNumber(min, max) {
				return Math.floor(Math.random() * (max - min + 1) + min);
			}
			
			$('#clap').on('click', function () {
				clapArticle($(this).data('article-id'));
			});
			
			
		});
	
	
	</script>
@endpush
