<?php
	require_once 'shop_global_backend.php';


	$work_slug = $_GET["slug"];
	$command1 = "SELECT * FROM yazilar WHERE yazilar.slug = ?";
	$statement = $dbconn->prepare($command1);
	$statement->bind_param("s", $work_slug);
	$statement->execute();
	$result1 = $statement->get_result();

	if ($result1->num_rows > 0) {
		// Get the first row
		$work_row = $result1->fetch_assoc();

		// Now you can use $row to access your data
		// For example: $row['column_name']
	} else {
		echo "work not found!";
		exit();
	}
	$statement->close();

	$command2 = "SELECT * FROM yazar WHERE id = ?";// . $work_row["yazar_id"];
	$statement = $dbconn->prepare($command2);
	$statement->bind_param("i", $work_row["yazar_id"]);
	$statement->execute();
	$result2 = $statement->get_result();
	if ($result2->num_rows > 0) {
		$author_row = $result2->fetch_assoc();
	} else {
		header("Location: /404");
		exit();
	}
	$statement->close();

	$pageName = "shopIndex";
	$headPageTitle = "İzEdebiyat - " . $author_row["yazar_ad"] . " - " . $work_row["baslik"] . " - " . $work_row["kategori_ad"];

	session_start();
?>
<!doctype html>

<html lang="tr">
<head>
<?php include "shop_head.php"; ?>
</head>
<body class="home single">

<!--Header -->
<?php include "shop_page_header.php"; ?>
<!-- /Header -->
<main id="content">
	<div class="container">
		<div class="entry-header">
			<div class="mb-5">
				<h1 class="entry-title mb-2">
					<?php
						echo replace_ascii($work_row["baslik"]);
					?>
				</h1>
				<div class="entry-meta align-items-center">
					<a class="author-avatar" href="/yazar/<?php echo $author_row["slug"]; ?>"><?php generateInitialsAvatar( $author_row["yazar_resim"], $author_row["yazar_ad"] ); ?></a>
					<a href="/yazar/<?php echo $author_row["slug"]; ?>"><?php echo $author_row["yazar_ad"]; ?></a><br><a
						href="/kume/<?php echo $work_row['ust_kategori_slug'] . "/" . $work_row["kategori_slug"]; ?>"><?php echo $work_row["ust_kategori_ad"]; ?><span class="middotDivider"></span> <?php echo $work_row["kategori_ad"]; ?></a><br>
					<span><?php echo $work_row["katilma_tarihi"]; ?></span>
					<span class="middotDivider"></span>
					<span class="readingTime"><?php echo site_estimated_reading_time($work_row["yazi"]); ?></span>
				</div>
			</div>
		</div> <!--end single header-->
		<div class="bar-long"></div>
		<article class="entry-wraper mb-5">
			<div class="entry-left-col">
				<div class="social-sticky">
					<a href="#"><i class="icon-heart"></i></a>
				</div>
			</div>
			<figure class="image zoom mb-5">
				<?php echo get_image($work_row['yazi_ana_resim'] ?? '', $category_images, $work_row['kategori_id'], '', 'width: 100%'); ?>
			</figure>  <!--figure-->

			<div class="excerpt mb-4">
				<p><?php
						echo $work_row["tanitim"];
					?></p>
			</div>
			<div class="entry-main-content "><!--dropcap-->
				<p><?php
						$yazi = $work_row["yazi"];
						$yazi = str_ireplace("\n", "</p><p>", $yazi);
						$yazi = str_ireplace("[[I]]", "<i>", $yazi);
						$yazi = str_ireplace("[[K]]", "<b>", $yazi);
						$yazi = str_ireplace("[[/I]]", "</i>", $yazi);
						$yazi = str_ireplace("[[/K]]", "</b>", $yazi);

						$yazi = preg_replace('/\[\[.*?\]\]/', ' ', $yazi);
						echo $yazi;

					?>
				</p>

			</div>
			<div class="entry-bottom">
				<div class="tags-wrap heading">
        <?php
	        // Get keywords for this article using the junction table
	        $command_keywords = "SELECT k.keyword, k.keyword_slug 
                               FROM keywords k 
                               INNER JOIN yazi_keyword yk ON k.id = yk.keyword_id 
                               WHERE yk.yazi_id = ? AND k.count>1";

	        $statement = $dbconn->prepare($command_keywords);
	        $statement->bind_param("i", $work_row["id"]);
	        $statement->execute();
	        $keywords_result = $statement->get_result();

	        while ($keyword_row = $keywords_result->fetch_assoc()) {
		        ?>
		        <a href="/etiket/<?php echo $keyword_row['keyword_slug']; ?>"><?php echo $keyword_row['keyword']; ?></a>
		        <?php
	        }
	        $statement->close();
        ?>
				</div>
			</div>
			<?php include_once "author_box.php"; ?>

			<?php
				include_once "subscription_box.php"
			?>

		</article> <!--entry-content-->
		<?php
			include_once "related_posts.php"
		?>


	</div> <!--container-->
</main>
<style>
    .bar-long {
        height: 3px;
        background-color: #CCC;
        width: 0px;
        z-index: 1000;
        position: fixed;
        top: 100px;
        left: 0;
    }

    .hide {
        display: none;
    }

    .readmore, .readless, .readmuchmore, .readmuchless {
        text-align: right;
        cursor: pointer;
    }

</style>
<script>
	$(window).scroll(function () {

		const element = document.querySelector('#main-menu');
		const computedStyle = window.getComputedStyle(element);


		var main_menu_height = $("#main-menu").outerHeight();
		console.log(main_menu_height);
		if (computedStyle.display === 'block') {
			$('.bar-long').css("top", (main_menu_height) + "px");
		} else {
			$('.bar-long').css("top", ($(".sticky-header").outerHeight()) + "px");
		}

		// calculate the percentage the user has scrolled down the page
		var scrollwin = $(window).scrollTop();
		var articleheight = $('.entry-main-content').outerHeight(true);
		var windowWidth = $(window).width();
		if (scrollwin >= ($('.entry-main-content').offset().top - 100)) {
			if (scrollwin <= (($('.entry-main-content').offset().top - 100) + (articleheight + 100))) {
				$('.bar-long').css('width', ((scrollwin - ($('.entry-main-content').offset().top - 100)) / (articleheight + 100)) * 100 + "%");
			} else {
				$('.bar-long').css('width', "100%");
			}
		} else {
			$('.bar-long').css('width', "0px");
		}


	});
</script>
<!-- Footer -->
<?php include "shop_page_footer.php"; ?>
<!-- /Footer -->

</body>
</html>
