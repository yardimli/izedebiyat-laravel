<footer class="mt-2 mb-3">
	<div class="container">
		<div class="divider mb-2"></div>
		<div class="row mb-1">
			<ul class="inline text-md-right">
				<li class="list-inline-item entry-excerpt"><a href="{{ url('/son-eklenenler') }}">Son Eklenenler</a></li>
				<li class="list-inline-item entry-excerpt"><a href="{{ url('/yazarlar') }}">Yazarlar</a></li>
				<li class="list-inline-item entry-excerpt"><a href="{{ url('/katilim') }}">Katılım</a></li>
				<li class="list-inline-item entry-excerpt"><a href="mailto:iletisim@izedebiyat.com">İletişim</a></li>
				{{-- MODIFIED: Link to the account recovery page --}}
				<li class="list-inline-item entry-excerpt"><a href="{{ route('account-recovery.create') }}">Hesabıma Erişemiyorum</a></li>
				<li class="list-inline-item entry-excerpt"><a href="{{ url('/yasallik') }}">Yasallık</a></li>
				<li class="list-inline-item entry-excerpt"><a href="{{ url('/gizlilik') }}">Saklılık & Gizlilik</a></li>
				<li class="list-inline-item entry-excerpt"><a href="{{ url('/yayin-ilkeleri') }}">Yayın İlkeleri</a></li>
				<li class="list-inline-item entry-excerpt"><a href="{{ url('/izedebiyat') }}">İzEdebiyat?</a></li>
				<li class="list-inline-item entry-excerpt"><a href="{{ url('/sorular') }}">Sıkça Sorulanlar</a></li>
				<li class="list-inline-item entry-excerpt"><a href="{{ url('/kunye') }}">Künye</a></li>
			</ul>
		</div>
		<div class="row">
			<div class="col-md-6 copyright text-xs-center">
				Copyright (c) {{ date('Y') }} İzEdebiyat
			</div>
			<div class="col-md-6">
				{{--				<ul class="social-network inline text-md-right text-sm-center">--}}
				{{--					<li class="list-inline-item"><a href="#"><i class="icon-facebook"></i></a></li>--}}
				{{--					<li class="list-inline-item"><a href="#"><i class="icon-twitter"></i></a></li>--}}
				{{--				</ul>--}}
			</div>
			<div class="col-md-12 mt-2 mb-1">
				<div class="text-center">
					<a href="https://bookcoverzone.com" style="font-family:Verdana; font-size:12px;"><img src="/images/bookcoverzone-logo.jpg" alt="Custom &amp; Premade Book Covers" style="width:64px"><br>Book Cover Zone - Premade Book Covers</a>
				</div>
			</div>
			<div class="col-md-9 text-muted" style="font-size: 12px;">
				İzEdebiyat'da yayınlanan bütün yazılar, telif hakları yasalarınca korunmaktadır. Tümü yazarlarının ya da telif hakkı sahiplerinin izniyle sitemizde yer almaktadır. Yazarların ya da telif hakkı sahiplerinin izni olmaksızın sitede yer alan metinlerin -kısa alıntı ve tanıtımlar dışında- herhangi bir biçimde basılması/yayınlanması kesinlikle yasaktır.
				Ayrıntılı bilgi icin Yasallık bölümüne bkz.
			</div>
		
		</div>
	</div>
</footer>

<a href="#" class="back-to-top heading">
	<i class="icon-left-open-big"></i>
	<span class="d-lg-inline d-md-none">Başa Dön</span>
</a>
