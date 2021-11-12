@extends('layouts.frontpage')

@section('content')

@php

// THIS IS THE FRONT PAGE

function getSection($id, $array)
{
	$section = null;
	
	if (array_key_exists($id, $array))
	{
		$section = $array[$id];
	}
	
	return $section;
}

//echo '<p>site: ' . $site->site_url . '</p>';
if (strtolower($site->site_url) == 'scotthub.com')
{
	$colors = [
		'sectionGray',
		'sectionGray',
		'sectionOrange',
		'sectionGray',
		'sectionOrange',
		'sectionGray',
		'sectionWhite',
	];
	
	$colorAlt = 'DarkBlue';
}
else
{
	$colors = [
		'sectionGray',
		'powerBlue',
		'sectionGray',
		'sectionGreen',
		'sectionGray',
		'sectionOrange',
		'sectionGray',
		'sectionWhite',
		'sectionGreen',
	];
	
	$colorAlt = 'DarkBlue';
}

$sectionCount = 0;

@endphp

<!--------------------------------------------------------------------------------------->
<!-- Title Logo Bar -->
<!--------------------------------------------------------------------------------------->

@if (count($sections) == 0)

<?php 
	$title = 'Welcome to the front page of this web site';
	$text = 'To add content, use the \'Sections\' menu option.'; 
?>

<section id="" class="sectionOrange" style="padding: 30px 0 40px 0;">
<div class="container" style="max-width:1400px;">	
	<div class="sectionHeader text-center">	
		
		<div class="hidden-xl hidden-lg hidden-md hidden-sm">
			<!-- xs only -->
			<h3 style="font-size:1.2em;" class="welcome-text main-font">{{$title}} </h3>
			<p>{{$text}}</p>
		</div>
		
		<div class="hidden-xs" >
			<!-- all other sizes -->
			<h3 class="welcome-text main-font">{{$title}} </h3>
			<p>{{$text}}</p>
		</div>

	</div>
</div>
</section>

@endif

	<!--------------------------------------------------------------------------------------->
	<!-- Sliders -->
	<!--------------------------------------------------------------------------------------->

@if (getSection(SECTION_SLIDERS, $sections) != null)
	
@if (count($sliders_h) > 0 && count($sliders_v) > 0)
<?php $sectionCount++; ?>
<div style="width:100%; background-color: white; background-position: center; background-repeat: no-repeat; background-image:url('/img/theme1/load-loop.gif'); " >
@else
<div style="width:100%; background-color: gray; background-position: cover; background-image:url('/img/theme1/bg-pattern.png'); " >
@endif
	<section>
		<div class="slider-center">
		
@if ($newWay)
			<div class="hidden-xl hidden-lg hidden-md hidden-sm"><!-- xs only -->
			
				<!------------------------------------------------------->
				<!-- this is the vertical slider for XS, the background image is set by javascript at the end of the page -->
				<!------------------------------------------------------->		
					
				<div id="slider-xs" style="xmin-height:800px; background-repeat: no-repeat; background-size: cover; background-position: center center; background-attachment:fixed;">
					<!------------------------------------------------------->
					<!-- this is the slider spacer used to size the slider-->
					<!------------------------------------------------------->			
					<img id="slider-spacer" src="/img/theme1/spacer.png" width="100%" height="550px;" />
				
					<!------------------------------------------------------->
					<!-- these are the slider mover arrows -->
					<!------------------------------------------------------->
					<div id="slider-arrow-left" style="font-size: 150px; position:absolute; top:0; left:0">
						<span id="slider-control-left" style="opacity:0.0; color: white;" onclick="slider_left()" onmouseover="showSliderControls(true)" onmouseout="showSliderControls(false)">
							<span class="glyphicon glyphicon-chevron-left" style="background-color:black; border-radius:8px;"></span>
						</span>
					</div>
					
					<div id="slider-arrow-right" style="font-size:150px; position:absolute; top:0; right:0;">
						<span id="slider-control-right" style="opacity:0.0; color: white;" onclick="slider_right()" onmouseover="showSliderControls(true)" onmouseout="showSliderControls(false)" >
							<span class="glyphicon glyphicon-chevron-right"  style="background-color:black; border-radius:8px;"></span>
						</span>
					</div>	
				</div>			
			
			</div>
			
			<div class="hidden-xs" ><!-- all other sizes -->

				<!------------------------------------------------------->
				<!-- this is the horizontal slider, the background image is set by javascript at the end of the page -->
				<!------------------------------------------------------->	
						
				<div id="slider" style="min-height:800px; background-repeat: no-repeat; position: relative;">
					<!------------------------------------------------------->
					<!-- this is the slider spacer used to size the slider-->
					<!------------------------------------------------------->			
					<img id="slider-spacer" src="/img/theme1/spacer.png" width="100%" />
				
					<!------------------------------------------------------->
					<!-- these are the slider mover arrows -->
					<!------------------------------------------------------->
					<div id="slider-arrow-left" style="font-size: 150px; position:absolute; top:0; left:0">
						<span id="slider-control-left" style="opacity:0.0; color: white;" onclick="slider_left()" onmouseover="showSliderControls(true)" onmouseout="showSliderControls(false)">
							<span class="glyphicon glyphicon-chevron-left" style="background-color:black; border-radius:8px;"></span>
						</span>
					</div>
					
					<div id="slider-arrow-right" style="font-size:150px; position:absolute; top:0; right:0;">
						<span id="slider-control-right" style="opacity:0.0; color: white;" onclick="slider_right()" onmouseover="showSliderControls(true)" onmouseout="showSliderControls(false)" >
							<span class="glyphicon glyphicon-chevron-right"  style="background-color:black; border-radius:8px;"></span>
						</span>
					</div>	
				</div>

			</div>	
