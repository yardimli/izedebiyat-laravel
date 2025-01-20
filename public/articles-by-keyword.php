<?php
	require_once 'shop_global_backend.php';
	session_start();
?>
<!doctype html>
<html lang="tr">
<head>
	<?php
		$tag_slug = $_GET["etiket_slug"];

		// Get keyword information
		$command1 = "SELECT * FROM keywords WHERE keyword_slug = ?";
		$statement = $dbconn->prepare($command1);
		$statement->bind_param("s", $tag_slug);
		$statement->execute();
		$result1 = $statement->get_result();
		$keyword_row = $result1->fetch_assoc();

		if (!$keyword_row) {
			echo "Tag not found!";
			exit();
		}

		$pageName = "shopIndex";
		$headPageTitle = "İzEdebiyat - Etiket: " . $keyword_row["keyword"];
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
				<div class="col-12">
					<h4 class="spanborder">
						<span>Etiket: <?php echo htmlspecialchars($keyword_row["keyword"]); ?></span>
					</h4>

					<?php
						// Get total count of articles with this tag
						$command_count = "SELECT COUNT(*) as total 
                                    FROM yazilar y 
                                    INNER JOIN yazi_keyword yk ON y.id = yk.yazi_id 
                                    WHERE yk.keyword_id = ? AND y.onay = 1 AND y.silindi = 0";
						$stmt = $dbconn->prepare($command_count);
						$stmt->bind_param("i", $keyword_row["id"]);
						$stmt->execute();
						$result = $stmt->get_result();
						$row = $result->fetch_assoc();
						$TotalArticle = $row['total'];

						$CurrentPage = isset($_GET["sayfa"]) ? intval($_GET["sayfa"]) : 1;
						$limit = 21; // 3 columns × 7 rows
						$offset = ($CurrentPage - 1) * $limit;

						// Get articles
						$command_articles = "SELECT * FROM yazilar 
	           INNER JOIN yazi_keyword ON yazi_keyword.yazi_id = yazilar.id 
						 WHERE yazilar.onay=1 AND yazilar.silindi=0 AND yazilar.bad_critical<3 AND yazi_keyword.keyword_id = ?
						 ORDER BY formul_ekim DESC LIMIT ? OFFSET ?";

						$stmt = $dbconn->prepare($command_articles);
						$stmt->bind_param("iii", $keyword_row["id"], $limit, $offset);
						$stmt->execute();
						$articles_result = $stmt->get_result();
					?>

					<div class="row">
						<?php
							while ($article = $articles_result->fetch_assoc()) {
								?>
								<article class="col-md-4">
									<div class="mb-3 d-flex row">
										<figure class="col-md-5"><a href="/yapit/<?php echo $article["slug"]; ?>"><?php echo get_image($article['yazi_ana_resim'] ?? '', $category_images, $article['kategori_id'], '','width:100%;');?></a></figure>
										<div class="entry-content col-md-7 pl-md-0">
											<h5 class="entry-title mb-3"><a href="/yapit/<?php echo $article["slug"]; ?>"><?php echo replace_ascii( $article["baslik"] ); ?></a></h5>
											<div class="entry-meta align-items-center">
												<a href="/yazar/<?php echo $article['yazar_slug']; ?>"><?php echo $article["yazar_ad"] ?></a> <br> <a
													href="/kume/<?php echo $article["ust_kategori_slug"] . "/" . $article["kategori_slug"]; ?>"><?php echo $article["ust_kategori_ad"] . "  <span class=\"middotDivider\"></span> " . $article["kategori_ad"]; ?></a><br>
												<span><?php echo time_elapsed_string( $article["katilma_tarihi"] ) ?></span>
												<span class="middotDivider"></span>
												<span class="readingTime"
												      title="<?php echo site_estimated_reading_time( $article["yazi"] ); ?>"><?php echo site_estimated_reading_time( $article["yazi"] ); ?></span>
											</div>
										</div>
									</div>
								</article>
								<?php
							}
						?>
					</div>

					<script>
						var currentPage = <?php echo $CurrentPage; ?>;
						var numberOfItems = <?php echo $TotalArticle; ?>;
						var limitPerPage = <?php echo $limit; ?>;
						var totalPages = Math.ceil(numberOfItems / limitPerPage);
						var NaviationNextPageURL = "/etiket/<?php echo $tag_slug; ?>/sayfa/";
					</script>
					<?php include_once "pagination.php"; ?>

				</div>
			</div>
		</div>
	</div>
</main>

<!-- Footer -->
<?php include "shop_page_footer.php"; ?>
<!-- /Footer -->

</section>
</html>
