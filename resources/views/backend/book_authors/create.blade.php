@extends('layouts.app')
@section('title', 'Yeni Kitap Yazarı Ekle')

@section('content')
	<main>
		<div class="container mb-5" style="min-height: calc(88vh);">
			<div class="row mt-3">
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
					
					<h5 class="mb-4">Yeni Kitap Yazarı Ekle</h5>
					
					{{-- The Form --}}
					<form method="POST" action="{{ route('book-authors.store') }}" enctype="multipart/form-data">
						@include('backend.book_authors._form', [
								'bookAuthor' => new \App\Models\BookAuthor(),
								'submitButtonText' => 'Yazarı Oluştur'
						])
					</form>
				
				</div>
			</div>
		</div>
	</main>
	
	@include('layouts.footer')
@endsection
