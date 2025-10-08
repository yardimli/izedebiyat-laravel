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
							
							<div class="text-center mb-4 book-review-submenu">
								<a href="{{ route('frontend.book-reviews.authors') }}" class="btn btn-sm btn-outline-secondary">Yazarlar</a>
								<a href="{{ route('frontend.book-reviews.categories') }}" class="btn btn-sm btn-secondary">Kümeler</a>
								<a href="{{ route('frontend.book-reviews.tags') }}" class="btn btn-sm btn-outline-secondary">Etiketler</a>
							</div>
							
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
