@extends('layouts.settings')

@section('settings-content')
	
	<div class="tab-pane" id="nav-setting-tab-1">
		<!-- Account settings START -->
		<div class="card mb-4">
			
			<!-- Title START -->
			<div class="card-header border-0 pb-0">
				<h1 class="h5 card-title">{{__('default.Account Settings')}}</h1>
			</div>
			<!-- Card header START -->
			<!-- Card body START -->
			<div class="card-body">
				<!-- Form settings START -->
				
				<!-- Display success or error messages -->
				
				<form action="{{ route('sahne-arkasi.update') }}" method="post" class="row g-3"
				      enctype="multipart/form-data">
					@csrf
					<!-- First name -->
					<div class="col-sm-6 col-lg-6">
						<label class="form-label">{{__('default.Name')}}</label>
						<input type="text" name="name" class="form-control" placeholder=""
						       value="{{ old('name', $user->name) }}">
					</div>
					<!-- User name -->
					<div class="col-sm-6">
						<label class="form-label">{{__('default.User name')}}</label>
						<input type="text" name="username" class="form-control" placeholder=""
						       value="{{ old('username', $user->username) }}">
					</div>
					
					<!-- Email address -->
					<div class="col-sm-6">
						<label class="form-label">{{__('default.Email')}}</label>
						<input type="email" name="email" class="form-control" placeholder=""
						       value="{{ old('email', $user->email) }}">
					</div>
					
					<!-- Company name -->
					<div class="col-sm-6">
						<label class="form-label">{{__('default.Company Name')}}</label>
						<input type="text"
						       name="page_title"
						       class="form-control"
						       placeholder="Enter your company name"
							       value="{{ old('page_title', $user->page_title) }}">
					</div>
					
					<!-- Company description -->
					<div class="col-12">
						<label class="form-label">{{__('default.Company Description')}}</label>
						<textarea name="about_me"
						          class="form-control"
						          rows="3"
						          placeholder="Enter your company description">{{ old('about_me', $user->about_me) }}</textarea>
					</div>
					
					<!-- Avatar upload -->
					<div class="col-sm-6">
						<label class="form-label">{{__('default.Avatar')}}</label>
						<input type="file" name="avatar" class="form-control" accept="image/*">
					</div>
					
					<!-- Button -->
					<div class="col-12 text-start">
						<button type="submit" class="btn btn-sm btn-primary mb-0">{{__('default.Save changes')}}
						</button>
					</div>
				</form>
				<!-- Settings END -->
			</div>
			<!-- Card body END -->
			
			<!-- Account settings END -->
			
			<!-- Change your password START -->
			
			<div class="card">
				<!-- Title START -->
				<div class="card-header border-0 pb-0">
					<h5 class="card-title">{{__('default.Change your password')}}</h5>
					<p
						class="mb-0">{{__('default.If you signed up with Google, leave the current password blank the first time you update your password.')}}</p>
				</div>
				<!-- Title START -->
				<div class="card-body">
					
					<form action="{{ route('sahne-arkasi.sifre-guncelle') }}" method="post"
					      class="row g-3">
						@csrf
						<!-- Current password -->
						<div class="col-12">
							<label class="form-label">{{__('default.Current password')}}</label>
							<input type="password" name="current_password" class="form-control"
							       placeholder="">
						</div>
						<!-- New password -->
						<div class="col-12">
							<label class="form-label">{{__('default.New password')}}</label>
							<!-- Input group -->
							<div class="input-group">
								<input class="form-control fakepassword psw-input" type="password"
								       name="new_password" id="psw-input"
								       placeholder="Enter new password">
								<span class="input-group-text p-0">
                          <i class="fakepasswordicon fa-solid fa-eye-slash cursor-pointer p-2 w-40px"></i>
                        </span>
							</div>
							<!-- Pswmeter -->
							<div id="pswmeter" class="mt-2"></div>
							<div id="pswmeter-message" class="rounded mt-1"></div>
						</div>
						
						<!-- Confirm new password -->
						<div class="col-12">
							<label class="form-label">{{__('default.Confirm password')}}</label>
							<input type="password" name="new_password_confirmation"
							       class="form-control" placeholder="">
						</div>
						<!-- Button -->
						<div class="col-12 text-end">
							<button type="submit" class="btn btn-primary mb-0">{{__('default.Update password')}}
							</button>
						</div>
						
						<!-- Display success or error messages -->
						@if (session('success'))
							<div class="alert alert-success mt-2">
								{{ session('success') }}
							</div>
						@endif
						
						@if ($errors->any())
							<div class="alert alert-danger mt-2">
								<ul>
									@foreach ($errors->all() as $error)
										<li>{{ $error }}</li>
									@endforeach
								</ul>
							</div>
						@endif
					</form>
					
					<!-- Settings END -->
				</div>
			</div>
			<!-- Card END -->
		</div>
	</div>
	
	
	<!-- Vendors -->
	<script src="/assets/vendor/pswmeter/pswmeter.js"></script>


@endsection

@push('scripts')
	<script>
		$(document).ready(function () {
		
		});
	</script>
@endpush
