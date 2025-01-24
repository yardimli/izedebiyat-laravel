<div class="box box-author mb-2">
	<div class="post-author row-flex">
		<div class="author-img">
			{!! \App\Helpers\MyHelper::generateInitialsAvatar($author->yazar_resim, $author->yazar_ad) !!}
		</div>
		<div class="author-content">
			<div class="top-author">
				<h5 class="heading-font">{{ $author->yazar_ad }}</h5>
			</div>
			<p>{{ $author->yazar_tanitim }}</p>
		</div>
	</div>
</div>
