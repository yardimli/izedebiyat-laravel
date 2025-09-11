<!doctype html>
<!--[if lt IE 7 ]>
<html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]>
<html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]>
<html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html lang="en">


<head>
	
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="author" content="DSAThemes">
	<meta name="description" content="{{__('default.İzEdebiyat')}} - {{__('default.Boilerplate Site Tagline')}}">
	<meta name="keywords"
	      content="{{__('default.İzEdebiyat')}} - {{__('default.Boilerplate Site Tagline')}}">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<!-- SITE TITLE -->
	<title>{{__('default.İzEdebiyat')}} - {{__('default.Boilerplate Site Tagline')}}</title>
	
	<!-- FAVICON AND TOUCH ICONS -->
	<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
	<link rel="manifest" href="/site.webmanifest">
	
	<!-- GOOGLE FONTS -->
	<link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&amp;display=swap"
	      rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&amp;display=swap"
	      rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&amp;display=swap"
	      rel="stylesheet">
	
	<!-- BOOTSTRAP CSS -->
	<link href="/assets/v2/css/bootstrap.min.css" rel="stylesheet">
	
	<!-- FONT ICONS -->
	<link href="/assets/v2/css/flaticon.css" rel="stylesheet">
	
	<!-- PLUGINS STYLESHEET -->
	<link href="/assets/v2/css/menu.css" rel="stylesheet">
	<link id="effect" href="/assets/v2/css/dropdown-effects/fade-down.css" media="all" rel="stylesheet">
	<link href="/assets/v2/css/magnific-popup.css" rel="stylesheet">
	<link href="/assets/v2/css/lunar.css" rel="stylesheet">
	
	<!-- ON SCROLL ANIMATION -->
	<link href="/assets/v2/css/animate.css" rel="stylesheet">
	
	<!-- TEMPLATE CSS -->
	<link href="/assets/v2/css/blue-theme.css" rel="stylesheet">
	
	<!-- RESPONSIVE CSS -->
	<link href="/assets/v2/css/responsive.css" rel="stylesheet">
	
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>
	
	<style>
      .reset-page-wrapper .form-control,
      .register-page-form .form-control {
          height: 50px;
          font-size: 16px !important;
          line-height: 1;
          margin-bottom: 25px;
          padding: 5px 20px;
      }

      .input-header {
          font-size: 14px !important;
          margin-bottom: 10px;
      }

      .form-data span {
          font-size: 14px !important;
      }
      .form-check-input {
		      width: 20px;
		      height: 20px;
		      margin-top: 3px;
      }
      
      .separator-line {
		      font-size: 14px;
		      margin-top: 5px;
		      margin-bottom: 5px;
      }
      
      .btn {
		      padding: 10px !important;
		      font-size: 16px !important;
		      height: 40px !important;
      }

      .register-page-form p.create-account {
					font-size: 14px;
					margin-top: 20px;
			}
      
      .errors-field-username {
		      font-size: 14px;
		      font-weight: normal;
      }

      .g-recaptcha {
          margin-bottom: 15px;
      }

      .g-recaptcha > div {
          margin: 0 auto;
      }

      @media screen and (max-width: 767px) {
          .g-recaptcha {
              transform: scale(0.95);
              transform-origin: 0 0;
          }
      }
      
	</style>

</head>


<body>