@else			
			<!------------------------------------------------------->
			<!-- ORIGINAL CODE: this is the slider, the background image is set by javascript at the end of the page -->
			<!------------------------------------------------------->	
					
			<div id="slider" style="min-height:800px; background-repeat: no-repeat; position: relative;">
				<!------------------------------------------------------->
				<!-- this is the slider spacer used to size the slider-->
				<!------------------------------------------------------->			
				<img id="slider-spacer" src="/img/theme1/spacer.png" width="100%" />
			
				<!------------------------------------------------------->
				<!-- these are the slider mover arrows -->
				<!------------------------------------------------------->
				<div id="slider-arrow-left" style="font-size: 150px; position:absolute; top:0; left:0">
					<span id="slider-control-left" style="opacity:0.0; color: white;" onclick="slider_left()" onmouseover="showSliderControls(true)" onmouseout="showSliderControls(false)">
						<span class="glyphicon glyphicon-chevron-left" style="background-color:black; border-radius:8px;"></span>
					</span>
				</div>
				
				<div id="slider-arrow-right" style="font-size:150px; position:absolute; top:0; right:0;">
					<span id="slider-control-right" style="opacity:0.0; color: white;" onclick="slider_right()" onmouseover="showSliderControls(true)" onmouseout="showSliderControls(false)" >
						<span class="glyphicon glyphicon-chevron-right"  style="background-color:black; border-radius:8px;"></span>
					</span>
				</div>	
			</div>
@endif

			<div class="{{$colorAlt}}">
				<!------------------------------------------------------->
				<!-- This is the slider caption -->
				<!------------------------------------------------------->
				<div class="hidden-xl hidden-lg hidden-md hidden-sm"><!-- xs only -->
					<div id="slider-text-xs" style="font-size:.8em; width:100%; font-style:italic;"></div>
				</div>
				<div class="hidden-xs" ><!-- all other sizes -->
					<div id="slider-text" style="width:100%; font-style:italic;"></div>
				</div>				
			</div>
		</div>
	</section>
</div>

@if ($newWay)
<script>

	var sliders_h = [
		@foreach($sliders_h as $slider)
			['{{$slider->filename}}', '{{$slider->location}}', '{{$slider->alt_text}}', {{$slider->type_flag}}],
		@endforeach
	];
	
	var sliders_v = [
		@foreach($sliders_v as $slider)
			['{{$slider->filename}}', '{{$slider->location}}', '{{$slider->alt_text}}', {{$slider->type_flag}}],
		@endforeach
	];
	
	var sliderPath = "{{$slider_path}}";
	
	// if firstslider is set then show the first one, otherwise show one randomly
	@if (isset($firstslider))
	//var ix = sliders.length > {{$slider_count / 2}} ? {{$slider_count / 2}} : 0;
	@else
	@endif

	var ix_v = Math.floor(Math.random() * sliders_v.length);
	var ix_h = Math.floor(Math.random() * sliders_h.length);

	slider_update(sliders_h, ix_h, "slider", "slider-text");
	slider_update(sliders_v, ix_v, "slider-xs", "slider-text-xs");
	
	function decodeHtml(html) {
		var txt = document.createElement("textarea");
		txt.innerHTML = html;
		return txt.value;
	}
	
	function showSliderControls(show)
	{	
		if (show)
		{
			document.getElementById("slider-control-left").style.opacity = "0.0";
		}
		else
		{
			document.getElementById("slider-control-left").style.opacity = "0.0";
		}
		
		document.getElementById("slider-control-right").style.opacity = document.getElementById("slider-control-left").style.opacity;
	}
	
	function slider_right()
	{
		ix_h = slider_right_update(sliders_h, ix_h, "slider", "slider-text")
		ix_v = slider_right_update(sliders_v, ix_v, "slider-xs", "slider-text-xs")
	}

	function slider_left()
	{
		ix_h = slider_left_update(sliders_h, ix_h, "slider", "slider-text")
		ix_v = slider_left_update(sliders_v, ix_v, "slider-xs", "slider-text-xs")
	}
	
	function slider_left_update(sliders, ix, slider_id, slider_text_id)
	{
		ix--;
		
		if (ix < 0)
			ix = sliders.length - 1;
			
		slider_update(sliders, ix, slider_id, slider_text_id);
		
		return ix;
	}
		
	function slider_right_update(sliders, ix, slider_id, slider_text_id)
	{
		ix++;
		
		if (ix > sliders.length - 1)
			ix = 0;
			
		slider_update(sliders, ix, slider_id, slider_text_id);
		
		return ix;
	}
	
	function slider_update(sliders, ix, slider_id, slider_text_id)
	{
		var img = sliders[ix][0];
		var loc = sliders[ix][1];
		var alt = sliders[ix][2];
	
		document.getElementById(slider_id).style.minHeight = ''; // the min-height is only set so they initial slider load isn't so jerky, once it's loaded, remove this
		document.getElementById(slider_id).style.backgroundImage = "url('" + sliderPath + img + "')";
		document.getElementById(slider_text_id).innerHTML = loc;
		document.getElementById(slider_id).title = decodeHtml(alt + ', ' + loc);
	}
