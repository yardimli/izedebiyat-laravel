<?php
	require_once 'shop_global_backend.php';
	$pageName = "yazarlar";
	$headPageTitle = "İzEdebiyat - Yazarlar";
	session_start();

	// Get filter parameter
	$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
	$CurrentPage = isset($_GET['sayfa']) ? (int)$_GET['sayfa'] : 1;
	$per_page = 24;
	$offset = ($CurrentPage - 1) * $per_page;

	$where_clause = "WHERE EXISTS (
        SELECT 1 FROM yazilar 
        WHERE yazilar.yazar_id = yazar.id 
        AND yazilar.onay = 1 
        AND yazilar.silindi = 0
    )";

	if ($filter !== 'all' && mb_strlen($filter, 'UTF-8') === 1) {
		$where_clause .= " AND yazar_ad COLLATE utf8mb4_bin LIKE ?";
	}

	// Get total count
	$count_sql = "SELECT COUNT(DISTINCT yazar.id) as TotalArticle 
                  FROM yazar 
                  " . $where_clause;
	$stmt = $dbconn->prepare($count_sql);
	if ($filter !== 'all' && mb_strlen($filter, 'UTF-8') === 1) {
		$like_param = $filter . '%';
		$stmt->bind_param('s', $like_param);
	}
	$stmt->execute();
	$total_result = $stmt->get_result();
	$total_row = $total_result->fetch_assoc();
	$TotalArticle = $total_row['TotalArticle'];

	// Get authors
	$sql = "SELECT DISTINCT yazar.* 
            FROM yazar 
            " . $where_clause . " 
            ORDER BY yazar_ad 
            LIMIT ? OFFSET ?";
	$stmt = $dbconn->prepare($sql);

	if ($filter !== 'all' && mb_strlen($filter, 'UTF-8') === 1) {
		$stmt->bind_param('sii', $like_param, $per_page, $offset);
	} else {
		$stmt->bind_param('ii', $per_page, $offset);
	}
	$stmt->execute();
	$authors = $stmt->get_result();
?>

<!doctype html>
<html lang="tr">
<head>
	<?php include "shop_head.php"; ?>
	<style>
      .yz-resim {
		      position: absolute;
          bottom: 0;
          right: 40%;
          transform: translateX(50%);
          background-color: rgba(0, 0, 0, 0.8);
          color: #aaa;
          padding: 2px 6px;
          border-radius: 50%;
          font-size: 0.7em;
      }
      .author-image {
          position: relative;
          display: flex;
          justify-content: center;
          align-items: center;
      }

          .filter-buttons {
              margin-bottom: 30px;
              text-align: center;
          }

          .filter-buttons a {
              margin-left: 1px;
              margin-right: 1px;
              padding: 5px 10px;
              border: 1px solid #ddd;
              border-radius: 3px;
              text-decoration: none;
              display: inline-block;
          }

          .filter-buttons a.active {
              background-color: #007bff;
              color: white;
          }

          .author-card {
              border: 1px solid #ddd;
              padding: 20px;
              margin-bottom: 20px;
              border-radius: 5px;
              height: 100%;
              text-align: center;
          }

          .author-image {
              display: flex;
              justify-content: center;
              align-items: center;
          }

          .author-image img {
              box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
              transition: transform 0.3s ease;
          }

          .author-image img:hover {
              transform: scale(1.05);
          }

          .recent-works {
              margin-top: 20px;
              font-size: 0.9em;
              text-align: left;
          }

          .recent-works ul {
              list-style: none;
              padding-left: 0;
          }

          .recent-works li {
              margin-bottom: 5px;
          }
      }
	</style>
</head>
<body class="archive">
<?php include "shop_page_header.php"; ?>

