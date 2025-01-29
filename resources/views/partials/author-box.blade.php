<div class="box box-author mb-2">
	<div class="post-author row-flex">
		<div class="author-img">
			{!! \App\Helpers\MyHelper::generateInitialsAvatar($user->avatar, $user->name) !!}
		</div>
		<div class="author-content">
			<div class="top-author">
				<h5 class="heading-font">{{ $user->name }}</h5>
			</div>
			<p>{{ $user->page_title }}</p>
		</div>
	</div>
</div>
