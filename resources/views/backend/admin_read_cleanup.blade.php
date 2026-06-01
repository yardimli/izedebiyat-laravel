@extends('layouts.app')
@section('title', 'IzEdebiyat - Read Cleanup')
@section('content')
	<main>
		<div class="container mt-5" style="min-height: calc(88vh);">
			@if($errors->any())
				<div class="alert alert-danger alert-dismissible fade show" role="alert">
					{{ $errors->first() }}
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>
			@endif

			@if($deletedReads !== null)
				<div class="alert alert-success alert-dismissible fade show" role="alert">
					{{ number_format($deletedReads) }} suspicious read records removed. Affected article read counts were recalculated.
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>
			@endif

			<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
				<h5 class="mb-0">Read Cleanup</h5>
				<form action="{{ route('admin.read-cleanup.index') }}" method="GET" class="d-flex flex-wrap gap-2">
					<div class="input-group input-group-sm" style="width: 190px;">
						<span class="input-group-text">Hits</span>
						<input type="number" min="1" max="100000" class="form-control" name="threshold" value="{{ $filters['threshold'] }}">
					</div>
					<div class="input-group input-group-sm" style="width: 190px;">
						<span class="input-group-text">Hours</span>
						<input type="number" min="1" max="720" class="form-control" name="window_hours" value="{{ $filters['window_hours'] }}">
					</div>
					<button class="btn btn-sm btn-primary" type="submit">Scan</button>
				</form>
			</div>

			@if($changes->isNotEmpty())
				<div class="card mb-4">
					<div class="card-body">
						<h6 class="mb-3">Articles that lost more than 50 reads</h6>
						<div class="table-responsive">
							<table class="table table-hover align-middle">
								<thead>
								<tr>
									<th>Article</th>
									<th>Author</th>
									<th>Old</th>
									<th>New</th>
									<th>Lost</th>
								</tr>
								</thead>
								<tbody>
								@foreach($changes as $change)
									<tr>
										<td>
											<a href="{{ route('article', $change->article->slug) }}" target="_blank">{{ strip_tags($change->article->title) }}</a>
										</td>
										<td>{{ $change->article->name ?: '-' }}</td>
										<td>{{ number_format($change->old_read_count) }}</td>
										<td>{{ number_format($change->new_read_count) }}</td>
										<td>{{ number_format($change->lost_read_count) }}</td>
									</tr>
								@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>
			@endif

			<div class="card">
				<div class="card-body">
					<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
						<div>
							<h6 class="mb-1">Suspicious IPs</h6>
							<div class="small text-muted">
								More than {{ number_format($filters['threshold']) }} reads in the last {{ $filters['window_hours'] }} hours.
							</div>
						</div>
						@if($suspiciousIps->isNotEmpty())
							<form action="{{ route('admin.read-cleanup.destroy') }}" method="POST" onsubmit="return confirm('Remove reads from these suspicious IPs and recalculate affected article counts?')">
								@csrf
								@method('DELETE')
								<input type="hidden" name="threshold" value="{{ $filters['threshold'] }}">
								<input type="hidden" name="window_hours" value="{{ $filters['window_hours'] }}">
								<button type="submit" class="btn btn-danger btn-sm">Remove Matching Reads</button>
							</form>
						@endif
					</div>

					@if($suspiciousIps->isEmpty())
						<p class="text-center my-3">No suspicious IPs found for this window.</p>
					@else
						<div class="table-responsive">
							<table class="table table-hover align-middle">
								<thead>
								<tr>
									<th>IP Address</th>
									<th>Hits</th>
									<th>Articles</th>
									<th>First Seen</th>
									<th>Last Seen</th>
								</tr>
								</thead>
								<tbody>
								@foreach($suspiciousIps as $ip)
									<tr>
										<td><code>{{ $ip->ip_address }}</code></td>
										<td>{{ number_format($ip->hits) }}</td>
										<td>{{ number_format($ip->article_count) }}</td>
										<td>{{ \Carbon\Carbon::parse($ip->first_seen)->format('d M Y H:i') }}</td>
										<td>{{ \Carbon\Carbon::parse($ip->last_seen)->format('d M Y H:i') }}</td>
									</tr>
								@endforeach
								</tbody>
							</table>
						</div>
					@endif
				</div>
			</div>
		</div>
	</main>
	@include('layouts.footer')
@endsection
