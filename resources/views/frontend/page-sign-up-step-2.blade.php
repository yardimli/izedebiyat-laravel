@extends('layouts.app-frontend')

@section('title', 'İzEdebiyat - Yayın İlkeleri')
@section('body-class', 'home')

@section('content')
	{{ \App\Helpers\MyHelper::initializeCategoryImages() }}
	
	<section class="home">
		<main id="content">
			<div class="container">
				<div class="entry-header">
					<div class="mb-5">
						<h1 class="entry-title mb-2">
							Yayın İlkeleri</h1>
						
						<p class="mt-2" style="font-family:Arial, Helvetica, sans-serif; font-size:16px;">
							<b>
								Edebi Türler (Şiir, Öykü, Deneme, Roman)</b>
							<br>
							1) Metin edebi bir metin olmalıdır. <br>
							2) Şiir ise serbest veya vezinli olarak,
							ancak her durumda şiirsel bir bütünlük içinde kaleme alınmış olmalıdır. <br>
							3) Öykü ya da roman ise düz yazı şeklinde kaleme alınmış olmalı, örgüsel bir tutarlılığa
							ve gerçek ya da hayali olaylara dayanan bir kurguya sahip olmalıdır.<br>
							4) Türü ne olursa olsun, metinde başka bir yazar, şair ya da eserden esinlenildiyse bu
							metnin başında açıkça belirtilmelidir. <br>
							5) Metnin içinde başka bir yazar, şair
							ya da eserden alıntı yapıldıysa, yapılan alıntının sahibi metnin içinde açıkça
							belirtilmelidir. Alıntı hiçbir durumunda metnin 1/5'inden daha uzun olamaz. <br>
							6) Öykü, şiir ya da deneme yazarın dışında gerçek bir kişinin ağzından anlatılıyorsa,
							söz konusu kişinin onayı şarttır. <br>
							7) Metin doğru küme altına yerleştirilmiş
							olmalıdır. Metin için uygun kümenin bulunmaması halinde site editörlerine yeni
							küme için başvuruda bulunulabilir.
						</p>
						
						<p class="mt-2" style="font-family:Arial, Helvetica, sans-serif; font-size:16px;">
							<b>İnceleme</b>
							<br>
							1) Gönderilen metin bir İnceleme ise araştırma niteliği taşımalıdır, hayali ya da
							kurgul olayları ve/ya da kahramanları konu almamalıdır. İnceleme bir görüşü çürütmek
							ya da desteklemek için kaleme alınmışsa, nesnel olgulara dayanan uslamlamalarla
							desteklenmemiş olmalıdır.<br>
							2) Yararlanılan kaynakların açıkça belirtilmesi
							gerekir
						</p>
						
						<p class="mt-2" style="font-family:Arial, Helvetica, sans-serif; font-size:16px;">
							<b>Eleştiri</b>
							<br>
							1) Gönderilen metin, politik bir yazıysa, Türkiye Cumhuriyeti anayasasına ve uluslararası
							internet hukukuna uygun olmalıdır. Yazılar, imgeler, yorumlar ve forum iletileri
							yoluyla hiçbir partinin ya da daha başka politik ya da ticari kurumun, hiçbir
							vakfın, derneğin ve benzeri örgütlenmelerin propagandası yapılamaz.<br>
							2) Gönderilen
							metin dinsel içerikli bir yazıysa, kimsenin dini inançlarına ya da tanrıtanımazlığına
							saldırı ve/ya da rencide edici deyimler, anlatımlar içermemelidir.
						</p>
						
						<p class="mt-2" style="font-family:Arial, Helvetica, sans-serif; font-size:16px;">
							<b>Bilimsel</b>
							<br>
							1) Gönderilen metin felsefi bir yazıysa, yazının uslamlamalar ve tanıtlamalarla kurulmuş
							olması gerekir. Bir sava karşılık, bir karşı sav getiriliyorsa, nedenselliğe bağlı
							olunması gerekir. Felsefi içerik oldukça geniş bir alanı kaplasa da 'benim hayat
							felsefem de bu' tarzındaki denemeler felsefi yazılar kapsamına girmezler.<br>
							2) Gönderilen metin doğa bilimlerini ilgilendiren bir yazıysa, bilimselliği gereğince,
							içeriğin kuramlarla destekleniyor olması gerekir. İçerik bir hipotezi konu alıyorsa,
							hipotez olduğu açıkça belirtilmiş olmalıdır. Bilimsel metinlerde tarafsızlık aranmaktadır.
							Öznel görüş ve inançlarla kaleme alınan metinler, bilimsel kümesine kabul edilmez.
							Alıntı yapılması durumunda kaynak belirtilmesi zorunludur.
						</p>
						
						<p class="mt-2" style="font-family:Arial, Helvetica, sans-serif; font-size:16px;">
							<b>Tüm kümeler için geçerli ilkeler:</b>
							<br>
							1) Yayınlanan yazıda bir kişiye, bir kuruma ya da bir devlete yönelik herhangi bir
							suçlama varsa, yazarın resmi bir belgeyle ya da yayınlanmış bir eserle bu suçlamayı
							destekleyebilmesi gerekir. Desteklememesi durumunda metin İzEdebiyat editörlerince
							siteden kaldırılabilir.<br>
							2) Her durumda T.C. Anayasasına aykırı içerik kabul
							edilmez. <br>
							3) Metnin konusu ne olursa olsun, hiçbir tüzel ya da gerçek kişiye
							ya da kuruma hakaret niteliği taşımaması gerekir.<br>
							4) Metnin konusu ne olursa
							olsun, hiçbir tüzel ya da gerçek kişiye ya da kuruma belgelerle kanıtlanmamış
							suçlamalarlarda bulunmaması gerekir. <br>
							5) Metinde okunmayı zorlaştıracak kadar
							imla hatasına rastlanması durumunda metin siteden kaldırılır.<br>
							6) Metnin başlığında
							ya da tanıtımında yazım hatası bulunan yazılar siteye hiçbir durumda kabul edilmez.
							Aynı şekilde sadece büyük harf kullanarak atılan başlıklar, alt başlıklar ve tanıtım
							yazıları da metnin siteye kabul edilmemesi için neden sayılabilir.<br>7) Metnin,
							telif hakları süresi dolmuş bir eser olmadığı sürece, gönderen kişiye ait olması
							şarttır. Telif hakları dolmuş bir eser ise, eserin gerçek yazarının metnin başında
							açıkça belirtilmiş olması gerekir. İzEdebiyat bir metinde telif ihlallerinin bulunduğunu
							görmesi durumunda hem yazıyı, hem yazarı siteden uzaklaştırma hakkını kendine
							saklı tutmaktadır.
						</p>
						
						<p class="mt-2 mb-0" style="font-family:Arial, Helvetica, sans-serif; font-size:16px;">
							<b>Saydamlık İlkesi</b>
						</p>
						
						<ul style="font-family:Arial, Helvetica, sans-serif; font-size:16px; margin-top:0px; padding-top: 0px;">
							<li>İzEdebiyat'ta
								tüm yazıların okunma oranları açıkça belirtilmektedir.
							</li>
							<li>İzEdebiyat
								listeleri tüm yazarların eşit değerlendirildiği karmaşık bir formüle göre sıralanmaktadır.
								Sıralamada kütüphane girişlerinden, yorumlara, zamansal bir eğri üzerinden okunma
								sayısına kadar birçok etken değerlendirmeye alınmaktadır. Hesaba katılan tüm değerler
								ve orantılar tüm yazarlar için eşittir.
							</li>
							<li>Site
								yönetimi ve/ya da editörler hiçbir şekilde listelere müdahale etmez.
							</li>
							<li>Site
								yönetimi ve/ya da editörleri sayfalarda belirtilen birkaç alan dışında hiçbir
								yazıyı öne çıkaramaz, hiçbir yazının reklamını yapmaz ve hiçbir yazıyı eleştirmez.
							</li>
							<li>Yazar olarak
								İzEdebiyat'ta yer alan editör ya da site yöneticileri hiçbir zaman <b>Seçtiklerimiz</b>
								ya da <b>Yazar Tanıtımı</b> bölümüne konu olamaz.
							</li>
							<li>İzEdebiyat,
								siteden kaldırılan bir yazının neden kaldırıldığına dair bir soru gelirse, nedenini
								yazara ve sadece yazara özel posta yoluyla açıklamakla yükümlüdür.
							</li>
							<li>İzEdebiyat
								siteye kabul edilmeyen bir metnin niye kabul edilmediğini bildirmekle yükümlü
								değildir.
							</li>
							<li>İzEdebiyat'a
								kişisel posta yoluyla gelen şikayetler yazardan izin alınmaksızın hiçbir şekilde
								başkasına aktarılamaz, genel görünüme (forum) açılamaz. Aynı şekilde İzEdebiyat
								editörleri ve yönetimi yazarlara gönderilen kişisel postaların, kendilerine danışılmaksızın
								foruma iletilmesi durumunda bu iletileri kaldırma hakkını kendilerine saklı tutmaktadır.
							</li>
							<li>İzEdebiyat'ın
								yazarlar hakkında topladığı bilgiler her zaman için saklıdır ve kimseye herhangi
								bir nedenle verilemez.
							</li>
							<li>Bir
								yazı yayınlandıktan sonra alıntı uzunluğunun 1/5'den fazla olması, temelsiz suçlamalarda bulunması ve
								benzeri
								durumların farkedilmesi durumunda yazı editörler tarafından İzEdebiyat'tan çıkarılır.
							</li>
						</ul>
						
						
						<p class="mt-3 text-center">
							<a href="{{route('register')}}"><img src="/frontend/anagrafikler/dugme_katilim2.gif" width="250" height="70"
							                        border="0"></a></p>
					</div>
				</div>
			</div>
		
		</main>
	</section>
@endsection
