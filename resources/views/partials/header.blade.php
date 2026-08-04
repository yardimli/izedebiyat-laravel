<!--Mobile navigation-->
<div class="sticky-header fixed d-lg-none d-md-block">
	<div class="text-right">
		<div class="container-lg mobile-menu-fixed pr-5">
			<a class="navbar-brand" href="{{route('frontend.index')}}">
				<img class="light-mode-item navbar-brand-item" src="{{ asset('assets/images/logo/logo-large.png') }}" alt="logo">
				<img class="dark-mode-item navbar-brand-item" src="{{ asset('assets/images/logo/logo-large-transparent.png') }}" alt="logo">
			</a>
			
			<button type="button" class="nav-link btn btn-light p-0" id="modeSwitcher-mobile" style="vertical-align: text-bottom; display: inline-block;">
				<i class="bi bi-sun fs-6 d-none" id="lightModeIcon-mobile"></i>
				<i class="bi bi-moon-stars fs-6" id="darkModeIcon-mobile"></i>
			</button>

@if (Auth::user())
	<a href="{{route('backend.account')}}">
		<img class="small-user-avatar"
		     src="{{ !empty(Auth::user()->avatar) ? Storage::url(Auth::user()->avatar) : '/assets/images/avatar/placeholder.jpg' }}"
		     alt="avatar">
	</a>
@else
	<a class="user-avatar" href="{{route('login')}}" style="cursor: pointer;">
		<img src="/assets/images/avatar/placeholder.jpg" alt="">
	</a>
@endif
<a href="javascript:void(0)" class="menu-toggle-icon">
	<span class="lines"></span>
</a>
</div>
</div>
<div class="mobi-menu" style="overflow: auto;">
    <nav class="mobile-mega-menu" aria-label="Mobil ana menü">
        <details class="mobile-mega-item">
            <summary><span>ANA SAYFA</span><i class="bi bi-chevron-down"></i></summary>
            <div class="mobile-mega-panel">@include('partials.mega-menu-content', ['menuType' => 'home'])</div>
        </details>
        @foreach($mainMenuCategories as $category)
            <details class="mobile-mega-item">
                <summary>
                    <span>{!! $category->category_name !!}</span>
                    <span class="mobile-menu-summary-meta">
                        @if($category->menu_new > 0)
                            <b>{{ number_format($category->menu_new) }} yeni</b>
                        @endif
                        <small>{{ number_format($category->menu_total) }} toplam</small><i class="bi bi-chevron-down"></i>
                    </span>
                </summary>
                <div class="mobile-mega-panel">@include('partials.mega-menu-content', ['menuType' => 'category', 'menuCategory' => $category])</div>
            </details>
        @endforeach
        @if(config('features.kitap_izleri_visible'))
            <details class="mobile-mega-item">
                <summary><span>KİTAP İZLERİ</span><i class="bi bi-chevron-down"></i></summary>
                <div class="mobile-mega-panel">@include('partials.mega-menu-content', ['menuType' => 'books'])</div>
            </details>
        @endif
        <details class="mobile-mega-item">
            <summary><span>FORUM</span><i class="bi bi-chevron-down"></i></summary>
            <div class="mobile-mega-panel">@include('partials.mega-menu-content', ['menuType' => 'forum'])</div>
        </details>
        <form action="{{ route('search') }}" method="get" class="mobile-mega-search">
            <i class="bi bi-search"></i>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Eser, yazar veya konu ara..." aria-label="Ara">
            <button type="submit">Bul</button>
        </form>
    </nav>
