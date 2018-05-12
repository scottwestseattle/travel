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
						<?php $user_type = (null !== Auth::user()) ? intval(Auth::user()->type) : 0; ?>
                        @guest
                            <li><a href="{{ route('login') }}">Login</a></li>
                            <li><a href="{{ route('register') }}">Register</a></li>
                        @else
							<?php if ($user_type >= 1000) : ?>
								<li id="debug-tag-lg"><a href="/">Home (L)</a></li>
								<li id="debug-tag-md"><a href="/">Home (M)</a></li>
								<li id="debug-tag-sm"><a href="/">Home (S)</a></li>
								<li id="debug-tag-xs"><a href="/">Home (X)</a></li>
								<li><a href="/tests/">Tests</a></li>
							<?php endif; ?>
							
							<?php if ($user_type >= 100) : ?>
								<li><a href="/visits/">Visits</a></li>
								<li><a href="/entries/index/">Entries</a></li>
							<?php endif; ?>
														
							<li><a href="/entries/tours">Tours</a></li>
							<li><a href="/photos/sliders">Sliders</a></li>
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
			
