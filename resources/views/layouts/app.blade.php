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

    <title>{{isset($page_title) ? $page_title : $_SERVER["SERVER_NAME"]}}</title>
	
    <!-- Styles -->
	<link href="{{ asset('css/app.css') }}" rel="stylesheet">
	<link href="{{ asset('css/project.css') }}" rel="stylesheet">
	<link href="{{ asset('css/default.css') }}" rel="stylesheet">
	<link href="{{ asset('css/footer.css') }}" rel="stylesheet">
	
    <!-- Bootstrap core CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

	<!-- App styles -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
	
	<!-- Fonts -->
	@if (!isset($localhost))
		<link href="https://fonts.apis.com/css?family=Volkhov:700|Oswald|Raleway|Ubuntu|Handlee" rel="stylesheet">
	@endif
		
	<!-- google fonts
	font-family: 'Raleway', sans-serif;
	-->
	
	<script type="text/javascript" src="{{ URL::asset('js/jquery.min.js') }}"></script>	
	<script type="text/javascript" src="{{ URL::asset('js/myscripts.js') }}"></script>	

	@if (!Auth::check())
		<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3301644572924270"
			 crossorigin="anonymous"></script>
	@endif

</head>

<?php 

$is_logged_in = (null !== Auth::user());
$user_type = $is_logged_in ? intval(Auth::user()->user_type) : 0;
//echo $user_type;

if ($user_type >= 1000) // super admin
{
	$color = 'purple'; 
}
else if ($user_type >= 100) // site admin
{
	$color = '#5CB85C'; // green
}
else if ($is_logged_in) // logged in, regular user
{
	$color = '#FF6900'; // orange header
}
else // not logged in
{
	$color = '#4993FD'; // blue header
}

?>

<body style="margin:0; padding:0;">
    <div id="app" style="min-height:500px;">
        <nav class="navbar navbar-default" style="background-color: {{$color}}; margin:0;">
			@if (isset($sections) && isset($site))
				@component('menu-main', ['sections' => $sections, 'site' => $site])@endcomponent
			@else
				@component('menu-main')@endcomponent
			@endif
        </nav>
		
@if(session()->has('message.level'))
    <div style="" class="alert alert-{{ session('message.level') }}"> 
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
	
	@if (isset($sections) && isset($site))
		@component('footer', ['sections' => $sections, 'site' => $site, 'geo' => $geo])@endcomponent			
	@else
		@component('footer')@endcomponent			
	@endif
	
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
