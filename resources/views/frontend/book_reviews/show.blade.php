@extends('layouts.app-frontend')

@section('title', $bookReview->title . ' - ' . __('default.Book Review'))
@section('body-class', 'home single')

@section('content')
	<main id="content">
		<div class="container-lg">
			<div class="entry-header">
				<div class="mb-5">
					<h1 class="entry-title mb-2">{{ $bookReview->title }}</h1>
					<h2 class="entry-subtitle mb-3 fst-italic">{{ $bookReview->author }}</h2>
					
					<div class="entry-meta align-items-center divider pb-4">
						<a class="user-avatar" href="{{ url('/yazar/' . $bookReview->user->slug) }}">
							{!! \App\Helpers\MyHelper::generateInitialsAvatar($bookReview->user->avatar, $bookReview->user->email,'width:50px; height:50px; border-radius:50%;','') !!}
						</a>
						<span>{{ __('default.Review by') }}: <a href="{{ url('/yazar/' . $bookReview->user->slug) }}">{{ $bookReview->user->name }}</a></span>
						<span class="middotDivider"></span>
						<span>{{ \App\Helpers\MyHelper::timeElapsedString($bookReview->published_at ?? $bookReview->created_at) }}</span>
					</div>
				</div>
			</div>
			
			<article class="entry-wrapper mb-5">
				<div class="row">
					{{-- Cover Image and Book Details Column --}}
					<div class="col-md-4">
						<figure class="image zoom mb-4 text-center">
							<img src="{{ $bookReview->cover_image ? asset($bookReview->cover_image) : asset('images/no-image.png') }}" style="max-width: 300px; width: 100%;" alt="Cover Image">
						</figure>
						
						{{-- ADDED: Book details section --}}
						<div class="book-details mb-4">
							@if($bookReview->publisher)
								<p><strong>Yayınevi:</strong> {{ $bookReview->publisher }}</p>
							@endif
							@if($bookReview->publication_place)
								<p><strong>Yayın Yeri:</strong> {{ $bookReview->publication_place }}</p>
							@endif
							@if($bookReview->publication_date)
								<p><strong>Yayın Tarihi:</strong> {{ \Carbon\Carbon::parse($bookReview->publication_date)->format('d F Y') }}</p>
							@endif
						</div>
						
						{{-- ADDED: Buy Now button --}}
						@if($bookReview->buy_url)
							<div class="text-center">
								<a href="{{ $bookReview->buy_url }}" class="btn btn-primary" target="_blank" rel="noopener noreferrer">
									<i class="bi bi-cart-fill me-2"></i>Şimdi Satın Al
								</a>
							</div>
						@endif
					</div>
					
					{{-- Review Content Column --}}
					<div class="col-md-8">
						<div class="entry-main-content article-text-color">
							{{-- MODIFIED: Use Markdown parser for review content --}}
							{!! (new \League\CommonMark\CommonMarkConverter())->convertToHtml(e($bookReview->review_content)) !!}
						</div>
						
						<div class="entry-bottom mt-4">
							@if($bookReview->categories->isNotEmpty())
								<div class="tags-wrap heading mb-2">
									<strong>{{ __('default.Categories') }}:</strong>
									@foreach($bookReview->categories as $category)
										<a href="#">{{ $category->name }}</a>
									@endforeach
								</div>
							@endif
							@if($bookReview->tags->isNotEmpty())
								<div class="tags-wrap heading">
									<strong>{{ __('default.Tags') }}:</strong>
									@foreach($bookReview->tags as $tag)
										<a href="#">{{ $tag->name }}</a>
									@endforeach
								</div>
							@endif
						</div>
					</div>
				</div>
			</article>
		</div>
	</main>
@endsection

@push('styles')
	<style>
      .book-details p {
          margin-bottom: 0.5rem;
          font-size: 0.9rem;
      }
      .book-details strong {
          color: var(--bs-body-color);
      }
	</style>
@endpush
