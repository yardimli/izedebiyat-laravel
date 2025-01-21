<?php
	require_once 'shop_global_backend.php';
	session_start();
?>
<!doctype html>

<html lang="tr">
<head>
	<?php

		$ust_kategori_slug = $_GET["ust_kategori_slug"];
		$ust_kategori_id = 0;
		$command1 = "SELECT * FROM kategoriler WHERE ust_kategori_id=0 AND slug='" . mysqli_real_escape_string($dbconn, $ust_kategori_slug) . "'";
		$result1 = $dbconn->query($command1);
		while ($row1 = $result1->fetch_assoc()) {
			$ust_kategori_id = $row1["id"];
			$ust_kategori_ad = $row1["kategori_ad"];
		}


		$alt_kategori_slug = $_GET["alt_kategori_slug"];
		$command1 = "SELECT * FROM kategoriler WHERE ust_kategori_id='" . mysqli_real_escape_string($dbconn, $ust_kategori_id) . "' AND slug='" . mysqli_real_escape_string($dbconn, $alt_kategori_slug) . "'";
		$result1 = $dbconn->query($command1);
		while ($row1 = $result1->fetch_assoc()) {
			$alt_kategori_ad = $row1["kategori_ad"];
			$alt_kategori_resim = $row1["picture"];
			$alt_kategori_id = $row1["id"];
		}

		$pageName = "shopIndex";
		$headPageTitle = "İzEdebiyat - " . $ust_kategori_ad . " - " . $alt_kategori_ad;
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
						<span style="text-transform: uppercase;"><?php
								echo "<a href=\"/kume/" . $ust_kategori_slug . "\">" . $ust_kategori_ad . "</a>";
								if ($alt_kategori_ad !== "") {
									echo " > " . $alt_kategori_ad;
								}
							?></span>
					</h4>
					<?php


						$TotalArticle = 0;
						$CurrentPage = 1;
						$story_command = "SELECT Count(*) AS TotalArticle FROM yazilar WHERE onay=1 AND silindi=0 AND bad_critical<3 AND kategori_id =" . $alt_kategori_id . "";
						if ($story_result = $dbconn->query($story_command)) {
							$story_row = $story_result->fetch_assoc();
							$TotalArticle = $story_row["TotalArticle"];
						}

						$CurrentPage = isset($_GET["sayfa"]) ? intval($_GET["sayfa"]) : 1;

						$story_command = "SELECT * FROM yazilar WHERE onay=1 AND silindi=0 AND bad_critical<3 AND kategori_id =" . $alt_kategori_id . " ORDER BY katilma_tarihi DESC LIMIT 300 OFFSET " . (($CurrentPage - 1) * 20);
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
								?>
								<article class="row justify-content-between mb-5 mr-0">
									<div class="col-md-9 ">
										<div class="align-self-center" style="min-height: 200px;">
											<div class="capsSubtle mb-2"><?php echo $story_row["sentiment"]; ?></div>
											<h3 class="entry-title mb-2"><a
													href="/yapit/<?php echo $story_row["slug"]; ?>"><?php echo replace_ascii($story_row["baslik"]); ?></a>
											</h3>
											<div class="entry-excerpt">
												<p class="mb-1">
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
												      title="3 min read"><?php echo site_estimated_reading_time($story_row["yazi"]); ?></span>
											</div>
										</div>
									</div>
									<div class="col-md-3"><a href="/yapit/<?php echo $story_row["slug"]; ?>"><?php echo get_image($story_row['yazi_ana_resim'] ?? '', $category_images, $story_row['kategori_id'], 'bgcover2', ''); ?></a></div>
								</article>
								<?php
							}
						}
					?>

					<script>
						var currentPage = <?php echo $CurrentPage; ?>;

						var numberOfItems = <?php echo $TotalArticle; ?>;
						var limitPerPage = 20;
						// Total pages rounded upwards
						var totalPages = Math.ceil(numberOfItems / limitPerPage);
						var NaviationNextPageURL = "/kume/<?php echo $ust_kategori_slug; ?>/<?php echo $alt_kategori_slug; ?>/sayfa/";
					</script>
					<?php

						include_once "pagination.php";
					?>

				</div> <!--col-md-8-->
				<div class="col-md-4 pl-md-5 sticky-sidebar">
					<div class="sidebar-widget latest-tpl-4">
						<h5 class="spanborder widget-title">
							<span>POPÜLER</span>
						</h5>
						<ol>
							<?php
								$story_command = "SELECT * FROM yazilar WHERE onay=1 AND silindi=0 AND bad_critical<3 AND kategori_id = " . $alt_kategori_id . " ORDER BY formul_ekim DESC LIMIT 300 OFFSET " . (($CurrentPage - 1) * 20);
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
										?>
										<li class="d-flex">
											<div class="post-count"><?php echo str_pad($counter, 2, "0", STR_PAD_LEFT); ?></div>
											<div class="post-content">
												<h5 class="entry-title mb-1"><a
														href="/yapit/<?php echo $story_row["slug"]; ?>"><?php echo $story_row["baslik"] ?></a></h5>
												<div class="entry-meta align-items-center">
													<a
														href="/yazar/<?php echo $story_row["yazar_slug"]; ?>"><?php echo $story_row["yazar_ad"] ?></a><br>
													<span><?php echo time_elapsed_string($story_row["katilma_tarihi"]) ?></span>
													<span class="middotDivider"></span>
													<span class="readingTime"
													      title="3 min read"><?php echo site_estimated_reading_time($story_row["yazi"]); ?> </span>
												</div>
											</div>
										</li>
										<?php
									}
								}

							?>
						</ol>
					</div>
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
