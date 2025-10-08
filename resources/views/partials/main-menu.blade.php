<nav id="main-menu" class="stick d-lg-block d-none">
	<div class="container">
		<div class="menu-primary">
			<ul>
				<li class="menu-item-has-children current-menu-item">
					<a href="{{ url('/') }}" style="text-transform: uppercase;">Ana Sayfa</a>
					<ul class="sub-menu dropdown-menu">
						<li><a href="{{ url('/son-eklenenler') }}">Son Eklenenler</a></li>
						<li><a href="{{ url('/yazarlar') }}">Yazarlar</a></li>
						<li><a href="{{ url('/katilim') }}">Katılım</a></li>
						<li><a href="mailto:iletisim@izedebiyat.com">İletişim</a></li>
						<li><a href="{{ url('/yasallik') }}">Yasallık</a></li>
						<li><a href="{{ url('/gizlilik') }}">Saklılık & Gizlilik</a></li>
						<li><a href="{{ url('/yayin-ilkeleri') }}">Yayın İlkeleri</a></li>
						<li><a href="{{ url('/izedebiyat') }}">İzEdebiyat?</a></li>
						<li><a href="{{ url('/sorular') }}">Sıkça Sorulanlar</a></li>
						<li><a href="{{ url('/kunye') }}">Künye</a></li>
					</ul>
				</li>
				@foreach($mainMenuCategories as $category)
					<li class="menu-item-has-children">
						<a href="{{ url('/kume/' . $category->slug) }}" style="text-transform: uppercase;">
							{!! $category->category_name !!}
						</a>
					</li>
				@endforeach
				<li class="nav-item">
					<a class="nav-link" href="{{ route('frontend.book-reviews.index') }}">KİTAP İZLERİ</a>
				</li>
			</ul>
			<span></span>
		</div>
	</div>
</nav>
@push('scripts')
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			// Function to adjust submenu position
			function adjustSubmenuPosition() {
				const submenus = document.querySelectorAll('.sub-menu.dropdown-menu');
				
				submenus.forEach(submenu => {
					// Reset position first
					submenu.style.left = '';
					submenu.style.right = '';
					
					// Get the submenu bounds
					const rect = submenu.getBoundingClientRect();
					const viewportWidth = window.innerWidth;
					
					// If submenu extends beyond viewport
					if (rect.right > viewportWidth) {
						const overflow = rect.right - viewportWidth;
						
						// If parent menu item is one of the last items
						const parentMenuItem = submenu.parentElement;
						const parentRect = parentMenuItem.getBoundingClientRect();
						
						if (parentRect.right > (viewportWidth * 0.7)) {
							// Align to the right if parent is towards the right side
							submenu.style.left = 'auto';
							submenu.style.right = '0';
						} else {
							// Otherwise, shift left by the overflow amount
							submenu.style.left = `-${overflow + 10}px`;
						}
					}
				});
			}
			
			// Initial adjustment
			adjustSubmenuPosition();
			
			// Adjust on window resize
			window.addEventListener('resize', adjustSubmenuPosition);
			
			// Adjust when hovering menu items (in case of dynamic content)
			const menuItems = document.querySelectorAll('.menu-item-has-children');
			menuItems.forEach(item => {
				item.addEventListener('mouseenter', () => {
					setTimeout(adjustSubmenuPosition, 0);
				});
			});
		});
	</script>
@endpush