</script>

@else

<script>

	function decodeHtml(html) {
		var txt = document.createElement("textarea");
		txt.innerHTML = html;
		return txt.value;
	}
	
	// load all the sliders so we can javascript through them
	var sliderPath = "{{$slider_path}}";
	var sliders = [
		@foreach($sliders as $slider)
			['{{$slider->filename}}', '{{$slider->location}}', '{{$slider->alt_text}}'],
		@endforeach
	];
	
	// if firstslider is set then show the first one, otherwise show one randomly
	@if (isset($firstslider))
	var ix = 0;
	@else
	var ix = Math.floor(Math.random() * sliders.length);
	@endif
	
	var img = sliders[ix][0];
	var loc = sliders[ix][1];
	var alt = sliders[ix][2];
	
	document.getElementById("slider").style.backgroundImage = "url('" + sliderPath + img + "')";
	document.getElementById("slider").style.minHeight = ''; // the min-height is only set so they initial slider load isn't so jerky, once it's loaded, remove this
	document.getElementById("slider-text").innerHTML = loc;
	document.getElementById("slider-text-xs").innerHTML = loc;
	document.getElementById("slider").title = decodeHtml(alt + ', ' + loc);

	function showSliderControls(show)
	{	
		if (show)
		{
			document.getElementById("slider-control-left").style.opacity = "0.0";
		}
		else
		{
			document.getElementById("slider-control-left").style.opacity = "0.0";
		}
		
		document.getElementById("slider-control-right").style.opacity = document.getElementById("slider-control-left").style.opacity;
	}
	
	function slider_left()
	{
		ix--;
		
		if (ix < 0)
			ix = sliders.length - 1;
			
		slider_update();
	}
	
	function slider_right()
	{
		ix++;
		
		if (ix > sliders.length - 1)
			ix = 0;
			
		slider_update();
	}
	
	function slider_update()
	{
		var img = sliders[ix][0];
		var loc = sliders[ix][1];
		var alt = sliders[ix][2];
		
		document.getElementById("slider").style.backgroundImage = "url('" + sliderPath + img + "')";
		document.getElementById("slider-text").innerHTML = loc;
		document.getElementById("slider-text-xs").innerHTML = loc;
		document.getElementById("slider").title = decodeHtml(alt + ', ' + loc);
	}

</script>	

@endif

@endif	

<!--------------------------------------------------------------------------------------->
<!-- SECTION 1: Welcome -->
<!--------------------------------------------------------------------------------------->	
@if (($section = getSection(SECTION_WELCOME, $sections)) != null)
<section id="" class="{{$colors[$sectionCount++]}}" style="padding: 30px 0 40px 0; xposition: relative; xtop: -30px; ">
<div class="container" style="max-width:1400px;">	
	<div class="sectionHeader text-center">	
		
	@if (strlen($section->description) > 0)
		<div class="hidden-xl hidden-lg hidden-md hidden-sm">
			<!-- xs only -->
			<h3 style="font-size:1.2em;" class="welcome-text main-font">{{$section->description}} </h3>
		</div>
		<div class="hidden-xs" >
			<!-- all other sizes -->
			<h3 class="welcome-text main-font">{{ $section->description }} </h3>
		</div>
	@endif
		
	@if ($showFullGallery)
		<div style="margin-top:10px;">
			<img style="width:95%; max-width:200px;" src="/img/theme1/logo-{{$domainName}}.png" />
		</div>
	@else
		<div style="margin-top:40px;">
		<?php 
			$p = '/img/theme1/logo-' . $domainName . '.png'; 
			$fp = base_path() . '/public' . $p; 
		?>
		@if (file_exists($fp))
			<img style="width:95%; max-width:200px;" src="{{$p}}" />
		@endif
		</div>
	@endif
	
	</div>	

	<!--------------------------------------------------------------------------------------->
	<!-- The "Join Us" button -->
	<!--------------------------------------------------------------------------------------->		
