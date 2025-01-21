<?php
	require_once 'shop_global_backend.php';
	session_start();
?>
<!doctype html>

<html lang="tr">
<head>
	<?php
		$story_row['ust_kategori_slug'] = $_GET["ust_kategori_slug"];
		$command1 = "SELECT * FROM kategoriler WHERE ust_kategori_id=0 AND slug='" . mysqli_real_escape_string($dbconn, $story_row['ust_kategori_slug']) . "'";
		$result1 = $dbconn->query($command1);
		while ($row1 = $result1->fetch_assoc()) {
			$ust_kategori_id = $row1["id"];
			$ust_kategori_ad = $row1["kategori_ad"];
		}

		$pageName = "shopIndex";
		$headPageTitle = "İzEdebiyat - " . $ust_kategori_ad;
		include "shop_head.php";
	?>
</head>
<body class="home">

<!--Header -->
<?php include "shop_page_header.php"; ?>
<!-- /Header -->

<section class="archive">
<main id="content">
	<div class="content-widget">
		<div class="container">
			<div class="row">
				<div class="col-md-8 col-12">
					<h4 class="spanborder">
						<span style="text-transform: uppercase;"><?php echo $ust_kategori_ad; ?></span>
					</h4>

					<?php
						$TotalArticle = 0;
						$CurrentPage = 1;
						$story_command = "SELECT Count(*) AS TotalArticle FROM yazilar WHERE onay=1 AND silindi=0 AND bad_critical<3 AND respect_moderation_value>=3 AND moderation_flagged = 0 AND ust_kategori_id =" . $ust_kategori_id . "";
						if ($story_result = $dbconn->query($story_command)) {
							$story_row = $story_result->fetch_assoc();
							$TotalArticle = $story_row["TotalArticle"];
						}

						$CurrentPage = isset($_GET["sayfa"]) ? intval($_GET["sayfa"]) : 1;


						$story_command = "SELECT * FROM yazilar WHERE onay=1 AND silindi=0 AND bad_critical<3 AND religious_moderation_value<3 AND respect_moderation_value>=3 AND moderation_flagged = 0 AND ust_kategori_id =" . $ust_kategori_id . " ORDER BY formul_ekim DESC LIMIT 500 OFFSET " . (($CurrentPage-1) * 20);
						//echo $story_command;
						$story_result = $dbconn->query($story_command);
						$counter = 0;

						$authors = [];
						$author_counts = [];
						$previous_author = null;

						while (($story_row = $story_result->fetch_assoc()) && ($counter < 20)) {
							$current_author = $story_row["yazar_id"];

							// Initialize author count if not exists
							if (!isset($author_counts[$current_author])) {
								$author_counts[$current_author] = 0;
							}

							// Check if author can be displayed
							if ($author_counts[$current_author] < 3 && $current_author !== $previous_author) {
								$author_counts[$current_author]++;
								$previous_author = $current_author;
								$counter++;

								if ($counter === 1) {
									?>
									<article class="first mb-3">
										<figure><a href="/yapit/<?php echo $story_row["slug"]; ?>"><?php echo get_image($story_row['yazi_ana_resim'] ?? '', $category_images, $story_row['kategori_id'], '', 'width: 100%; max-height:120px; object-fit: cover'); ?></a></figure>
										<h1 class="entry-title mb-3"><a
												href="/yapit/<?php echo $story_row["slug"]; ?>"><?php echo replace_ascii($story_row["baslik"]); ?></a>
										</h1>
										<div class="entry-excerpt">
											<p>
												<?php
													if ($story_row['ust_kategori_slug'] === "siir") {
														echo get_words(replace_ascii($story_row["yazi"]), 16, false);
													} else {
														echo get_words(replace_ascii($story_row["tanitim"]), 48);
													}
												?>
											</p>
										</div>
										<div class="entry-meta align-items-center">
											<a href="/yazar/<?php echo $story_row["yazar_slug"]; ?>"><?php echo $story_row["yazar_ad"] ?></a>
											- <a
												href="/kume/<?php echo $story_row['ust_kategori_slug'] . "/" . $story_row["kategori_slug"]; ?>"><?php echo $story_row["kategori_ad"]; ?></a><br>
											<span><?php echo time_elapsed_string($story_row["katilma_tarihi"]) ?></span>
											<span class="middotDivider"></span>
											<span class="readingTime"
											      title="<?php echo site_estimated_reading_time($story_row["yazi"]); ?>"><?php echo site_estimated_reading_time($story_row["yazi"]); ?></span>
										</div>
									</article>
									<div class="divider"></div>

									<?php
									?><?php
								} else if (($counter >= 4 && $counter <= 7) || ($counter >= 14 && $counter <= 17)) {
									if ($counter === 4 || $counter === 14) {
										?>
										<div class="row justify-content-between">
										<div class="divider-2"></div>
										<?php
									}

									?>
									<article class="col-md-6">
										<div class="mb-3 d-flex row">
											<figure class="col-md-5"><a href="/yapit/<?php echo $story_row["slug"]; ?>"><?php echo get_image($story_row['yazi_ana_resim'] ?? '', $category_images, $story_row['kategori_id'], '', 'width: 100%;'); ?></a>
											</figure>
											<div class="entry-content col-md-7 pl-md-0">
												<h5 class="entry-title mb-3"><a
														href="/yapit/<?php echo $story_row["slug"]; ?>"><?php echo replace_ascii($story_row["baslik"]); ?></a>
												</h5>
												<div class="entry-meta align-items-center">
													<a
														href="/yazar/<?php echo $story_row["yazar_slug"]; ?>"><?php echo $story_row["yazar_ad"] ?></a>
													- <a
														href="/kume/<?php echo $story_row['ust_kategori_slug'] . "/" . $story_row["kategori_slug"]; ?>"><?php echo $story_row["kategori_ad"]; ?></a><br>
													<span><?php echo time_elapsed_string($story_row["katilma_tarihi"]) ?></span>
													<span class="middotDivider"></span>
													<span class="readingTime"
													      title="<?php echo site_estimated_reading_time($story_row["yazi"]); ?>"><?php echo site_estimated_reading_time($story_row["yazi"]); ?></span>
												</div>
											</div>
										</div>
									</article>

									<?php

									if ($counter === 7 || $counter === 17) {
										?>
										</div>
										<?php
									}
								} else {
									?>
									<article class="row justify-content-between mb-5 mr-0">
										<div class="col-md-9 ">
											<div class="align-self-center" style="min-height: 200px;">
												<div class="capsSubtle mb-2"><a
														href="/kume/<?php echo $story_row['ust_kategori_slug'] . "/" . $story_row["kategori_slug"]; ?>"><?php echo $story_row["kategori_ad"]; ?></a>
												</div>
												<h3 class="entry-title mb-3"><a
														href="/yapit/<?php echo $story_row["slug"]; ?>"><?php echo replace_ascii($story_row["baslik"]); ?></a>
												</h3>
												<div class="entry-excerpt">
													<p>
														<?php
															if ($story_row['ust_kategori_slug'] === "siir") {
																echo get_words(replace_ascii($story_row["yazi"]), 16, false);
															} else {
																echo get_words(replace_ascii($story_row["tanitim"]), 48);
															}
														?>
													</p>
												</div>
												<div class="entry-meta align-items-center">
													<a
														href="/yazar/<?php echo $story_row["yazar_slug"]; ?>"><?php echo $story_row["yazar_ad"] ?></a><br>
													<span><?php echo time_elapsed_string($story_row["katilma_tarihi"]) ?></span>
													<span class="middotDivider"></span>
													<span class="readingTime"
													      title="<?php echo site_estimated_reading_time($story_row["yazi"]); ?>"><?php echo site_estimated_reading_time($story_row["yazi"]);; ?></span>
												</div>
											</div>
										</div>
										<div class="col-md-3"><a href="/yapit/<?php echo $story_row["slug"]; ?>"><?php echo get_image($story_row['yazi_ana_resim'] ?? '', $category_images, $story_row['kategori_id'], 'bgcover2', ''); ?></a></div>
									</article>
									<?php
								}
							}
						}
						if ($counter < 7 || $counter < 17) {
					?>
				</div>
				<?php
					}
				?>

				<script>
					var currentPage = <?php echo $CurrentPage; ?>;

					var numberOfItems = <?php echo $TotalArticle; ?>;
					var limitPerPage = 20;
					// Total pages rounded upwards
					var totalPages = Math.ceil(numberOfItems / limitPerPage);
					var NaviationNextPageURL = "/kume/<?php echo $story_row['ust_kategori_slug']; ?>/sayfa/";
				</script>
				<?php

					include_once "pagination.php";
				?>


			</div> <!--col-md-8-->
			<div class="col-md-4 pl-md-5 sticky-sidebar">
				<?php
					include_once "category_sidebar.php";
				?>
			</div> <!--col-md-4-->
		</div>
	</div> <!--content-widget-->
	</div>

	<div class="content-widget">
		<div class="container">
			<div class="sidebar-widget ads">
				<a href="#"><img src="/frontend/assets/images/ads/ads-2.png" alt="ads" style="max-width:80%;"></a>
			</div>
			<div class="hr"></div>
		</div>
	</div> <!--content-widget-->
</main>

<!-- Footer -->
<?php include "shop_page_footer.php"; ?>
<!-- /Footer -->

</section>
</html>
