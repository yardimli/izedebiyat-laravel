@extends('layouts.app')

@section('title', 'Verify Thank You')

@section('content')

<!-- **************** ANA İÇERİK BAŞLANGIÇ **************** -->
<main>
	
	<!-- Kapsayıcı BAŞLANGIÇ -->
	<div class="container" style="min-height: calc(88vh);">
		<div class="row g-4">
			<!-- Ana içerik BAŞLANGIÇ -->
			<div class="col-lg-8 mx-auto">
				<!-- Kart BAŞLANGIÇ -->
				<div class="card">
					<div class="card-header py-3 border-0 d-flex align-items-center justify-content-between">
						<h1 class="h5 mb-0">Teşekkürler!</h1>
					</div>
					<div class="card-body p-3">
						E-posta adresinizi doğruladığınız için teşekkür ederiz. Artık eserlerinizi yazmaya başlayabilirsiniz.
						<br>
						<br>
						Eserinizi yazmaya başlamak için yukarıdaki "Yaz" bağlantısına tıklayabilirsiniz.
						<br>
						<br>
						Eserlerinizi görüntülemek için yukarıdaki "Eserlerim" bağlantısına tıklayın.
						<br>
						<br>
						<div style="text-align: center; ">
							<img src="{{ asset('/assets/images/logo/logo-large.png') }}"
							     style="height: 200px;" alt="Teşekkürler" class="img-fluid">
						</div>
					</div>
				</div>
				<!-- Kart BİTİŞ -->
			</div>
		</div> <!-- Satır BİTİŞ -->
	</div>
	<!-- Kapsayıcı BİTİŞ -->

</main>



@include('layouts.footer')
