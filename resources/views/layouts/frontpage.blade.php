<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>

@if (isset($site))
	@if ($site->site_url == 'GrittyTravel.com')
		<meta name="google-site-verification" content="i7p4o3hPqvPXmI5kIW5RV4FPCWSH_dtRr-O7I8mI2WM" />
	@elseif ($site->site_url == 'EpicTravelGuide.com')
		<meta name="google-site-verification" content="MEp4jIJmKsPp12t-haoya25iUKZ6m4rrp2oUVPMPmv4" />
	@elseif ($site->site_url == 'HikeBikeBoat.com')
		<meta name="google-site-verification" content="bG25umkXbLrLjb4gUdQM4dk59Ot5jrIIwlZKK6Jt1gY" />	
	@elseif ($site->site_url == 'ScottHub.com')
		<meta name="google-site-verification" content="qYo4zBjsZSY-XJByMrHDUDKO_mTKFZ9tJKO2WdQTmPo" />	
	@endif
@endif
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
	<link href="{{ asset('css/footer.css') }}" rel="stylesheet">
	<link href="{{ asset('css/gallery.css') }}" rel="stylesheet">
	
    <!-- Bootstrap core CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

	<!-- App styles -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
	
	<!-- Fonts -->
	@if (!isset($localhost))
		<link href="https://fonts.googleapis.com/css?family=Volkhov:700|Oswald|Raleway|Ubuntu|Handlee" rel="stylesheet">
	@endif
	
	<script type="text/javascript" src="{{ URL::asset('js/jquery.min.js') }}"></script>	
	<script type="text/javascript" src="{{ URL::asset('js/myscripts.js') }}"></script>	
	
	@if (false && SITE_ID == 1)
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
<body onresize="onResize()" onload="onResize()">
    <div id="app" style="min-height: 500px;">
	
        <nav class="navbar navbar-default navbar-static-top {{(SITE_ID == 3) ? 'purple' : 'powerBlue'}}" style="margin-bottom:0px; border-width: 0 0 0px;"> 
			@component('menu-main', ['sections' => $sections, 'site' => $site])@endcomponent
        </nav>

		@if(session()->has('message.level'))
			<div style="" class="alert alert-{{ session('message.level') }}"> 
			{!! session('message.content') !!}
			</div>
		@endif

        @yield('content')
		
    </div>

	@component('footer', ['sections' => $sections, 'site' => $site])@endcomponent			
		
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/gallery.js') }}"></script>
</body>
</html>
