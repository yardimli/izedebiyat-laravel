<h1>{{ $user->name }}</h1>
<h2>İçindekiler</h2>
<ol class="toc-list">
	<li>
		<div class="toc-title">Yazar tanıtımı</div>
	</li>
	@foreach($articles as $article)
		<li>
			<div class="toc-title">{{ \App\Helpers\MyHelper::replaceAscii($article->title) }}</div>
			<div class="toc-meta">
				{{ optional($article->created_at)->format('d.m.Y') }}
				@if($article->parent_category_name || $article->category_name)
					· {{ $article->parent_category_name }}@if($article->parent_category_name && $article->category_name) / @endif{{ $article->category_name }}
				@endif
				@if($includeReadCount)
					· {{ (int)$article->read_count }} okunma
				@endif
			</div>
		</li>
	@endforeach
</ol>