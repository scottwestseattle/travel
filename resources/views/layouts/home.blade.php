<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

	
	<!-- set up the favicon.ico -->
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="manifest" href="/site.webmanifest">
<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#4993fd">
<meta name="msapplication-TileColor" content="#4993fd">
<meta name="theme-color" content="#ffffff">
	
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

	<?php
		if (!isset($page_title))
			$page_title = config('app.name', 'Travel Guide');
		else
			$page_title = config('app.name', 'Travel Guide') . ' - ' . $page_title;
	?>
    <title>{{$page_title}}</title>
	
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
	<link href="{{ asset('css/home.css') }}" rel="stylesheet">
	<link href="{{ asset('css/footer.css') }}" rel="stylesheet">
	
    <!-- Bootstrap core CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

	<!-- App styles -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
	
	<!-- Fonts -->
	@if (!isset($localhost))
		<link href="https://fonts.googleapis.com/css?family=Oswald|Raleway|Ubuntu|Handlee" rel="stylesheet">
	@endif
	
	<script type="text/javascript" src="{{ URL::asset('js/jquery.min.js') }}"></script>	
	<script type="text/javascript" src="{{ URL::asset('js/myscripts.js') }}"></script>	
		
</head>
<body>
    <div id="app" style="min-height: 500px;">
        <nav class="navbar navbar-default purple" style="margin-bottom:0px; border-width: 0 0 0px;"> 
			@component('menu-main')
			@endcomponent
        </nav>

        @yield('content')
		
    </div>

	@component('footer')@endcomponent			
		
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
