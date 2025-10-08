@extends('layouts.app-frontend')

@section('title', $author->name . ' - Kitap İzleri')
@section('body-class', 'home single')

@section('content')
	<main id="content">
		<div class="container-lg">
			{{-- Author Header --}}
			<div class="author-header-container my-4 p-4 rounded">
				<div class="row align-items-center">
					<div class="col-md-3 text-center">
						<img src="{{ $author->picture ?? asset('assets/images/avatar/placeholder.jpg') }}" class="img-fluid rounded-circle" alt="{{ $author->name }}" style="width: 150px; height: 150px; object-fit: cover;">
					</div>
					<div class="col-md-9">
						<h1 class="entry-title mb-2">{{ $author->name }}</h1>
						@if($author->biography)
							<div class="author-bio-short">
								{!! Str::limit((new \League\CommonMark\CommonMarkConverter())->convertToHtml(e($author->biography)), 250) !!}
							</div>
						@endif
					</div>
				</div>
			</div>
			
			{{-- Author Details & Reviews --}}
			<div class="row">
				<div class="col-lg-4">
					<h4 class="spanborder">
						<span>{{ $author->name }} Kitap İzleri</span>
					</h4>
					<div class="row">
						@forelse($bookReviews as $review)
							<div class="col-lg-4 col-md-6 mb-4">
								<div class="book-grid-item">
									<a href="{{ route('frontend.book-review.show', $review->slug) }}">
										<img src="{{ $review->cover_image ?? asset('images/no-image.png') }}" class="img-fluid book-cover" alt="{{ $review->title }}">
										<div class="book-info-overlay">
											<h5 class="book-title">{{ $review->title }}</h5>
											<span class="btn btn-sm btn-outline-light">{{ __('default.Read Review') }}</span>
										</div>
									</a>
								</div>
							</div>
						@empty
							<p>Bu yazara ait kitap izi bulunamadı.</p>
						@endforelse
					</div>
				</div>
				
				<div class="col-lg-8">
					@if($author->biography)
						<div class="sidebar-widget">
							<h4 class="spanborder widget-title">
								<span>Biyografi</span>
							</h4>
							<div class="author-content">
								{!! (new \League\CommonMark\CommonMarkConverter())->convertToHtml(e($author->biography)) !!}
							</div>
						</div>
					@endif
					
					@if($author->bibliography)
						<div class="sidebar-widget mt-4">
							<h4 class="spanborder widget-title">
								<span>Bibliyografya</span>
							</h4>
							<div class="author-content">
								{!! (new \League\CommonMark\CommonMarkConverter())->convertToHtml(e($author->bibliography)) !!}
							</div>
						</div>
					@endif
				</div>
			</div>
			@if($bookReviews->lastPage() > 1)
				<div class="d-flex justify-content-center mt-4">
					{{ $bookReviews->links() }}
				</div>
			@endif
		
		</div>
	</main>
@endsection

@push('styles')
	<style>
      .author-header-container {
          background-color: var(--bs-tertiary-bg);
      }
      .book-grid-item {
          position: relative;
          overflow: hidden;
          border-radius: 5px;
          box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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
          background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0) 100%);
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
      .author-content ul {
          padding-left: 20px;
      }
	</style>
@endpush
