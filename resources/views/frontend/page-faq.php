<?php
	require_once 'shop_global_backend.php';
	$pageName = "legal";
	$headPageTitle = "İzEdebiyat - Sıkça Sorulan Sorular";

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
					Sıkça Sorulanlar
				</h1>

				<p class="mt-5" style="font-family:Arial, Helvetica, sans-serif; font-size:18px; line-height: 1.8;">
					<a href="#1">İzEdebiyat kimlere açık</a><br>
					<a href="#9">İzEdebiyat'a yüklenen yazılar denetleniyor ya da eleniyor mu?</a><br>
					<a href="#2">İzEdebiyat'a ne tür metinler gönderebilirim?</a><br>
					<a href="#10">Bana ait bir yazının başka birinin adıyla yayınlandığını görüyorum. Ne yapmalıyım?</a><br>
					<a href="#3">Yazdığım yazıya uygun bir küme bulamıyorum. Ne yapmalıyım?</a><br>
					<a href="#11">Yazılarımın haklarını elde etmek için ne yapmalıyım?</a><br>
					<a href="#4">Üyelik paralı mı?</a><br>
					<a href="#12">Facebook ve benzeri sosyal platformlarda İzEdebiyat gruplarına üye olmalı mıyım?</a><br>
					<a href="#5">Nasıl üye olunur?</a><br>
					<a href="#13">İzEdebiyat bir daha kitap basacak mı?</a><br>
					<a href="#6">İzEdebiyat'ta ünlü yazarlar var mı?</a><br>
					<a href="#14">İzEdebiyat sitesini ne zaman baştan aşağı yenileyecek?</a><br>
					<a href="#7">İzEdebiyat'ın politik bir eğilimi var mı?</a><br>
					<a href="#15">Çok kötü bir yazıyla karşılaştım. Ne yapmalıyım?</a><br>
					<a href="#8">İzEdebiyat'a bana ait olmayan yazılar yükleyebilir miyim?</a><br>
					<a href="#16">İzEdebiyat'ın arkasındaki insanlarla iletişim kurmak istiyorum. Mümkün mü?</a>
				</p>

				<p class="mt-5" style="font-family:Arial, Helvetica, sans-serif; font-size:16px;">
					<b>İzEdebiyat kimlere açık?</b><a name="1"></a><br>

					Herkese! Elbette bir şeyler (mümkünse de iyi bir şeyler) yazıyor olmalısınız. İzEdebiyat bir sosyal paylaşım
					sitesi ya da arkadaş bulma sitesi değil. Ama bu, site içerisinde harika arkadaşlar edinemeyeceğiniz anlamına
					gelmiyor. Tersine, İzEdebiyat, sizinle aynı duyarlılıklara sahip, benzer idealler paylaşan sayısız insanla
					tanışabileceğiniz harika bir platform aynı zamanda.<br>
					<a href="#tepe" class="btn btn-sm btn-primary">TEPE</a>
				</p>

				<p class="mt-5" style="font-family:Arial, Helvetica, sans-serif; font-size:16px;">
					<b>İzEdebiyat'a ne tür metinler
						gönderebilirim?</b><a name="2" id="2"></a><br>

					Sitemizin adı her ne kadar İz<b>Edebiyat</b> olsa da, sitemizde azıcık gezdiyseniz, içeriğin sadece edebi
					metinlerle sınırlı olmadığını görmüşsünüzdür. İzEdebiyat'ta öykü, roman ve şiirlerin yanı sıra binlerce
					güncel, bilimsel, tarihsel ya da felsefi metin de bulabilirsiniz. Yazarlarımızdan tek bir ricamız var.
					Yazdığınız yazıyı yüklemeden önce, metninizin başkalarını ilgilendirip ilgilendirmediğni kendinize sorun.
					Eserinizi ne kadar derli toplu, kullandığınız dil ne kadar anlaşılır ve düşünceleriniz ne kadar özgün olursa,
					o kadar çok kişinin ilgisini çekersiniz. Biz sizden bütün yazılarınızı beklemiyoruz, en güzellerini
					bekliyoruz!<br>
					<a href="#tepe" class="btn btn-sm btn-primary">TEPE</a>
				</p>

				<p class="mt-5" style="font-family:Arial, Helvetica, sans-serif; font-size:16px;">
					<b>Yazdığım yazıya uygun bir küme bulamıyorum. Ne yapmalıyım?</b><a name="3" id="3"></a><br>
					<i>Kategorizasyon</i> sözcüğü edebiyat ve sanatla uğraşan insanların -doğaları gereği- pek de
					hoşlanmadıkları
					bir kavram. Bununla birlikte, on binlerce insanın yazı gönderdiği bir platformda, yazıları bir şekilde
					düzenlemek şart. Göndermeyi düşündüğünüz esere uyan bir alt küme bulamıyorsanız en yakın kümeyi
					seçebilirsiniz. Biraz empatiden de zarar gelmez.... Seçeceğiniz kümeyi tıklayan bir kişi, gerçekten o
					kümenin
					altında sizin yazınızla karşılaşmayı umuyor mu? Yanıtın "evet" olduğunu düşünüyorsanız, yazınızı oraya
					yerleştirebilirsiniz!<br>
					<a href="#tepe" class="btn btn-sm btn-primary">TEPE</a>
				</p>

				<p class="mt-5" style="font-family:Arial, Helvetica, sans-serif; font-size:16px;">
					<b>Üyelik paralı mı?</b><a name="4" id="4"></a><br>
					Hayır. Üyelik baştan aşağı ücretsiz. Zaman zaman paralı bir takım bölümler açmayı düşünmüyor değiliz, ama
					bugüne kadar bu sadece bir düşünce olarak kaldı. Aynı zamanda İzEdebiyat'ın olabildiğince reklamsız bir site
					olması için çalışıyoruz. Siteyle ilgilenen herkes, "gönüllülük prensibi" çerçevesinde siteye vakitlerini
					ayırıyorlar.<br>
					<a href="#tepe" class="btn btn-sm btn-primary">TEPE</a>
				</p>

				<p class="mt-5" style="font-family:Arial, Helvetica, sans-serif; font-size:16px;">
					<b>Nasıl üye olunur?</b><a name="5" id="5"></a><br>
					Bütün sayfalarda yer alan Katılım bağlantısını tıklayarak hemen üye olabilirsiniz. Aynı zamanda <a
						href="/resources/views/frontend/page-sign-up-step-1.php">burayı tıklayarak</a> da hemen üye olabilirsiniz. Üye olmak için öncelikle kendinize bir
					Kişisel Yazar Sayfası oluşturmalısınız. Kişisel Sayfanız, yazılarınızı, bloglarınızı, bağlantılarınızı ve
					resimlerinizi paylaşabileceğiniz ve aynı zamanda sizinle ilgili bilgiler içeren size özel bir sayfa. Bu
					sayfanın içeriğine ve tasarımına siz karar veriyorsunuz. Bütün yazılarınız buraya toplanıyor ama elbette
					sadece buraya toplanmıyor, bütün sitede uygun kümeler ve listeler altında da görünür oluyor.<br>
					<a href="#tepe" class="btn btn-sm btn-primary">TEPE</a>
				</p>

				<p class="mt-5" style="font-family:Arial, Helvetica, sans-serif; font-size:16px;">
					<b>İzEdebiyat'ta ünlü yazarlar var
					mı?</b><a name="6" id="6"></a><br>
					Ün, oldukça göreceli bir kavram. Kitabı basılmış, ödüller almış yazarlar bulunuyor, ancak (bildiğimiz
					kadarıyla) Orhan Pamuk ya da Latife Tekin henüz sitemize üye olmadılar.
					<br>
					<a href="#tepe" class="btn btn-sm btn-primary">TEPE</a>
				</p>

				<p class="mt-5" style="font-family:Arial, Helvetica, sans-serif; font-size:16px;">
					<b>İzEdebiyat'ın politik bir eğilimi var mı?</b><a name="7" id="7"></a><br>
					Elbette bütün yöneticilerinin ve editörlerinin (herkes gibi) bazı duyarlılıkları var. Ama açık konuşmak
					gerekirse İzEdebiyat'ta şeriat hayaliyle yaşayanlarla, darbe hayaliyle yaşayanları aynı listelerde
					bulabilirsiniz. Elbette bunlar en uç örnekler. Yazıların çok büyük bir kısmı ılımlı ve sağduyulu insanlar
					tarafından kaleme alınmış ve İzEdebiyat'ı politik bir dövüş platformu olarak değerlendirmeyen metinler.
					(Ulusal günlerimizde sitenin tepesinde göreceğiniz güzel bir Atatürk resmi sitenin politik eğilimi hakkında
					çok genel bir fikir verebilir size.)<br>
					<a href="#tepe" class="btn btn-sm btn-primary">TEPE</a>
				</p>

				<p class="mt-5" style="font-family:Arial, Helvetica, sans-serif; font-size:16px;">
					<b>İzEdebiyat'a bana ait olmayan yazılar yükleyebilir miyim?</b><a name="8" id="8"></a><br>
					Bunun yanıtı, ne yazık ki (ama kesin olarak) hayır. İzEdebiyat amatör bir edebiyat platformudur ve herkes
					sadece kendi yazılarını yükleyebilir. Herhangi bir telif ihlali olmasın diye çeviri çalışmalarda da sadece
					telif hakkı dolmuş metinlerin çevirilerini koyabilirsiniz. Ola ki, telif ihlali bulunan bir metin gözünüze
					çarparsa, bunu bize bildirirseniz sadece bizi değil, yazının gerçek sahibini de mutlu etmiş olursunuz.<br>
					<a href="#tepe" class="btn btn-sm btn-primary">TEPE</a>
				</p>

				<p class="mt-5" style="font-family:Arial, Helvetica, sans-serif; font-size:16px;">
					<b>İzEdebiyat'a yüklenen yazılar denetleniyor ya da eleniyor mu?</b><a name="9" id="9"></a><br>
					Hem evet, hem hayır. Başka bir deyişle denetleniyor ama elenmiyor. İzEdebiyat'ta her okurunuzun, bir okur
					olmanın yanısıra metninizi değerlendiren bir "gönüllü editör" olduğunu unutmayın. İzEdebiyat'ın şeffaflık
					anlayışı gereğince, sitede yeri olmayan bir yazının sitede uzun süre barınması da çoğunlukla olanaklı olmuyor.
					Zira her yazının altında yer alan "Kötü İçerik Bildir" düğmesi ile bize bildirilen tüm yazılar editörlerce
					değerlendiriliyor ve şikayetin haklı olduğuna karar verilmesi durumunda, metin siteden kaldırılıyor. Bir
					yazının siteden kadırılması için birçok gerekçe olabilir. Bunların arasında özellikle imla hataları, cümle
					düşüklükleri ve yanlış sınıflandırma (örn. bir şiiri inceleme kümesinin altına koymak) başı çekiyor. Ama "baş
					editörün" kendiniz olduğunu yine de unutmayın. Yazınızı İzEdebiyat'a gönderirken titiz davranmaya özen
					göstermenizi bekliyoruz.<br>
					<a href="#tepe" class="btn btn-sm btn-primary">TEPE</a>
				</p>

				<p class="mt-5" style="font-family:Arial, Helvetica, sans-serif; font-size:16px;">
					<b>İzEdebiyat'ta bana ait bir
					yazının başka birinin adıyla yayınlandığını görüyorum. Ne
					yapmalıyım?</b><a
						name="10" id="10"></a><br>
					Öncelikle yazıyı yükleyen kişiye bu durumu bildirip, yazıyı kaldırmasını isteyebilirsiniz. Bundan herhangi bir
					sonuç alamadığınızı düşünüyorsanız bizimle yazışabilirsiniz. Yazının sizin sayfanızda, söz konusu kişinin
					sayfasından daha erken bir tarihte yayınlandığını görürsek, yazıyı aynı gün içerisinde sistemden siliyoruz.
					Yazarın bunu yanlışlıkla yaptığına kanaat getirirsek, hoşgörülü davranıyoruz. Sistematik bir yazı-çalma
					durumuyla karşı karşıya olduğumuzu düşünürsek, bu kişiyi siteden uzaklaştırıyoruz.<br>
					<a href="#tepe" class="btn btn-sm btn-primary">TEPE</a>
				</p>

				<p class="mt-5" style="font-family:Arial, Helvetica, sans-serif; font-size:16px;"><b>İzEdebiyat'ta yer alan bir
					yazım, başka bir sitede yayınlanıyor. Ne yapmalıyım?</b><a
						name="11" id="11"></a><br>
					Ne yazık ki, çok büyük ölçüde geçerli olan durum, yazıyı kopyalamış kişinin ya da söz konusu sitenin
					moderatörünün iyi niyetine güvenmek dışında yapabileceğiniz fazla bir şeyin olmaması. İnternette yer alan tüm
					belge ve görsellerin bir anlamda "kamuya açık" olduğunu unutmayın. Biz, her ne kadar, sitenin her sayfasına
					sitedeki tüm yazıların haklarının yazarlarına ait olduğunu belirtsek de, kötü niyetli ya da sorumsuz birileri
					buradan beğendikleri yazıları alıp, kendi sitelerinde sergileyebiliyorlar. Evet, bu durum bizim de hoşumuza
					gitmiyor ama bizim de elimiz kolumuz sizinki kadar bağlı. Burada -bize kalırsa- dikkat edilmesi gereken en
					önemli "ahlaki" boyut, yazıyı kendi sitesine taşıyan kişinin, isminizi de beraberinde taşıyıp taşımaması. Size
					ait bir yazının başka bir kişinin adıyla yayınlanması halinde, ve yazının haklarının size ait olduğunu
					kanıtlayabileceğiniz kesin ise, bu kişileri yasal mercilere başvurmakla uyarabilirsiniz. Ancak kitap
					dünyasında bile bu tür davaların sonuçlanmasının yıllar aldığını unutmayın.<br>
					<a href="#tepe" class="btn btn-sm btn-primary">TEPE</a>
				</p>

				<p class="mt-5" style="font-family:Arial, Helvetica, sans-serif; font-size:16px;"><b>Yazılarımın haklarını elde
						etmek için ne yapmalıyım?</b><a name="12" id="12"></a><br>
					Son yıllarda yazılarını noterlere gidip tasdik ettirenlerin sayısında büyük bir artış gerçekleşti. Birçok
					yazar, sadece internete değil, yayınevlerine başvururken de bu yöntemi tercih etmekte. Ancak bunun
					-özellikle
					de çok fazla yazınız varsa- bir hayli maliyetli olabileceğini dikkate almalısınız. Dünyanın farklı
					yerlerinde,
					bu konuda farklı yöntemler uygulanıyor. Bizim de önerebileceğimiz yöntemlerden biri, yazılarınızın CD'sini
					ve
					basılmış hallerini, kendi adresinize APS ile göndermeniz. Daha sonra bu zarfı açmadan güvenli bir yerde
					saklamanız. APS'nin üzerindeki etiket resmi bir belge niteliği taşıdığından, aynı zamanda o günün tarihini
					de
					belirttiğinde, yazıların söz konusu tarihte basıldığının bir kanıtıdır. Elbette bastığınız sayfaların her
					birinin altına isminizi ve imzanızı da atmanız sizi daha da fazla koruyacaktır.<br>
					<a href="#tepe" class="btn btn-sm btn-primary">TEPE</a>
				</p>

				<p class="mt-5" style="font-family:Arial, Helvetica, sans-serif; font-size:16px;">
					<b>Facebook ve benzeri sosyal
						platformlarda İzEdebiyat gruplarına üye olmalı mıyım?</b><a
						name="13" id="13"></a><br>
					Buna kendiniz karar verebilirsiniz. Ancak bu toplulukların hiçbirinin İzEdebiyat tarafından kurulmadığını ve
					denetlenmediğini lütfen dikkate alın. Çoğunlukla İzEdebiyat'ı hayatlarının bir parçası olarak benimsemiş
					yazarlar tarafından işletilen bu tür gruplarda İzEdebiyat'ın yönetici ya da editörlerine ulaşma ihtimaliniz de
					oldukça düşük.<br>
					<a href="#tepe" class="btn btn-sm btn-primary">TEPE</a>
				</p>

				<p class="mt-5" style="font-family:Arial, Helvetica, sans-serif; font-size:16px;">
					<b>İzEdebiyat bir daha kitap
					basacak mı?</b><a name="14" id="14"></a><br>
					2003 yılında çıkardığımız e-debiyat Yıllığı, övünerek belirtelim ki, medyada bir hayli ilgi çekmişti. Radikal,
					CNN Türk vb. medya kurumlarının arşivlerini tarayan biri, bu eser hakkında bol bol yazı bulacaktır. Ancak
					satış anlamında tam bir fiyasko yaşadık. Sitenin üyelerinin %1'i bile bu dev kitabı almaya yanaşmadı. (Satılan
					kitapların neredeyse tümü kitabevlerinde satıldı.) Dolayısıyla bu işe bir daha girişmeme kararı aldık. Ancak
					prensiplerimize her zaman uymak gibi bir prensibimiz yok. Dolayısıyla, günün birinde, elbette tekrar böyle bir
					maceraya atılabiliriz... Biraz da yazarlardan alacağımız geri dönüşlere bağlı böyle şeyler.<br>
					<a href="#tepe" class="btn btn-sm btn-primary">TEPE</a>
				</p>

				<p class="mt-5" style="font-family:Arial, Helvetica, sans-serif; font-size:16px;"><b>İzEdebiyat sitesini ne zaman
						baştan aşağı yenileyecek?</b><a name="15" id="15"></a><br>
						Buna zaman zaman niyetlensek (ve hatta başlasak bile) hem vakit, hem de yarı-gönüllülük nedeniyle hiçbir
						zaman
						sonunu getiremiyoruz. İzEdebiyat'ın sisteminin çok büyük ölçüde amacına yeterli miktarda hizmet ettiğini
						düşünüyoruz. Bir sosyal paylaşım sitesi olmaması dolayısıyla, chat, video vb. özellikler koymayı çok anlamlı
						bulmuyoruz. Bununla birlikte, yeni nesil web sitelerinde bulabileceğiniz birçok havalı özelliğin de
						İzEdebiyat'ta şık durabileceğinin farkındayız. Siteyi baştan aşağı yenilemek yerine, yavaş yavaş çağa ayak
						uydurmayı tercih ediyoruz. En büyük eksiğimiz, editör sistemimizin yeterince oturmamış olması (hala!).
						Herhalde yapacağımız en büyük yenilik bu olacak. Ama uzunca bir süre şu anda karşınızda gördüğünüz
						turuncu-siyah siteyi görebileceksiniz.<br>
						<a href="#tepe" class="btn btn-sm btn-primary">TEPE</a>
				</p>

				<p class="mt-5" style="font-family:Arial, Helvetica, sans-serif; font-size:16px;">
					<b>Çok kötü bir yazıyla
					karşılaştım. Ne yapmalıyım?</b><a
						name="16" id="16"></a><br>
					Yazıda belirtilen fikirlere katılmıyorsanız, yazıya yorum yazabilir, fikirlerinizi yazarıyla
					paylaşabilirsiniz. Temiz ve anlaşılır bir dil ve saygılı bir üslupla yazılmış bir eleştiri, aklı başında bütün
					yazarların ciddiye alacağı bir şeydir. Yazıda herhangi bir fikir yoksa ya da yer alan fikirler sağduyu ve akıl
					sınırlarının dışındaysa, hemen yazının altında bulunan KÖTÜ İÇERİK BİLDİR düğmesini tıklayarak, bizi
					uyarabilirsiniz. Fazla imla hatası, özensizlik, saldırganlık, seviyesizlik, bölücülük, rahatsız edici derecede
					pornografik içerik ya da resim... içinize sinmeyen herhangi bir şey varsa, gecikmeden bizi uyarın.<br>
					<a href="#tepe" class="btn btn-sm btn-primary">TEPE</a>
				</p>

				<p class="mt-5" style="font-family:Arial, Helvetica, sans-serif; font-size:16px;"><b>İzEdebiyat'ın arkasındaki
					insanlarla iletişim kurmak istiyorum. Mümkün mü?</b><a
						name="17" id="162"></a><br>
					Posta yoluyla, evet. Yüz yüze bir çay içmek istiyorsanız ve sitenin geleceği hakkında fikir alışverişinde
					bulunmak istiyorsanız, bu nazik teklifinizi ne yazık ki geri çevirmek durumundayız. İzEdebiyat'ın tüm yönetici
					ve editörleri yayın ve kitap dünyasında çalışan insanlar. Sitemiz bizim için en değerli varlıklarımızdan biri
					olsa da hiçbirimizin mesleği İzEdebiyat değil. (Keşke olsa.) Bizimle iletisim@izedebiyat.com adresinden
					görüşebilirsiniz. Sanal ve gerçek adresimiz bu.<br>
					<a href="#tepe" class="btn btn-sm btn-primary">TEPE</a>
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
