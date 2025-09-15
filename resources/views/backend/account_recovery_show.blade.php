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
								<h6>Kullanıcı Ara ve Onayla</h6>
								
								<!-- MODIFIED: Replaced automatic user list with a search form -->
								<form action="{{ route('admin.account-recovery.show', $request->id) }}" method="GET" class="mb-3">
									<div class="input-group">
										<input type="text" name="search" class="form-control" placeholder="İsim veya e-posta ile ara..." value="{{ $searchQuery ?? '' }}">
										<button class="btn btn-outline-secondary" type="submit">Ara</button>
									</div>
								</form>
								
								<!-- MODIFIED: Display search results and approval form -->
								@if(isset($searchQuery))
									@if($possible_users->isEmpty())
										<div class="alert alert-warning">Bu bilgilere uyan kullanıcı bulunamadı.</div>
									@else
										<form action="{{ route('admin.account-recovery.approve', $request->id) }}" method="POST">
											@csrf
											<div class="mb-3">
												<label class="form-label">Onaylanacak Kullanıcıyı Seçin</label>
												<div class="list-group">
													@foreach($possible_users as $user)
														<label class="list-group-item">
															<input class="form-check-input me-1" type="radio" name="user_id" value="{{ $user->id }}" required>
															<a href="{{ route('user', $user->slug) }}" target="_blank">{{ $user->name }}</a> ({{ $user->email }})
														</label>
													@endforeach
												</div>
											</div>
											<div class="mb-3">
												<label for="notes_approve" class="form-label">Notlar (İsteğe Bağlı)</label>
												<textarea name="notes" id="notes_approve" class="form-control" rows="3"></textarea>
											</div>
											<button type="submit" class="btn btn-success">Talebi Onayla, E-postayı Güncelle ve Şifre Gönder</button>
										</form>
									@endif
								@else
									<p class="text-muted">Lütfen onaylamak için bir kullanıcı arayın.</p>
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
