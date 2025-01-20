<?php
	require_once 'shop_global_backend.php';
	session_start();
?>
<!doctype html>

<html lang="tr">
<head>
	<?php
		$pageName = "shopIndex";
		$headPageTitle = "İzEdebiyat - Uyelik";
		include "shop_head.php";
	?>
</head>
<body class="home">
<?php
	echo $_SERVER['DOCUMENT_ROOT'];
?>
<!--Header -->
<?php include "shop_page_header.php"; ?>
<!-- /Header -->

<section class="home">
<main id="content">
	<?php
		$command1 = "SELECT * FROM kategoriler WHERE ust_kategori_id=0 ORDER BY kategori_ad";
		$result1 = $dbconn->query($command1);
		$alternate_bg = true;
		while ($row1 = $result1->fetch_assoc()) {
			$ust_kategori_id = $row1["id"];
			$alternate_bg = !$alternate_bg;
			$alternate_bg_class = $alternate_bg ? "alternate_background" : "";
			?>
			<div class="section-featured featured-style-1 pt-2 mb-4 <?php echo $alternate_bg_class; ?>">
				<div class="container">
					<div class="row">
						<!--begin featured-->
						<div class="col-sm-12 col-md-9 col-xl-9">
							<h2 class="spanborder h4">
								<span><?php echo $row1["kategori_ad"]; ?></span>
							</h2>
							<div class="row">
								<?php
									$story_command = "SELECT * FROM yazilar WHERE onay=1 AND silindi=0 AND bad_critical<3 AND religious_moderation_value<3 AND respect_moderation_value>=3 AND moderation_flagged = 0 AND ust_kategori_id =" . $ust_kategori_id . " ORDER BY formul_ekim DESC LIMIT 100";
									//echo $story_command;
									$story_result = $dbconn->query($story_command);
									$counter = 0;
									$authors = [];

									while (($story_row = $story_result->fetch_assoc()) && ($counter < 20)) {

										if (!in_array($story_row["yazar_id"], $authors)) {
											$counter++;
											$authors[] = $story_row["yazar_id"];
											if ($counter === 1) {
												?>
												<div class="col-sm-12 col-md-6">
												<article class="first mb-4">
													<figure><a href="/yapit/<?php echo $story_row["slug"]; ?>"><?php echo get_image($story_row['yazi_ana_resim'] ?? '', $category_images, $story_row['kategori_id'], '', 'width: 100%'); ?></a></figure>
													<h3 class="entry-title mb-2"><a
															href="/yapit/<?php echo $story_row["slug"]; ?>"><?php echo replace_ascii($story_row["baslik"]); ?></a>
													</h3>
													<div class="entry-excerpt">
														<p class="mb-1">
															<?php
																if ($story_row['ust_kategori_slug'] === "siir") {
																	echo get_words(replace_ascii($story_row["yazi"]), 16, false);
																} else {
																	echo get_words(replace_ascii($story_row["tanitim"]), 16);
																}
															?>
														</p>
													</div>
													<div class="entry-meta align-items-center">
														<a href="/yazar/<?php echo $story_row["yazar_slug"]; ?>"><?php echo $story_row["yazar_ad"] ?></a> -
														<a
															href="/kume/<?php echo $story_row['ust_kategori_slug'] . "/" . $story_row["kategori_slug"]; ?>"><?php echo $story_row["kategori_ad"]; ?></a><br>
														<span><?php echo time_elapsed_string($story_row["katilma_tarihi"]) ?></span>
														<span class="middotDivider"></span>
														<span class="readingTime"
														      title="<?php echo site_estimated_reading_time($story_row["yazi"]); ?>"><?php echo site_estimated_reading_time($story_row["yazi"]); ?></span>

													</div>
												</article>
												</div>
												<div class="col-sm-12 col-md-6">
												<?php
											}

											if ($counter > 1 && $counter <= 5) {
												?>
												<article>
													<div class="mb-3 d-flex row">
														<figure class="col-4 col-md-4"><a href="/yapit/<?php echo $story_row["slug"]; ?>"><?php echo get_image($story_row['yazi_ana_resim'] ?? '', $category_images, $story_row['kategori_id'], '', 'width: 100%'); ?></a></figure>
														<div class="entry-content col-8 col-md-8 pl-md-0">
															<h5 class="entry-title mb-2"><a
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
											}
											if ($counter === 5) {
												?>
												</div>
												<?php
											}
										}
									}

								?>
							</div>
						</div>
						<!--end featured-->

						<!--begin Trending-->
						<div class="col-sm-12 col-md-3 col-xl-3">
							<div class="sidebar-widget latest-tpl-4">
								<h4 class="spanborder">
									<span>Yeni</span>
								</h4>
								<ol>
									<?php
										$story_command = "SELECT * FROM yazilar WHERE onay=1 AND silindi=0 AND bad_critical<3 AND respect_moderation_value>=3 AND moderation_flagged = 0 AND ust_kategori_id =" . $ust_kategori_id . " ORDER BY id DESC LIMIT 300";
										$story_result = $dbconn->query($story_command);
										$counter = 0;
										$authors = [];

										while (($story_row = $story_result->fetch_assoc()) && ($counter < 5)) {

											if (!in_array($story_row["yazar_id"], $authors)) {
												$counter++;
												$authors[] = $story_row["yazar_id"];
												?>
												<li class="d-flex">
													<div class="post-count"><?php echo str_pad($counter, 2, "0", STR_PAD_LEFT); ?></div>
													<div class="post-content">
														<h5 class="entry-title mb-2"><a href="/yapit/<?php echo $story_row["slug"]; ?>"><?php echo replace_ascii($story_row["baslik"]); ?></a></h5>
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
												</li>
												<?php
											}
										}
									?>
								</ol>
							</div>
						</div> <!--end Trending-->
					</div> <!--end row-->
<!--					<div class="divider"></div>-->
				</div> <!--end container-->
			</div> <!--end section-featured-->
			<?php
		}
	?>

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
