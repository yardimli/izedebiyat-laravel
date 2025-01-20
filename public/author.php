<?php
	require_once 'shop_global_backend.php';


	$author_slug = $_GET["slug"];

	$stmt = $dbconn->prepare("SELECT * FROM yazar WHERE slug=?");
	$stmt->bind_param("s", $author_slug);
	$stmt->execute();
	$result = $stmt->get_result();
	if ($result->num_rows === 0) {
		header("Location: /404");
		exit();
	}
	$author_row = $result->fetch_assoc();

	$author_name = $author_row["yazar_ad"];


	$pageName = "shopIndex";
	$headPageTitle = "İzEdebiyat - " . $author_name;

	session_start();
?>
<!doctype html>

<html lang="tr">
<head>
	<?php include "shop_head.php"; ?>
</head>
<body class="archive">
<script>
	$(document).ready(function () {

		$('.readmore').click(function (event) {
			$("#" + $(this).data("tid")).slideDown();
			console.log($(this).data("tid"));

			// Toggle the controls
			$(".readmore").hide();
			$(".readless").show();

			event.preventDefault();
		});

		$('.readless').click(function (event) {
			$("#" + $(this).data("tid")).slideUp();

			// Toggle the controls
			$(".readless").hide();
			$(".readmore").show();

			event.preventDefault();
		});


	});

</script>

<!--Header -->
<?php include "shop_page_header.php"; ?>
<!-- /Header -->

<main id="content">
	<div class="content-widget">
		<div class="container">
			<div class="row">
				<div class="col-md-8 col-12">
					<div class="box box-author mb-2">
						<div class="post-author row-flex">
							<div class="author-img">
								<?php generateInitialsAvatar( $author_row["yazar_resim"], $author_row["yazar_ad"] ); ?><br>
							</div>
							<div class="author-content">
								<div class="top-author">
									<h5 class="heading-font"><?php
											echo $author_row["yazar_ad"];

											//	echo  "<br><br>";
											//	echo $author_row["sayfa_baslik"];
										?></h5></div>
								<p class="d-none d-md-block"><?php

										$about_author = $author_row["yazar_tanitim"];
										$about_author = preg_replace("/\r\n|\n\r|\r|\n/", '<br/>', $about_author);
									?>
								<p>
									<?php echo $about_author; ?>
								</p>

								<div class="readmore" data-tid="yazar_hakkinda">
									Devamını göster &gt;
								</div>

								<p class="hide mt-2" id="yazar_hakkinda">
									<?php echo $author_row["yazar_gecmis"];
										if ($author_row["yazar_gecmis"] !== "") {
											echo "<br><br>";
										}

										echo $author_row["yazar_konum"];
										if ($author_row["yazar_konum"] !== "") {
											echo "<br><br>";
										}

										echo $author_row["yazar_resim_yazi"];
										if ($author_row["yazar_resim_yazi"] !== "") {
											echo "<br><br>";
										}

										echo $author_row["site_adres"];
										if ($author_row["site_adres"] !== "") {
											echo "<br><br>";
										}

										echo $author_row["katilma_tarihi"];
									?>

								</p>

								<div class="readless hide" data-tid="yazar_hakkinda">
									Daha az &gt;
								</div>

								<!--								<div class="content-social-author">-->
								<!--									<a target="_blank" class="author-social" href="#">Facebook </a>-->
								<!--									<a target="_blank" class="author-social" href="#">Twitter </a>-->
								<!--									<a target="_blank" class="author-social" href="#"> Google + </a>-->
								<!--								</div>-->
							</div>
						</div>
					</div>
					<h4 class="spanborder">
						<span>Yeni</span>
					</h4>
					<?php


						$TotalArticle = 0;
						$CurrentPage = 1;
						$story_command = "SELECT Count(*) AS TotalArticle FROM yazilar WHERE onay=1 AND silindi=0 AND bad_critical<4 AND yazar_id =" . $author_row["id"] . "";
						if ($story_result = $dbconn->query($story_command)) {
							$story_row = $story_result->fetch_assoc();
							$TotalArticle = $story_row["TotalArticle"];
						}

						$CurrentPage = isset($_GET["sayfa"]) ? intval($_GET["sayfa"]) : 1;

						$story_command = "SELECT * FROM yazilar WHERE onay=1 AND silindi=0 AND bad_critical<4 AND yazar_id =" . $author_row["id"] . " ORDER BY katilma_tarihi DESC LIMIT 20 OFFSET " . (($CurrentPage - 1) * 20);
						//					echo $story_command;
						$story_result = $dbconn->query($story_command);
						$counter = 0;
						$authors = [];

						$div_open = false;

						while (($story_row = $story_result->fetch_assoc()) && ($counter < 20)) {
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
										<?php echo $story_row["yazar_ad"] ?> - <a
											href="/kume/<?php echo $story_row['ust_kategori_slug'] . "/" . $story_row["kategori_slug"]; ?>"><?php echo $story_row["ust_kategori_ad"] . " - " . $story_row["kategori_ad"]; ?></a><br>
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
									$div_open = true;
									?>
									<div class="row justify-content-between">
									<div class="divider-2"></div>
									<?php
								}

								?>
								<article class="col-md-6">
									<div class="mb-3 d-flex row">
										<figure class="col-md-5"><a href="/yapit/<?php echo $story_row["slug"]; ?>"><?php echo get_image($story_row['yazi_ana_resim'] ?? '', $category_images, $story_row['kategori_id'], '', 'width: 100%'); ?></a>
										</figure>
										<div class="entry-content col-md-7 pl-md-0">
											<h5 class="entry-title mb-1"><a
													href="/yapit/<?php echo $story_row["slug"]; ?>"><?php echo replace_ascii($story_row["baslik"]); ?></a>
											</h5>
											<div class="entry-meta align-items-center">
												<?php echo $story_row["yazar_ad"] ?> <br>
												<span><?php echo time_elapsed_string($story_row["katilma_tarihi"]) ?></span>
												<br>
												<span class="readingTime"
												      title="<?php echo site_estimated_reading_time($story_row["yazi"]); ?>"><?php echo site_estimated_reading_time($story_row["yazi"]); ?></span>
											</div>
										</div>
									</div>
								</article>

								<?php

								if ($counter === 7 || $counter === 17) {
									$div_open = false;
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
													href="/kume/<?php echo $story_row['ust_kategori_slug'] . "/" . $story_row["kategori_slug"]; ?>"><?php echo $story_row["ust_kategori_ad"] . " - " . $story_row["kategori_ad"]; ?></a>
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
												<?php echo $story_row["yazar_ad"] ?><br>
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

						if ($div_open) {
						$div_open = false;
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
					var NaviationNextPageURL = "/yazar/<?php echo $author_slug; ?>/sayfa/";
				</script>
				<?php

					include_once "pagination.php";
				?>

			</div> <!--col-md-8-->
			<div class="col-md-4 pl-md-5 sticky-sidebar">
				<?php
					include_once "author_sidebar.php";
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
<style>
    .hide {
        display: none;
    }

    .readmore, .readless, .readmuchmore, .readmuchless {
        text-align: right;
        cursor: pointer;
    }
</style>
<!-- Footer -->
<?php include "shop_page_footer.php"; ?>
<!-- /Footer -->
</div> <!--#wrapper-->

</body>
</html>