</div>
</div>
<!--Desktop Header-->
<div id="wrapper">
	<header id="header" class="d-lg-block d-none">
		<div class="container-lg">
			<div class="w-100 d-flex align-items-center">
				<a class="navbar-brand me-auto " href="{{route('frontend.index')}}">
					<img class="light-mode-item navbar-brand-item" src="{{ asset('assets/images/logo/logo-large.png') }}" alt="logo">
					<img class="dark-mode-item navbar-brand-item" src="{{ asset('assets/images/logo/logo-large-transparent.png') }}" alt="logo">
				</a>
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
						
						@if (Auth::user())
							<a href="{{route('backend.account')}}" class="user-avatar" id="UserMenu" style="cursor: pointer;">
								<img src="{{ !empty(Auth::user()->avatar) ? Storage::url(Auth::user()->avatar) : '/assets/images/avatar/placeholder.jpg' }}"
								     alt="avatar">
							</a>
						@else
							<ul class="top-menu navbar-nav w-100 d-lg-flex align-items-center ms-2">
								<li><a href="{{route('register')}}" class="btn">ÜYE OL</a></li>
							</ul>
							<a href="{{route('login')}}" class="user-avatar" id="UserMenu" style="cursor: pointer;">
								<img src="{{ asset('/assets/images/avatar/placeholder.jpg') }}" alt="">
							</a>
						@endif
					</div>
					<form action="{{ route('search') }}" method="get" class="search-form d-lg-flex float-end">
						<a href="javascript:void(0)" class="search-toggle" style="padding-left:10px; padding-right:10px;">
							<i class="bi bi-search"></i>
						</a>
						<input type="text" class="search_field" name="q" value="{{ request('q') }}" placeholder="Ara...">
					</form>
				</div>
			</div>
		</div>
		@isset($inspirationalQuote)
			<div class="container-lg text-center pt-2 pb-1 d-none d-lg-block border-dashed">
				<p class="fst-italic inspirational-quote mb-0 small text-muted">"{{ $inspirationalQuote }}"</p>
			</div>
		@endisset
		
		{{-- MODIFIED: Conditionally display the main menu or the book reviews menu based on the current route. --}}
		@if(config('features.kitap_izleri_visible') && (request()->routeIs('frontend.book-review.*') || request()->routeIs('frontend.book-reviews.*')))
			@include('partials.book-reviews-main-menu')
		@else
			@include('partials.main-menu')
		@endif
	</header>
</div>

@isset($inspirationalQuote)
	<div class="container-fluid text-center pt-2 pb-1 d-lg-none">
		<p class="fst-italic inspirational-quote-mobile mb-0 small text-muted">"{{ $inspirationalQuote }}"</p>
	</div>
@endisset

@push('scripts')
	<script>
		
		// <!-- Dark mode -->
		const storedTheme = localStorage.getItem('theme');
		
		const getPreferredTheme = () => {
			if (storedTheme) {
				return storedTheme;
			}
			return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
		};
		
		const setTheme = function (theme) {
			if (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
				document.documentElement.setAttribute('data-bs-theme', 'dark');
			} else {
				document.documentElement.setAttribute('data-bs-theme', theme);
			}
		};
		
		setTheme(getPreferredTheme());
		
		window.addEventListener('DOMContentLoaded', () => {
			var el = document.querySelector('.theme-icon-active');
			if (el != 'undefined' && el != null) {
				const showActiveTheme = theme => {
					const activeThemeIcon = document.querySelector('.theme-icon-active use');
					const btnToActive = document.querySelector(`[data-bs-theme-value="${theme}"]`);
					const svgOfActiveBtn = btnToActive.querySelector('.mode-switch use').getAttribute('href');
					
					document.querySelectorAll('[data-bs-theme-value]').forEach(element => {
						element.classList.remove('active');
					});
					
					btnToActive.classList.add('active');
					activeThemeIcon.setAttribute('href', svgOfActiveBtn);
				};
				
				window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
					if (storedTheme !== 'light' || storedTheme !== 'dark') {
						setTheme(getPreferredTheme());
					}
				});
				
				showActiveTheme(getPreferredTheme());
				
				document.querySelectorAll('[data-bs-theme-value]')
					.forEach(toggle => {
						toggle.addEventListener('click', () => {
							const theme = toggle.getAttribute('data-bs-theme-value');
							localStorage.setItem('theme', theme);
							setTheme(theme);
							showActiveTheme(theme);
						});
					});
				
			}
			
			const modeSwitcher_mobile = document.getElementById('modeSwitcher-mobile');
			const lightModeIcon_mobile = document.getElementById('lightModeIcon-mobile');
			const darkModeIcon_mobile = document.getElementById('darkModeIcon-mobile');
			
			
			const modeSwitcher = document.getElementById('modeSwitcher');
			const lightModeIcon = document.getElementById('lightModeIcon');
			const darkModeIcon = document.getElementById('darkModeIcon');
			
			// Set initial icon state
			if (getPreferredTheme() === 'dark') {
				lightModeIcon.classList.remove('d-none');
				darkModeIcon.classList.add('d-none');
				lightModeIcon_mobile.classList.remove('d-none');
				darkModeIcon_mobile.classList.add('d-none');
			} else {
				lightModeIcon.classList.add('d-none');
				darkModeIcon.classList.remove('d-none');
				lightModeIcon_mobile.classList.add('d-none');
				darkModeIcon_mobile.classList.remove('d-none');
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
			
			modeSwitcher_mobile.addEventListener('click', () => {
				const currentTheme = document.documentElement.getAttribute('data-bs-theme');
				const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
				
				// Update theme
				localStorage.setItem('theme', newTheme);
				setTheme(newTheme);
				
				// Toggle icons
				lightModeIcon_mobile.classList.toggle('d-none');
				darkModeIcon_mobile.classList.toggle('d-none');
			});
		});
	</script>

@endpush
