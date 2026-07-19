<h2>{{ \App\Helpers\MyHelper::replaceAscii($article->title) }}</h2>
<div class="meta">
	{{ $article->category_name }} · {{ optional($article->created_at)->format('d.m.Y') }} · {{ (int)$article->read_count }} okunma
</div>
<div class="content">
	{!! $article->pdf_text !!}
</div>