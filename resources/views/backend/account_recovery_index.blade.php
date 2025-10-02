@extends('layouts.app')
@section('title', 'Hesap Kurtarma Talepleri')
@section('content')
	<main>
		<div class="container mt-5" style="min-height: calc(88vh);">
			@if(session('success'))
				<div class="alert alert-success">{{ session('success') }}</div>
			@endif
			<div class="d-flex justify-content-between align-items-center mb-4">
				<h5>Hesap Kurtarma Talepleri</h5>
			</div>
			<div class="card">
				<div class="card-body">
					@if($requests->isEmpty())
						<p class="text-center my-3">Bekleyen talep bulunamadı.</p>
					@else
						<div class="table-responsive">
							<table class="table table-hover">
								<thead>
								<tr>
									<th>Ad Soyad</th>
									<th>İletişim E-posta</th>
									<th>Durum</th>
									<th>Talep Tarihi</th>
									<th>İşlemler</th>
								</tr>
								</thead>
								<tbody>
								@foreach($requests as $request)
									<tr>
										<td>{{ $request->real_name }}</td>
										<td>{{ $request->contact_email }}</td>
										<td>
											{{-- MODIFIED: Added handling for approved_deleted status --}}
											<span class="badge bg-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'approved' ? 'success' : ($request->status === 'approved_deleted' ? 'dark' : 'danger')) }}">
                                                {{ ucfirst($request->status) }}
                                            </span>
										</td>
										<td>{{ $request->created_at->format('d M Y H:i') }}</td>
										<td>
											<a href="{{ route('admin.account-recovery.show', $request->id) }}" class="btn btn-sm btn-info">Detayları Gör</a>
										</td>
									</tr>
								@endforeach
								</tbody>
							</table>
						</div>
						<div class="d-flex justify-content-center mt-4">
							{{ $requests->links() }}
						</div>
					@endif
				</div>
			</div>
		</div>
	</main>
@endsection
