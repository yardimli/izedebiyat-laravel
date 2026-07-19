<div class="{{ ($compactEntry ?? false) ? 'entry-block' : '' }}">
	<h2>{{ \App\Helpers\MyHelper::replaceAscii($article->title) }}</h2>
	@if($article->subheading)
		<div class="subheading">{{ \App\Helpers\MyHelper::replaceAscii($article->subheading) }}</div>
	@endif
	<div class="meta">
		{{ $article->parent_category_name }}@if($article->parent_category_name && $article->category_name) / @endif{{ $article->category_name }}
		· {{ optional($article->created_at)->format('d.m.Y') }}
		@if($includeReadCount)
			· {{ (int)$article->read_count }} okunma
		@endif
	</div>
	<div class="content">
		{!! $article->pdf_text !!}
	</div>
</div>
