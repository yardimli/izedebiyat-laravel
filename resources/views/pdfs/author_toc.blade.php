<h1>{{ $user->name }}</h1>
<h2>İçindekiler</h2>
<ol class="toc-list">
	<li>Yazar tanıtımı</li>
	@foreach($articles as $article)
		<li>{{ \App\Helpers\MyHelper::replaceAscii($article->title) }}</li>
	@endforeach
</ol>