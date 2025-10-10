{{-- NEW: This partial displays a random book review in a wide format. --}}
@isset($randomBookReviewForAd)
	<div class="content-widget">
		<div class="container-lg">
			<div class="row">
				<div class="col-12">
					<h4 class="spanborder">
						<span><a href="{{ route('frontend.book-reviews.index') }}">KİTAP İZLERİ</a></span>
					</h4>
					<div class="card book-ad-card-content mb-4">
						<div class="row g-0">
							<div class="col-md-3 col-4">
								<a href="{{ route('frontend.book-review.show', $randomBookReviewForAd->slug) }}">
									<img src="{{ $randomBookReviewForAd->cover_image ?? asset('images/no-image.png') }}"
									     class="img-fluid rounded-start book-ad-img-content"
									     alt="{{ $randomBookReviewForAd->title }}">
								</a>
							</div>
							<div class="col-md-9 col-8">
								<div class="card-body">
									<h5 class="card-title">
										<a href="{{ route('frontend.book-review.show', $randomBookReviewForAd->slug) }}">{{ $randomBookReviewForAd->title }}</a>
									</h5>
									<p class="card-text"><small
											class="text-muted">{{ $randomBookReviewForAd->display_author }}</small></p>
									<div class="card-text d-none d-md-block">
										{!! \Illuminate\Support\Str::limit(strip_tags($randomBookReviewForAd->review_content), 150) !!}
									</div>
									<a href="{{ route('frontend.book-review.show', $randomBookReviewForAd->slug) }}"
									   class="btn btn-sm btn-outline-info mt-2">{{ __('default.Read Review') }}</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endisset

@push('styles')
	<style>
      .book-ad-card-content {
          border: 1px solid var(--bs-border-color);
      }

      .book-ad-img-content {
          width: 100%;
          height: 100%;
          object-fit: cover;
          aspect-ratio: 2 / 3;
      }

      .book-ad-card-content .card-body {
          padding: 1rem;
      }

      .book-ad-card-content .card-title {
          font-size: 1.1rem;
          margin-bottom: 0.25rem;
      }

      .book-ad-card-content .card-text {
          font-size: 0.9rem;
      }

      @media (max-width: 767px) {
          .book-ad-card-content .card-body {
              padding: 0.75rem;
          }

          .book-ad-card-content .card-title {
              font-size: 1rem;
          }
      }

      [data-bs-theme="dark"] .book-ad-card-content {
          background-color: var(--bs-gray-800);
      }
	</style>
@endpush