<!-- PAGE CONTENT ============================================= -->
<div id="page" class="page font--jakarta">
	<!-- SIGN UP PAGE
	============================================= -->
	<div id="signup" class="bg--scroll login-section division">
		<div class="container">
			<div class="row justify-content-center">
				
				
				<!-- REGISTER PAGE WRAPPER -->
				<div class="col-lg-11">
					<div class="register-page-wrapper r-16 bg--fixed">
						<div class="row">
							
							
							<!-- SIGN UP FORM -->
							<div class="col-md-6">
								<div class="text-center mt-2">
									<a href="/" class="logo-black"><img src="{{ asset('/assets/images/logo/logo-large.png') }}"
									                                    id="site_logo" alt="logo"
									                                    style="max-height: 80px;"></a>
								</div>
								<div class="register-page-form" style="margin-top: 5px; padding-top: 5px;">
									
									<form class="row sign-up-form" method="POST" action="{{ route('register') }}" name="signupform"
									      role="form">
										{{--											<form name="signupform" class="row sign-up-form">--}}
										@csrf
										
										<!-- Google Button -->
										<div class="col-md-12">
											<a href="{{ url('login/google')}}" class="btn btn-google ico-left" style="margin-bottom: 10px;">
												<img src="/assets/v2/images/png_icons/google.png"
												     alt="google-icon"> {{__('default.Sign up with Google')}}
											</a>
										</div>
										
										<!-- Login Separator -->
										<div class="col-md-12 text-center">
											<div class="separator-line">{{__('default.Or Use Email')}}</div>
										</div>
										
										<!-- Form Input -->
										<div class="col-md-12 {{ $errors->has('username') ? ' has-danger' : '' }}">
											<p class="p-sm input-header" style="margin-bottom: 5px;">{{__('default.Name Slug')}}</p>
											<input class="form-control name" type="text" name="username" style="margin-bottom: 15px;"
											       placeholder="{{__('default.Enter Username...') }}" value="{{ old('username') }}"
											       autocomplete="username" autofocus required>
											@if ($errors->has('username'))
												<div id="username-error" class="error text-danger pl-3" for="username"
												     style="display: block; font-size: 14px">
													<span class="errors-field-username">{{ $errors->first('username') }}</span>
												</div>
											@endif
										</div>
										
										<div class="col-md-12 {{ $errors->has('name') ? ' has-danger' : '' }}">
											<p class="p-sm input-header" style="margin-bottom: 5px;">{{__('default.Real Name')}}</p>
											<input class="form-control name" type="text" name="name" style="margin-bottom: 15px;"
											       placeholder="{{__('default.Enter real name...') }}" value="{{ old('name') }}"
											       autocomplete="name" autofocus required>
											@if ($errors->has('name'))
												<div id="name-error" class="error text-danger pl-3" for="name"
												     style="display: block; font-size: 14px">
													<span class="errors-field-username">{{ $errors->first('name') }}</span>
												</div>
											@endif
										</div>
										
										<!-- Form Input -->
										<div class="col-md-12 {{ $errors->has('email') ? ' has-danger' : '' }}">
											<p class="p-sm input-header" style="margin-bottom: 5px;">{{__('default.Email')}}<span
													style="font-size: 12px; margin-left: 10px; color: gray">{{__('default.We\'ll never share your email with anyone else.')}}</span>
											</p>
											
											<input class="form-control email" type="email" name="email" style="margin-bottom: 15px;"
											       placeholder="example@example.com"
											       value="{{ old('email') }}"
											       required autocomplete="email">
											
											
											@if ($errors->has('email'))
												<div id="email-error" class="error text-danger pl-3" for="name"
												     style="display: block; font-size: 14px">
													<span class="errors-field-email">{{ $errors->first('email') }}</span>
												</div>
											@endif
										</div>
										
										<!-- Form Input -->
										<div class="col-md-12">
											<p class="p-sm input-header">{{__('default.Password')}}</p>
											<div class="wrap-input">
												<input class="form-control password" type="password" name="password" style="margin-bottom: 5px;"
												       placeholder="{{__('default.at least 8 characters')}}"
												       autocomplete="new-password" required>
											</div>
										</div>
										
										<div class="col-md-12">
											<input class="form-control" style="margin-bottom: 5px;" type="password"
											       placeholder="{{__('default.Confirm Password')}}"
											       autocomplete="new-password"
											       name="password_confirmation" id="password_confirmation" required>
										</div>
										
										@if ($errors->has('password'))
											<div id="password-error" class="error text-danger pl-3" for="password"
											     style="display: block; font-size: 14px">
												<span class="errors-field-pass">{{ $errors->first('password') }}</span>
											</div>
										@endif
										
										<!-- Checkbox -->
										<div class="col-md-12">
											<div class="form-data">
												<input class="form-check-input" type="checkbox" name="policy" id="policy"
												       style="float: left; margin-right: 5px;"
												       value="1" {{ old('policy', 0) ? 'checked' : '' }}>
												<span>{!! __('default.I Agree with', [ 'terms_url' => route('frontend.legal'), 'privacy_url' => route('frontend.secrecy') ]) !!}</span>
											</div>
										</div>
										@if ($errors->has('policy'))
											<div id="policy-error" class="error text-danger pl-3" for="policy"
											     style="display: block; font-size: 14px">
												<span class="errors-field-pass">{{ $errors->first('policy') }}</span>
											</div>
										@endif
										
										<div class="col-md-12 mb-3">
											<div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
											@if ($errors->has('g-recaptcha-response'))
												<div class="error text-danger">
													{{ $errors->first('g-recaptcha-response') }}
												</div>
											@endif
										</div>
										
										<!-- Form Submit Button -->
										<div class="col-md-12">
											<button type="submit"
											        class="btn btn--theme hover--theme submit">{{__('default.Create Account')}}</button>
										</div>
										
										<!-- Log In Link -->
										<div class="col-md-12">
											<p class="create-account text-center">
												{!! __('default.Already Have Account Sign In', ['login_url' => route('login')]) !!}
												<br>
												<a href="{{route('account-recovery.create')}}" class="btn btn-link text-decoration-none ms-2 text-nowrap">Hesabıma Erişemiyorum</a>
											</p>
										</div>
									
									</form>
								</div>
							</div>  <!-- END SIGN UP FORM -->
							
							
							<!-- SIGN UP PAGE TEXT -->
							<div class="col-md-6">
								<div class="register-page-txt color--white">
									
									<!-- Text -->
									<p
										class="p-md mt-25">{{__('default.Boilerplate Site Tagline')}}
									</p>
									
									<!-- Copyright -->
									<div class="register-page-copyright">
										<p class="p-sm">{!! __('default.&copy; 2025 www.izedebiyat.com All rights reserved.') !!}</p>
									</div>
								
								</div>
							</div>  <!-- END SIGN UP PAGE TEXT -->
						
						
						</div>  <!-- End row -->
					</div>  <!-- End register-page-wrapper -->
				</div>  <!-- END REGISTER PAGE WRAPPER -->
			
			
			</div>     <!-- End row -->
		</div>     <!-- End container -->
	</div>  <!-- END SIGN UP PAGE -->

</div>  <!-- END PAGE CONTENT -->
</body>
</html>
