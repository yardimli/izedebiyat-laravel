@extends('layouts.app')
@section('title', 'Kitap Yazarları')
@section('content')
	<main>
		<div class="container" style="min-height: calc(88vh);">
			@if(session('success'))
				<div class="alert alert-success alert-dismissible fade show" role="alert">
					{{ session('success') }}
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>
			@endif
			
			<div class="row mt-3">
				<div class="col-12">
					<div class="d-flex justify-content-between align-items-center mb-4">
						<h5>Kitap Yazarları</h5>
						<a href="{{ route('book-authors.create') }}" class="btn btn-primary">
							Yeni Yazar Ekle
						</a>
					</div>
					
					<div class="card">
						<div class="card-body">
							@if($authors->isEmpty())
								<p class="text-center my-3">Hiç yazar bulunamadı.</p>
							@else
								<div class="table-responsive">
									<table class="table table-hover">
										<thead>
										<tr>
											<th style="width: 10%;">Fotoğraf</th>
											<th>Yazar Adı</th>
											<th>Oluşturulma Tarihi</th>
											<th>İşlemler</th>
										</tr>
										</thead>
										<tbody>
										@foreach($authors as $author)
											<tr>
												<td>
													<img src="{{ $author->picture ?? '/assets/images/avatar/placeholder.jpg' }}" alt="{{ $author->name }}" class="img-fluid rounded" style="max-width: 50px;">
												</td>
												<td>{{ $author->name }}</td>
												<td>{{ $author->created_at->format('d M Y') }}</td>
												<td>
													<div class="btn-group">
														<a href="{{ route('book-authors.edit', $author->id) }}" class="btn btn-sm btn-primary">{{ __('default.Edit') }}</a>
														<form action="{{ route('book-authors.destroy', $author->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('default.Are you sure?') }}')">
															@csrf
															@method('DELETE')
															<button type="submit" class="btn btn-sm btn-danger">{{ __('default.Delete') }}</button>
														</form>
													</div>
												</td>
											</tr>
										@endforeach
										</tbody>
									</table>
								</div>
								<div class="d-flex justify-content-center mt-4">
									{{ $authors->links() }}
								</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</main>
@endsection
