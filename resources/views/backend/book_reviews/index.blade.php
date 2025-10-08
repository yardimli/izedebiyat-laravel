@extends('layouts.app')
@section('title', __('default.Book Reviews'))
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
						<h5>{{ __('default.Book Reviews') }}</h5>
						<a href="{{ route('book-reviews.create') }}" class="btn btn-primary">
							{{ __('default.Create New Book Review') }}
						</a>
					</div>
					
					<div class="card">
						<div class="card-body">
							@if($bookReviews->isEmpty())
								<p class="text-center my-3">{{ __('default.No book reviews found') }}</p>
							@else
								<div class="table-responsive">
									<table class="table table-hover">
										<thead>
										<tr>
											<th>{{ __('default.Book Title') }}</th>
											<th>{{ __('default.Book Author') }}</th>
											<th>{{ __('default.Reviewer') }}</th>
											<th>{{ __('default.Status') }}</th>
											<th>{{ __('default.Created At') }}</th>
											<th>{{ __('default.Actions') }}</th>
										</tr>
										</thead>
										<tbody>
										@foreach($bookReviews as $review)
											<tr>
												<td>{{ $review->title }}</td>
												{{-- MODIFIED: Use the display_author accessor to get the correct author name --}}
												<td>{{ $review->display_author }}</td>
												<td>{{ $review->user->name }}</td>
												<td>
                                                <span class="badge bg-{{ $review->is_published ? 'success' : 'warning' }}">
                                                    {{ $review->is_published ? __('default.Published') : __('default.Draft') }}
                                                </span>
												</td>
												<td>{{ $review->created_at->format('d M Y') }}</td>
												<td>
													<div class="btn-group">
														<a href="{{ route('frontend.book-review.show', $review->slug) }}" class="btn btn-sm btn-info" target="_blank">{{ __('default.View') }}</a>
														<a href="{{ route('book-reviews.edit', $review->id) }}" class="btn btn-sm btn-primary">{{ __('default.Edit') }}</a>
														<form action="{{ route('book-reviews.destroy', $review->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('default.Are you sure?') }}')">
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
									{{ $bookReviews->links() }}
								</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</main>
@endsection
