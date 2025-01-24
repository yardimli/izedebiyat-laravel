<?php
	require_once 'shop_global_backend.php';
	$pageName = "legal";
	$headPageTitle = "İzEdebiyat - Katılım";

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
					İzEdebiyat'a Hoşgeldiniz!
				</h1>
				<p style="font-family: Arial, Helvetica, sans-serif; font-size: 16px; font-weight: bold">
					Yazarlık kaydınız on-on beş dakikadan uzun sürmeyecektir. Ancak lütfen oraya ilerlemeden önce bu metni
					dikkatle okuyun.
				</p>

				<p style="font-family: Arial, Helvetica, sans-serif; font-size: 16px">
					<span style="font-weight: bold">İzEdebiyat</span>'ın temel işlevi, edebi, bilimsel, felsefi ve sanatsal
					metinlerin internet ortamında yayınlanması ve geniş bir okur kitlesine ulaşmasını sağlamaktır. Baştan sonra
					özgürlük<span style="font-style: italic"> </span>ve eşitlik ilkeleri üzerine kurulu yapı ürün veren <span
						style="font-weight: bold">tüm yazar ve yazar adaylarına açıktır.</span> Bu özelliği sayesinde de dünyada
					türünün tek örneğidir! İzEdebiyat'ta yayınlanan yazılar, editörlerce elenerek seçilmezler, ancak bu
					editörlerce okunmuyorlar anlamına gelmez. Editörlerimizin sizden en önemli beklentileri şiir, öykü ve deneme
					türlerinde gönderdiğiniz metinlerin gerçekten birer şiir, birer öykü, ya da deneme olmalarıdır. Aynı şekilde
					bilimsel metinlerin gerçekten bilimsel, incelemelerin de gerçekten inceleme olmaları gerekir. Bu konuyla
					ilgili ayrıntılı bilgiyi aşağıda bulacaksınız. Bunun dışında sitenin birinci amacı yazı yazan herkese bir okur
					kapısı açmaktır. Bu doğrultuda çok genel bazı yayın ilkeleri dışında, gönderilen tüm metinler siteye kabul
					edilmektedir.
				</p>

				<p style="font-family: Trebuchet MS, Tahoma, Arial,sans-serif; font-size: 24px; font-weight: bold; ">
					Başlamadan Önce Editörden Öneriler...
				</p>

				<hr style="border: 1px solid; width: 100%">

				<p style="font-family: Arial, Helvetica, sans-serif; font-size: 16px">
					<span style="font-weight: bold">İzEdebiyat editörleri ne iş yapar?</span><br>
					Editörlerimiz aşağıda bulacağınız yayın ilkeleri
					doğrultusunda siteyi işletmekten sorumludur. Bundan öte de pek bir işlevleri olduğu
					söylenemez. Bazıları forumlarda gezinir, bazıları daha sessiz kalmayı seçer. Ama
					onlar her zaman yanınızdadır ve bu heyecanlı serüven boyunca size yardımcı olmak
					için ellerinden geleni yaparlar. Ancak geleneksel anlamda bir editörden farklı
					olarak İzEdebiyat editörleri oldukça hoşgörülüdür. Bu tutumları sayesinde İzEdebiyat
					sitesi onların değil, siz yazarların bir eseri olmuştur bugün. Başka bir deyişle,
					İzEdebiyat'ın yayın anlayışının bir sonucu olarak sitenin nitelikli eserler barındırması,
					okurlar ve yazarlar için zevkle gezinebilecekleri bir platform olması herşeyden
					çok yazarlarına bağlıdır. Denklem basittir... güzel yazılar geldikçe İzEdebiyat
					da güzelleşir...
				</p>

				<p style="font-family: Arial, Helvetica, sans-serif; font-size: 16px;">
					<b>Editörlerimiz bu konuda şunları söylüyorlar</b>
					<br>
					"İzEdebiyat'a
					bir yazınızı göndermeden önce, metni olası imla hataları ve anlatım bozukluklarını
					gidermek için tekrar gözden geçirmeye vakit ayırın. Aynı zamanda tüm internet
					kullanıcılarına açık olan İzEdebiyat'a göndereceğiniz metinlerde sanatsal bir
					eserde beklenilen nesnelliğin ve evrenselliğin bulunup bulunmadığını kendinize
					sorun. Sadece sizi ya da yakınınızdakileri (sevgililer, arkadaşlar, vb.) ilgilendiren
					şiirler ve denemeleri buraya gönderirseniz internete, okurlara ve sitede bulunan
					diğer yazarlara haksızlık etmiş olursunuz. Aynı zamanda sitenin adındaki "edebiyat"
					sözcüğüne de haksızlık etmiş olursunuz. Okurlarınız da sizi zaten tanıyan ve belki
					de yazdıklarınızı nasıl olsa görecek olan birkaç kişiden öteye geçmeyebilir. Sadece
					kendi derdiyle uğraşan bir yazara okurlar pek ilgi göstermez. Ama tam tersine,
					karşısında geniş bir okur kitlesinin bulunduğunu dikkate alan yazar, onlara karşı
					burada bulunarak bir sorumluluk üstlendiğini fark eden ve olanaklı olduğunca açık
					ve anlaşılır yazılar göndermeye dikkat eden yazar, okurları tarafından da fark
					edilir.
				</p>

				<p style="font-family: Arial, Helvetica, sans-serif; font-size: 16px;">
					İzEdebiyat,
					sanal ve elektronik olduğu kadar da organik bir ortamdır; metinlerini özenle oluşturan
					ve seçerek siteye gönderen yazarlar her zaman için siteden daha verimli bir şekilde
					yararlanabilmiş, daha geniş bir okur kitlesine seslenebilmişlerdir. Unutmayın
					ki metin miktarından çok, gönderilen metnin niteliği önem taşımaktadır. Beş tane
					seçilerek gönderilmiş metin, yüz tane dikkatsizce gönderilmiş metinden daha çok
					okunmazını, daha iyi tanınmanızı, daha çok sevilmenizi, hatta bir yayınevinden
					teklif alabilmenizi sağlayabilir."
				</p>

				<p style="font-family: Arial, Helvetica, sans-serif; font-size: 16px">
					Unutmadan söyleyelim, İzEdebiyat, birçok yayınevi tarafından takip edilmektedir
					ve birçok yazarımızın eserleri İzEdebiyat'a girdikten sonra yayınlanmıştır. Aynı
					zamanda İzEdebiyat yavaş yavaş kendi yayınevini kurmaya başlamıştır. Bunun ilk
					meyvesi Eylül 2003'de yayınlanan <b>İzEdebiyat e-debiyat Yıllığı </b>olmuştur.
				</p>

				<p style="font-family: Arial, Helvetica, sans-serif; font-size: 16px; font-weight: bold;">
					Gelelim
					Yayın İlkelerimize...
				</p>

				<p style="text-align: center">
					<a href="/resources/views/frontend/page-sign-up-step-2.php">
						<img src="/frontend/anagrafikler/dugme_katilim.gif" width="250" height="70" style="border: 0">
					</a>
				</p>

			</div>
		</div>
	</div>


</main>

<!-- Footer -->
<?php include "shop_page_footer.php"; ?>
<!-- /Footer -->

</body>
</html>
