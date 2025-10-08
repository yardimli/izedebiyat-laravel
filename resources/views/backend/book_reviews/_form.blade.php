@csrf
{{-- Book Title --}}
<div class="mb-3">
	<label for="title" class="form-label">{{ __('default.Book Title') }}</label>
	<input type="text" class="form-control" id="title" name="title"
	       value="{{ old('title', $bookReview->title ?? '') }}" required>
</div>

{{-- MODIFIED: Author selection --}}
<div class="mb-3">
	<label for="book_author_id" class="form-label">Kitap Yazarı (Veritabanından)</label>
	<select class="form-select" id="book_author_id" name="book_author_id">
		<option value="">Yazar Seçin veya Aşağıya Manuel Girin</option>
		@isset($authors)
			@foreach($authors as $author)
				<option value="{{ $author->id }}" {{ (old('book_author_id', $bookReview->book_author_id ?? '') == $author->id) ? 'selected' : '' }}>
					{{ $author->name }}
				</option>
			@endforeach
		@endisset
	</select>
	<small class="form-text text-muted">Eğer yazar listede yoksa, aşağıdaki alana manuel olarak yazabilirsiniz. Yeni yazarları <a href="{{ route('book-authors.index') }}" target="_blank">Yazar Yönetimi</a> sayfasından ekleyebilirsiniz.</small>
</div>

<div class="mb-3">
	<label for="author" class="form-label">{{ __('default.Book Author') }} (Manuel)</label>
	<input type="text" class="form-control" id="author" name="author"
	       value="{{ old('author', $bookReview->author ?? '') }}">
	<small class="form-text text-muted">Veritabanından bir yazar seçilmediyse bu alan zorunludur.</small>
</div>
{{-- END MODIFIED --}}


{{-- MODIFIED: New optional fields START --}}
<div class="row">
	<div class="col-md-6 mb-3">
		<label for="publisher" class="form-label">Yayınevi</label>
		<input type="text" class="form-control" id="publisher" name="publisher"
		       value="{{ old('publisher', $bookReview->publisher ?? '') }}">
	</div>
	<div class="col-md-6 mb-3">
		<label for="publication_date" class="form-label">Yayın Tarihi</label>
		<input type="date" class="form-control" id="publication_date" name="publication_date"
		       value="{{ old('publication_date', $bookReview->publication_date ?? '') }}">
	</div>
</div>
<div class="row">
	<div class="col-md-6 mb-3">
		<label for="publication_place" class="form-label">Yayın Yeri</label>
		<input type="text" class="form-control" id="publication_place" name="publication_place"
		       value="{{ old('publication_place', $bookReview->publication_place ?? '') }}">
	</div>
	<div class="col-md-6 mb-3">
		<label for="buy_url" class="form-label">Satın Alma URL</label>
		<input type="url" class="form-control" id="buy_url" name="buy_url"
		       value="{{ old('buy_url', $bookReview->buy_url ?? '') }}" placeholder="https://...">
	</div>
</div>
{{-- MODIFIED: New optional fields END --}}


{{-- Review Content with EasyMDE --}}
<div class="mb-3">
	<label for="review_content_textarea" class="form-label">{{ __('default.Review Content') }}</label>
	<textarea class="form-control" id="review_content_textarea" name="review_content"
	          rows="15">{{ old('review_content', $bookReview->review_content ?? '') }}</textarea>
</div>

{{-- MODIFIED: Cover Image Selection changed to simple file upload --}}
<div class="mb-3">
	<label for="cover_image" class="form-label">{{ __('default.Cover Image') }}</label>
	<input type="file" class="form-control" id="cover_image" name="cover_image" accept="image/*">
	@if(isset($bookReview) && $bookReview->cover_image)
		<div class="mt-2">
			<small>Mevcut Kapak:</small><br>
			<img src="{{ $bookReview->cover_image }}" alt="Cover Image" class="img-fluid mt-1" style="max-width: 150px; border-radius: 5px;">
		</div>
	@endif
</div>


{{-- Categories with AI Suggestion --}}
<div class="mb-3">
	<label for="categories" class="form-label d-inline-block">{{ __('default.Categories') }}</label>
	<div id="generateCategory" class="ai-generate-button">{{__('default.Generate with AI')}}</div>
	<input type="text" class="form-control" id="categories" name="categories"
	       value="{{ old('categories', isset($bookReview) ? $bookReview->categories->pluck('name')->implode(', ') : '') }}">
</div>

