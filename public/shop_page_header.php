<!--<div class="top-scroll-bar"></div>-->
<!--Mobile navigation-->
<div class="sticky-header fixed d-lg-none d-md-block">
	<div class="text-right">
		<div class="container mobile-menu-fixed pr-5">
			<h1 class="logo-small navbar-brand"><a href="/ana-sayfa" class="logo">izedebiyat</a></h1>

			<a class="author-avatar" href="/giris-yap" style="cursor: pointer;"><img
					src="/frontend/assets/images/author-avata-2.jpg" alt=""></a>

			<!--			<ul class="social-network heading navbar-nav d-lg-flex align-items-center">-->
			<!--				<li><a href="#"><i class="icon-facebook"></i></a></li>-->
			<!--			</ul>-->

			<a href="javascript:void(0)" class="menu-toggle-icon">
				<span class="lines"></span>
			</a>
		</div>
	</div>

	<div class="mobi-menu" style="overflow: auto;">
		<h1 class="logo navbar-brand mt-4 mb-3"><a href="/ana-sayfa" class="logo">izedebiyat</a></h1>
		<form action="/arabul" method="get" class="menu-search-form d-lg-flex" style="margin-bottom: 0px;">
			<input type="text" class="search_field" placeholder="Ara..." value="" name="q">
		</form>
		<nav style="text-align: left; padding:10px;">
			<div class="current-menu-item"><a href="/ana-sayfa" style="text-transform: uppercase;">Ana Sayfa</a></div>
			<div class="pl-4">
				<a href="/son-eklenenler">Son Eklenenler</a>
				<a href="/yazarlar">Yazarlar</a>
				<a href="/katilim">Katılım</a>
				<a href="mailto:iletisim@izedebiyat.com">İletişim</a>
				<a href="/yasallik">Yasallık</a>
				<a href="/gizlilik">Saklılık & Gizlilik</a>
				<a href="/yayin-ilkeleri">Yayın İlkeleri</a>
				<a href="/izedebiyat">İzEdebiyat?</a>
				<a href="/sorular">Sıkça Sorulanlar</a>
				<a href="/kunye">Künye</a>
			</div>

			<?php

				$command1 = "SELECT * FROM kategoriler WHERE ust_kategori_id=0 ORDER BY slug ASC";
				//echo $command1;
				$result1 = $dbconn->query($command1);

				while ($row1 = $result1->fetch_assoc()) {
					$command2 = "SELECT COUNT(*) AS CatCount FROM kategoriler WHERE ust_kategori_id=" . $row1["id"];
					$result2 = $dbconn->query($command2);
					$row2 = $result2->fetch_assoc();
					$CatCount = $row2["CatCount"];
					?>
					<div class="pt-2"><a href="/kume/<?php echo $row1["slug"]; ?>"
					                     style="text-transform: uppercase;"><?php echo $row1["kategori_ad"]; ?></a></div>
					<div class="pl-4">

						<?php
							$command2 = "SELECT * FROM kategoriler WHERE ust_kategori_id=" . $row1["id"];
							$result2 = $dbconn->query($command2);
							$TempCount = 0;
							while ($row2 = $result2->fetch_assoc()) {
								?>
								<a
									href="/kume/<?php echo $row1["slug"]; ?>/<?php echo $row2["slug"]; ?>"><?php echo $row2["kategori_ad"]; ?></a>,
								<?php
							}
						?>
					</div>
					<?php
				}
			?>
		</nav>
	</div>
</div>
<!--Mobile navigation-->


