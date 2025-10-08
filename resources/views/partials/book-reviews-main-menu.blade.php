<nav id="main-menu" class="stick d-lg-block d-none">
	<div class="container">
		<div class="menu-primary">
			<ul>
				<li>
					<a href="{{ route('frontend.index') }}">ANA SAYFA</a>
				</li>
				<li class="{{ request()->routeIs('frontend.book-reviews.index') ? 'current-menu-item' : '' }}">
					<a href="{{ route('frontend.book-reviews.index') }}">TÜM KİTAP İZLERİ</a>
				</li>
				<li class="{{ request()->routeIs('frontend.book-reviews.authors') ? 'current-menu-item' : '' }}">
					<a href="{{ route('frontend.book-reviews.authors') }}">YAZARLAR</a>
				</li>
				<li class="{{ request()->routeIs('frontend.book-reviews.categories') ? 'current-menu-item' : '' }}">
					<a href="{{ route('frontend.book-reviews.categories') }}">KÜMELER</a>
				</li>
				<li class="{{ request()->routeIs('frontend.book-reviews.tags') ? 'current-menu-item' : '' }}">
					<a href="{{ route('frontend.book-reviews.tags') }}">ETİKETLER</a>
				</li>
				{{-- ADDED: Link for user to submit their book --}}
				@auth
					<li class="{{ request()->routeIs('frontend.book-reviews.create-submission') ? 'current-menu-item' : '' }}">
						<a href="{{ route('frontend.book-reviews.create-submission') }}">KİTABINI GÖNDER</a>
					</li>
				@endauth
			</ul>
			<span></span>
		</div>
	</div>
</nav>
