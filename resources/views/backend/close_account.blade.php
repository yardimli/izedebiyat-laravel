@extends('layouts.settings')

@section('settings-content')
	
	<!-- Close account tab START -->
	<div class="tab-pane" id="nav-setting-tab-6">
		<!-- Card START -->
		<div class="card">
			<!-- Card header START -->
			<div class="card-header border-0 pb-0">
				<h5 class="card-title">{{__('default.Delete account')}}</h5>
				<p class="mb-2">{{__('default.We are sorry to hear that you wish to delete your account.')}}</p>
				<p
					class="mb-2">{{__('default.Please note that deleting your account may result in the permanent loss of your data.')}}</p>
				<p
					class="mb-2">{{__('default.We are sad to see you go, but we hope that İzEdebiyat has been an enjoyable experience for you. We wish you the best in your future endeavors. Goodbye!')}}</p>
			</div>
			<!-- Card header START -->
			<!-- Card body START -->
			<div class="card-body">
				<!-- Delete START -->
				<h6>{{__('default.Before you go...')}}</h6>
				<ul>
					<li>{{__('default.If you delete your account, you will lose your all data.')}}</li>
				</ul>
				<div class="form-check form-check-md my-4">
					<input class="form-check-input" type="checkbox" value=""
					       id="deleteaccountCheck">
					<label class="form-check-label"
					       for="deleteaccountCheck">{{__('default.Yes, I\'d like to delete my account')}}</label>
				</div>
				<a href="#" class="btn btn-success-soft btn-sm mb-2 mb-sm-0">{{__('default.Keep my account')}}</a>
				<a href="#" class="btn btn-danger btn-sm mb-0">{{__('default.Delete my account')}}</a>
				<!-- Delete END -->
			</div>
			<!-- Card body END -->
		</div>
		<!-- Card END -->
	</div>
	<!-- Close account tab END -->
@endsection

@push('scripts')
	<script>
		$(document).ready(function () {
		
		});
	</script>
@endpush
