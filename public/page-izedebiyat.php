<?php
	require_once 'shop_global_backend.php';
	$pageName = "legal";
	$headPageTitle = "İzEdebiyat - İzEdebiyat Hakkında";

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
					İzEdebiyat Hakkında
				</h1>
				<div class="entry-meta align-items-center">
					<p class="text-center">
						<img src="/frontend/anagrafikler/amblem-buyuk.png" style="max-width: 400px;"><br>
						<br>
						<span style="font-family: Arial, Helvetica, sans-serif; color: red; font-size: 2rem; font-weight: bold">İzEdebiyat Nedir?</span>
					</p>

					<p style="text-align: center;" class="mt-3">
						<a href="#paylasma"><img src="/frontend/ikon/102.gif" style="width:43px; height:43px;" class="mt-1 mb-0"
							></a>
						<a href="#tanisma"><img src="/frontend/ikon/37.gif" style="width:43px; height:43px;" class="mt-1 mb-0" alt="Tanışma"
							></a>
						<a href="#yuzlesme"><img src="/frontend/ikon/80.gif" style="width:43px; height:43px;" class="mt-1 mb-0"
						                         alt="Yüzleşme"></a>
						<a href="#eglence"><img src="/frontend/ikon/99.gif" style="width:43px; height:43px;" class="mt-1 mb-0" alt="Eğlence"
							></a>
						<a href="#imgelem"><img src="/frontend/ikon/48.gif" style="width:43px; height:43px;" class="mt-1 mb-0" alt="İmgelem"
							></a>
						<a href="#ozgurluk"><img src="/frontend/ikon/107.gif" style="width:43px; height:43px;" class="mt-1 mb-0"
						                         alt="Özgürlük"></a>
						<a href="#katilim"><img src="/frontend/ikon/116.gif" style="width:43px; height:43px;" class="mt-1 mb-0"
						                        alt="Katılım"></a>
					</p>

					<hr>

					<!-------------- Paylaşma -------------->
					<div class="text-center mb-2">
						<img src="/frontend/ikon/102.gif" style="width:43px; height:43px;" class="mt-1 mb-0"><br>
						<h3 class="text-center"
						    style="font-family: Tahoma, Verdana, Arial; font-size: 2rem; font-weight: bold; color: #FF0033">
							Paylaşma</h3>
					</div>

					<p class="text-center"
					   style="font-family: Times New Roman, Times, serif; font-size: 1.5rem; line-height: 1.5; color: #666696">
						Çoğu yazarın en büyük düşü okunmaktır.<br>
						Kimileri buna şöhret der.<br>
						Biz buna paylaşma diyoruz.<br>
						İzEdebiyat olanaklı olduğunca çok insana bu olanağı vermek için kuruldu.<br>
						Burada söylemek istediklerinizi<br>
						kimse size karışmadan
					</p>

					<p class="text-center"
					   style="font-family: Times New Roman, Times, serif; font-size: 1.5rem; line-height: 1.5; color: #666696">
						söyleyebilirsiniz.<br>
						Yayınevlerinin sizi beğenmesi derdiniz yok...
					</p>

					<p class="text-center"
					   style="font-family: Times New Roman, Times, serif; font-size: 1.5rem; line-height: 1.5; color: #666696">
						'Mainstream' yok.
					</p>

					<p class="text-center"
					   style="font-family: Times New Roman, Times, serif; font-size: 1.5rem; line-height: 1.5; color: #666696">
						Sadece listeler var...<br>
						onları da okur belirliyor.<br>
						Beğendiklerini öne getirerek.
					</p>

					<p class="text-center"
					   style="font-family: Times New Roman, Times, serif; font-size: 1.5rem; line-height: 1.5; color: #666696">
						Kısacası burada herşey okurlarınızla sizin aranızda.
					</p>

					<div class="text-center mt-3 mb-3" style="font-size:30px;">• • •</div>

					<!-------------- Tanışma -------------->

					<div class="text-center mb-2">
						<a name="tanisma"></a>
						<img src="/frontend/ikon/37.gif" style="width:43px; height:43px;" class="mt-1 mb-0">
						<h3 class="text-center"
						    style="font-family: Tahoma, Verdana, Arial; font-size: 2rem; font-weight: bold; color: #FF0033">
							Tanışma</h3>
					</div>

					<p class="text-center"
					   style="font-family: Times New Roman, Times, serif; font-size: 1.5rem; line-height: 1.5; color: #666696">
						İzEdebiyat'da kendiniz gibi insanlarla tanışacaksınız.<br>
						Çünkü burası sizin gibi insanların yeri.<br><br>
						Çoğu yazı yazan insanın yalnızlıktan şikayetçi olduklarını duyduk.<br>
						Modalara ve tüketime esir düşmüş bir dünyada,<br>
						içinden geçenleri paylaşamadıklarını.
					</p>

					<p class="text-center"
					   style="font-family: Times New Roman, Times, serif; font-size: 1.5rem; line-height: 1.5; color: #666696">
						Onlara katılmamak elde değildi...
					</p>

					<p class="text-center"
					   style="font-family: Times New Roman, Times, serif; font-size: 1.5rem; line-height: 1.5; color: #666696">
						Bir de şansınızı burada deneyin, bakalım ;)
					</p>

					<div class="text-center mt-3 mb-3" style="font-size:30px;">• • •</div>

					<!-------------- Yüzleşme -------------->

					<div class="text-center mb-2">
						<a name="yuzlesme"></a>
						<img src="/frontend/ikon/80.gif" style="width:43px; height:43px;" class="mt-1 mb-0">
						<h3 class="text-center"
						    style="font-family: Tahoma, Verdana, Arial; font-size: 2rem; font-weight: bold; color: #FF0033">
							Yüzleşme</h3>
					</div>

					<p class="text-center"
					   style="text-align: center; font-family: Times New Roman, Times, serif; font-size: 1.5rem; line-height: 1.5; color: #666696">
						Yazmak elbette sizi kendinizle yüzleştirmiştir.<br>
						Her zaman da yüzleştirecektir.<br>
						Yazarlığın en yorucu ve zor tarafı belki de budur.<br>
						Ancak madalyonun öbür yüzü de var.<br>
						En az birincisi kadar yorucu<br>
					</p>
					<p class="text-center"
					   style="font-family: Times New Roman, Times, serif; font-size: 1.5rem; line-height: 1.5; color: #666696">
						...ve bir o kadar heyecanlı
					</p>
					<p class="text-center"
					   style="font-family: Times New Roman, Times, serif; font-size: 1.5rem; line-height: 1.5; color: #666696">
						Orada okurlar duruyor.
					</p>
					<p class="text-center"
					   style="font-family: Times New Roman, Times, serif; font-size: 1.5rem; line-height: 1.5; color: #666696">
						Onlarla yüzleşmeyi deneyediniz mi?
					</p>

					<p class="text-center"
						style="font-family: Times New Roman, Times, serif; font-size: 1.5rem; line-height: 1.5; color: #666696">
						Onların da size anlatacakları çok şey var...
					</p>

					<div class="text-center mt-3 mb-3" style="font-size:30px;">• • •</div>

					<!-------------- Eğlence -------------->

					<div class="text-center mb-2">
						<a name="eglence"></a>
						<img src="/frontend/ikon/99.gif" style="width:43px; height:43px;" class="mt-1 mb-0">
						<h3 class="text-center"
						    style="font-family: Tahoma, Verdana, Arial; font-size: 2rem; font-weight: bold; color: #FF0033">
							Eğlence</h3>
					</div>

					<p class="text-center"
					   style="text-align: center; font-family: Times New Roman, Times, serif; font-size: 1.5rem; line-height: 1.5; color: #666696">
						Edebiyatın elbette soylu bir tacı var... öbür sanatlara benzemez.<br>
						Onunla ne dans edebilirsiniz,<br>
						ne de eşliğinde şarkı söyleyip<br>
						kendinizden geçebilirsiniz.<br>
						Onu alkışlayamazsınız da.<br>
						Hepsi bir yana, okumak gibi kimilerinin zor bulduğu bir külfeti var.<br>
						Ayrıca imgelem gücünüzü çalıştırmadığınız sürece dünyanın en güzel romanı bile<br>
						bir sürü yaprak, bir sürü harften başka birşey olmaz.
					</p>
					<p class="text-center"
					   style="font-family: Times New Roman, Times, serif; font-size: 1.5rem; line-height: 1.5; color: #666696">
						Yine de...
					</p>

					<div class="text-center mt-3 mb-3" style="font-size:30px;">• • •</div>

					<!-------------- İmgelem -------------->

					<div class="text-center mb-2">
						<a name="imgelem"></a>
						<img src="/frontend/ikon/48.gif" style="width:43px; height:43px;" class="mt-1 mb-0">
						<h3 class="text-center"
						    style="font-family: Tahoma, Verdana, Arial; font-size: 2rem; font-weight: bold; color: #FF0033">
							İmgelem</h3>
					</div>

					<p class="text-center"
					   style="text-align: center; font-family: Times New Roman, Times, serif; font-size: 1.5rem; line-height: 1.5; color: #666696">
						İzEdebiyat'da yüzelliyi aşkın yazı kümesi var.<br>
						Burada insan imgeleminin içinde uzun bir yolculuk sizi bekliyor.<br>
						En güzel bilim kurgu öykülerini bulabilirsiniz.<br>
						En duygulu aşk şiirlerini.<br>
						Ya da en sıcak sevgi ve arkadaşlık anılarını.<br>
						Çarpıcı eleştiriler, cesur araştırmalar...<br>
					</p>

					<p class="text-center"
					   style="font-family: Times New Roman, Times, serif; font-size: 1.5rem; line-height: 1.5; color: #666696">
						İzEdebiyat'da anlamaya çalışanlar var,<br>
						başkaldıranlar var,<br>
						sorgulayanlar var.<br>
						Dertlenenler var.<br>
						Karamsarlar var, umutlular var.<br>
						Sevenler var, sevginin insanı aldattığını düşününler var.<br>
					</p>

					<p class="text-center"
					   style="font-family: Times New Roman, Times, serif; font-size: 1.5rem; line-height: 1.5; color: #666696">
						Kısacası tam bir çorba burası...
					</p>

					<p class="text-center"
					   style="font-family: Times New Roman, Times, serif; font-size: 1.5rem; line-height: 1.5; color: #666696">
						(Ama düzenli kümeler ve gelişmiş bir arama motoru da hizmetinizdedir.)
					</p>

					<p class="text-center"
					   style="font-family: Times New Roman, Times, serif; font-size: 1.5rem; line-height: 1.5; color: #666696">
						Yolunuzu bulacaksınız..
					</p>

					<div class="text-center mt-3 mb-3" style="font-size:30px;">• • •</div>

					<!-------------- Özgürlük -------------->

					<div class="text-center mb-2">
						<a name="ozgurluk"></a>
						<img src="/frontend/ikon/107.gif" style="width:43px; height:43px;" class="mt-1 mb-0">
						<h3 class="text-center"
						    style="font-family: Tahoma, Verdana, Arial; font-size: 2rem; font-weight: bold; color: #FF0033">
							Özgürlük</h3>
					</div>

					<p class="text-center"
					   style="text-align: center; font-family: Times New Roman, Times, serif; font-size: 1.5rem; line-height: 1.5; color: #666696">
						Bir zamanlar engizisyonlar vardı.<br>
						Görevleri fazla düşünenleri, fazla sorgulayanları susturmaktı
					</p>
					<p class="text-center"
					   style="font-family: Times New Roman, Times, serif; font-size: 1.5rem; line-height: 1.5; color: #666696">
						Daha sonra kitle piyasaları çıktı<br>
						Bu kez ancak 'istenilen'i yazanlar yazar sayılmaya başlandı.<br>
						İstenilmeyeni yazanlar yine bir şekilde susturuluyorlardı.<br>
						Sadece bu kez susturanlar da sessizdiler.<br>
						Kendileri yoklardı ortada, sadece yaptıklarını biliyorduk.
					</p>

					<p class="text-center"
					   style="font-family: Times New Roman, Times, serif; font-size: 1.5rem; line-height: 1.5; color: #666696">
						Dünyanın çeşitli yerlerinde zaman zaman işe 'otorite'ler girdi<br>
						Bunu yazamazsınız, şunu yazabilirsiniz dediler...
					</p>

					<p class="text-center"
					   style="font-family: Times New Roman, Times, serif; font-size: 1.5rem; line-height: 1.5; color: #666696">
						Sonuçta görünüş değişse de aslında değişen çok birşey olmadı hiçbir zaman...
					</p>

					<p class="text-center"
					   style="font-family: Times New Roman, Times, serif; font-size: 1.5rem; line-height: 1.5; color: #666696">
						Ta ki şu ünlü 'www' karşımıza çıkıncaya dek.
					</p>

					<p class="text-center"
					   style="font-family: Times New Roman, Times, serif; font-size: 1.5rem; line-height: 1.5; color: #666696">
						İzEdebiyat bundan yararlanarak edebiyat tarihinde yepyeni bir sayfa açıyor.<br>
						Bu sayfada Özgürlüğü bulacaksınız,<br>
						Eşitliği bulacaksınız ve,<br>
						Paylaşımı bulacaksınız.
					</p>

					<div class="text-center mt-3 mb-3" style="font-size:30px;">• • •</div>

					<!-------------- Katilim -------------->
					<div class="text-center mb-2">
						<a name="katilim"></a>
						<h3 class="text-center"
						    style="font-family: Tahoma, Verdana, Arial; font-size: 2rem; text-transform: uppercase;">— K A T I L I M
							—</h3>

						<p class="text-center"
						   style="text-align: center; font-family: Times New Roman, Times, serif; font-size: 1.5rem; line-height: 1.5;">
							Kimler katılabilir?</p>

						<img src="/frontend/ikon/91.gif" style="width:43px; height:43px;" class="mt-1 mb-0"><br>
						<p
							style="font-family: Tahoma, Verdana, Arial; font-size: 2rem; font-weight: bold; color: #FF0033">Herkes</p>
					</div>

					<div class="text-center">
						<p style="font-family: Arial, Helvetica, sans-serif; font-size: 1.5em">Katılmak için ne yapmam
							gerekiyor?</p>

						<img src="/frontend/ikon/116.gif" style="width:43px; height:43px;" class="mt-1 mb-0"><br>
						<p style="font-family: Tahoma, Verdana, Arial; font-size: 2rem; font-weight: bold; color: #FF0033">
							Yazmak.<br><br>
							<a href="/katilim" style="font-size: 1rem;">
								Sonra da burayı tıklayarak kendinize bir İzEdebiyat Kişisel Yazar sayfası açmak.
							</a>
						</p>
					</div>

					<div
						style="text-align: center; font-family: Times New Roman, Times, serif; font-size: 1.5rem;">
						İzEdebiyat dünyasına hoşgeldin :)
					</div>

</main>

<!-- Footer -->
<?php include "shop_page_footer.php"; ?>
<!-- /Footer -->

</body>
</html>
