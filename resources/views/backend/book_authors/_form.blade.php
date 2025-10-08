@csrf
{{-- Author Name --}}
<div class="mb-3">
	<label for="name" class="form-label">Yazar Adı</label>
	<input type="text" class="form-control" id="name" name="name"
	       value="{{ old('name', $bookAuthor->name ?? '') }}" required>
</div>

{{-- Author Picture --}}
<div class="mb-3">
	<label for="picture" class="form-label">Yazar Fotoğrafı</label>
	<input type="file" class="form-control" id="picture" name="picture" accept="image/*">
	@if(isset($bookAuthor) && $bookAuthor->picture)
		<div class="mt-2">
			<small>Mevcut Fotoğraf:</small><br>
			<img src="{{ $bookAuthor->picture }}" alt="{{ $bookAuthor->name }}" class="img-fluid mt-1" style="max-width: 150px; border-radius: 5px;">
		</div>
	@endif
</div>

{{-- Biography --}}
<div class="mb-3">
	<label for="biography_textarea" class="form-label">Biyografi</label>
	<textarea class="form-control" id="biography_textarea" name="biography"
	          rows="10">{{ old('biography', $bookAuthor->biography ?? '') }}</textarea>
</div>

{{-- Bibliography --}}
<div class="mb-3">
	<label for="bibliography_textarea" class="form-label">Bibliyografya (Eserleri)</label>
	<textarea class="form-control" id="bibliography_textarea" name="bibliography"
	          rows="10">{{ old('bibliography', $bookAuthor->bibliography ?? '') }}</textarea>
</div>


{{-- Submit Button --}}
<button type="submit" class="btn btn-primary">{{ $submitButtonText }}</button>

@push('styles')
	{{-- Styles for EasyMDE --}}
	<link rel="stylesheet" type="text/css" href="https://unpkg.com/easymde/dist/easymde.min.css">
@endpush

@push('scripts')
	{{-- Scripts for EasyMDE --}}
	<script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>
	<script>
		document.addEventListener("DOMContentLoaded", function () {
			// Initialize EasyMDE for Biography
			new EasyMDE({
				element: document.getElementById('biography_textarea'),
				spellChecker: false,
				forceSync: true,
				status: ["lines", "words"],
				toolbar: ["bold", "italic", "heading", "|", "quote", "unordered-list", "ordered-list", "|", "preview", "guide"],
			});
			
			// Initialize EasyMDE for Bibliography
			new EasyMDE({
				element: document.getElementById('bibliography_textarea'),
				spellChecker: false,
				forceSync: true,
				status: ["lines", "words"],
				toolbar: ["bold", "italic", "heading", "|", "quote", "unordered-list", "ordered-list", "|", "preview", "guide"],
			});
		});
	</script>
@endpush