{{-- Tags (Keywords) with AI Suggestion --}}
<div class="mb-3">
	<label for="tags" class="form-label d-inline-block">{{ __('default.Tags') }}</label>
	<div id="generateKeywords" class="ai-generate-button">{{__('default.Generate with AI')}}</div>
	<input type="text" class="form-control" id="tags" name="tags"
	       value="{{ old('tags', isset($bookReview) ? $bookReview->tags->pluck('name')->implode(', ') : '') }}"
	       placeholder="{{ __('default.Enter keywords separated by commas or spaces') }}">
</div>

{{-- Publication Status --}}
<div class="form-check mb-3">
	<input class="form-check-input" type="hidden" name="is_published" value="0"> {{-- sends 0 if checkbox is not checked --}}
	<input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1"
		{{ (isset($bookReview) && $bookReview->is_published) || old('is_published') ? 'checked' : '' }}>
	<label class="form-check-label" for="is_published">
		{{ __('default.Publish Review') }}
	</label>
</div>

{{-- Submit Button --}}
<button type="submit" class="btn btn-primary">{{ $submitButtonText }}</button>

@push('styles')
	{{-- Styles for Tagify and EasyMDE --}}
	<link rel="stylesheet" type="text/css" href="/css/tagify.css">
	<link rel="stylesheet" type="text/css" href="https://unpkg.com/easymde/dist/easymde.min.css">
	<style>
      .ai-generate-button {
          border: 1px solid #ccc;
          font-size: 12px;
          border-radius: 3px;
          padding: 2px 5px;
          margin-left: 5px;
          display: inline-block;
          cursor: pointer;
      }
      .ai-generate-button:hover {
          text-decoration: underline;
      }
	</style>
@endpush

@push('scripts')
	{{-- Scripts for Tagify, EasyMDE, and custom logic --}}
	<script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>
	<script src="/js/tagify.js"></script>
	<script>
		document.addEventListener("DOMContentLoaded", function () {
			// Initialize EasyMDE rich text editor
			const easyMDE = new EasyMDE({
				element: document.getElementById('review_content_textarea'),
				spellChecker: false,
				forceSync: true,
				status: ["lines", "words"],
				toolbar: ["bold", "italic", "heading", "|", "quote", "unordered-list", "ordered-list", "|", "preview", "guide"],
			});
			
			// Initialize Tagify for Categories
			var categoriesInput = document.querySelector('input[name=categories]');
			var categoriesTagify = new Tagify(categoriesInput, {
				delimiters: ", ",
				trim: true,
				dropdown: {enabled: 0} // No dropdown suggestions from a whitelist
			});
			
			// Initialize Tagify for Tags
			var tagsInput = document.querySelector('input[name=tags]');
			var tagsTagify = new Tagify(tagsInput, {
				delimiters: ", ",
				trim: true,
				dropdown: {enabled: 0}
			});
			
			// AI Suggestion for Categories
			$('#generateCategory').on('click', function () {
				const reviewText = easyMDE.value();
				if (!reviewText) {
					alert('{{__('default.Please enter some content first.')}}');
					return;
				}
				const button = $(this);
				button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
				$.ajax({
					url: '{{ route("book-reviews.generate-category") }}',
					method: 'POST',
					data: {main_text: reviewText, _token: '{{ csrf_token() }}'},
					success: function (response) {
						categoriesTagify.removeAllTags();
						categoriesTagify.addTags(response.categories);
					},
					error: function() {
						alert('{{__('default.Error generating category:')}}');
					},
					complete: function () {
						button.prop('disabled', false).html('{{__('default.Generate with AI')}}');
					}
				});
			});
			
			// AI Suggestion for Tags (Keywords)
			$('#generateKeywords').on('click', function () {
				const reviewText = easyMDE.value();
				if (!reviewText) {
					alert('{{__('default.Please enter some content first.')}}');
					return;
				}
				const button = $(this);
				button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
				$.ajax({
					url: '{{ route("book-reviews.generate-keywords") }}',
					method: 'POST',
					data: {main_text: reviewText, _token: '{{ csrf_token() }}'},
					success: function (response) {
						tagsTagify.removeAllTags();
						tagsTagify.addTags(response.keywords);
					},
					error: function() {
						alert('{{__('default.Error generating keywords:')}}');
					},
					complete: function () {
						button.prop('disabled', false).html('{{__('default.Generate with AI')}}');
					}
				});
			});
			
			// MODIFIED: Removed the modal image selection click handler.
			// The simple file input handles the upload automatically on form submission.
		});
	</script>
@endpush
