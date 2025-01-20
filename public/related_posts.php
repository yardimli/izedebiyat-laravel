<!--Begin post related-->
<?php
	$related_command = "SELECT * FROM yazilar 
	WHERE yazilar.kategori_id = ? AND yazilar.yazar_id = ? AND onay=1 AND silindi=0 AND bad_critical<3 ORDER BY yazilar.katilma_tarihi DESC LIMIT 20";
	$statement = $dbconn->prepare($related_command);
	$statement->bind_param("ii", $work_row["kategori_id"], $work_row["yazar_id"]);

	$statement->execute();
	$related_result = $statement->get_result();
	$first_row = true;
	$yazi_ids = [];
	$counter = 0;
	while ($related_row = $related_result->fetch_assoc()) {
	if (!in_array($related_row["id"], $yazi_ids) && $counter < 3) {
	$yazi_ids[] = $related_row["id"];
	$counter++;

	if ($first_row) {
	$first_row = false;
?>
<div class="related-posts mb-5">
	<h4 class="spanborder text-center mb-1">
		<span>Yazarın <?php echo $work_row["kategori_ad"]; ?> kümesinde bulunan diğer yazıları...</span>
	</h4>
	<div class="row">
		<div class="divider-2 mb-1"></div>
		<?php
			}
		?>
		<article class="col-md-4">
			<div class="mb-2 d-flex row">
				<figure class="col-md-5"><a href="/yapit/<?php echo $related_row["slug"]; ?>"><?php echo get_image($related_row['yazi_ana_resim'] ?? '', $category_images, $related_row['kategori_id'], '', 'width: 100%'); ?></a>
				</figure>
				<div class="entry-content col-md-7 pl-md-0">
					<h5 class="entry-title mb-2"><a
							href="/yapit/<?php echo $related_row["slug"]; ?>"><?php echo replace_ascii($related_row["baslik"]); ?></a>
					</h5>
					<div class="entry-meta align-items-center">
						<a href="/yazar/<?php echo $related_row['yazar_slug']; ?>"><?php echo $related_row["yazar_ad"] ?></a> - <a
							href="/kume/<?php echo $related_row['ust_kategori_slug'] . "/" . $related_row['alt_kategori_slug']; ?>"><?php echo $related_row['ust_kategori_ad'] . " <br> " . $related_row['alt_kategori_ad']; ?></a><br>
						<span><?php echo time_elapsed_string($related_row["katilma_tarihi"]) ?></span>
						<span class="middotDivider"></span>
						<span class="readingTime"><?php echo site_estimated_reading_time($related_row["yazi"]); ?></span>
					</div>
				</div>
			</div>
		</article>

		<?php
			}
			}
			if (!$first_row) {
		?>
	</div>
</div>
<!--End post related-->
<?php
	}

?>