@if (false)
	<div class="row text-center" style="margin-top:40px;">
		<div class="header">
			<a href="/register"><button class="textWhite formControlSpace20 btn btn-submit btn-lg bgGreen"><span class="glyphicon glyphicon-user"></span>&nbsp;Click Here to Join us!</button></a>
		</div>		
	</div>
@endif

	<!--------------------------------------------------------------------------------------->
	<!-- The charming Quote -->
	<!--------------------------------------------------------------------------------------->		
@if (strlen($section->description_short) > 0)
	<div class="sectionHeader text-center" style="margin-top:20px;">
		<h3 style="font-size:1.2em;" class="welcome-text main-font"><i>{{$section->description_short}}</i></h3>
	</div>
@endif
	
</div>
</section>
@endif

<!--------------------------------------------------------------------------------------->
<!-- SECTION: Photo Gallery -->
<!--------------------------------------------------------------------------------------->

@if (($section = getSection(SECTION_GALLERY, $sections)) != null)

<div id="load-loop" class="" style="width:100%; text-align: center; padding-top:100px; display:none;">
	<img src="/img/theme1/load-loop.gif" />
</div>

<div id="content" style="display:default;">
<div id="container" class="{{$colors[$sectionCount++]}}" style="min-height:200px;" >

@if (!$showFullGallery)

	@if (false)
	
	<div class="text-center" style="padding: 25px 10px 0 10px;">
		<a target="_blank" href="https://www.booking.com/index.html?aid=1535308">
			<img style="border: 1px solid black; width:100%; max-width:500px;" src="/img/banners/banner-booking-fp.png" />
		</a>
	</div>
	
	@else
	
	<a href="https://www.booking.com/index.html?aid=1535308" target="_blank" >
	<div class="text-center" style="padding: 25px 10px 0 10px;">
		<div style="margin:auto; border: solid 1px #0f367c; line-height:75px; height:75px; width:100%; max-width:500px; 
			xbackground-size:100%; xbackground-repeat: no-repeat; 
			background-image:url('/img/banners/banner-booking-fp{{(false) ? 11 : $bannerIndex}}.png');">
			<div style="text-align: right;">
				<a style="margin: 5px 5px 0 0; vertical-align: top; background-color:#0f367c; color:white;" class="btn btn-info" 
					href="https://www.booking.com/index.html?aid=1535308" target="_blank" role="button">
					<div style="font-size:11px">@LANG('ads.Explore the world with')</div>
					<div style="font-size:18px">Booking<span style="color:#449edd">@LANG('ads..com')</span></div>
				</a> 
			</div>
		</div>
	</div>	
	</a>
	
	@endif
	
	<div class="text-center main-font">
		<h1 style="margin:0;padding: 25px 0 30px 0">
			@if (isset($section->title))
				{{$section->title}}
			@else
				Photo Gallery
			@endif
		</h1>
	</div>
	
@endif

	<!------------------------------------------------------------------------------------------------------------->
	<!-- Content -------------------------------------------------------------------------------------------------->
	<!------------------------------------------------------------------------------------------------------------->
	
	@if (\App\Tools::isSuperAdmin())
	<div id="debug" style="margin-left: 5px; color: black;"></div>
	@endif
	
	<div id="content" style='margin:0; padding: 0 0 {{$showFullGallery ? '5px' : '30px'}} 0; min-height: 200px; text-align: center;'>
		@foreach($gallery as $record)
			<div id="box{{$loop->index}}" class='frontpage-box' style="" >
			
				<!-- BACKGROUND PHOTO LINK -->
				<a href="{{route('gallery.permalink', [$record->permalink])}}" class="frontpage-box-link" style="width: 200px; 
					height: 150px; background-size: 100%; background-repeat: no-repeat; 
					background-image: url('{{$record->photo_path}}/{{$record->photo}}')" >
				</a>

				<div style='white-space: nowrap; overflow: hidden;' class='frontpage-box-text'>
					{{$record->title}}
				</div>
			</div>													
		@endforeach			
	</div>

	<div class='text-center' style="margin: 15px 0;">
		<a href="/galleries">
			<button style="margin-bottom:10px;" type="button" class="btn btn-info">@lang('ui.Show All Galleries')</button>
		</a>
	</div>
		
</div><!-- container -->
</div><!-- content -->

@endif


<!--------------------------------------------------------------------------------------->
<!-- SECTION: GYG Widget -->
<!--------------------------------------------------------------------------------------->

