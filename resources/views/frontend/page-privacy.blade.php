@extends('layouts.app-frontend')

@section('title', 'İzEdebiyat - Gizlilik ve Saklılık')
@section('body-class', 'home')

@section('content')
	{{ \App\Helpers\MyHelper::initializeCategoryImages() }}
	
	<section class="home">
		<main id="content">

			<div class="container-lg">
				<div class="entry-header">
					<div class="mb-5">
						<h1 class="entry-title mb-2">
							Gizlilik</h1>
						
						<p style="font-family: Arial, Helvetica, sans-serif; font-size: 16px;">İzEdebiyat yazarların gizliliğini
							korumak için çeşitli önlemleri almaktadır. Sayfalarda yer alan e-mail adresleri
							ve kişisel bilgiler yazarların kendi kararlarıyla İzEdebiyat sayfalarında
							genel görünüme açıktır. Bunların dışında İzEdebiyat, yazarların vermiş olduğu
							tüm özel bilgileri (adres, telefon, vb.) kendine saklı tutmaktadır. Bu bilgiler
							yazarları daha iyi tanıyabilmemiz ve buna bağlı olarak sitenin eksiklerini giderebilmemiz
							için toplanmaktadır. </p>
						<h1 class="entry-title mb-2">
							Saklılık</h1>
						
						<p style="font-family: Arial, Helvetica, sans-serif; font-size: 16px;">İzEdebiyat sitede yer alan
							yazıların üzerinde herhangi bir hak talep etmez. Yazarlar sayfalarında yer alan tüm metinlerin haklarını
							kendilerine saklı tutmaktadır. Onlarda sınırsız değişiklik,
							güncelleme ya da indirme yapma hakları vardır. Bununla
							birlikte bu tür işlemlerin sitede etkin duruma gelmesi birkaç gün alabilir. Söz konusu
							gecikmenin yoğun metin trafiğinden kaynaklandığını belirterek yazarların bu durumu hoşgörüyle
							karşılayacaklarını umuyoruz. </p>
						
						<p style="font-family: Arial, Helvetica, sans-serif; font-size: 16px;">Bir yazar sayfasını büsbütün
							kapatmak istiyorsa /iletisim.asp adresinden bu isteğini bildiren bir e-postayla
							başvurması gerekir. Böyle bir mektubun gelmesi durumunda yazara en kısa zamanda bir onay
							mektubu iletilecektir. Bu mektubun gerisin geri yazarın eposta adresiyle ve
							adıyla yukarıdaki adrese gönderilmesi durumunda sayfa en geç birkaç gün içinde kapatılacaktır. Tüm kişisel
							kayıtlar ve yazılar İzEdebiyat sunucu bilgisayarlarından silinecektir.</p>
						
						<p style="font-family: Arial, Helvetica, sans-serif; font-size: 16px;">Saklılık hakkının yazar
							üzerine getirdiği tek yükümlülük yayınlanan yazılardan yazarın sorumlu olmasıdır. İzEdebiyat temelde bir
							arşivleme sitesi olduğundan bu konuda hiçbir yasal yükümlülük taşımaz.
							Genel yayın ahlakına aykırı ya da T.C. anayasası ve/ya da uluslararası yasalara
							aykırı bir yazının yüklenmesi durumunda bundan doğacak tüm sorunlar birinci
							dereceden yazarın ve/ya da kişisel sayfa sorumlusunu ilgilendirecektir.
							İzEdebiyat giriş yapılan yazıları elinden geldiğince böyle bir riske karşı denetlemeye çalışmaktadır.
							Yine de yazarların, tarafımızca ya da yasalarca yapılacak herhangi bir uyarıya maruz kalmaksızın bu
							duyarlılığı göstereceklerini varsayıyoruz. </p>
						
						<p style="font-family: Arial, Helvetica, sans-serif; font-size: 16px;"><b>Yazarlardan ne tür bilgiler
								topluyoruz?</b></p>
						
						<p style="font-family: Arial, Helvetica, sans-serif; font-size: 16px;">Ad, email, posta adresi dışında
							İzEdebiyat tüm
							kullanıcıların
							harddisklerine birer "cookie" yerleştirmektedir. Cookie'ler ufak bilgi
							dosyalarıdır. Bu dosyalarda yer alan bilgiler sayesinde bir yazar yeniden sitemize
							girdiğinde onu tanıyabiliyoruz. Bu, gerektiğinde ona kişisel hizmetler sunmamız olanaklı kılıyor.
							Cookie'ler herhangi bir biçimde karşı tarafın sisteminde bir tehlike yaratmıyordur ve yalnızca
							İzEdebiyat'ta daha önce hangi sayfaları gezmiş olduğunuzun, en son ne zaman
							girmiş olduğunuzun kaydını tutuyordur. Cookie bilgileri yalnız şifre sahipleri görüntüleyebilmektedir
							ve hiçbir şekilde dışarı sızmıyor, hiçbir 'e-mail ticareti' kanalına girmiyordur.</p>
					</div>
				</div>
			</div>
		
		
		</main>
	</section>
@endsection
