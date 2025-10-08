{{-- ADDED: This is a new menu specifically for the book reviews section --}}
<nav id="main-menu" class="stick d-lg-block d-none">
	<div class="container">
		<div class="menu-primary">
			<ul>
				{{-- Link to return to the main homepage --}}
				<li>
					<a href="{{ url('/') }}" style="text-transform: uppercase;">ANA SAYFAYA DÖN</a>
				</li>
				{{-- MODIFIED: Changed the Book Reviews link to a dropdown menu --}}
				<li class="menu-item-has-children current-menu-item">
					<a href="{{ route('frontend.book-reviews.index') }}">KİTAP İZLERİ</a>
					{{-- ADDED: Sub-menu for book review navigation --}}
					<ul class="sub-menu dropdown-menu">
						<li><a href="{{ route('frontend.book-reviews.authors') }}">Yazarlar</a></li>
						<li><a href="{{ route('frontend.book-reviews.categories') }}">Kümeler</a></li>
						<li><a href="{{ route('frontend.book-reviews.tags') }}">Etiketler</a></li>
					</ul>
				</li>
			</ul>
			<span></span>
		</div>
	</div>
</nav>
