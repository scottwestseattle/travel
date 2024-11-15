
<!-- FOOTER -->
<footer id="footer">
	<div class="container">

		<div class="row">
						
			<div class="col-md-3">

				<!---------------------------------------------------->
				<!-- Small Description -->
				<!---------------------------------------------------->
				<h4 class="letter-spacing-1">@LANG('ui.CONTACT INFORMATION')</h4>

				<!---------------------------------------------------->
				<!-- Contact Address -->
				<!---------------------------------------------------->
				<address>
					<ul class="FooterListButton list-unstyled">
						<li class="address"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-map-marker"></span>{{isset($site) ? $site->site_url : ''}}<br></li>
						<li class="phone"><button id='phoneButton' class="btn btn-success"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-earphone"></span>@lang('ui.Show Phone Number')</button></li>
						<li class="email"><button id='emailButton' class="btn btn-success"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-envelope"></span>@lang('ui.Show Email Address')</button></li>
					</ul>
				</address>
				<!-- /Contact Address -->

						</div>

						<div style="margin:15px 0px 25px 0px" class="col-md-3">

@if (true || (isset($showBooking) && $showBooking))
					
<ins class="bookingaff" 
	data-aid="1535308" 
	data-target_aid="1535308" 
	data-prod="banner" 
	data-width="200" 
	data-height="200" 
	data-banner_id="67896" 
	data-lang="{{isset($geo) && $geo->isValid() ? $geo->language() : 'es-US'}}">
	
    <!-- Anything inside here will go away once widget is loaded. -->
    <a href="//www.booking.com?aid=1535308">Booking.com</a>
    <!-- end -->
</ins>

<script type="text/javascript">
    (function(d, sc, u) {
      var s = d.createElement(sc), p = d.getElementsByTagName(sc)[0];
      s.type = 'text/javascript';
      s.async = true;
      s.src = u + '?v=' + (+new Date());
      p.parentNode.insertBefore(s,p);
      })(document, 'script', '//aff.bstatic.com/static/affiliate_base/js/flexiproduct.js');
</script>

@endif

						</div>

						<div class="col-md-2">

							<!---------------------------------------------------->
							<!-- Site Map -->
							<!---------------------------------------------------->
							<h4 class="letter-spacing-1">@LANG('ui.SITE MAP')</h4>
							<ul class="FooterList list-unstyled">
								<li><a href="/"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-home"></span>@LANG('ui.Home')</a></li>
							@if (isset($sections) && array_key_exists(SECTION_ARTICLES, $sections))
								<li><a href="/articles"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-list"></span>@LANG('ui.Articles')</a></li>
							@endif
							@if (isset($sections) && array_key_exists(SECTION_BLOGS, $sections))
								<li><a href="/blogs/index"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-grain"></span>@LANG('ui.Blogs')</a></li>
							@endif
							@if (isset($sections) && array_key_exists(SECTION_HOTELS, $sections))
								<li><a href="/hotels"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-list"></span>@LANG('content.Hotels')</a></li>
							@endif
							@if (isset($sections) && array_key_exists(SECTION_TOURS, $sections))
								<li><a href="/tours/index"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-leaf"></span>@LANG('ui.Tours/Hikes')</a></li>
							@endif
							@if (false && isset($sections) && array_key_exists(SECTION_TOURS, $sections))
								<!-- this still uses Activities instead of tours -->
								<li><a href="/locations/index"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-map-marker"></span>@LANG('ui.Locations')</a></li>
							@endif
							@if (false && isset($sections) && array_key_exists(SECTION_TOURS, $sections))
								<li><a href="/activities/maps"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-globe"></span>@LANG('ui.Maps')</a></li>
							@endif
							@if (isset($sections) && array_key_exists(SECTION_SLIDERS, $sections))
								<li><a href="/photos/sliders"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-picture"></span>@LANG('ui.Photos')</a></li>
							@endif
							@if (isset($sections) && array_key_exists(SECTION_GALLERY, $sections))
								<li><a href="/galleries"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-picture"></span>@LANG('ui.Gallery')</a></li>
							@endif
								<li><a href="/login"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-log-in"></span>@LANG('ui.Login')</a></li>
								@if (false)
									<li><a href="/register"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-user"></span>@LANG('ui.Register')</a></li>
								@endif
							@if (Auth::user() && intval(Auth::user()->user_type) > 10)
								<li id="debug-tag-xl"><a href="/about"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-modal-window"></span>@LANG('ui.About') (XL)</a></li>
								<li id="debug-tag-lg"><a href="/about"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-modal-window"></span>@LANG('ui.About') (L)</a></li>
								<li id="debug-tag-md"><a href="/about"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-modal-window"></span>@LANG('ui.About') (M)</a></li>
								<li id="debug-tag-sm"><a href="/about"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-modal-window"></span>@LANG('ui.About') (S)</a></li>
								<li id="debug-tag-xs"><a href="/about"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-modal-window"></span>@LANG('ui.About') (XS)</a></li>
							@else
								<li><a href="/about"><span class="glyphSiteMap glyphCustom glyphicon glyphicon-modal-window"></span>@LANG('ui.About')</a></li>
							@endif
							
							</ul>

						</div>

						<div style="margin-top:15px;" class="col-md-4">

						@if (true || (isset($showGyg) && $showGyg))
							<a href="https://getyourguide.com?partner_id=RTJHCDQ&utm_medium=online_publisher&placement=banner" target="_blank">
								<img src="/img/theme1/gyg.svg" width="100" />
							</a>
						@endif

						@if (isset($site) && isset($site->instagram_link))
							<div style="margin-top: 20px;">
								<a href="{{$site->instagram_link}}" target="_blank"><img width="100" src="/img/theme1/instagram.png" /></a>
							</div>
						@endif

						</div>

					</div>

				</div>

				<div class="copyright">
					<div class="container center">
						<!-- ul class="pull-right nomargin list-inline mobile-block">
							<li><a href="#">Terms &amp; Conditions</a></li>
							<li>&bull;</li>
							<li><a href="#">Privacy</a></li>
						</ul -->
						<div class="text-center">
							@LANG('ui.All Rights Reserved') &copy; 2018 &ndash; {{date("Y")}}
						</div>
						<div class="text-center">
							<a href="https://info.flagcounter.com/ASyl" target="_blank"><img style="width:100px;" src="https://s01.flagcounter.com/mini/ASyl/bg_252525/txt_FFFFFF/border_252525/flags_0/" alt="Flag Counter" border="0"></a>			
						</div>
					</div>
	</div>
</footer>
<!-- /FOOTER -->
		
<script src="/js/jquery-2.1.4.min.js"></script>	
<script>

$(document).ready(function() {

$('#phoneButton').click(function() { 
	$('#phoneButton').remove();
	$('.phone').append('<span>{{isset($site) ? $site->telephone : ''}}</span>');
});

$('#emailButton').click(function() { 
	$('#emailButton').remove();
	$('.email').append('<span>{{isset($site) ? $site->email : ''}}</span>');
});

});

</script>	