<main id="content">
	<div class="container">
		<h1 class="text-center mb-4">Yazarlar</h1>

		<!-- Filter Buttons -->
		<div class="filter-buttons">
			<a href="?filter=all" class="<?php echo $filter === 'all' ? 'active' : ''; ?>">Tümü</a>
			<a href="?filter=A" class="<?php echo $filter === 'A' ? 'active' : ''; ?>">A</a>
			<a href="?filter=B" class="<?php echo $filter === 'B' ? 'active' : ''; ?>">B</a>
			<a href="?filter=C" class="<?php echo $filter === 'C' ? 'active' : ''; ?>">C</a>
			<a href="?filter=Ç" class="<?php echo $filter === 'Ç' ? 'active' : ''; ?>">Ç</a>
			<a href="?filter=D" class="<?php echo $filter === 'D' ? 'active' : ''; ?>">D</a>
			<a href="?filter=E" class="<?php echo $filter === 'E' ? 'active' : ''; ?>">E</a>
			<a href="?filter=F" class="<?php echo $filter === 'F' ? 'active' : ''; ?>">F</a>
			<a href="?filter=G" class="<?php echo $filter === 'G' ? 'active' : ''; ?>">G</a>
			<a href="?filter=H" class="<?php echo $filter === 'H' ? 'active' : ''; ?>">H</a>
			<a href="?filter=I" class="<?php echo $filter === 'I' ? 'active' : ''; ?>">I</a>
			<a href="?filter=İ" class="<?php echo $filter === 'İ' ? 'active' : ''; ?>">İ</a>
			<a href="?filter=J" class="<?php echo $filter === 'J' ? 'active' : ''; ?>">J</a>
			<a href="?filter=K" class="<?php echo $filter === 'K' ? 'active' : ''; ?>">K</a>
			<a href="?filter=L" class="<?php echo $filter === 'L' ? 'active' : ''; ?>">L</a>
			<a href="?filter=M" class="<?php echo $filter === 'M' ? 'active' : ''; ?>">M</a>
			<a href="?filter=N" class="<?php echo $filter === 'N' ? 'active' : ''; ?>">N</a>
			<a href="?filter=O" class="<?php echo $filter === 'O' ? 'active' : ''; ?>">O</a>
			<a href="?filter=Ö" class="<?php echo $filter === 'Ö' ? 'active' : ''; ?>">Ö</a>
			<a href="?filter=P" class="<?php echo $filter === 'P' ? 'active' : ''; ?>">P</a>
			<a href="?filter=R" class="<?php echo $filter === 'R' ? 'active' : ''; ?>">R</a>
			<a href="?filter=S" class="<?php echo $filter === 'S' ? 'active' : ''; ?>">S</a>
			<a href="?filter=Ş" class="<?php echo $filter === 'Ş' ? 'active' : ''; ?>">Ş</a>
			<a href="?filter=T" class="<?php echo $filter === 'T' ? 'active' : ''; ?>">T</a>
			<a href="?filter=U" class="<?php echo $filter === 'U' ? 'active' : ''; ?>">U</a>
			<a href="?filter=Ü" class="<?php echo $filter === 'Ü' ? 'active' : ''; ?>">Ü</a>
			<a href="?filter=V" class="<?php echo $filter === 'V' ? 'active' : ''; ?>">V</a>
			<a href="?filter=Y" class="<?php echo $filter === 'Y' ? 'active' : ''; ?>">Y</a>
			<a href="?filter=Z" class="<?php echo $filter === 'Z' ? 'active' : ''; ?>">Z</a>
		</div>

		<!-- Authors Grid -->
		<!-- Authors Grid -->
		<div class="row">
			<?php while ($author = $authors->fetch_assoc()): ?>
				<div class="col-md-4 mb-4">
					<div class="author-card">
						<!-- Add author image -->
						<!-- Add author image -->
						<div class="author-image mb-3 position-relative">
							<?php generateInitialsAvatar($author["yazar_resim"], $author["yazar_ad"], 'width: 100px; height: 100px; object-fit: cover;', 'img-fluid rounded-circle'); ?>
							<?php if (strpos($author["yazar_resim"], 'ai_yazar_resimler') !== false): ?>
								<span class="yz-resim">YZ</span>
							<?php endif; ?>
						</div>
						<h4><a href="/yazar/<?php echo $author['slug']; ?>"><?php echo $author['yazar_ad']; ?></a></h4>
						<p><?php echo substr($author['yazar_tanitim'], 0, 100) . '...'; ?></p>
						<!-- Recent Works -->
						<div class="recent-works">
							<h5>Son Yapıtları</h5>
							<?php
								// Update the SQL query to include katilma_tarihi
								$works_stmt = $dbconn->prepare("SELECT baslik, slug, katilma_tarihi FROM yazilar WHERE yazar_id = ? AND onay=1 AND silindi=0 ORDER BY katilma_tarihi DESC LIMIT 3");
								$works_stmt->bind_param('i', $author['id']);
								$works_stmt->execute();
								$works = $works_stmt->get_result();
							?>
							<ul>
								<?php while ($work = $works->fetch_assoc()): ?>
									<li>
										<a href="/yapit/<?php echo $work['slug']; ?>"><?php echo $work['baslik']; ?></a>
										<small class="text-muted">
											(<?php echo time_elapsed_string($work['katilma_tarihi']); ?>)
										</small>
									</li>
								<?php endwhile; ?>
							</ul>
						</div>
					</div>
				</div>
			<?php endwhile; ?>
		</div>

		<script>
			var currentPage = <?php echo $CurrentPage; ?>;
			var numberOfItems = <?php echo $TotalArticle; ?>;
			var limitPerPage = <?php echo $per_page; ?>;
			var totalPages = Math.ceil(numberOfItems / limitPerPage);
			var NaviationNextPageURL = "/yazarlar?filter=<?php echo $filter; ?>&sayfa=";
		</script>
		<?php include_once "pagination.php"; ?>
	</div>
</main>

<?php include "shop_page_footer.php"; ?>
</body>
</html>
