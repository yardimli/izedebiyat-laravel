<!doctype html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html lang="en">




	<head>

		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="author" content="DSAThemes">
		<meta name="description" content="{{__('default.İzEdebiyat')}} - {{__('default.Boilerplate Site Tagline')}}">
		<meta name="keywords" content="{{__('default.İzEdebiyat')}} - {{__('default.Boilerplate Site Tagline')}}">
		<meta name="viewport" content="width=device-width, initial-scale=1">
				
  		<!-- SITE TITLE -->
		<title>{{__('default.İzEdebiyat')}} - {{__('default.Boilerplate Site Tagline')}}</title>
		
		<!-- FAVICON AND TOUCH ICONS -->
		<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
		<link rel="manifest" href="/site.webmanifest">

		<!-- GOOGLE FONTS -->
		<link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&amp;display=swap" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet">
		
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

        .form-check-label {
						font-size: 16px;
				}
		</style>
		
	</head>




	<body>








		<!-- PAGE CONTENT ============================================= -->
		<div id="page" class="page font--jakarta">




			<!-- LOGIN PAGE
			============================================= -->
			<div id="login" class="bg--scroll login-section division">
				<div class="container">
					<div class="row justify-content-center">


						<!-- REGISTER PAGE WRAPPER -->
						<div class="col-lg-11">
							<div class="register-page-wrapper r-16 bg--fixed">
								<div class="row">


									<!-- LOGIN PAGE TEXT -->
									<div class="col-md-6">
										<div class="register-page-txt color--white">

											<!-- Logo -->
											<img class="img-fluid" src="{{ asset('/assets/images/logo/logo-large.png') }}" id="site_logo" alt="logo-image" style="max-width: 100px;">
											
											<!-- Text -->
											<p class="p-md mt-25">{{__('default.Boilerplate Site Tagline')}}
											</p>

											<!-- Copyright -->
											<div class="register-page-copyright">
												<p class="p-sm">&copy; 2025 www.izedebiyat.com</p>
											</div>

										</div>
									</div>	<!-- END LOGIN PAGE TEXT -->


									<!-- LOGIN FORM -->
									<div class="col-md-6">
										<div class="text-center mt-2">
											<a href="/" class="logo-black"><img src="{{ asset('/assets/images/logo/logo-large.png') }}" id="site_logo" alt="logo" style="max-width:100px;"></a>
										</div>
										
										<div class="register-page-form" style="margin-top: 5px; padding-top: 5px;">
											<form name="signinform" roll="form" class="row sign-in-form" method="POST" action="{{ route('login') }}">
												@csrf
												<!-- Google Button -->
												<input type="hidden" name="login2" value="true">
												<div class="col-md-12">
													<a  href="{{ url('login/google')}}" class="btn btn-google ico-left" style="margin-bottom: 10px;">
														<img src="/assets/v2/images/png_icons/google.png" alt="google-icon"> {{__('default.Log in with Google')}}
													</a>
													
												</div>

												<!-- Login Separator -->
												<div class="col-md-12 text-center">
													<div class="separator-line">{{__('default.Or')}}</div>
												</div>

												<!-- Form Input -->
												<div class="col-md-12">
													<p class="p-sm input-header">{{__('default.Email address')}}</p>
													<input class="form-control email" type="email" name="email" placeholder="example@example.com" value="{{ old('email', '') }}" required>
													<span class="form-group email-error {{ $errors->has('email') ? ' has-danger' : '' }}"></span>
												</div>

												<!-- Form Input -->
												<div class="col-md-12">
													<p class="p-sm input-header">{{__('default.Password')}}</p>
													<div class="wrap-input">
														<span class="btn-show-pass ico-20"><span class="flaticon-visibility eye-pass"></span></span>
														<input class="form-control password" type="password" name="password" placeholder="* * * * * * * * *" required>
														<span class="form-group {{ $errors->has('password') ? ' has-danger' : '' }}"></span>
														@include('alerts.feedback', ['field' => 'password'])
													</div>
												</div>

												<!-- Reset Password Link -->
												<div class="col-md-12">
													<div class="reset-password-link d-sm-flex justify-content-between">
														<div>
															<input class="form-check-input" type="checkbox"
																   name="remember" {{ old('remember') ? 'checked' : '' }} id="rememberCheck">
															<label class="form-check-label" for="rememberCheck">{{__('default.Remember me?')}}</label>
														</div>
														<a style="font-size: 16px;" href="/password/reset">{{__('default.Forgot password?')}}</a>
													</div>
												</div>

												<!-- Form Submit Button -->
												<div class="col-md-12">
													<button type="submit" class="btn btn--theme hover--theme submit">{{__('default.Login')}}</button>
												</div>

												<!-- Sign Up Link -->
												<div class="col-md-12">
													<p class="create-account text-center">{{__('default.Not a member yet?')}}<a href="{{route('register')}}" class="color--theme"> {{__('default.Sign up')}}</a></p>
												</div>

											</form>
										</div>
									</div>	<!-- END LOGIN FORM -->


								</div>  <!-- End row -->
							</div>	<!-- End register-page-wrapper -->
						</div>	<!-- END REGISTER PAGE WRAPPER -->


			 		</div>	   <!-- End row -->
			 	</div>	   <!-- End container -->
			</div>	<!-- END LOGIN PAGE -->




		</div>	<!-- END PAGE CONTENT -->
	</body>
</html>
