<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

	
	<!-- set up the favicon.ico -->
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
<link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
<link rel="manifest" href="/manifest.json">
<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">	
	
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Travel Guide') }}</title>
	
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
   <link href="{{ asset('css/default.css') }}" rel="stylesheet">
	
    <!-- Bootstrap core CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

	<!-- App styles -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
	
	<!-- Fonts -->
	<link href="https://fonts.googleapis.com/css?family=Oswald|Raleway|Ubuntu" rel="stylesheet">
	
	<script type="text/javascript" src="{{ URL::asset('js/jquery.min.js') }}"></script>	
	<script type="text/javascript" src="{{ URL::asset('js/myscripts.js') }}"></script>	
		
</head>
<body>
    <div id="app" style="">
        <nav class="navbar navbar-default navbar-static-top" style="margin-bottom:0px; background-color: #FF6900 /* orange */; border-width: 0 0 0px;"> 
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse" aria-expanded="false">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar" style="background-color: white;"></span>
                        <span class="icon-bar" style="background-color: white;"></span>
                        <span class="icon-bar" style="background-color: white;"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
						<span class="glyphCustom glyphicon glyphicon-home"></span>
						<!--
						<img width="45px" src="/img/logo-top.png" />
                        {{ config('app.name', 'Travel') }}
						-->
                    </a>
					
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        &nbsp;
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
						<li><a href="/posts">Posts</a></li>
						<li><a href="/tours">Tours</a></li>
                        @guest
                            <li><a href="{{ route('login') }}">Login</a></li>
                            <li><a href="{{ route('register') }}">Register</a></li>
                        @else
							<li><a href="/entries/posts">Edit Posts</a></li>
							<li><a href="/entries/tours">Edit Tours</a></li>
                            <li><a href="/tasks/index">Tasks</a></li>
							
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        @yield('content')
		
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
	
	<script>

	var items = [
  [1, 2],
  [3, 4],
  [5, 6]
];
		var sliders = [
			 ["Jerusalem, Israel", "white"],
			 ["Vienna, Austria", "black"],
			 ["Budapest, Hungary", "white"],
			 ["Notre Dame, Paris", "#3F98FD"],
			 ["Bandar Seri Begawan, Brunei", "white"],
			 ["Vietnam", "white"],
			 ["Kuala Selangor, Malaysia", "white"],
			 ["Mekong River, Cambodia", "white"],
			 ["Ankgor Wat, Cambodia", "white"],
			 ["Mongolia", "white"],
			 ["Costa Brava, Spain", "white"],
		];

	
		var rnd = Math.floor(Math.random() * 11) + 1;
		var img = "slider" + rnd + ".jpg";
		document.getElementById("slider").style.backgroundImage = "url('/img/theme1/slider" + rnd + ".jpg')";
		document.getElementById("logo-big-text").style.color = sliders[rnd-1][1];
		document.getElementById("slider").title = img;
		document.getElementById("slider-text").innerText = sliders[rnd-1][0];
		document.getElementById("slider-text").style.color = 'white'; //sliders[rnd-1][1];
		//alert(document.getElementById("slider").title);
		//alert("url('/img/theme1/slider'" + rnd + "'.jpg')");
	</script>
	
	
</body>
</html>
