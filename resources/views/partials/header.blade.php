<!--Mobile navigation-->
<div class="sticky-header fixed d-lg-none d-md-block">
	<div class="text-right">
		<div class="container mobile-menu-fixed pr-5">
			<h1 class="logo-small navbar-brand"><a href="{{ url('/') }}" class="logo">izedebiyat</a></h1>
			<a class="author-avatar" href="{{ url('/giris-yap') }}" style="cursor: pointer;">
				<img src="{{ asset('frontend/assets/images/author-avata-2.jpg') }}" alt="">
			</a>
			<a href="javascript:void(0)" class="menu-toggle-icon">
				<span class="lines"></span>
			</a>
		</div>
	</div>
	<div class="mobi-menu" style="overflow: auto;">
		<h1 class="logo navbar-brand mt-4 mb-3"><a href="{{ url('/') }}" class="logo">izedebiyat</a></h1>
		<form action="{{ url('/arabul') }}" method="get" class="menu-search-form d-lg-flex" style="margin-bottom: 0px;">
			<input type="text" class="search_field" placeholder="Ara..." value="" name="q">
		</form>
		<nav style="text-align: left; padding:10px;">
			<div class="current-menu-item"><a href="{{ url('/') }}" style="text-transform: uppercase;">Ana Sayfa</a></div>
			<div class="pl-4">
				<a href="{{ url('/son-eklenenler') }}">Son Eklenenler</a>
				<a href="{{ url('/yazarlar') }}">Yazarlar</a>
				<a href="{{ url('/katilim') }}">Katılım</a>
				<a href="mailto:iletisim@izedebiyat.com">İletişim</a>
				<a href="{{ url('/yasallik') }}">Yasallık</a>
				<a href="{{ url('/gizlilik') }}">Saklılık & Gizlilik</a>
				<a href="{{ url('/yayin-ilkeleri') }}">Yayın İlkeleri</a>
				<a href="{{ url('/izedebiyat') }}">İzEdebiyat?</a>
				<a href="{{ url('/sorular') }}">Sıkça Sorulanlar</a>
				<a href="{{ url('/kunye') }}">Künye</a>
			</div>
			
			@foreach($mainMenuCategories as $category)
				<div class="pt-2">
					<a href="{{ url('/kume/' . $category->slug) }}" style="text-transform: uppercase;">
						{!! $category->kategori_ad !!}
					</a>
				</div>
				<div class="pl-4">
					@foreach($category->subCategories as $subCategory)
						<a href="{{ url('/kume/' . $category->slug . '/' . $subCategory->slug) }}">
							{{ $subCategory->kategori_ad }}
						</a>,
					@endforeach
				</div>
			@endforeach
		</nav>
	</div>
</div>

<!--Desktop Header-->
<div id="wrapper">
	<header id="header" class="d-lg-block d-none">
		<div class="container">
			<div class="w-100 d-flex align-items-center">
				<h1 class="logo me-auto navbar-brand">
					<a href="{{ url('/') }}" class="logo">izedebiyat</a>
				</h1>
				<div class="header-right w-50 float-end">
					<div class="d-inline-flex float-end text-end align-items-center">
						<ul class="d-inline-flex">
							<li class="nav-item ms-2">
								<button type="button" class="nav-link btn btn-light p-0" id="modeSwitcher">
									<i class="bi bi-sun fs-6 d-none" id="lightModeIcon"></i>
									<i class="bi bi-moon-stars fs-6" id="darkModeIcon"></i>
								</button>
							</li>
						</ul>
						<ul class="top-menu navbar-nav w-100 d-lg-flex align-items-center ms-2">
							<li><a href="{{ url('/katilim') }}" class="btn">ÜYE OL</a></li>
						</ul>
						<a href="{{ url('/giris-yap') }}" class="author-avatar" id="UserMenu" style="cursor: pointer;">
							<img src="{{ asset('frontend/assets/images/author-avata-2.jpg') }}" alt="">
						</a>
					</div>
					<form action="{{ url('/arabul') }}" method="get" class="search-form d-lg-flex float-end">
						<a href="javascript:void(0)" class="search-toggle" style="padding-left:10px; padding-right:10px;">
							<i class="bi bi-search"></i>
						</a>
						<input type="text" class="search_field" placeholder="Ara..." value="" name="q">
					</form>
				</div>
			</div>
		</div>
		
		@include('partials.main-menu')
	</header>
</div>

@push('scripts')
	<script>
		
		// <!-- Dark mode -->
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
			
			
			const modeSwitcher = document.getElementById('modeSwitcher');
			const lightModeIcon = document.getElementById('lightModeIcon');
			const darkModeIcon = document.getElementById('darkModeIcon');
			
			// Set initial icon state
			if (getPreferredTheme() === 'dark') {
				lightModeIcon.classList.remove('d-none');
				darkModeIcon.classList.add('d-none');
			} else {
				lightModeIcon.classList.add('d-none');
				darkModeIcon.classList.remove('d-none');
			}
			
			modeSwitcher.addEventListener('click', () => {
				const currentTheme = document.documentElement.getAttribute('data-bs-theme');
				const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
				
				// Update theme
				localStorage.setItem('theme', newTheme);
				setTheme(newTheme);
				
				// Toggle icons
				lightModeIcon.classList.toggle('d-none');
				darkModeIcon.classList.toggle('d-none');
			});
		});
	</script>

@endpush
