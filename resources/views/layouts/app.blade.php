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
	<link href="{{ asset('css/default.css') }}" rel="stylesheet">
	<link href="{{ asset('css/footer.css') }}" rel="stylesheet">
	
    <!-- Bootstrap core CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

	<!-- App styles -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
	
	<!-- Fonts -->
	<link href="https://fonts.googleapis.com/css?family=Volkhov:700|Oswald|Raleway|Ubuntu|Handlee" rel="stylesheet">
		
	<!-- google fonts
	font-family: 'Raleway', sans-serif;
	-->
	
	<script type="text/javascript" src="{{ URL::asset('js/jquery.min.js') }}"></script>	
	<script type="text/javascript" src="{{ URL::asset('js/myscripts.js') }}"></script>	

	@if (defined('SITE_ID') && SITE_ID == 1)
	<!-- Google AdSense Activator -------------------------------->
	<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
	<script>
	  (adsbygoogle = window.adsbygoogle || []).push({
		google_ad_client: "ca-pub-3301644572924270",
		enable_page_level_ads: true
	  });
	</script>	
	@endif
	
</head>

<?php 

$is_logged_in = (null !== Auth::user());
$user_type = $is_logged_in ? intval(Auth::user()->user_type) : 0;
//echo $user_type;

if ($user_type >= 1000) // super admin
{
	$color = 'red'; 
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
    <div id="app" style="min-height:500px; ">
        <nav class="navbar navbar-default navbar-static-top" style="background-color: {{$color}}; ">
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

        @yield('content')
		
    </div>
	
	@if (isset($sections) && isset($site))
		@component('footer', ['sections' => $sections, 'site' => $site])@endcomponent			
	@else
		@component('footer')@endcomponent			
	@endif
	
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
