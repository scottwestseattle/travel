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
                    </a>
					
					@if ($user_type >= 100)
						<a class="navbar-brand" href="{{ url('/admin') }}">
							<span class="glyphCustom glyphicon glyphicon-user"></span>
						</a>

						<a class="navbar-brand" href="{{url('/search')}}"><span class="glyphCustom glyphicon glyphicon-search"></span></a>
					@endif

					<div style="float:left;" class="dropdown" >
						<a href="#" class="dropdown-toggle navbar-brand" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true"><img width="25" src="/img/theme1/language-{{App::getLocale()}}.png" /></a>
						<ul class="dropdown-menu">
							<li><a href="/language/en"><img src="/img/theme1/language-en.png" /></a></li>
							<li><a href="/language/es"><img src="/img/theme1/language-es.png" /></a></li>
							<li><a href="/language/zh"><img src="/img/theme1/language-zh.png" /></a></li>
						</ul>
					</div>
					
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->


                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        @guest
							@if (null !== (session('spy', null)))
								<li><a href="/spyoff">@LANG('ui.Turn Spy Off')</a></li>
							@endif
							@if (isset($sections) && array_key_exists(SECTION_ARTICLES, $sections))
								<li><a href="/articles"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-list"></span>@lang('ui.Articles')</a></li>
							@endif
							@if (false && isset($sections) && array_key_exists(SECTION_HOTELS, $sections))
								<li><a href="/hotels"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-list"></span>@lang('content.Hotels')</a></li>
							@endif							
							@if (isset($sections) && array_key_exists(SECTION_TOURS, $sections))
								<li><a href="/tours/index"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-tree-conifer"></span>@lang('ui.Tours/Hikes')</a></li>
							@endif
							@if (isset($sections) && array_key_exists(SECTION_BLOGS, $sections))
							<li><a href="/blogs/index"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-grain"></span>@lang('ui.Blogs')</a></li>
							@endif
							@if (isset($sections) && array_key_exists(SECTION_GALLERY, $sections))
								<li><a href="/galleries"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-picture"></span>@lang('ui.Galleries')</a></li>
							@endif
							@if (false && isset($sections) && array_key_exists(SECTION_TOURS, $sections))
								<li><a href="/locations/index"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-map-marker"></span>Locations</a></li>
							@endif
							@if (false && isset($sections) && array_key_exists(SECTION_TOURS, $sections))
								<li><a href="/activities/maps"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-globe"></span>Maps</a></li>
							@endif	
							<li><a href="{{ route('login') }}"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-log-in"></span>@lang('ui.Login')</a></li>
							@if (true)
                            	<li><a href="/register"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-user"></span>@lang('ui.Register')</a></li>
							@endif
                        @else							
							@if ($user_type >= 100)
								<li><a href="/about">@lang('ui.About')</a></li>
								@if (isset($sections) && array_key_exists(SECTION_LESSONS, $sections))
									<li><a href="/lessons">@lang('ui.Lessons')</a></li>
								@endif
								<li><a href="/entries/indexadmin/{{ENTRY_TYPE_ENTRY}}/">@lang('ui.Entries')</a></li>
								<li><a href="/visitors/">@lang('ui.Visitors')</a></li>										
								<li class="dropdown">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">@lang('ui.More') <span class="caret"></span></a>
									<ul class="dropdown-menu">
										<li><a href="/admin">@lang('ui.Admin')</a></li>
										@if (isset($sections) && array_key_exists(SECTION_ARTICLES, $sections))
											<li><a href="/articles">@lang('ui.Articles')</a></li>
										@endif
										@if (isset($sections) && array_key_exists(SECTION_BLOGS, $sections))
											<li><a href="/blogs/indexadmin">@lang('ui.Blogs')</a></li>
										@endif
										<li><a href="/comments">@lang('ui.Comments')</a></li>
										<li><a href="/events/index">@lang('ui.Events')</a></li>
										@if (isset($sections) && array_key_exists(SECTION_GALLERY, $sections))
											<li><a href="/galleries">@lang('ui.Galleries')</a></li>
										@endif
										@if (isset($sections) && array_key_exists(SECTION_HOTELS, $sections))
											<li><a href="/hotels">@lang('content.Hotels')</a></li>
										@endif
										<li><a href="/locations/indexadmin">@lang('ui.Locations')</a></li>
										<li><a href="/photos/indexadmin">@lang('ui.Photos')</a></li>
										<li><a href="/sections">@lang('ui.Sections')</a></li>
										<li><a href="/sites/index">@lang('ui.Sites')</a></li>
										<li><a href="/sitemap/">@lang('ui.Site Map')</a></li>
										<li><a href="/photos/sliders">@lang('ui.Sliders')</a></li>
										<li><a href="/test/">@lang('ui.Tests')</a></li>
										@if (isset($sections) && array_key_exists(SECTION_TOURS, $sections))
											<li><a href="/tours/indexadmin">@lang('ui.Tours')</a></li>
										@endif
										<li><a href="/translations/">@lang('ui.Translations')</a></li>
									</ul>
								</li>
								
								@if (isset($sections) && array_key_exists(SECTION_CASH, $sections))
								<li class="dropdown">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">@lang('ui.Cash') <span class="caret"></span></a>
									<ul class="dropdown-menu">
										<li><a href="/transactions/summary">@lang('ui.Summary')</a></li>
										<li><a href="/transactions/filter">@lang('ui.Transactions')</a></li>
										<li><a href="/transactions/balances">@lang('ui.Balances')</a></li>
										<li><a href="/accounts/index">@lang('ui.Accounts')</a></li>
										<li><a href="/categories/indexadmin">@lang('ui.Categories')</a></li>
										<li><a href="/email/check">@lang('ui.Email')</a></li>
										<li><a href="/transactions/expenses">@lang('ui.Expenses')</a></li>
										<li><a href="/subcategories/indexadmin">@lang('ui.Subcategories')</a></li>
									</ul>
								</li>
								@endif
							@else
								@if (null !== (session('spy', null)))
									<li><a href="/spyoff">@LANG('ui.Turn Spy Off')</a></li>
								@endif
								@if (isset($sections) && array_key_exists(SECTION_ARTICLES, $sections))
									<li><a href="/articles"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-list"></span>@lang('ui.Articles')</a></li>
								@endif
								@if (isset($sections) && array_key_exists(SECTION_TOURS, $sections))
									<li><a href="/tours/index"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-tree-conifer"></span>@lang('ui.Tours/Hikes')</a></li>
								@endif
								@if (isset($sections) && array_key_exists(SECTION_BLOGS, $sections))
								<li><a href="/blogs/index"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-grain"></span>@lang('ui.Blogs')</a></li>
								@endif
								@if (isset($sections) && array_key_exists(SECTION_SLIDERS, $sections))
									<li><a href="/photos/sliders"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-picture"></span>@lang('ui.Photos')</a></li>
								@endif
								<li><a href="/about"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-question-sign"></span>@lang('ui.About')</a></li>
							@endif
														
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu">
								
									@if (Auth::check())
										<li><a href="/users/">Settings ({{$user_type_name}})</a></li>
										@if (Auth::user()->user_type >= 1000)
											<li><a href="/activities/indexadmin">@LANG('ui.Activities')</a></li>
											<li><a href="/tasks/index">@LANG('ui.Tasks')</a></li>
											<li><a href="/templates/indexadmin">@LANG('ui.Templates')</a></li>
										@endif
									@endif

                                    <li>
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            @LANG('ui.Logout')
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
			
