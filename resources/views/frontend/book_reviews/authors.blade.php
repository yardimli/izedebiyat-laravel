@extends('layouts.app-frontend')

@section('title', 'Kitap Yazarları - Kitap İzleri')
@section('body-class', 'home')

@section('content')
	<section class="archive">
		<main id="content">
			<div class="content-widget">
				<div class="container-lg">
					<div class="row">
						<div class="col-12">
							<h4 class="spanborder">
								<span>Kitap Yazarları</span>
							</h4>
							
							{{-- Submenu for filtering --}}
							<div class="text-center mb-4 book-review-submenu">
								<a href="{{ route('frontend.book-reviews.authors') }}" class="btn btn-sm btn-secondary">Yazarlar</a>
								<a href="{{ route('frontend.book-reviews.categories') }}" class="btn btn-sm btn-outline-secondary">Kümeler</a>
								<a href="{{ route('frontend.book-reviews.tags') }}" class="btn btn-sm btn-outline-secondary">Etiketler</a>
							</div>
							
							<div class="row">
								@forelse($authors as $author)
									<div class="col-lg-4 col-md-6 mb-4">
										<div class="card author-card h-100">
											<div class="card-body text-center">
												<a href="{{ route('frontend.book-reviews.author', $author->slug) }}">
													<img src="{{ $author->picture ?? asset('assets/images/avatar/placeholder.jpg') }}" class="rounded-circle mb-3" alt="{{ $author->name }}" style="width: 100px; height: 100px; object-fit: cover;">
												</a>
												<h5 class="card-title">
													<a href="{{ route('frontend.book-reviews.author', $author->slug) }}">{{ $author->name }}</a>
												</h5>
												<p class="card-text text-muted">
													{{ $author->book_reviews_count }} kitap izi
												</p>
											</div>
										</div>
									</div>
								@empty
									<p class="text-center">İncelemesi bulunan yazar bulunamadı.</p>
								@endforelse
							</div>
							
							@if($authors->lastPage() > 1)
								<div class="d-flex justify-content-center mt-4">
									{{ $authors->links() }}
								</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</main>
	</section>
@endsection

@push('styles')
	<style>
      .author-card {
          transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
      }
      .author-card:hover {
          transform: translateY(-5px);
          box-shadow: 0 8px 16px rgba(0,0,0,0.1);
      }

      /* MODIFIED: Added dark mode styles for author cards */
      [data-bs-theme="dark"] .author-card {
          background-color: var(--bs-gray-800);
          border-color: var(--bs-gray-700);
      }
      [data-bs-theme="dark"] .author-card .card-title a {
          color: var(--bs-light);
      }
      [data-bs-theme="dark"] .author-card .card-text {
          color: var(--bs-gray-500) !important;
      }
	</style>
@endpush
