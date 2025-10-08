@extends('layouts.app-frontend')

@section('title', 'Kümeler - Kitap İzleri')
@section('body-class', 'home')

@section('content')
	<section class="archive">
		<main id="content">
			<div class="content-widget">
				<div class="container-lg">
					<div class="row">
						<div class="col-12">
							<h4 class="spanborder">
								<span>Kitap İzleri Kümeleri</span>
							</h4>
							
							{{-- MODIFIED: Removed the submenu as it's now in the main header for this section --}}
							
							<div class="list-group">
								@forelse($categories as $category)
									<a href="{{ route('frontend.book-reviews.show-by-category', $category->slug) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
										{{ $category->name }}
										<span class="badge bg-primary rounded-pill">{{ $category->book_reviews_count }}</span>
									</a>
								@empty
									<p class="text-center">Hiç küme bulunamadı.</p>
								@endforelse
							</div>
							
							@if($categories->lastPage() > 1)
								<div class="d-flex justify-content-center mt-4">
									{{ $categories->links() }}
								</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</main>
	</section>
@endsection

{{-- ADDED: Styles for dark mode --}}
@push('styles')
	<style>
      [data-bs-theme="dark"] .list-group-item {
          background-color: var(--bs-gray-800);
          border-color: var(--bs-gray-700);
          color: var(--bs-light);
      }
      [data-bs-theme="dark"] .list-group-item-action:hover,
      [data-bs-theme="dark"] .list-group-item-action:focus {
          background-color: var(--bs-gray-700);
      }
	</style>
@endpush
