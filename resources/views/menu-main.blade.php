           <div class="xcontainer">
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
						<?php 
							$user_type = (null !== Auth::user()) ? intval(Auth::user()->user_type) : 0; 
							$user_type_name = 'not set';
							if ($user_type >= 1000)
								$user_type_name = "super admin";
							else if ($user_type >= 100)
								$user_type_name = "admin";
							else if ($user_type >= 10)
								$user_type_name = "confirmed";
							else
								$user_type_name = "unconfirmed";	
						?>
                        @guest
                            <li><a href="{{ route('login') }}">Login</a></li>
                            <li><a href="{{ route('register') }}">Register</a></li>
                        @else
							<?php if (false && $user_type >= 1000) : ?>
								<li><a href="/tests/">Tests</a></li>
							<?php endif; ?>
							
							<?php if ($user_type >= 100) : ?>
								<li><a href="/admin">Admin</a></li>
								<li><a href="/activities/indexadmin">Activities</a></li>
								<li><a href="/entries/index/">Entries</a></li>
								<li><a href="/locations/index">Locations</a></li>
								<li><a href="/photos/sliders">Sliders</a></li>
								<li><a href="/tasks/index">Tasks</a></li>
								<li><a href="/tags/index">Tags</a></li>
								<li><a href="/users/">Users</a></li>
								<li><a href="/visits/">Visits</a></li>
							<?php endif; ?>
														
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu">
								
									<?php if (Auth::check()) : ?>
										<li><a href="/users/">Settings ({{$user_type_name}})</a></li>
									<?php endif; ?>

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
			
