<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>

	<meta name="google-adsense-account" content="ca-pub-3301644572924270">
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

    <title>{{$page_title}}</title>
	
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
	<link href="{{ asset('css/frontpage.css') }}" rel="stylesheet">
	<link href="{{ asset('css/project.css') }}" rel="stylesheet">
	<link href="{{ asset('css/footer.css') }}" rel="stylesheet">
	<link href="{{ asset('css/gallery.css') }}" rel="stylesheet">
	
    <!-- Bootstrap core CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

	<!-- App styles -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
	
	<!-- Fonts -->
	@if (isset($localhost) && $localhost)
		<?php //dump('localhost'); ?>
	@else
		<link href="https://fonts.googleapis.com/css?family=Volkhov:700|Oswald|Raleway|Ubuntu|Handlee" rel="stylesheet">
	@endif
	
	<script type="text/javascript" src="{{ URL::asset('js/jquery.min.js') }}"></script>	
	<script type="text/javascript" src="{{ URL::asset('js/myscripts.js') }}"></script>	
	
	@if (!Auth::check())
		<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3301644572924270"
			 crossorigin="anonymous"></script>
	@endif
	
</head>
<body onresize="onResize()" onload="">
    <div id="app" style="min-height: 500px;">
	
        <nav class="navbar navbar-default {{$colorMain}}" style="margin-bottom:0px; border-width: 0 0 0px;"> 
			@component('menu-main', ['sections' => $sections, 'site' => $site])@endcomponent
        </nav>

		@if(session()->has('message.level'))
			<div style="margin:0; padding: 5px 5px 5px 20px;" class="alert alert-{{ session('message.level') }}"> 
			{!! session('message.content') !!}
			</div>
		@endif

		@if (isset($euNoticeAccepted) && !$euNoticeAccepted)
			<div style="margin:0; padding: 5px 5px 5px 20px;" id="euNoticeAccepted" class="alert alert-success"> 
				<span>@LANG('content.European Union Privacy Notice')</span>
				<button type="submit" onclick="event.preventDefault(); ajaxexec('/eunoticeaccept'); $('#euNoticeAccepted').hide();" class="btn btn-primary" style="padding:1px 4px; margin:5px;">@LANG('ui.Accept')</button>
			</div>
		@endif
		
        @yield('content')
		
    </div>

	@component('footer', ['sections' => $sections, 'site' => $site, 'geo' => $geo])@endcomponent			
		
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/gallery.js') }}" onload="onResize()"></script>
</body>
</html>
