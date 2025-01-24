<?php
	require_once 'shop_global_backend.php';
	session_start();
?>
<!doctype html>

<html lang="tr">
<head>
	<?php
		$pageName = "shopIndex";
		$headPageTitle = "İzEdebiyat - 404";
		include "shop_head.php";
	?>
</head>
<body class="home">

<!--Header -->
<?php include "shop_page_header.php"; ?>
<!-- /Header -->

<section class="single page-404">
	<main id="content">
		<div class="container">
			<article class="entry-wraper mb-5">
				<h1 class="text-center mb-3 mt-5">404</h1>

				<p class="text-center">The link you clicked may be broken or the page may have been removed.<br>
					visit the <a href="/index.php">Homepage</a> or <a href="contact.html">Contact us</a> about the problem
				</p>
			</article> <!--entry-content-->
		</div> <!--container-->
	</main>
	</div> <!--#wrapper-->

	<!-- Footer -->
	<?php include "shop_page_footer.php"; ?>
	<!-- /Footer -->

</section>
</html>
