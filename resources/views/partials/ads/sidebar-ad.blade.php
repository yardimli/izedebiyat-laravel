{{-- MODIFIED: This partial displays a different random book review each time it's called. --}}
@isset($randomBookReviewsForAd)
	@php
		// Pop a review from the collection to ensure it's unique on the page.
		$randomBookReviewForAd = $randomBookReviewsForAd->isNotEmpty() ? $randomBookReviewsForAd->pop() : null;
	@endphp
	
	@if($randomBookReviewForAd)
		<div class="sidebar-widget">
			<h4 class="spanborder widget-title">
				<span><a href="{{ route('frontend.book-reviews.index') }}">KİTAP İZLERİ</a></span>
			</h4>
			<div class="card book-ad-card-sidebar text-center">
				<a href="{{ route('frontend.book-review.show', $randomBookReviewForAd->slug) }}">
					{{-- MODIFIED: Added inline style for max-height and object-fit --}}
					<img src="{{ $randomBookReviewForAd->cover_image ?? asset('images/no-image.png') }}"
					     class="card-img-top"
					     style="max-height: 100px; object-fit: contain; width: auto; margin-top: 0.5rem;"
					     alt="{{ $randomBookReviewForAd->title }}">
				</a>
				<div class="card-body">
					<h6 class="card-title">
						<a href="{{ route('frontend.book-review.show', $randomBookReviewForAd->slug) }}">{{ $randomBookReviewForAd->title }}</a>
					</h6>
					<p class="card-text text-muted small mb-2">{{ $randomBookReviewForAd->display_author }}</p>
					<a href="{{ route('frontend.book-review.show', $randomBookReviewForAd->slug) }}"
					   class="btn btn-sm btn-outline-info">{{ __('default.Read Review') }}</a>
				</div>
			</div>
		</div>
	@endif
@endisset

@push('styles')
	<style>
      .book-ad-card-sidebar {
          border: 1px solid var(--bs-border-color);
      }

      .book-ad-card-sidebar .card-body {
          padding: 0.75rem;
      }

      .book-ad-card-sidebar .card-title {
          font-size: 1rem;
          margin-bottom: 0.25rem;
      }

      [data-bs-theme="dark"] .book-ad-card-sidebar {
          background-color: var(--bs-gray-800);
      }
	</style>
@endpush
