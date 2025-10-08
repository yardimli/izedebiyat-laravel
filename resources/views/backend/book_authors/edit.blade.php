@extends('layouts.app')
@section('title', 'Kitap Yazarını Düzenle')

@section('content')
	<main>
		<div class="container mb-5" style="min-height: calc(88vh);">
			<div class="row mt-3">
				<div class="col-12 col-xl-8 col-lg-8 mx-auto">
					
					@if ($errors->any())
						<div class="alert alert-danger">
							<ul class="mb-0">
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif
					
					<h5 class="mb-4">Kitap Yazarını Düzenle</h5>
					
					<form method="POST" action="{{ route('book-authors.update', $bookAuthor->id) }}" enctype="multipart/form-data">
						@method('PUT')
						@include('backend.book_authors._form', [
								'submitButtonText' => 'Yazarı Güncelle'
						])
					</form>
				</div>
			</div>
		</div>
	</main>
	@include('layouts.footer')
@endsection
