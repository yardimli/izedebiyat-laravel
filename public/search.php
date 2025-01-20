<?php
	require_once 'shop_global_backend.php';

	$pageName = "shopIndex";
	$headPageTitle = "İzEdebiyat - Arama Sonuçları";

	session_start();
?>
<!doctype html>

<html lang="tr">
<head>
	<?php include "shop_head.php"; ?>
</head>
<body class="home archive">

<!--Header -->
<?php include "shop_page_header.php"; ?>
<!-- /Header -->

<main id="content">
	<div class="content-widget">
		<div class="container">
			<!--Begin Archive Header-->
			<div class="row">
				<div class="col-12 archive-header text-center pt-3 pb-0">
					<h3 class="mb-0">Arama Sonuçları</h3>
				</div>
			</div>
			<div class="divider"></div>
			<!--End Archive Header-->

			<div class="row">
				<?php

					$TotalArticle = 0;
					$CurrentPage = 1;
					$query = "%" . $_GET["q"] . "%";
					if (strlen($query) < 5) {
					echo "<div class='col-12'><h3 class='text-center'>Arama sorgusu en az 3 karakter olmalıdır.</h3></div>";
				} else {

					$story_command = "SELECT Count(*) AS TotalArticle FROM yazilar WHERE onay=1 AND silindi=0 AND bad_critical<4 AND (baslik LIKE ? OR yazar_ad LIKE ?)";
					$stmt = $dbconn->prepare($story_command);
					$stmt->bind_param("ss", $query, $query);
					$stmt->execute();
					$result = $stmt->get_result();
					$row = $result->fetch_assoc();
					$TotalArticle = $row['TotalArticle'];

					$CurrentPage = isset($_GET["sayfa"]) ? intval($_GET["sayfa"]) : 1;

					$search_command = "SELECT * FROM yazilar WHERE onay=1 AND silindi=0 AND bad_critical<4 AND (baslik LIKE ? OR yazar_ad LIKE ?) ORDER BY katilma_tarihi DESC LIMIT 20 OFFSET ?";
					$search_stmt = $dbconn->prepare($search_command);
					$offset = ($CurrentPage - 1) * 20;
					$search_stmt->bind_param("ssi", $query, $query, $offset);
					$search_stmt->execute();
					$search_result = $search_stmt->get_result();

					$counter = 0;
					while (($search_row = $search_result->fetch_assoc()) && ($counter < 20)) {

						?>
						<article class="col-12 col-xl-3 col-md-4 justify-content-between mb-5 mr-0">
							<a
								href="/yapit/<?php echo $search_row["slug"]; ?>"><?php
									echo get_image($search_row['yazi_ana_resim'], $category_images, $search_row['kategori_id'], 'mb-2', 'width:100%; max-height:178px; object-fit: cover'); ?></a>

							<div class="align-self-center" style="min-height: 200px;">
								<h3 class="entry-title mb-3"><a
										href="/yapit/<?php echo $search_row["slug"]; ?>"><?php echo replace_ascii($search_row["baslik"]); ?></a>
								</h3>
								<div class="capsSubtle mb-2"><a
										href="/kume/<?php echo $search_row['ust_kategori_slug'] . "/" . $search_row["kategori_slug"]; ?>"><?php echo $search_row["ust_kategori_ad"] . " - " . $search_row["kategori_ad"]; ?></a>
								</div>
								<div class="entry-excerpt">
									<p>
										<?php
											if ($search_row['ust_kategori_slug'] === "siir") {
												echo get_words(replace_ascii($search_row["yazi"]), 16, false);
											} else {
												echo get_words(replace_ascii($search_row["tanitim"]), 48);
											}
										?>
									</p>
								</div>
								<div class="entry-meta align-items-center">
									<a
										href="/yazar/<?php echo $search_row["yazar_slug"]; ?>"><?php echo $search_row["yazar_ad"] ?></a><br>
									<span><?php echo time_elapsed_string($search_row["katilma_tarihi"]) ?></span>
									<span class="middotDivider"></span>
									<span class="readingTime"
									      title="<?php echo site_estimated_reading_time($search_row["yazi"]); ?>"><?php echo site_estimated_reading_time($search_row["yazi"]);; ?></span>
								</div>
							</div>
						</article>
						<?php
						$counter++;
					}
				?>
			</div>


			<script>
				var currentPage = <?php echo $CurrentPage; ?>;

				var numberOfItems = <?php echo $TotalArticle; ?>;
				var limitPerPage = 20;
				// Total pages rounded upwards
				var totalPages = Math.ceil(numberOfItems / limitPerPage);
				var NaviationNextPageURL = "/arabul?q=<?php echo $_GET["q"]; ?>&sayfa=";
			</script>
			<?php

				include_once "pagination.php";
				}
			?>
		</div> <!--content-widget-->
	</div>

	<div class="content-widget">
		<div class="container">
			<div class="sidebar-widget ads">
				<a href="#"><img src="/frontend/assets/images/ads/ads-2.png" alt="ads"></a>
			</div>
			<div class="hr"></div>
		</div>
	</div> <!--content-widget-->
</main>
<!-- Footer -->
<?php include "shop_page_footer.php"; ?>
<!-- /Footer -->
</div> <!--#wrapper-->

</body>
</html>
