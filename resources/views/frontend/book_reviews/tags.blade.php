@extends('layouts.app-frontend')

@section('title', 'Etiketler - Kitap İzleri')
@section('body-class', 'home')

@section('content')
	<section class="archive">
		<main id="content">
			<div class="content-widget">
				<div class="container-lg">
					<div class="row">
						<div class="col-12">
							<h4 class="spanborder">
								<span>{{ __('default.Book Reviews') }}</span>
							</h4>
						</div>
					</div>
					
					<div class="row">
						<div class="col-12">
							<h4 class="spanborder">
								<span>Kitap İzleri Etiketleri</span>
							</h4>
							
							{{-- MODIFIED: Removed the submenu as it's now in the main header for this section --}}
							
							<div class="tag-cloud">
								@forelse($tags as $tag)
									<a href="{{ route('frontend.book-reviews.show-by-tag', $tag->slug) }}" class="btn btn-outline-info m-1">
										{{ $tag->name }} <span class="badge bg-secondary">{{ $tag->book_reviews_count }}</span>
									</a>
								@empty
									<p class="text-center">Hiç etiket bulunamadı.</p>
								@endforelse
							</div>
							
							@if($tags->lastPage() > 1)
								<div class="d-flex justify-content-center mt-4">
									{{ $tags->links() }}
								</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</main>
	</section>
@endsection
