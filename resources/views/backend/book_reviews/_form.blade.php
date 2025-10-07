@csrf
<input type="file" id="image-upload-input" style="display: none" accept="image/*">
<input type="hidden" id="cover_image" name="cover_image"
       value="{{ $bookReview->cover_image ?? old('cover_image') }}">

<!-- Title -->
<div class="mb-3">
	<label for="title" class="form-label">{{ __('default.Book Title') }}</label>
	<input type="text" class="form-control" id="title" name="title"
	       value="{{ $bookReview->title ?? old('title') }}" required>
</div>

<!-- Author -->
<div class="mb-3">
	<label for="author" class="form-label">{{ __('default.Book Author') }}</label>
	<input type="text" class="form-control" id="author" name="author"
	       value="{{ $bookReview->author ?? old('author') }}" required>
</div>

<!-- Review Content -->
<div class="mb-3">
	<label for="review_content" class="form-label">{{ __('default.Review Content') }}</label>
	<textarea class="form-control" id="review_content_textarea" name="review_content"
	          rows="15">{{ $bookReview->review_content ?? old('review_content') }}</textarea>
</div>

<!-- Cover Image -->
<div class="mb-3">
	<label class="form-label">{{ __('default.Cover Image') }}</label>
	<div id="selectedImagePreview" class="mt-2">
		@if(isset($bookReview) && $bookReview->cover_image)
			<img src="{{ $bookReview->cover_image }}" alt="Cover Image" style="max-width: 200px;">
		@endif
	</div>
	<div class="mt-2">
		<button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
		        data-bs-target="#imageModal">
			{{ __('default.Select Cover Image') }}
		</button>
	</div>
</div>

<!-- Categories -->
<div class="mb-3">
	<label for="categories" class="form-label">{{ __('default.Categories') }}</label>
	<div id="generateCategory" class="ai-generate-button">{{__('default.Generate with AI')}}</div>
	<input type="text" class="form-control" id="categories" name="categories"
	       value="{{ isset($bookReview) ? $bookReview->categories->pluck('name')->implode(', ') : old('categories') }}">
</div>

<!-- Tags -->
<div class="mb-3">
	<label for="tags" class="form-label">{{ __('default.Tags') }}</label>
	<div id="generateKeywords" class="ai-generate-button">{{__('default.Generate with AI')}}</div>
	<input type="text" class="form-control" id="tags" name="tags"
	       value="{{ isset($bookReview) ? $bookReview->tags->pluck('name')->implode(', ') : old('tags') }}"
	       placeholder="{{ __('default.Enter keywords separated by commas or spaces') }}">
</div>

<!-- Publication Status -->
<div class="form-check mb-3">
	<input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1"
		{{ (isset($bookReview) && $bookReview->is_published) || old('is_published') ? 'checked' : '' }}>
	<label class="form-check-label" for="is_published">
		{{ __('default.Publish Review') }}
	</label>
</div>

<button type="submit" class="btn btn-primary">{{ $submitButtonText }}</button>

@push('scripts')
	<script src="/js/easymde.min.js"></script>
	<script src="/js/tagify.js"></script>
	<script>
		document.addEventListener("DOMContentLoaded", function() {
			// Initialize EasyMDE
			const easyMDE = new EasyMDE({
				element: document.getElementById('review_content_textarea'),
				spellChecker: false
			});
			
			// Initialize Tagify for Categories
			var categoriesInput = document.querySelector('input[name=categories]');
			var categoriesTagify = new Tagify(categoriesInput, {
				delimiters: ", ",
				trim: true,
				dropdown: { enabled: 0 }
			});
			
			// Initialize Tagify for Tags
			var tagsInput = document.querySelector('input[name=tags]');
			var tagsTagify = new Tagify(tagsInput, {
				delimiters: ", ",
				trim: true,
				dropdown: { enabled: 0 }
			});
			
			// AI Generation
			$('#generateCategory').on('click', function () {
				const reviewText = easyMDE.value();
				if (!reviewText) { return; }
				$(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
				$.ajax({
					url: '{{ route("book-reviews.generate-category") }}',
					method: 'POST',
					data: { main_text: reviewText, _token: '{{ csrf_token() }}' },
					success: function (response) {
						categoriesTagify.removeAllTags();
						categoriesTagify.addTags(response.categories);
					},
					complete: function () {
						$('#generateCategory').prop('disabled', false).html('{{__('default.Generate with AI')}}');
					}
				});
			});
			
			$('#generateKeywords').on('click', function () {
				const reviewText = easyMDE.value();
				if (!reviewText) { return; }
				$(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
				$.ajax({
					url: '{{ route("book-reviews.generate-keywords") }}',
					method: 'POST',
					data: { main_text: reviewText, _token: '{{ csrf_token() }}' },
					success: function (response) {
						tagsTagify.removeAllTags();
						tagsTagify.addTags(response.keywords);
					},
					complete: function () {
						$('#generateKeywords').prop('disabled', false).html('{{__('default.Generate with AI')}}');
					}
				});
			});
			
			// Handle image selection from modal
			$(document).on('click', '.select-modal-image', function () {
				const imageUrl = $(this).data('image-url');
				$('#cover_image').val(imageUrl);
				$('#selectedImagePreview').html(`<img src="${imageUrl}" alt="Selected Image" class="img-fluid" style="max-width: 200px;">`);
				$('#imageModal').modal('hide');
			});
		});
	</script>
@endpush
@push('styles')
	<link rel="stylesheet" type="text/css" href="/css/tagify.css">
	<style>.ai-generate-button{border:1px solid #ccc;font-size:12px;border-radius:3px;padding:2px 5px;margin-left:5px;display:inline-block;cursor:pointer}.ai-generate-button:hover{text-decoration:underline}</style>
@endpush```

#### New View: `resources/views/backend/book_reviews/create.blade.php`
```php
@extends('layouts.app')
@section('title', __('default.Create Book Review'))
@section('content')
	<main>
		<div class="container mb-5" style="min-height: calc(88vh);">
			<div class="row mt-3">
				<div class="col-12 col-xl-8 col-lg-8 mx-auto">
					<h5>{{ __('default.Create Book Review') }}</h5>
					<form method="POST" action="{{ route('book-reviews.store') }}">
						@include('backend.book_reviews._form', ['submitButtonText' => __('default.Create Book Review')])
					</form>
				</div>
			</div>
		</div>
	</main>
	@include('layouts.footer')
@endsection
