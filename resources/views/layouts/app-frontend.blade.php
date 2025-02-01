<!DOCTYPE html>
<html lang="tr">
<head>
	<title>@yield('title', 'İzEdebiyat')</title>
	
	<!-- Meta Tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="author" content="izedebiyat.com">
	<meta name="description"
	      content="{{__('default.İzEdebiyat')}} - {{__('default.Boilerplate Site Tagline')}}">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	
	<!-- Favicons -->
	<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
	<link rel="manifest" href="/site.webmanifest">
	
	<!-- Google Font -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
	
	<link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet"></link>
	<link href="https://fonts.googleapis.com/css2?family=Noto+Serif:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet"></link>
	<link href="https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet"></link>
	
	<link href="https://fonts.googleapis.com/css?family=Fira+Sans:300|Roboto+Condensed|Roboto+Mono|Ubuntu&display=swap" rel="stylesheet">
	
	<!-- Google fonts-->
	<link href="https://fonts.googleapis.com/css?family=Cabin:400,700&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=B612+Mono&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Lora:400,400i,700,700i&display=swap" rel="stylesheet">
	
	<!-- CSS -->
	<link href="{{ asset('/frontend/assets/css/bootstrap.css') }}" rel="stylesheet">
	<link href="{{ asset('/frontend/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
	
	<!-- Add other CSS -->
	<link href="{{ asset('/frontend/assets/css/style.css') }}" rel="stylesheet">
	<link href="{{ asset('/frontend/assets/css/widgets.css') }}" rel="stylesheet">
	<link href="{{ asset('/frontend/assets/css/color-default.css') }}" rel="stylesheet">
	<link href="{{ asset('/frontend/assets/css/responsive.css') }}" rel="stylesheet">
	<link href="{{ asset('/frontend/assets/css/fontello.css') }}" rel="stylesheet">
	<link href="{{ asset('/frontend/css/izedebiyat.css') }}" rel="stylesheet">
	<link href="{{ asset('/frontend/css/material-icons.min.css') }}" rel="stylesheet">
	
	<!-- Scripts -->
	<script src="{{ asset('/frontend/assets/js/jquery.min.js') }}"></script>
	<script src="{{ asset('/frontend/js/bootstrap.bundle.js') }}"></script>
	
	<script src="{{ asset('/frontend/assets/js/jquery.lazy.min.js') }}"></script>
	<script src="{{ asset('/frontend/assets/js/jquery-scrolltofixed-min.js') }}"></script>
	<script src="{{ asset('/frontend/assets/js/theia-sticky-sidebar.js') }}"></script>
	<script src="{{ asset('/frontend/assets/js/scripts.js') }}"></script>
	
	<!-- Google tag (gtag.js) -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-WSXNSG0STF"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());
		
		gtag('config', 'G-WSXNSG0STF');
	</script>
	
	
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
