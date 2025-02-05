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
				<!-- Form settings START -->
				
				<!-- Display success or error messages -->
				
				
				<div class="row">
					<div class="col-md-6">
						<h6>{{__('default.Authors I Follow')}}</h6>
						@foreach($following as $follow)
							<div class="card mb-3">
								<div class="card-body">
									<h6 class="card-title">
										<a href="{{ $follow->following && $follow->following->slug ? route('user', $follow->following->slug) : '#' }}">
											{{ $follow->following ? $follow->following->name : __('default.Deleted User') }}
										</a>
									</h6>
									<p class="card-text">{{__('default.Following since:')}} {{ $follow->created_at->format('M d, Y') }}</p>
								</div>
							</div>
						@endforeach
						{{ $following->links() }}
					</div>
					
					<div class="col-md-6">
						<h6>{{__('default.Bookmarked Articles')}}</h6>
						@foreach($favorites as $favorite)
							<div class="card mb-3">
								<div class="card-body">
									<h6 class="card-title">
										<a href="{{ route('article', $favorite->article->slug) }}">
											{{ $favorite->article->title }}
										</a>
									</h6>
									<p class="card-text">{{__('default.Bookmarked on:')}} {{ $favorite->created_at->format('M d, Y') }}</p>
								</div>
							</div>
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
		
		});
	</script>
@endpush


