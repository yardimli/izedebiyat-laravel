{{-- MODIFIED: This partial displays a different random book review each time it's called. --}}
@isset($randomBookReviewsForAd)
	@php
		// Pop a review from the collection to ensure it's unique on the page.
		$randomBookReviewForAd = $randomBookReviewsForAd->isNotEmpty() ? $randomBookReviewsForAd->pop() : null;
	@endphp
	
	@if($randomBookReviewForAd)
		<div class="content-widget">
			<div class="container-lg">
				<div class="row">
					<div class="col-12">
						<h4 class="spanborder">
							<span><a href="{{ route('frontend.book-reviews.index') }}">KİTAP İZLERİ</a></span>
						</h4>
						<div class="card book-ad-card-content mb-4">
							<div class="row g-0">
								{{-- MODIFIED: Adjusted column size and added flex properties for image centering --}}
								<div class="col-md-2 col-4 d-flex align-items-center justify-content-center p-2">
									<a href="{{ route('frontend.book-review.show', $randomBookReviewForAd->slug) }}">
										{{-- MODIFIED: Added inline style for max-height --}}
										<img src="{{ $randomBookReviewForAd->cover_image ?? asset('images/no-image.png') }}"
										     class="img-fluid rounded"
										     style="max-height: 150px; width: auto;"
										     alt="{{ $randomBookReviewForAd->title }}">
									</a>
								</div>
								{{-- MODIFIED: Adjusted column size --}}
								<div class="col-md-10 col-8">
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
	@endif
@endisset

{{-- MODIFIED: Removed the dedicated style block as styles are now inline or simple enough not to require it --}}