<?php
	$related_command = "SELECT * FROM yazilar 
	WHERE ust_kategori_id = ? AND yazilar.yazar_id = ? AND onay=1 AND silindi=0 AND bad_critical<3 ORDER BY yazilar.katilma_tarihi DESC LIMIT 20";
	$statement = $dbconn->prepare($related_command);
	$statement->bind_param("ii", $work_row["ust_kategori_id"], $work_row["yazar_id"]);

	$statement->execute();
	$related_result = $statement->get_result();
	$first_row = true;

	$counter = 0;
	while ($related_row = $related_result->fetch_assoc()) {
	if (!in_array($related_row["id"], $yazi_ids) && $counter < 3) {
	$yazi_ids[] = $related_row["id"];
	$counter++;

	if ($first_row) {
	$first_row = false;
?>
<div class="related-posts mb-5">
	<h4 class="spanborder text-center mb-1">
		<span>Yazarın <?php echo $work_row["ust_kategori_ad"]; ?> kümesinde bulunan diğer yazıları...</span>
	</h4>
	<div class="row ">
		<div class="divider-2 mb-1"></div>
		<?php
			}
		?>
		<article class="col-md-4">
			<div class="mb-2 d-flex row">
				<figure class="col-md-5"><a href="/yapit/<?php echo $related_row["slug"]; ?>"><?php echo get_image($related_row['yazi_ana_resim'] ?? '', $category_images, $related_row['kategori_id'], '', 'width: 100%'); ?></a>
				</figure>
				<div class="entry-content col-md-7 pl-md-0">
					<h5 class="entry-title mb-2"><a
							href="/yapit/<?php echo $related_row["slug"]; ?>"><?php echo replace_ascii($related_row["baslik"]); ?></a>
					</h5>
					<div class="entry-meta align-items-center">
						<a href="/yazar/<?php echo $related_row['yazar_slug']; ?>"><?php echo $related_row["yazar_ad"] ?></a> - <a
							href="/kume/<?php echo $related_row['ust_kategori_slug'] . "/" . $related_row['alt_kategori_slug']; ?>"><?php echo $related_row['ust_kategori_ad'] . " <br> " . $related_row['alt_kategori_ad']; ?></a><br>
						<span><?php echo time_elapsed_string($related_row["katilma_tarihi"]) ?></span>
						<span class="middotDivider"></span>
						<span class="readingTime"><?php echo site_estimated_reading_time($related_row["yazi"]); ?></span>
					</div>
				</div>
			</div>
		</article>

		<?php
			}
			}
			if (!$first_row) {
		?>
	</div>
</div>
<!--End post related-->
<?php
	}
?>





<?php
	$related_command = "SELECT * FROM yazilar 
	WHERE yazilar.yazar_id = ? AND onay=1 AND silindi=0 AND bad_critical<3 ORDER BY yazilar.katilma_tarihi DESC LIMIT 20";
	$statement = $dbconn->prepare($related_command);
	$statement->bind_param("i", $work_row["yazar_id"]);

	$statement->execute();
	$related_result = $statement->get_result();
	$first_row = true;
	$counter = 0;
	while ($related_row = $related_result->fetch_assoc()) {
	if (!in_array($related_row["id"], $yazi_ids) && $counter < 6) {
		$counter++;
	$yazi_ids[] = $related_row["id"];


	if ($first_row) {
	$first_row = false;
?>
<div class="related-posts mb-5">
	<h4 class="spanborder text-center mb-1">
		<span>Yazarın diğer ana kümelerde yazmış olduğu yazılar...</span>
	</h4>
	<div class="row ">
		<div class="divider-2 mb-1"></div>
		<?php
			}
		?>
		<article class="col-md-4">
			<div class="mb-2 d-flex row">
				<figure class="col-md-5"><a href="/yapit/<?php echo $related_row["slug"]; ?>"><?php echo get_image($related_row['yazi_ana_resim'] ?? '', $category_images, $related_row['kategori_id'], '', 'width: 100%'); ?></a>
				</figure>
				<div class="entry-content col-md-7 pl-md-0">
					<h5 class="entry-title mb-2"><a
							href="/yapit/<?php echo $related_row["slug"]; ?>"><?php echo replace_ascii($related_row["baslik"]); ?></a>
					</h5>
					<div class="entry-meta align-items-center">
						<a href="/yazar/<?php echo $related_row['yazar_slug']; ?>"><?php echo $related_row["yazar_ad"] ?></a> - <a
							href="/kume/<?php echo $related_row['ust_kategori_slug'] . "/" . $related_row['alt_kategori_slug']; ?>"><?php echo $related_row['ust_kategori_ad'] . " <br> " . $related_row['alt_kategori_ad']; ?></a><br>
						<span><?php echo time_elapsed_string($related_row["katilma_tarihi"]) ?></span>
						<span class="middotDivider"></span>
						<span class="readingTime"><?php echo site_estimated_reading_time($related_row["yazi"]); ?></span>
					</div>
				</div>
			</div>
		</article>

		<?php
			}
			}
			if (!$first_row) {
		?>
	</div>
</div>
<!--End post related-->
<?php
	}
?>
