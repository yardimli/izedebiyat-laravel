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
												<td>{{ \App\Helpers\MyHelper::timeString($article->created_at)}}</td>
												<td>
													<div class="btn-group">
														<a href="{{ route('yapit', $article->slug) }}"
														   class="btn btn-sm btn-info">
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
									{{ $articles->links() }}
								</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</main>
@endsection