@if ($geo->isLocalhost())

	<!-- skip these for localhost in case we're working with no internet connection -->
	<div class="text-center" style="margin-top:40px;" >
		<h3>GYG Widget goes here in production</h3>
	</div>
	
	<!-- GYG LINK -->
	<div class="text-center" style="margin:20px; border: 1px black solid; background-color:#054589;">
		<a target="_blank" href="https://www.getyourguide.com/?partner_id=RTJHCDQ"><img width="300" src="/img/banners/gyg-block.png" /></a>
	</div>		
	
	
@else

	<div style="margin-top:20px;" >
		<div data-gyg-href="https://widget.getyourguide.com/default/activites.frame" 
		data-gyg-locale-code="{{$geo->language()}}" 
		data-gyg-widget="activities" 
		data-gyg-number-of-items="3" 
		data-gyg-currency="{{$geo->currency()}}" 
		data-gyg-partner-id="RTJHCDQ" 
		data-gyg-q="{{$geo->gygLocation()}}">
		</div>
	</div>

	<script async defer src="https://widget.getyourguide.com/v2/widget.js"></script>

@endif

@if ($geo->isValid()))
<div class="text-center" style="margin:20px;">
	<a target="_blank" href="https://www.getyourguide.com/s/?q={{$geo->gygLocation()}}&partner_id=RTJHCDQ" role="button" class="btn btn-info">
		@lang('content.Show More Tours For') {{$geo->gygLocation()}}
	</a>
</div>
@else
<div class="text-center" style="margin:20px;">
	<a target="_blank" href="https://www.getyourguide.com/s/?partner_id=RTJHCDQ" role="button" class="btn btn-info">
		@lang('content.Show More Tours')
	</a>
</div>
@endif

<!--------------------------------------------------------------------------------------->
<!-- SECTION: Articles -->
<!--------------------------------------------------------------------------------------->
@if (isset($articles) && count($articles) > 0)
@if (($section = getSection(SECTION_ARTICLES, $sections)) != null)
<section class="{{$colors[$sectionCount++]}}">
	<div class="container main-font" style="max-width:95%;">	
		<div class="sectionHeader text-center">			
						
			<h1 style="margin-bottom: 30px;" class="">
				{{$section->title}}
			@if (Auth::check())
				<a style="color: white;" href="/entries/add"><span style="font-size:.6em;" class="glyphicon glyphicon-plus"></span></a>
			@endif			
			</h1>

			<div class="row clearfix text-left" >
				
				<table style="width:100%;">
				<tbody>
				@foreach($articles as $record)
					<!-- tr style="width:100%; vertical-align:middle; border: solid 1px rgba(0, 0, 0, 0.08);" -->
					<tr style="width:100%; vertical-align:middle; 
-webkit-box-shadow: 0px 1px 4px 0px rgba(0,0,0,0.1);
-moz-box-shadow: 0px 1px 4px 0px rgba(0,0,0,0.1);
box-shadow: 0px 1px 4px 0px rgba(0,0,0,0.1);
">
						<td style="margin-bottom:10px; width:100px;" >
							<a href="/entries/{{$record->permalink}}">
								@component('entries.show-main-photo', ['record' => $record, 'class' => 'index-article'])@endcomponent
							</a>							
						</td>
						
						<td style="color:white; padding: 0 10px;">
							<div style="font-size:10px;">
			
								<div style="font-size:{{strlen($record->title) > 50 ? '1' : '1.2'}}em; font-weight:bold; margin-bottom: 5px;">
									<a style="color:white;" href="/entries/{{$record->permalink}}">{{$record->title}}</a>
								</div>
								
								@if (isset($record->location))
									@if ($record->location_type != LOCATION_TYPE_COUNTRY)
										<div style="font-size:1.1em; ">{{$record->location}}, {{$record->location_parent}}</div>
									@else
										<div style="font-size:1.1em;">{{$record->location}}</div>
									@endif
								@endif
								
								@if (isset($record->display_date))
									<div style="margin-top:5px;">{{App\Tools::translateDate($record->display_date)}}</div>
								@endif	
								
								<div>{{$record->view_count}} @LANG('ui.views')</div>
							</div>
						</td>
					</tr>
					<tr><td>&nbsp;</td><td></td></tr>
				@endforeach
				</tbody>
				</table>
					
			</div><!-- row -->	

				<a href="/articles"><button style="margin-bottom:10px;" type="button" class="btn btn-info">@lang('ui.Show All Articles')
					@if (false)
					&nbsp;<span class="badge badge-light">{{$tourCount}}</span>
					@endif
				</button></a>
			
		</div><!-- text-center -->
	</div><!-- container -->
</section>
@endif
@endif


