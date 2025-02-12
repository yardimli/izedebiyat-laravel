<!DOCTYPE html>
<html lang="en">
<head>
	<title>{{__('default.İzEdebiyat')}} - {{__('default.Boilerplate Site Tagline')}}</title>
	
	<!-- Meta Tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="author" content="izedebiyat.com">
	<meta name="description"
	      content="İzEdebiyat - {{__('default.Boilerplate Site Tagline')}}">
	
	<!-- Dark mode -->
	<script>
		const storedTheme = localStorage.getItem('theme')
		
		const getPreferredTheme = () => {
			if (storedTheme) {
				return storedTheme
			}
			return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
		}
		
		const setTheme = function (theme) {
			if (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
				document.documentElement.setAttribute('data-bs-theme', 'dark')
			} else {
				document.documentElement.setAttribute('data-bs-theme', theme)
			}
		}
		
		setTheme(getPreferredTheme())
		
		window.addEventListener('DOMContentLoaded', () => {
			var el = document.querySelector('.theme-icon-active');
			if (el != 'undefined' && el != null) {
				const showActiveTheme = theme => {
					const activeThemeIcon = document.querySelector('.theme-icon-active use')
					const btnToActive = document.querySelector(`[data-bs-theme-value="${theme}"]`)
					const svgOfActiveBtn = btnToActive.querySelector('.mode-switch use').getAttribute('href')
					
					document.querySelectorAll('[data-bs-theme-value]').forEach(element => {
						element.classList.remove('active')
					})
					
					btnToActive.classList.add('active')
					activeThemeIcon.setAttribute('href', svgOfActiveBtn)
				}
				
				window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
					if (storedTheme !== 'light' || storedTheme !== 'dark') {
						setTheme(getPreferredTheme())
					}
				})
				
				showActiveTheme(getPreferredTheme())
				
				document.querySelectorAll('[data-bs-theme-value]')
					.forEach(toggle => {
						toggle.addEventListener('click', () => {
							const theme = toggle.getAttribute('data-bs-theme-value')
							localStorage.setItem('theme', theme)
							setTheme(theme)
							showActiveTheme(theme)
						})
					})
				
			}
		})
	
	</script>
	
	<!-- Favicon -->
	<link rel="shortcut icon" href="/assets/images/favicon.ico">
	
	<!-- Google Font -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
	
	<!-- Plugins CSS -->
	<link rel="stylesheet" type="text/css" href="/assets/vendor/font-awesome/css/all.min.css">
	<link rel="stylesheet" type="text/css" href="/assets/vendor/bootstrap-icons/bootstrap-icons.css">
	
	<!-- Theme CSS -->
	<link rel="stylesheet" type="text/css" href="/assets/css/style.css">

</head>
<body>

<main>
	
	<!-- **************** MAIN CONTENT START **************** -->
	
	<!-- Main content START -->
	<div class="bg-primary pt-5 pb-0 position-relative">
		@include('layouts.svg-image')
		
		<!-- Container START -->
		<div class="container">
			<div class="row justify-content-center text-center">
				<div class="col-12">
					<!-- Title -->
					<h1 class="display-4 text-white mb-4 position-relative">İzEdebiyat</span>'a Tekrar Hoş Geldiniz!</h1>
					</h1>
					@include('layouts.svg2-image')
				</div>
				<div class="col-sm-10 col-md-8 col-lg-6 position-relative z-index-1">
					<!-- Sign in form START -->
					<div class="card card-body p-4 p-sm-5 mt-sm-n5 mb-n5">
						<!-- Title -->
						<h2 class="h1 mb-2">Şifre Sıfırlama</h2>
						<!-- Form START -->
						<form roll="form" class="mt-4" method="POST" action="{{ route('password.update') }}">
							@csrf
							
							<input type="hidden" name="token" value="{{ $token }}">
							
							<!-- Email -->
							<div class="mb-3 position-relative input-group-lg">
								
								<input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
								       aria-label="E-posta" name="email" value="{{ old('email', $email) }}"
								       placeholder="E-posta Giriniz..." required autocomplete="email" autofocus>
								
								
								@error('email')
								<span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
								@enderror
							</div>
							<!-- New password -->
							<div class="mb-3">
								
								<!-- Input group -->
								<div class="input-group input-group-lg">
									<input type="password" class="form-control fakepassword psw-input @error('password') is-invalid @enderror"
									       aria-label="Şifre"
									       id="psw-input" name="password"
									       placeholder="Yeni Şifre..." value="" required autocomplete="new-password">
									<span class="input-group-text p-0"><i
											class="fakepasswordicon fa-solid fa-eye-slash cursor-pointer p-2 w-40px"></i></span>
									@error('password')
									<span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
									@enderror
								</div>
								<!-- Pswmeter -->
								<div id="pswmeter" class="mt-2"></div>
								<div class="d-flex mt-1">
									<div id="pswmeter-message" class="rounded"></div>
									<!-- Password message notification -->
									<div class="ms-auto">
										<i class="bi bi-info-circle ps-1" data-bs-container="body" data-bs-toggle="popover"
										   data-bs-placement="top"
										   data-bs-content="En az bir büyük harf, bir küçük harf, bir özel karakter, bir rakam içermeli ve 8 karakter uzunluğunda olmalıdır."
										   data-bs-original-title="" title=""></i>
									</div>
								</div>
								
								
								<div class="input-group input-group-lg">
									<input type="password" class="form-control fakepassword2"
									       aria-label="Şifre Onayı"
									       id="password-confirm" name="password_confirmation"
									       placeholder="Şifreyi Onaylayın..." value="" required autocomplete="new-password">
									<span class="input-group-text p-0"><i
											class="fakepasswordicon2 fa-solid fa-eye-slash cursor-pointer p-2 w-40px"></i></span>
								</div>
							</div>
							<!-- Button -->
							<div class="d-grid">
								<button type="submit" class="btn btn-lg btn-primary-soft">{{ __('default.Reset Password') }}</button>
							</div>
							<!-- Copyright -->
							<p class="mb-0 mt-3">©2025 <a target="_blank" href="https://www.izedebiyat.com/">izedebiyat</a> Tüm hakları saklıdır</p>
						
						</form>
						<!-- Form END -->
					</div>
					<!-- Sign in form START -->
				</div>
			</div> <!-- Row END -->
		</div>
		<!-- Container END -->
	</div>
	<!-- Main content END -->

</main>
<!-- **************** MAIN CONTENT END **************** -->

<!-- =======================
Footer START -->
<footer class="pt-5 pb-2 pb-sm-4 position-relative bg-mode">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-sm-10 col-md-8 col-lg-6">
				<div class="d-grid d-sm-flex justify-content-center justify-content-sm-between align-items-center mt-3">
					<!-- Nav -->
				</div>
			</div>
		</div>
	</div>
</footer>
<!-- =======================
Footer END -->

<!-- **************** MAIN CONTENT END **************** -->

<!-- =======================
JS libraries, plugins and custom scripts -->

<!-- Bootstrap JS -->
<script src="/assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<!-- Vendors -->
<script src="/assets/vendor/pswmeter/pswmeter.min.js"></script>

<!-- Theme Functions -->
<script src="/js/functions.js"></script>

</body>
</html>






