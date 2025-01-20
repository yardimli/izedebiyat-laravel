<div class="sidebar-widget latest-tpl-4">
	<h5 class="spanborder widget-title">
		<span>Son Eklenenler</span>
	</h5>
	<ol>
		<?php
			$category_sidebar_command = "SELECT * FROM yazilar WHERE onay=1 AND silindi=0 AND bad_critical<3 AND ust_kategori_id = ? ORDER BY katilma_tarihi DESC LIMIT 500 OFFSET ?";
			$statement5 = $dbconn->prepare($category_sidebar_command);
			$offset = (($CurrentPage-1) * 10);
			$statement5->bind_param("ii", $ust_kategori_id, $offset);
			$statement5->execute();
			$category_sidebar_result = $statement5->get_result();

			$counter = 0;

			$authors = [];
			$author_counts = [];
			$previous_author = null;

			while (($category_sidebar_row = $category_sidebar_result->fetch_assoc()) && ($counter < 20)) {
				$current_author = $category_sidebar_row["yazar_id"];

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
									href="/yapit/<?php echo $category_sidebar_row["slug"]; ?>"><?php echo replace_ascii($category_sidebar_row["baslik"]); ?></a></h5>
							<div class="entry-meta align-items-center">
								<a href="/yazar/<?php echo $category_sidebar_row["yazar_slug"]; ?>"><?php echo $category_sidebar_row["yazar_ad"] ?></a><br>
								<span><?php echo time_elapsed_string($category_sidebar_row["katilma_tarihi"]) ?></span>
								<span class="middotDivider"></span>
								<span class="readingTime"
								      title="3 min read"><?php echo site_estimated_reading_time($category_sidebar_row["yazi"]); ?> </span>
							</div>
						</div>
					</li>
					<?php
				}
			}

		?>
	</ol>
</div>

<?php
?>
