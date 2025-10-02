@extends('layouts.app-frontend')

@section('title', 'Hesap Kurtarma')

@section('content')
	<div class="container my-5">
		<div class="row justify-content-center">
			<div class="col-lg-8">
				<div class="card">
					<div class="card-header">
						<h1 class="h4 mb-0">Hesabıma Erişemiyorum</h1>
					</div>
					<div class="card-body">
						<p class="text-muted">Hesabınıza erişiminizi kaybettiyseniz, lütfen aşağıdaki formu doldurun. Ekibimiz talebinizi inceleyecek ve size yardımcı olacaktır.</p>
						
						@if(session('success'))
							<div class="alert alert-success">{{ session('success') }}</div>
						@endif
						
						@if ($errors->any())
							<div class="alert alert-danger">
								<ul class="mb-0">
									@foreach ($errors->all() as $error)
										<li>{{ $error }}</li>
									@endforeach
								</ul>
							</div>
						@endif
						
						<form action="{{ route('account-recovery.store') }}" method="POST" enctype="multipart/form-data">
							@csrf
							<div class="mb-3">
								<label for="real_name" class="form-label">Adınız ve Soyadınız</label>
								<input type="text" class="form-control" id="real_name" name="real_name" value="{{ old('real_name') }}" required>
								<div class="form-text">Lütfen hesabınızı oluştururken kullandığınız gerçek adınızı ve soyadınızı girin.</div>
							</div>
							
							<div class="mb-3">
								<label for="remembered_emails" class="form-label">Hatırladığınız E-posta Adresleri</label>
								<textarea class="form-control" id="remembered_emails" name="remembered_emails" rows="3" required>{{ old('remembered_emails') }}</textarea>
								<div class="form-text">Hesabınızla ilişkili olabileceğini düşündüğünüz tüm e-posta adreslerini (erişiminiz olmasa bile) virgülle ayırarak yazın.</div>
							</div>
							
							<div class="mb-3">
								<label for="profile_url" class="form-label">İzEdebiyat Profil Sayfanızın Adresi (İsteğe Bağlı)</label>
								<input type="url" class="form-control" id="profile_url" name="profile_url" value="{{ old('profile_url') }}" placeholder="https://www.izedebiyat.com/yazar/kullanici-adiniz">
								<div class="form-text">Erişmeye çalıştığınız profil sayfanızın tam adresini biliyorsanız buraya yapıştırın.</div>
							</div>
							
							<div class="mb-3">
								<label for="contact_email" class="form-label">İletişim Kurulacak E-posta Adresi</label>
								<input type="email" class="form-control" id="contact_email" name="contact_email" value="{{ old('contact_email') }}" required>
								<div class="form-text">Size ulaşabileceğimiz ve yeni şifrenizi göndereceğimiz güncel bir e-posta adresi girin.</div>
							</div>
							
							<div class="mb-3">
								<label for="id_document" class="form-label">Kimlik Belgesi</label>
								<input class="form-control" type="file" id="id_document" name="id_document" required>
								<div class="form-text">
									Hesabın size ait olduğunu doğrulamak için kimlik belgenizin bir fotoğrafını yükleyin.
									<strong>Önemli:</strong> Güvenliğiniz için, fotoğrafınız dahil olmak üzere adınız ve doğum yılınız dışındaki tüm bilgileri karalayarak gizleyebilirsiniz.
								</div>
							</div>
							
							{{-- ADDED: Checkbox for account deletion request --}}
							<div class="form-check mb-3">
								<input class="form-check-input" type="checkbox" name="delete_account" id="delete_account" value="1" {{ old('delete_account') ? 'checked' : '' }}>
								<label class="form-check-label" for="delete_account">
									Hesabımı ve tüm içeriğini kalıcı olarak silmek istiyorum. Bu işlemin geri alınamayacağını anlıyorum.
								</label>
							</div>
							
							<button type="submit" class="btn btn-primary">Kurtarma Talebi Gönder</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
