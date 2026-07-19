<section class="title-page">
	@if($logoPath)
		<img class="title-logo-img" src="{{ $logoPath }}" alt="İzEdebiyat">
	@else
		<div class="title-logo">İzEdebiyat</div>
	@endif
	<div class="title-author">{{ $user->name }}</div>
	<div class="title-subtitle">Yazarın İzEdebiyat'ta yayımlanmış yazıları</div>

	<div class="title-summary">
		<p>Bu PDF, {{ $user->name }} adlı yazarın İzEdebiyat'ta yayımlanmış {{ $articles->count() }} yazısını içerir.</p>
		<p>
			Tarih aralığı:
			@if($dateRange['start'] && $dateRange['end'])
				{{ $dateRange['start'] }} - {{ $dateRange['end'] }}
			@else
				Yayımlanmış yazı bulunmuyor
			@endif
		</p>
		<p>Dışa aktarma tarihi: {{ $exportedAt }}</p>
		@if($includeReadCount)
			<p>Yazı bilgilerinde okunma sayıları yer almaktadır.</p>
		@endif
	</div>

	<p>Bu derleme okuma kolaylığı için hazırlanmıştır. Metinlerin tüm hakları yazarına aittir.</p>
</section>