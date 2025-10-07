@extends('layouts.app')
@section('title', __('default.Create Book Review'))

@section('content')
	<main>
		<div class="container mb-5" style="min-height: calc(88vh);">
			<div class="row mt-3">
				{{-- Main Content Column --}}
				<div class="col-12 col-xl-8 col-lg-8 mx-auto">
					
					{{-- Display validation errors if any --}}
					@if ($errors->any())
						<div class="alert alert-danger">
							<ul class="mb-0">
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif
					
					<h5 class="mb-4">{{ __('default.Create New Book Review') }}</h5>
					
					{{-- The Form --}}
					<form method="POST" action="{{ route('book-reviews.store') }}" enctype="multipart/form-data">
						{{-- MODIFIED: Added enctype for file uploads --}}
						{{--
								Include the reusable form partial.
								Pass the text for the submit button.
						--}}
						@include('backend.book_reviews._form', [
								'bookReview' => new \App\Models\BookReview(), // Pass an empty model
								'submitButtonText' => __('default.Create Book Review')
						])
					</form>
				
				</div>
			</div>
		</div>
	</main>
	
	{{--
			The image selection modal is assumed to be in the main layout (`layouts.app`).
			If not, it should be included here.
	--}}
	{{-- @include('partials.image-modal') --}}
	
	@include('layouts.footer')
@endsection
