@extends('layouts.app-frontend')

@section('title', $listTitle ?? 'Kitap İzleri')
@section('body-class', 'home')

@section('content')
	<section class="archive">
		<main id="content">
			<div class="content-widget">
				<div class="container-lg">
					<div class="row">
						<div class="col-12">
							<h4 class="spanborder">
								<span>{{ __('default.Book Reviews') }} - {{ $listTitle }}</span>
							</h4>
						</div>
					</div>
					
					<div class="row">
						@forelse($bookReviews as $review)
							<div class="col-lg-3 col-md-4 col-sm-6 mb-4">
								<div class="book-grid-item">
									<a href="{{ route('frontend.book-review.show', $review->slug) }}">
										<img src="{{ $review->cover_image ?? asset('images/no-image.png') }}" class="img-fluid book-cover"
										     alt="{{ $review->title }}">
										<div class="book-info-overlay">
											<h5 class="book-title">{{ $review->title }}</h5>
											<p class="book-author">{{ $review->display_author }}</p>
											<span class="btn btn-sm btn-outline-light">{{ __('default.Read Review') }}</span>
										</div>
									</a>
								</div>
							</div>
						@empty
							<p class="text-center">{{ __('default.No book reviews found.') }}</p>
						@endforelse
					</div>
					
					@if($bookReviews->lastPage() > 1)
						<div class="d-flex justify-content-center mt-4">
							{{ $bookReviews->links() }}
						</div>
					@endif
				</div>
			</div>
		</main>
	</section>
@endsection

@push('styles')
	<style>
      .book-grid-item {
          position: relative;
          overflow: hidden;
          border-radius: 5px;
          box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      }

      .book-grid-item .book-cover {
          width: 100%;
          height: auto;
          aspect-ratio: 2 / 3;
          object-fit: cover;
          transition: transform 0.3s ease;
      }

      .book-grid-item:hover .book-cover {
          transform: scale(1.05);
      }

      .book-info-overlay {
          position: absolute;
          bottom: 0;
          left: 0;
          right: 0;
          background: linear-gradient(to top, rgba(0, 0, 0, 0.9) 0%, rgba(0, 0, 0, 0) 100%);
          color: white;
          padding: 20px 15px 15px;
          text-align: center;
          opacity: 0;
          transform: translateY(20px);
          transition: all 0.3s ease;
      }

      .book-grid-item:hover .book-info-overlay {
          opacity: 1;
          transform: translateY(0);
      }

      .book-info-overlay .book-title {
          font-size: 1.1rem;
          margin-bottom: 5px;
          font-weight: bold;
      }

      .book-info-overlay .book-author {
          font-size: 0.9rem;
          margin-bottom: 10px;
          font-style: italic;
      }
	</style>
@endpush
