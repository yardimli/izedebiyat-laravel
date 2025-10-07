@extends('layouts.app')
@section('title', __('default.Edit Book Review'))
@section('content')
	<main>
		<div class="container mb-5" style="min-height: calc(88vh);">
			<div class="row mt-3">
				<div class="col-12 col-xl-8 col-lg-8 mx-auto">
					<h5>{{ __('default.Edit Book Review') }}</h5>
					<form method="POST" action="{{ route('book-reviews.update', $bookReview->id) }}" enctype="multipart/form-data">
						{{-- MODIFIED: Added enctype for file uploads --}}
						@method('PUT')
						@include('backend.book_reviews._form', ['submitButtonText' => __('default.Update Book Review')])
					</form>
				</div>
			</div>
		</div>
	</main>
	@include('layouts.footer')
@endsection
