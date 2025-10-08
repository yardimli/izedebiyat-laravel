{{-- ADDED: This is a new menu specifically for the book reviews section --}}
<nav id="main-menu" class="stick d-lg-block d-none">
	<div class="container">
		<div class="menu-primary">
			<ul>
				{{-- Link to return to the main homepage --}}
				<li class="nav-item">
					<a class="nav-link" href="{{ route('frontend.book-reviews.index') }}" style="text-transform: uppercase;">KİTAP İZLERİ</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="{{ route('frontend.book-reviews.authors') }}" style="text-transform: uppercase;">Yazarlar</a>
				</li>
				<li>
					<a class="nav-link" href="{{ route('frontend.book-reviews.categories') }}" style="text-transform: uppercase;">Kümeler</a>
				</li>
				<li>
					<a class="nav-link" href="{{ route('frontend.book-reviews.tags') }}" style="text-transform: uppercase;">Etiketler</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="{{ url('/') }}" style="text-transform: uppercase;">ANA SAYFAYA DÖN</a>
				</li>
			</ul>
			<span></span>
		</div>
	</div>
</nav>
