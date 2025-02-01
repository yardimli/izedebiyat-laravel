@extends('layouts.app')
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
						<h5>{{ __('default.Articles') }}</h5>
						<a href="{{ route('articles.create') }}" class="btn btn-primary">
							{{ __('default.Create New Article') }}
						</a>
					</div>
					
					<!-- Filter Form -->
					<div class="card mb-4">
						<div class="card-body">
							<form method="GET" class="row g-3">
								<div class="col-md-4">
									<input type="text" class="form-control" name="search"
									       placeholder="{{ __('default.Search articles...') }}"
									       value="{{ request('search') }}">
								</div>
								<div class="col-md-3">
									<select class="form-select" name="status">
										<option value="">{{ __('default.All Status') }}</option>
										<option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>
											{{ __('default.Published') }}
										</option>
										<option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>
											{{ __('default.Draft') }}
										</option>
									</select>
								</div>
								<div class="col-md-3">
									<select class="form-select" name="sort">
										<option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>
											{{ __('default.Newest First') }}
										</option>
										<option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>
											{{ __('default.Oldest First') }}
										</option>
										<option value="read_count" {{ request('sort') === 'read_count' ? 'selected' : '' }}>
											{{ __('default.Most Read') }}
										</option>
									</select>
								</div>
								<div class="col-md-2">
									<button type="submit" class="btn btn-primary w-100">
										{{ __('default.Filter') }}
									</button>
								</div>
							</form>
						</div>
					</div>
					
					<!-- Articles List -->
					<div class="card">
						<div class="card-body">
							@if($articles->isEmpty())
								<p class="text-center my-3">{{ __('default.No articles found') }}</p>
							@else
								<div class="table-responsive">
									<table class="table table-hover">
										<thead>
										<tr>
											<th>{{ __('default.Title') }}</th>
											<th>{{ __('default.Status') }}</th>
											<th>{{ __('default.Read Count') }}</th>
											<th>{{ __('default.Created At') }}</th>
											<th>{{ __('default.Actions') }}</th>
										</tr>
										</thead>
										<tbody>
										@foreach($articles as $article)
											<tr>
												<td>{!! $article->title !!}</td>
												<td>
                                                <span class="badge bg-{{ $article->is_published ? 'success' : 'warning' }}">
                                                    {{ $article->is_published ? __('default.Published') : __('default.Draft') }}
                                                </span>
												</td>
												<td>{{ number_format($article->read_count) }}</td>
												<td>{{ \App\Helpers\MyHelper::timeString($article->created_at)}}</td>
												<td>
													<div class="btn-group">
														<a href="{{ route('article', $article->slug) }}" class="btn btn-sm btn-info">
															{{ __('default.Read') }}
														</a>
														<a href="{{ route('articles.edit', \App\Helpers\IdHasher::encode($article->id)) }}"
														   class="btn btn-sm btn-primary">
															{{ __('default.Edit') }}
														</a>
														<form action="{{ route('articles.destroy', \App\Helpers\IdHasher::encode($article->id)) }}"
														      method="POST" class="d-inline"
														      onsubmit="return confirm('{{ __('default.Are you sure you want to delete this article?') }}')">
															@csrf
															@method('DELETE')
															<button type="submit" class="btn btn-sm btn-danger">
																{{ __('default.Delete') }}
															</button>
														</form>
													</div>
												</td>
											</tr>
										@endforeach
										</tbody>
									</table>
								</div>
								<!-- Pagination -->
								<div class="d-flex justify-content-center mt-4">
									{{ $articles->withQueryString()->links() }}
								</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</main>
@endsection
