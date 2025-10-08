<?php
// resources/views/frontend/book_reviews/create_submission.blade.php:

@extends('layouts.app-frontend')

@section('title', __('default.Submit Your Book for Review'))
@section('body-class', 'home single')

@section('content')
	<main id="content">
		<div class="container-lg">
			<div class="row">
				<div class="col-12 col-xl-8 col-lg-8 mx-auto">
					<h4 class="spanborder">
						<span>{{ __('default.Submit Your Book for Review') }}</span>
					</h4>
					
					{{-- Info Box --}}
					<div class="alert alert-info d-flex align-items-center" role="alert">
						<i class="bi bi-info-circle-fill me-3 fs-4"></i>
						<div>
							Bu form aracılığıyla kitabınızın sitemizde tanıtımını talep edebilirsiniz. Formu doldurduktan sonra, el yazmanızı gönderme ve diğer adımlar hakkında bilgi vermek için sizinle iletişime geçeceğiz. Bu hizmet ücretlidir; maliyet ve ödeme detayları size e-posta ile bildirilecektir.
						</div>
					</div>
					
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
					
					<form method="POST" action="{{ route('frontend.book-reviews.store-submission') }}" enctype="multipart/form-data">
						@csrf
						
						<h5 class="mt-4">Kitap Bilgileri</h5>
						<hr>
						{{-- Book Title --}}
						<div class="mb-3">
							<label for="book_title" class="form-label">Kitap Adı</label>
							<input type="text" class="form-control" id="book_title" name="book_title" value="{{ old('book_title') }}" required>
						</div>
						
						{{-- Book Cover Image --}}
						<div class="mb-3">
							<label for="book_cover_image" class="form-label">Kapak Görseli</label>
							<input type="file" class="form-control" id="book_cover_image" name="book_cover_image" accept="image/*" required>
						</div>
						
						{{-- Book Description --}}
						<div class="mb-3">
							<label for="book_description" class="form-label">Kitap Açıklaması / Tanıtım Yazısı</label>
							<textarea class="form-control" id="book_description" name="book_description" rows="10" required>{{ old('book_description') }}</textarea>
						</div>
						
						<h5 class="mt-5">Yazar Bilgileri</h5>
						<hr>
						{{-- Author Name --}}
						<div class="mb-3">
							<label for="author_name" class="form-label">Yazar Adı</label>
							<input type="text" class="form-control" id="author_name" name="author_name" value="{{ old('author_name') }}" required>
							<small class="form-text text-muted">Eğer yazar sistemde mevcutsa, bilgilerini yine de giriniz. Mevcut yazar bilgileri kullanılacaktır.</small>
						</div>
						
						{{-- Author Picture --}}
						<div class="mb-3">
							<label for="author_picture" class="form-label">Yazar Fotoğrafı (İsteğe Bağlı)</label>
							<input type="file" class="form-control" id="author_picture" name="author_picture" accept="image/*">
						</div>
						
						{{-- Author Biography --}}
						<div class="mb-3">
							<label for="author_biography" class="form-label">Yazar Biyografisi</label>
							<textarea class="form-control" id="author_biography" name="author_biography" rows="5" required>{{ old('author_biography') }}</textarea>
						</div>
						
						{{-- Author Bibliography --}}
						<div class="mb-3">
							<label for="author_bibliography" class="form-label">Yazarın Diğer Eserleri (Bibliyografya - İsteğe Bağlı)</label>
							<textarea class="form-control" id="author_bibliography" name="author_bibliography" rows="5">{{ old('author_bibliography') }}</textarea>
						</div>
						
						{{-- Submit Button --}}
						<button type="submit" class="btn btn-primary mt-3">{{ __('default.Submit Book') }}</button>
					</form>
				</div>
			</div>
		</div>
	</main>
@endsection

@push('styles')
	<style>
      /* Add some spacing for the form */
      form {
          margin-top: 2rem;
          margin-bottom: 2rem;
      }
	</style>
@endpush