<div id="wrapper" class="container">
	<header id="header" class="d-lg-block d-none">
		<div class="container">
			<div class="w-100 d-flex align-items-center">
				<h1 class="logo me-auto navbar-brand">
					<a href="//" class="logo">izedebiyat</a>
				</h1>
				<div class="header-right w-50 float-end">
					<div class="d-inline-flex float-end text-end align-items-center">

						<ul class="d-inline-flex">
							<li class="nav-item ms-2">
								<button type="button" class="nav-link btn btn-light p-0" id="modeSwitcher">
									<!-- Sun icon for dark mode -->
									<i class="bi bi-sun fs-6 d-none" id="lightModeIcon"></i>
									<!-- Moon icon for light mode -->
									<i class="bi bi-moon-stars fs-6" id="darkModeIcon"></i>
								</button>
							</li>
						</ul>

						<ul class="top-menu navbar-nav w-100 d-lg-flex align-items-center ms-2">
							<li><a href="/katilim" class="btn">ÜYE OL</a></li>
						</ul>

						<a href="/giris-yap" class="author-avatar" id="UserMenu" style="cursor: pointer;">
							<img src="/frontend/assets/images/author-avata-2.jpg" alt="">
						</a>

					</div>

					<form action="/arabul" method="get" class="search-form d-lg-flex float-end">
						<a href="javascript:void(0)" class="search-toggle" style="padding-left:10px; padding-right:10px;">
							<i class="bi bi-search"></i>
						</a>
						<input type="text" class="search_field" placeholder="Ara..." value="" name="q">
					</form>

				</div>
			</div>
			<div class="clearfix"></div>
		</div>
		<nav id="main-menu" class="stick d-lg-block d-none">
			<div class="container">
				<div class="menu-primary">
					<ul>
						<li class="menu-item-has-children current-menu-item"><a href="/ana-sayfa"
						                                                        style="text-transform: uppercase;">Ana Sayfa</a>

							<ul class="sub-menu dropdown-menu">
								<li><a href="/son-eklenenler">Son Eklenenler</a></li>
								<li><a href="/yazarlar">Yazarlar</a></li>
								<li><a href="/katilim">Katılım</a></li>
								<li><a href="mailto:iletisim@izedebiyat.com">İletişim</a></li>
								<li><a href="/yasallik">Yasallık</a></li>
								<li><a href="/gizlilik">Saklılık & Gizlilik</a></li>
								<li><a href="/yayin-ilkeleri">Yayın İlkeleri</a></li>
								<li><a href="/izedebiyat">İzEdebiyat?</a></li>
								<li><a href="/sorular">Sıkça Sorulanlar</a></li>
								<li><a href="/kunye">Künye</a></li>
							</ul>
						</li>

						<?php

							$command1 = "SELECT * FROM kategoriler WHERE ust_kategori_id=0 ORDER BY slug ASC";
							//echo $command1;
							$result1 = $dbconn->query($command1);

							while ($row1 = $result1->fetch_assoc()) {
								$command2 = "SELECT COUNT(*) AS CatCount FROM kategoriler WHERE ust_kategori_id=" . $row1["id"];
								$result2 = $dbconn->query($command2);
								$row2 = $result2->fetch_assoc();
								$CatCount = $row2["CatCount"];

								if ($CatCount > 20) {
									$Colums = 3;
									$MaxRows = 12;
									$ColName = "col-sm-4";
								}
								if ($CatCount <= 20) {
									$Colums = 2;
									$MaxRows = 10;
									$ColName = "col-sm-6";
								}
								?>
								<li class="menu-item-has-children"><a href="/kume/<?php echo $row1["slug"]; ?>"
								                                      style="text-transform: uppercase;"><?php echo $row1["kategori_ad"]; ?></a>
									<ul class="sub-menu dropdown-menu multi-column columns-<?php echo $Colums; ?>">

										<div class="row">
											<div class="<?php echo $ColName; ?>">
												<ul class="multi-column-dropdown">

													<?php

														$command2 = "SELECT * FROM kategoriler WHERE ust_kategori_id=" . $row1["id"];
														$result2 = $dbconn->query($command2);
														$TempCount = 0;
														while ($row2 = $result2->fetch_assoc()) {
														$TempCount++;
														if ($TempCount > $MaxRows) {
														$TempCount = 1;
													?>
												</ul>
											</div>
											<div class="<?php echo $ColName; ?>">
												<ul class="multi-column-dropdown">
													<?php
														}
													?>
													<li><a
															href="/kume/<?php echo $row1["slug"]; ?>/<?php echo $row2["slug"]; ?>"><?php echo $row2["kategori_ad"]; ?></a>
													</li>
													<?php
														}
													?>
												</ul>
											</div>
									</ul>
								</li>
								<?php
							}
						?>
					</ul>
					<span></span>
				</div>
			</div>
		</nav>
	</header>


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
