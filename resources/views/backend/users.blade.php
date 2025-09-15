@extends('layouts.app')
@section('title', 'İzEdebiyat - Kullanıcılar')
@section('content')
	<!-- **************** MAIN CONTENT START **************** -->
	<main>
		<!-- Container START -->
		<div class="container mt-5">
			<div class="row align-items-center">
				{{-- Search Form --}}
				<form action="/users" method="GET" class="col-md-8 col-lg-9">
					<div class="input-group mb-3">
						<input name="search" type="text" class="form-control" placeholder="Search users" value="{{ request('search') }}">
						<button class="btn btn-primary" type="submit">Search</button>
					</div>
				</form>
				{{-- MODIFIED: New Button Added for Account Recovery --}}
				@if (Auth::user()->member_type === 1)
					<div class="col-md-4 col-lg-3 text-md-end mb-3">
						<a href="{{ route('admin.account-recovery.index') }}" class="btn btn-warning">Hesap Kurtarma Talepleri</a>
					</div>
				@endif
			</div>
			
			<table class="table table-bordered">
				<thead>
				<tr>
					<th style="width: 50px"></th>
					<th>Name</th>
					<th>Email</th>
					<th>Stories</th>
					<th>Last Story</th>
					<th>Created</th>
					<th>Actions</th>
				</tr>
				</thead>
				<tbody>
				@foreach($users as $user)
					<tr style="background-color: #222;">
						<td class="text-center">
							@if($user->avatar)
								<img src="{{ !empty($user->avatar) ? Storage::url($user->avatar) : '/assets/images/avatar/placeholder.jpg' }}" class="rounded-circle" width="40" height="40">
							@else
								<div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
								     style="width: 40px; height: 40px; font-size: 18px;">
									{{ strtoupper(substr($user->name, 0, 1)) }}
								</div>
							@endif
						</td>
						<td>{{ $user->name }}</td>
						<td>{{ $user->email }}</td>
						<td class="text-center">{{ $user->story_count }}</td>
						<td>
							@if($user->last_story_date)
								{{ \Carbon\Carbon::parse($user->last_story_date)->format('d M Y') }}
							@else
								-
							@endif
						</td>
						<td>{{ $user->created_at->format('d M Y') }}</td>
						<td>
							<form action="{{ route('users-login-as') }}" method="POST">
								@csrf
								<input type="hidden" name="user_id" value="{{ $user->id }}"/>
								<button type="submit" class="btn btn-primary btn-sm">Login As</button>
							</form>
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>
			
			<!-- Pagination Links -->
			<?php $users = $users->appends([
				'purchase' => $_GET['purchase'] ?? 'no',
				'written' => $_GET['written'] ?? 'no',
				'search' => $_GET['search'] ?? ''
			]); ?>
			
			<div class="d-flex justify-content-center">
				@if ($users->onFirstPage())
					<button class="btn btn-secondary mx-1" disabled>First</button>
				@else
					<a href="{{ $users->url(1) }}" class="btn btn-primary mx-1">First</a>
				@endif
				
				@if ($users->onFirstPage())
					<button class="btn btn-secondary mx-1" disabled>Previous</button>
				@else
					<a href="{{ $users->previousPageUrl() }}" class="btn btn-primary mx-1">Previous</a>
				@endif
				
				@foreach(range(1, $users->lastPage()) as $i)
					@if ($i >= $users->currentPage() - 2 && $i <= $users->currentPage() + 2)
						@if ($i == $users->currentPage())
							<button class="btn btn-secondary mx-1">{{ $i }}</button>
						@else
							<a href="{{ $users->url($i) }}" class="btn btn-primary mx-1">{{ $i }}</a>
						@endif
					@endif
				@endforeach
				
				@if ($users->hasMorePages())
					<a href="{{ $users->nextPageUrl() }}" class="btn btn-primary mx-1">Next</a>
				@else
					<button class="btn btn-secondary mx-1" disabled>Next</button>
				@endif
				
				@if ($users->currentPage() === $users->lastPage())
					<button class="btn btn-secondary mx-1" disabled>Last</button>
				@else
					<a href="{{ $users->url($users->lastPage()) }}" class="btn btn-primary mx-1">Last</a>
				@endif
			</div>
			
			<p>Viewing {{ $users->firstItem() }} - {{ $users->lastItem() }} out of {{ $users->total() }}</p>
		</div>
	</main>
	<!-- **************** MAIN CONTENT END **************** -->
	@include('layouts.footer')
@endsection

@push('scripts')
	<script>
		var current_page = 'privacy';
		$(document).ready(function () {
		});
	</script>
@endpush