<!--------------------------------------------------------------------------------------->
<!-- SECTION: Blogs -->
<!--------------------------------------------------------------------------------------->
@if (($section = getSection(SECTION_BLOGS, $sections)) != null && isset($posts))
<section class="{{$colors[$sectionCount++]}}">
	<div class="container main-font" style="max-width:1440px;">	
	
		<!-- section header text -->
		<div class="sectionHeader text-center">
			<h1 style="margin-bottom: 30px;" class="">{{$section->title}}</h1>
		</div>

		<div class="row" style="margin-bottom:10px;">
				
			@foreach($posts as $record)
			
			@if (true)
			<!---------------------------------------------------------->
			<!-- This is the new style, with the text on the photo    -->
			<!---------------------------------------------------------->
			<div style="max-width: 400px; padding:10px;" class="col-sm-4"><!-- outer div needed for the columns and the padding, otherwise they won't center -->
				<div class="drop-box" style="height:215px; " ><!-- inner col div -->
					<!-- blog photo -->
						<div class="index-blog-post text-center" style="padding:15px; background-image: url('{{$record->photo_path}}/{{$record->photo}}'); ">
								<p><a href="/blogs/show/{{$record->blog_id}}" class="blog-post-text">{{$record->blog_title}}</a></p>	
								<a class="blog-post-text" style="font-size:1.4em;" href="/entries/{{$record->permalink}}">{{ $record->title }}</a>
								<p class="blog-post-text">{{$record->display_date}}</p>
								<!-- p class="blog-post-text">url('{{$record->photo_path}}/{{$record->photo}}')</p -->
								<!-- p>{{$record->location . ', ' . $record->location_parent}}</p -->
						</div>
				</div><!-- inner col div -->
			</div><!-- outer col div -->
			@else
			<!---------------------------------------------------------->
			<!-- This is the old style, with the text below the photo -->
			<!---------------------------------------------------------->
			<div style="max-width: 400px; padding:10px;" class="col-sm-4"><!-- outer div needed for the columns and the padding, otherwise they won't center -->
				<div class="drop-box" style="min-height:450px; color: black; background-color: white; " ><!-- inner col div -->
				
					<!-- blog photo -->
					<a href="/entries/{{$record->permalink}}">
						@component('entries.show-main-photo', ['record' => $record, 'class' => 'index-blog-post'])@endcomponent
					</a>							
							
					<!-- blog text -->
					<div class="" style="padding:10px;">
						<p><a href="/blogs/show/{{$record->blog_id}}" style="color:green; text-decoration:none;">{{$record->blog_title}}</a></p>
						
						<a style="font-family: 'Volkhov', serif; color: black; font-size:1.4em; font-weight:bold; text-decoration: none; " href="/entries/{{$record->permalink}}">{{ $record->title }}</a>
						
						<p style="color: gray; font-size:.9em;">{{$record->display_date}}</p>
					</div>
					
				</div><!-- inner col div -->
			</div><!-- outer col div -->			
			@endif
			
			@endforeach		

			<div class="text-center">
				<a href="/blogs/index/" style=""><button style="margin-top:20px;" type="button" class="btn btn-info">@lang('ui.Show All Blogs')&nbsp;<span class="badge badge-light">{{$blogCount}}</span></button></a>			
			</div>

		</div><!-- row -->									
					
	</div><!-- container -->
</section>
@endif

<!--------------------------------------------------------------------------------------->
<!-- SECTION: Contact -->
<!--------------------------------------------------------------------------------------->
<?php if (false) : ?>

<section id="contact" class="sectionWhite">
	<div class="container" style="max-width:1400px;">

		<div class="sectionHeader text-center">	
			<div class="sectionImage sectionImageBlue"><span class="sectionImageBlue glyphicon glyphicon-envelope"></span></div>
			<h1 class="sectionImageBlue">Contact Us</h1>
		</div>
		
		<div class="clearfix marginTop40">
			<!-- ?php echo $this->element('form-contact'); ? -->
		</div>
	
	</div>
</section>

<?php endif; ?>

<!--------------------------------------------------------------------------------------->
<!-- SECTION: Current Location -->
<!--------------------------------------------------------------------------------------->
		
