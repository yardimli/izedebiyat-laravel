@extends('layouts.app')
@section('title', 'Talep Detayı')
@section('content')
	<main>
		<div class="container mt-5" style="min-height: calc(88vh);">
			<a href="{{ route('admin.account-recovery.index') }}" class="btn btn-secondary mb-4">&larr; Taleplere Geri Dön</a>
			<h5>Talep Detayı #{{ $request->id }}</h5>
			
			@if(session('error'))
				<div class="alert alert-danger">{{ session('error') }}</div>
			@endif
			
			<div class="row">
				<div class="col-md-6">
					<div class="card">
						<div class="card-header">Talep Bilgileri</div>
						<div class="card-body">
							<p><strong>Ad Soyad:</strong> {{ $request->real_name }}</p>
							<p><strong>İletişim E-posta:</strong> {{ $request->contact_email }}</p>
							<p><strong>Hatırlanan E-postalar:</strong></p>
							<pre>{{ $request->remembered_emails }}</pre>
							<p><strong>Durum:</strong> <span class="badge bg-{{ $request->status === 'pending' ? 'warning' : ($request->status === 'approved' ? 'success' : 'danger') }}">{{ ucfirst($request->status) }}</span></p>
							<p><strong>Talep Tarihi:</strong> {{ $request->created_at->format('d M Y H:i') }}</p>
						</div>
					</div>
					<div class="card mt-4">
						<div class="card-header">Kimlik Belgesi</div>
						<div class="card-body">
							<a href="{{ Storage::url($request->id_document_path) }}" target="_blank">
								<img src="{{ Storage::url($request->id_document_path) }}" class="img-fluid" alt="ID Document">
							</a>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					@if($request->status === 'pending')
						<div class="card">
							<div class="card-header">İşlemler</div>
							<div class="card-body">
								<h6>Eşleşen Kullanıcıyı Seçin ve Onaylayın</h6>
								@php
									$remembered_emails = array_map('trim', explode(',', $request->remembered_emails));
									$possible_users = \App\Models\User::where('name', 'like', '%' . $request->real_name . '%')
											->orWhereIn('email', $remembered_emails)
											->get();
								@endphp
								
								@if($possible_users->isEmpty())
									<div class="alert alert-warning">Bu bilgilere uyan kullanıcı bulunamadı.</div>
								@else
									<form action="{{ route('admin.account-recovery.approve', $request->id) }}" method="POST">
										@csrf
										<div class="mb-3">
											<label for="user_id" class="form-label">Onaylanacak Kullanıcı</label>
											<select name="user_id" id="user_id" class="form-select" required>
												<option value="">Kullanıcı Seçin...</option>
												@foreach($possible_users as $user)
													<option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
												@endforeach
											</select>
										</div>
										<div class="mb-3">
											<label for="notes_approve" class="form-label">Notlar (İsteğe Bağlı)</label>
											<textarea name="notes" id="notes_approve" class="form-control" rows="3"></textarea>
										</div>
										<button type="submit" class="btn btn-success">Talebi Onayla ve Şifre Gönder</button>
									</form>
								@endif
								
								<hr>
								
								<h6>Talebi Reddet</h6>
								<form action="{{ route('admin.account-recovery.reject', $request->id) }}" method="POST">
									@csrf
									<div class="mb-3">
										<label for="notes_reject" class="form-label">Reddetme Nedeni (İsteğe Bağlı)</label>
										<textarea name="notes" id="notes_reject" class="form-control" rows="3"></textarea>
									</div>
									<button type="submit" class="btn btn-danger">Talebi Reddet</button>
								</form>
							</div>
						</div>
					@else
						<div class="card">
							<div class="card-header">İşlem Sonucu</div>
							<div class="card-body">
								<p>Bu talep daha önce işleme alınmış.</p>
								@if($request->user)
									<p><strong>İlişkili Kullanıcı:</strong> <a href="{{ route('user', $request->user->slug) }}" target="_blank">{{ $request->user->name }}</a></p>
								@endif
								<p><strong>Notlar:</strong></p>
								<p>{{ $request->notes ?? 'Not bırakılmamış.' }}</p>
							</div>
						</div>
					@endif
				</div>
			</div>
		</div>
	</main>
@endsection
