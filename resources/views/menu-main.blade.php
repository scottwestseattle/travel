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
							@if (null !== (session('spy', null)))
								<li><a href="/spy">Turn Spy Off</a></li>
							@endif
							@if (isset($sections) && array_key_exists(SECTION_ARTICLES, $sections))
								<li><a href="/articles"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-list"></span>Articles</a></li>
							@endif
							@if (isset($sections) && array_key_exists(SECTION_TOURS, $sections))
								<li><a href="/tours/index"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-tree-conifer"></span>Tours/Hikes</a></li>
							@endif
							@if (isset($sections) && array_key_exists(SECTION_BLOGS, $sections))
							<li><a href="/blogs/index"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-grain"></span>Blogs</a></li>
							@endif
							@if (isset($sections) && array_key_exists(SECTION_SLIDERS, $sections))
								<li><a href="/photos/sliders"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-picture"></span>Photos</a></li>
							@endif
							@if (false && isset($sections) && array_key_exists(SECTION_TOURS, $sections))
								<li><a href="/locations/index"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-map-marker"></span>Locations</a></li>
							@endif
							@if (false && isset($sections) && array_key_exists(SECTION_TOURS, $sections))
								<li><a href="/activities/maps"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-globe"></span>Maps</a></li>
							@endif	
							<li><a href="{{ route('login') }}"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-log-in"></span>Login</a></li>
                            <li><a href="{{ route('register') }}"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-user"></span>Register</a></li>
                        @else							
							@if ($user_type >= 100)
								<li><a href="/about">About</a></li>
								<li><a href="/admin">Admin</a></li>
								<li><a href="/articles">Articles</a></li>
								<li><a href="/entries/indexadmin/{{ENTRY_TYPE_ENTRY}}/">Entries</a></li>
								<li><a href="/photos/sliders">Sliders</a></li>
								<li><a href="/visitors/">Visitors</a></li>

								<li class="dropdown">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">More <span class="caret"></span></a>
									<ul class="dropdown-menu">
										<li><a href="/blogs/indexadmin">Blogs</a></li>
										<li><a href="/galleries">Galleries</a></li>
										<li><a href="/locations/indexadmin">Locations</a></li>
										<li><a href="/photos/indexadmin">Photos</a></li>
										<li><a href="/sites/index">Sites</a></li>
										<li><a href="/tours/indexadmin">Tours</a></li>
									</ul>
								</li>
								
								<li class="dropdown">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">Cash <span class="caret"></span></a>
									<ul class="dropdown-menu">
										<li><a href="/transactions/summary">Summary</a></li>
										<li><a href="/transactions/filter">Transactions</a></li>
										<li><a href="/accounts/index">Accounts</a></li>
										<li><a href="/categories/indexadmin">Categories</a></li>
										<li><a href="/email/check">Email</a></li>
										<li><a href="/transactions/expenses">Expenses</a></li>
										<li><a href="/subcategories/indexadmin">Subcategories</a></li>
									</ul>
								</li>
							@else
								@if (null !== (session('spy', null)))
									<li><a href="/spy">Turn Spy Off</a></li>
								@endif
								@if (isset($sections) && array_key_exists(SECTION_ARTICLES, $sections))
									<li><a href="/articles"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-list"></span>Articles</a></li>
								@endif
								@if (isset($sections) && array_key_exists(SECTION_TOURS, $sections))
									<li><a href="/tours/index"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-tree-conifer"></span>Tours/Hikes</a></li>
								@endif
								@if (isset($sections) && array_key_exists(SECTION_BLOGS, $sections))
								<li><a href="/blogs/index"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-grain"></span>Blogs</a></li>
								@endif
								@if (isset($sections) && array_key_exists(SECTION_SLIDERS, $sections))
									<li><a href="/photos/sliders"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-picture"></span>Photos</a></li>
								@endif
								<li><a href="/about"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-question-sign"></span>About</a></li>
							@endif
														
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu">
								
									@if (Auth::check())
										<li><a href="/users/">Settings ({{$user_type_name}})</a></li>
										@if (Auth::user()->user_type >= 1000)
											<li><a href="/activities/indexadmin">Activities</a></li>
											<li><a href="/tasks/index">Tasks</a></li>
											<li><a href="/templates/indexadmin">Templates</a></li>
										@endif
									@endif

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
			