@if (($section = getSection(SECTION_CURRENT_LOCATION, $sections)) != null)
<section class="{{$colors[$sectionCount++]}}">
<div class="container">	

	<div class="sectionHeader text-center main-font">	
	
		<div class="" style="font-size: 4em;"><span class="glyphicon glyphicon-globe"></span></div>
		
		<!-- Current Location: Location -->
		@if (isset($section->description) && $section->description != '')
			<h1>{{$section->title}}</h1>
		@else
			<h1>@LANG('content.Current Location'): @LANG('geo.' . $currentLocation)</h1>
		@endif
				
		<!-- Main Photo or Map Location -->
		@if (isset($site->current_location_map_link))
			<iframe src="{{$site->current_location_map_link}}" width="90%" height="500" frameborder="0" style="border:0" allowfullscreen></iframe>
		@elseif (false && isset($section->photo) && strpos($section->photo, PHOTOS_PLACEHOLDER_PREFIX) === false)
			<img src="{{$section->photo_path}}/{{$section->photo}}" title="{{$section->photo_title}}" width="100%" style="max-width:700px" />
		@else
			<img src="{{$currentLocationPhoto}}" title="" width="100%" style="max-width:700px" />
		@endif

		<!-- Previous Locations -->
		@if (isset($section->description_short))
			<h3>{{$section->description_short}}</h3>
		@else
			<h3>@LANG('content.Previous Locations:')</h3>
		@endif
			
		@if (isset($section->description) && $section->description != '')						
			<p style="font-size:1.2em;">{!!nl2br($section->description)!!}</p>
		@else
			<p style="font-size:1.2em;">{!!$latestLocations!!}</p>
			<div class="text-center">
				<a href="/entries/recent-locations" style=""><button style="margin-top:20px;" type="button" class="btn btn-info">@lang('ui.Show All')</button></a>			
			</div>

		@endif
		
		@if (false)
		<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d172138.65427095353!2d-122.48214666413614!3d47.61317464018482!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x5490102c93e83355%3A0x102565466944d59a!2sSeattle%2C+WA!5e0!3m2!1sen!2sus!4v1523908332154" width="90%" height="500" frameborder="0" style="border:0" allowfullscreen></iframe>		
		@endif
		
	</div>
	
</div>
</section>
@endif


<!--------------------------------------------------------------------------------------->
<!-- SECTION: Tours, Hikes, Things To Do -->
<!--------------------------------------------------------------------------------------->

@if (($section = getSection(SECTION_TOURS, $sections)) != null)

<section id="" class="{{$colors[$sectionCount++]}}" style="padding-bottom: 50px;" >
	<div class="container">	
		<div class="text-center">			
			
			<!-------------------- Section header image --------->
			<div style="padding-top:40px;" class="sectionHeader hidden-xs">	
				<h1 style="" class="main-font sectionImageBlue">{{$section->title}}</h1>
			</div>		
			<div style="padding-top:40px;" class="sectionHeader hidden-xl hidden-lg hidden-md hidden-sm">	
				<h3 style="margin:0; padding:0;" class="main-font sectionImageBlue">{{$section->title}}</h3>
			</div>		

			<!---------------------------------------------------->
			<!-- Locations -->
			<!---------------------------------------------------->
			@if (isset($locations) && $tour_count > 0)
			<div style="margin:20px; 0" class="text-center">
				<a href="/tours/index/"><button style="margin-bottom:10px;" type="button" class="btn btn-info">Show All&nbsp;<span class="badge badge-light">{{$tourCount}}</span></button></a>
				@foreach($locations as $location)
					@if ($location->count > 0)
						<a href="/tours/location/{{$location->id}}">
							<button style="margin-bottom:10px;" type="button" class="btn btn-success">{{$location->name}}&nbsp;
								<span class="badge badge-light">{{$location->count}}</span>
							</button>
						</a>
					@endif
				@endforeach
			</div>			
			@endif
						
			<!---------------------------------------------------->
			<!-- Tours, Hikes, Things To Do -->
			<!---------------------------------------------------->
			<div class="clearfix">
						
				<div id="tourParentDiv" class="row">

					@if ($tour_count > 0)
					<?php $count = 0; ?>
					@foreach($tours as $entry)
						<div style="display:{{$count++ < 6 ? 'default' : 'none'}};" class="col-md-4 col-sm-6">
						
							<a href="{{ route('tour.permalink', [$entry->permalink]) }}">
								<div style="min-height:220px; background-color: #4993FD; background-size: cover; background-position: center; background-image: url('{{$entry->photo_path}}/{{$entry->photo}}'); "></div>
							</a>
							
							<!-- tour title -->
							<div class="trim-text" style="color: white; font-size:1.2em; font-weight:bold; padding:5px; margin-bottom:20px; background-color: #3F98FD;">
								<a style="font-family: Raleway; color: white; font-size:1em; text-decoration: none; " href="{{ route('tour.permalink', [$entry->permalink]) }}">{{ $entry->title }}</a>
							</div>
							
						</div>
					
					@endforeach
					@else
						@guest
						<div class="" style="color: white; font-size:1.2em; font-weight:bold;">
							<a style="font-family: Raleway; color: gray; font-size:1em; text-decoration: none; " href="/login">Log-in to add content for this section</a>
						</div>
						@else
						<div class="" style="color: white; font-size:1.2em; font-weight:bold;">
							<i><a style="font-family: Raleway; font-size:1.1em; text-decoration: none; " href="/tours/add">Click here to add content for this section</a></i>
						</div>
						@endguest
					@endif
					
				</div><!-- row -->	
				
				<a href="/tours/index/"><button style="margin-bottom:10px;" type="button" class="btn btn-info">@lang('ui.Show All Tours')&nbsp;<span class="badge badge-light">{{$tourCount}}</span></button></a>

			</div>
						
		</div><!-- text-center -->
	</div><!-- container -->
