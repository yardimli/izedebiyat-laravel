<!DOCTYPE html>
<html lang="tr">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>@yield('title', 'İzEdebiyat')</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	
	<!-- Favicons -->
	<link rel="apple-touch-icon" sizes="57x57" href="{{ asset('frontend/assets/images/favicon/apple-icon-57x57.png') }}">
	<!-- Add other favicon links -->
	
	<!-- CSS -->
	<link href="{{ asset('frontend/assets/css/bootstrap.css') }}" rel="stylesheet">
	<link href="{{ asset('frontend/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
	
	<link href="{{ asset('frontend/assets/css/owl.carousel.min.css') }}" rel="stylesheet">
	<link href="{{ asset('frontend/assets/css/owl.theme.default.min.css') }}" rel="stylesheet">
	
	<!-- Add other CSS -->
	<link href="{{ asset('frontend/assets/css/style.css') }}" rel="stylesheet">
	<link href="{{ asset('frontend/assets/css/widgets.css') }}" rel="stylesheet">
	<link href="{{ asset('frontend/assets/css/color-default.css') }}" rel="stylesheet">
	<link href="{{ asset('frontend/assets/css/responsive.css') }}" rel="stylesheet">
	<link href="{{ asset('frontend/assets/css/fontello.css') }}" rel="stylesheet">
	<link href="{{ asset('frontend/css/izedebiyat.css') }}" rel="stylesheet">
	<link href="{{ asset('frontend/css/material-icons.min.css') }}" rel="stylesheet">

	
	<link href="https://fonts.googleapis.com/css?family=Fira+Sans:300|Roboto+Condensed|Roboto+Mono|Ubuntu&display=swap" rel="stylesheet">
	
	<!-- Google fonts-->
	<link href="https://fonts.googleapis.com/css?family=Cabin:400,700&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=B612+Mono&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Lora:400,400i,700,700i&display=swap" rel="stylesheet">
	
	
	<!-- Scripts -->
	<script src="{{ asset('frontend/assets/js/jquery.min.js') }}"></script>
	<script src="{{ asset('frontend/js/bootstrap.bundle.js') }}"></script>
	
	<script src="{{ asset('frontend/assets/js/jquery.lazy.min.js') }}"></script>
	<script src="{{ asset('frontend/assets/js/owl.carousel.min.js') }}"></script>
	<script src="{{ asset('frontend/assets/js/jquery-scrolltofixed-min.js') }}"></script>
	<script src="{{ asset('frontend/assets/js/theia-sticky-sidebar.js') }}"></script>
	<script src="{{ asset('frontend/assets/js/scripts.js') }}"></script>
	
	@stack('styles')
</head>
<body class="@yield('body-class')">
@include('partials.header')

<main>
	@yield('content')
</main>

@include('partials.footer')

@stack('scripts')
</body>
</html>
