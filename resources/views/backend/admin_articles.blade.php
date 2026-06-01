@extends('layouts.app')
@section('title', 'IzEdebiyat - Admin Articles')
@section('content')
	<main>
		<div class="container mt-5" style="min-height: calc(88vh);">
			@if(session('success'))
				<div class="alert alert-success alert-dismissible fade show" role="alert">
					{{ session('success') }}
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>
			@endif

			<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
				<h5 class="mb-0">Yeni Yazilar</h5>
				<form action="{{ route('admin.articles.index') }}" method="GET" class="flex-grow-1" style="max-width: 620px;">
					<div class="input-group">
						<input name="search" type="text" class="form-control" placeholder="Title, subtitle, or text ara" value="{{ request('search') }}">
						<button class="btn btn-primary" type="submit">Search</button>
						@if(request('search'))
							<a class="btn btn-outline-secondary" href="{{ route('admin.articles.index') }}">Clear</a>
						@endif
					</div>
				</form>
			</div>

			<div class="card">
				<div class="card-body">
					@if($articles->isEmpty())
						<p class="text-center my-3">No articles found.</p>
					@else
						<div class="table-responsive">
							<table class="table table-hover align-middle">
								<thead>
								<tr>
									<th style="min-width: 320px;">Article</th>
									<th>Author</th>
									<th>Created</th>
									<th>Status</th>
									<th style="min-width: 250px;">Flags</th>
									<th>Actions</th>
								</tr>
								</thead>
								<tbody>
								@foreach($articles as $article)
									<tr>
										<td>
											<div class="fw-semibold">{{ strip_tags($article->title) }}</div>
											@if($article->subtitle)
												<div class="small text-muted">{{ strip_tags($article->subtitle) }}</div>
											@endif
											<div class="small text-muted mt-1">
												{{ \Illuminate\Support\Str::limit(strip_tags($article->main_text), 140) }}
											</div>
											<div class="small text-muted mt-1">
												{{ $article->parent_category_name }}@if($article->category_name) / {{ $article->category_name }}@endif
											</div>
										</td>
										<td>
											@if($article->user)
												<a href="{{ route('user', $article->user->slug) }}" target="_blank">{{ $article->user->name }}</a>
											@else
												{{ $article->name ?: '-' }}
											@endif
										</td>
										<td>{{ $article->created_at ? $article->created_at->format('d M Y H:i') : '-' }}</td>
										<td>
											<div class="d-flex flex-column gap-1">
												<span class="badge bg-{{ $article->is_published ? 'success' : 'warning' }}">
													{{ $article->is_published ? 'Published' : 'Draft' }}
												</span>
												<span class="badge bg-{{ $article->approved ? 'success' : 'secondary' }}">
													{{ $article->approved ? 'Approved' : 'Not Approved' }}
												</span>
											</div>
										</td>
										<td>
											<form action="{{ route('admin.articles.flags', $article) }}" method="POST" class="d-flex flex-column gap-2">
												@csrf
												@method('PATCH')
												<input type="hidden" name="page" value="{{ request('page') }}">
												<input type="hidden" name="search" value="{{ request('search') }}">
												<div class="d-flex gap-2">
													<select class="form-select form-select-sm" name="is_published">
														<option value="1" {{ $article->is_published ? 'selected' : '' }}>Published</option>
														<option value="0" {{ !$article->is_published ? 'selected' : '' }}>Draft</option>
													</select>
													<select class="form-select form-select-sm" name="approved">
														<option value="1" {{ $article->approved ? 'selected' : '' }}>Approved</option>
														<option value="0" {{ !$article->approved ? 'selected' : '' }}>Not Approved</option>
													</select>
												</div>
												<button type="submit" class="btn btn-sm btn-primary align-self-start">Save</button>
											</form>
										</td>
										<td>
											<div class="d-flex flex-wrap gap-2">
												@if($article->is_published && $article->approved && !$article->deleted)
													<a href="{{ route('article', $article->slug) }}" class="btn btn-sm btn-info" target="_blank">Live URL</a>
												@else
													<span class="btn btn-sm btn-secondary disabled">Not Live</span>
												@endif
												<form action="{{ route('admin.articles.destroy', $article) }}" method="POST" onsubmit="return confirm('Delete this article?')">
													@csrf
													@method('DELETE')
													<input type="hidden" name="page" value="{{ request('page') }}">
													<input type="hidden" name="search" value="{{ request('search') }}">
													<button type="submit" class="btn btn-sm btn-danger">Delete</button>
												</form>
											</div>
										</td>
									</tr>
								@endforeach
								</tbody>
							</table>
						</div>

						<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4">
							<div class="small text-muted">
								Viewing {{ $articles->firstItem() }} - {{ $articles->lastItem() }} out of {{ $articles->total() }}
							</div>
							{{ $articles->links() }}
						</div>
					@endif
				</div>
			</div>
		</div>
	</main>
	@include('layouts.footer')
@endsection