</section>	

@endif

<!--------------------------------------------------------------------------------------->
<!-- SECTION: Affiliates -->
<!--------------------------------------------------------------------------------------->

@if (($section = getSection(SECTION_AFFILIATES, $sections)) != null)
<section class="{{$colors[$sectionCount++]}}">
<div class="container">	
	<div style="margin-top: 0px;" class="sectionHeader text-center main-font">	
	
		<!-- div class="" style="font-size: 4em; margin-bottom:20px;"><span class="glyphicon glyphicon-bed"></span></div -->
		<h1>{{$section->title}}</h1>

		<!-- BOOKING AFFILIATE -->
		<div style="float:left; margin:20px;">

			<ins class="bookingaff" 
				data-aid="1535322" 
				data-target_aid="1535306" 
				data-prod="banner" 
				data-width="300" 
				data-height="250" 
				data-lang="{{$geo->isValid() ? $geo->language() : 'es-US'}}">
				<!-- Anything inside will go away once widget is loaded. -->
				<a href="//www.booking.com?aid=1535306">Booking.com</a>
			</ins>
			
			@if (!$geo->isLocalhost())
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

		<!-- GYG LINK -->
		<div style="float:left; margin:20px; border: 1px black solid; background-color:#054589;">
			<a target="_blank" href="https://www.getyourguide.com/?partner_id=RTJHCDQ"><img width="300" src="/img/banners/gyg-block.png" /></a>
		</div>		
	</div>
</div>
</section>
@endif

<!--------------------------------------------------------------------------------------->
<!-- SECTION: Affiliates -->
<!--------------------------------------------------------------------------------------->

@if (($section = getSection(SECTION_COMMENTS, $sections)) != null)
<section class="{{$colors[$sectionCount++]}}">

<div class="container text-center" style="max-width: 500px;">	
	
	<div style="margin-top: 0px; font-size: 1.5em;" class="sectionHeader main-font">	
		<h1>@LANG('content.Leave a Comment')</h1>
	</div>
	
	<div class="text-left" style="font-size: 1.2em;">
		<form method="POST" action="/comments/create">
				
			<input type="hidden" name="parent_id" value="0" />	
					
			<label for="name" class="control-label">@LANG('ui.Name'):</label>
			<input type="text" name="name" class="form-control" maxlength="50" />

			<label for="comment" class="control-label" style="margin-top:20px;">@LANG('content.Comment'):</label>
			<textarea name="comment" class="form-control" maxlength="500"></textarea>
			
			<div class="submit-button text-center" style="margin: 20px 0;">
				<button type="submit" name="update" class="btn btn-primary">@LANG('ui.Submit')</button>
			</div>
						
			{{ csrf_field() }}

		</form>
	</div>
	
</div>

<div class="container text-center" style="max-width:800px;">	
	<div class="text-center" style="margin-top: 50px;">
		<table style="width:100%">
		@foreach($comments as $record)
		
		<tr class="drop-box" style="vertical-align:middle; box-shadow: 2px 2px 2px 2px rgba(0, 0, 0, 0.2), 0 1px 1px 0 rgba(0, 0, 0, 0.19);">
			<td style="min-width:100px; font-size: 1.5em; padding:10px; color: white; background-color: #327ab6; margin-bottom:10px;" >
				<div style="margin:0; padding:0; line-height:100%;">
					<div style="font-family:impact; font-size:1.7em; margin:10px 0 10px 0;">@LANG('dt.' . strtoupper(date_format($record->created_at, "M")))</div>
					<div style="font-family:impact; font-size:1.5em; margin-bottom:10px;">{{date_format($record->created_at, "j")}}</div>
					<div>{{date_format($record->created_at, "Y")}}</div>
				</div>
			</td>
			<td style="background-color:#f1f1f1; padding: 0 10px; text-align:left; padding:15px;">
				<table>
				<tbody>
					<tr><td style="color: #327ab6; padding-bottom:10px; font-size:1.3em; font-weight:bold;">{{$record->name}}</td></tr>
					<tr><td style="font-size: 1em; "><a href="/comments">{{$record->comment}}</a></td></tr>
				</tbody>
				</table>
			</td>
		</tr>
		
		<tr><td>&nbsp;</td><td></td></tr>
		
		@endforeach
		</table>
		
		<div class='text-center' style="margin-bottom: 10px;">
			<a href="/comments">
				<button style="margin-bottom:10px;" type="button" class="btn btn-info">@lang('content.Show All Comments')</button>
			</a>
		</div>
		
	</div>
</div>

</section>
@endif

@endsection

