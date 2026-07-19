<h2>{{ $user->page_title ?: 'Yazar tanıtımı' }}</h2>
<div class="content">
	{!! $aboutMe ?: '<p>Yazar tanıtımı bulunmuyor.</p>' !!}
</div>