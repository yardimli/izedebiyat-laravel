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
				
				@foreach(\App\Models\Kategori::where('ust_kategori_id', 0)->orderBy('slug_tr')->get() as $category)
					@php
						$subCategoryCount = $category->subCategories->count();
						$columns = $subCategoryCount > 20 ? 3 : 2;
						$maxRows = $subCategoryCount > 20 ? 12 : 10;
						$colClass = $subCategoryCount > 20 ? 'col-sm-4' : 'col-sm-6';
					@endphp
					
					<li class="menu-item-has-children">
						<a href="{{ url('/kume/' . $category->slug_tr) }}" style="text-transform: uppercase;">
							{{ $category->kategori_ad }}
						</a>
						<ul class="sub-menu dropdown-menu multi-column columns-{{ $columns }}">
							<div class="row">
								<div class="{{ $colClass }}">
									<ul class="multi-column-dropdown">
										@foreach($category->subCategories as $index => $subCategory)
											@if($index > 0 && $index % $maxRows === 0)
									</ul>
								</div>
								<div class="{{ $colClass }}">
									<ul class="multi-column-dropdown">
										@endif
										<li>
											<a href="{{ url('/kume/' . $category->slug_tr . '/' . $subCategory->slug_tr) }}">
												{{ $subCategory->kategori_ad }}
											</a>
										</li>
										@endforeach
									</ul>
								</div>
							</div>
						</ul>
					</li>
				@endforeach
			</ul>
			<span></span>
		</div>
	</div>
</nav>
