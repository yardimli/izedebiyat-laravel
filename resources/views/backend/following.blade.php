@extends('layouts.settings')

@section('settings-content')
	<div class="tab-pane" id="nav-setting-tab-1">
		<!-- Account settings START -->
		<div class="card mb-4">
			<!-- Title START -->
			<div class="card-header border-0 pb-0">
				<h1 class="h5 card-title">{{__('default.Favorites')}}</h1>
			</div>
			<!-- Card header START -->
			
			<!-- Card body START -->
			<div class="card-body">
				<div class="row">
					<div class="col-md-6">
						<h6>{{__('default.Authors I Follow')}}</h6>
						@foreach($following as $follow)
							@if ($follow->following)
								<div class="card mb-3">
									<div class="card-body">
										<div class="d-flex justify-content-between align-items-center">
											<div>
												<h6 class="card-title">
													<a
														href="{{ $follow->following && $follow->following->slug ? route('user', $follow->following->slug) : '#' }}">
														{{ $follow->following ? $follow->following->name : __('default.Deleted User') }}
													</a>
												</h6>
												<p
													class="card-text">{{__('default.Following since:')}} {{ \App\Helpers\MyHelper::timeElapsedString($follow->created_at) }}</p>
											</div>
											@if($follow->following)
												<button class="btn btn-sm btn-outline-danger unfollow-btn"
												        data-user-id="{{ $follow->following->id }}">
													{{__('default.Unfollow')}}
												</button>
											@endif
										</div>
									</div>
								</div>
							@endif
						@endforeach
						{{ $following->links() }}
					</div>
					
					<div class="col-md-6">
						<h6>{{__('default.Bookmarked Articles')}}</h6>
						@foreach($favorites as $favorite)
							@if ($favorite->article)
								<div class="card mb-3">
									<div class="card-body">
										<div class="d-flex justify-content-between align-items-center">
											<div>
												<h6 class="card-title">
													<a href="{{ route('article', $favorite->article->slug) }}">
														{{ $favorite->article->title }}
													</a>
												</h6>
												<p
													class="card-text">{{__('default.Bookmarked on:')}} {{ \App\Helpers\MyHelper::timeElapsedString($favorite->created_at) }}</p>
											</div>
											<button class="btn btn-sm btn-outline-danger remove-favorite-btn"
											        data-article-id="{{ $favorite->article->id }}">
												{{__('default.Remove')}}
											</button>
										</div>
									</div>
								</div>
							@endif
						@endforeach
						{{ $favorites->links() }}
					</div>
				</div>
			</div>
			<!-- Card body END -->
		</div>
		<!-- Account settings END -->
	</div>
@endsection

@push('scripts')
	<script>
		$(document).ready(function () {
			// Unfollow user
			$('.unfollow-btn').click(function () {
				const button = $(this);
				const userId = button.data('user-id');
				
				$.ajax({
					url: `/favori/yazar/${userId}`,
					type: 'POST',
					data: {
						_token: '{{ csrf_token() }}'
					},
					success: function (response) {
						if (!response.following) {
							// Remove the entire card
							button.closest('.card').fadeOut(function () {
								$(this).remove();
							});
						}
					},
					error: function (xhr) {
						console.error('Error:', xhr);
						alert('{{__("default.An error occurred while unfollowing")}}');
					}
				});
			});
			
			// Remove favorite article
			$('.remove-favorite-btn').click(function () {
				const button = $(this);
				const articleId = button.data('article-id');
				
				$.ajax({
					url: `/favori/eser/${articleId}`,
					type: 'POST',
					data: {
						_token: '{{ csrf_token() }}'
					},
					success: function (response) {
						if (!response.favorited) {
							// Remove the entire card
							button.closest('.card').fadeOut(function () {
								$(this).remove();
							});
						}
					},
					error: function (xhr) {
						console.error('Error:', xhr);
						alert('{{__("default.An error occurred while removing favorite")}}');
					}
				});
			});
		});
	</script>
@endpush
