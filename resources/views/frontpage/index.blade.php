@extends('layouts.frontpage')

@section('content')

@php

//
// THIS IS THE FRONT PAGE
//

if ($showFullGallery)
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
}
else
{
	$colors = [
		'sectionGray',
		'sectionGray',
		'sectionGreen',
		'sectionGray',
		'sectionOrange',
		'powerBlue',
		'sectionGray',
		'sectionWhite',
		'sectionGreen',
	];
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

@if (\App\Tools::getSection(SECTION_SLIDERS, $sections) != null)
	
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
@if (($section = \App\Tools::getSection(SECTION_WELCOME, $sections)) != null)
<section id="" class="{{\App\Tools::getSafeArray($colors, $sectionCount++, 'sectionRed')}}" style="padding: 30px 0 40px 0; xposition: relative; xtop: -30px; ">
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

@if (($section = \App\Tools::getSection(SECTION_GALLERY, $sections)) != null)

<div id="load-loop" class="" style="width:100%; text-align: center; padding-top:100px; display:none;">
	<img src="/img/theme1/load-loop.gif" />
</div>

<div id="content" style="display:default;">
<div id="container" class="{{\App\Tools::getSafeArray($colors, $sectionCount++, 'sectionGray')}}" style="min-height:200px;" >

@if (!$showFullGallery)

	@if (false)
	
	<div class="text-center" style="padding: 25px 10px 0 10px;">
		<a target="_blank" href="https://www.booking.com/index.html?aid=1535308">
			<img style="border: 1px solid black; width:100%; max-width:500px;" src="/img/banners/banner-booking-fp.png" />
		</a>
	</div>
	
	@else
	
	<a href="https://www.booking.com/index.html?aid=1535308" target="_blank" >
	<div class="text-center" style="padding-top: 5px;">
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
	
	@if (false)
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
	
@endif

	<!------------------------------------------------------------------------------------------------------------->
	<!-- Content -------------------------------------------------------------------------------------------------->
	<!------------------------------------------------------------------------------------------------------------->
	
	@if (\App\Tools::isSuperAdmin())
	<div id="debug" style="margin-left: 5px; color: black;"></div>
	@endif
	
	<div id="content" style='margin:0; padding: 0 0 {{$showFullGallery ? '5px' : '30px'}} 0; min-height: 200px; text-align: center;'>
		@foreach($gallery as $record)
			<div id="box{{$loop->index}}" class='frontpage-box {{$colorBox}}' style="" >
			
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

	<div class='text-center' xstyle="margin: 0;">
		<a href="/galleries">
			<button style="margin-bottom:10px;" type="button" class="btn btn-info">@lang('ui.Show All Galleries')</button>
		</a>
	</div>
		
</div><!-- container -->
</div><!-- content -->

@endif

<!--------------------------------------------------------------------------------------->
<!-- SECTION: Logo without Welcome text -->
<!--------------------------------------------------------------------------------------->

@if (true || ($section = \App\Tools::getSection(SECTION_LOGO, $sections)) != null)
	@php
		$p = '/img/theme1/logo-' . $domainName . '.png'; 
		$fp = base_path() . '/public' . $p; 
	@endphp
	@if (file_exists($fp))
		<div class="sectionHeader text-center">	
		<div style="margin:15px 0">
			<img style="width:95%; max-width:200px;" src="{{$p}}" />
		</div>
		</div>
	@endif
@endif

<!--------------------------------------------------------------------------------------->
<!-- SECTION: GYG Widget -->
<!--------------------------------------------------------------------------------------->

@if (($section = \App\Tools::getSection(SECTION_AFFILIATES, $sections)) != null)

@if ($geo->isLocalhost())

	<h1 style="margin-bottom: 30px;" class="">Get Your Guide</h1>

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

@endif

<!--------------------------------------------------------------------------------------->
<!-- SECTION: Articles -->
<!--------------------------------------------------------------------------------------->
@if (isset($articles) && count($articles) > 0)
@if (($section = \App\Tools::getSection(SECTION_ARTICLES, $sections)) != null)
<section class="{{\App\Tools::getSafeArray($colors, $sectionCount++, 'sectionGray')}}">
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
@if (($section = \App\Tools::getSection(SECTION_BLOGS, $sections)) != null && isset($posts))
<section class="{{\App\Tools::getSafeArray($colors, $sectionCount++, 'sectionRed')}}">
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
		
@if (($section = \App\Tools::getSection(SECTION_CURRENT_LOCATION, $sections)) != null)
<section class="{{\App\Tools::getSafeArray($colors, $sectionCount++, 'sectionRed')}}">
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

@if (($section = \App\Tools::getSection(SECTION_TOURS, $sections)) != null)
<section id="" class="{{\App\Tools::getSafeArray($colors, $sectionCount++, 'colorWhite')}}" style="padding-bottom: 50px;" >
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

@if (($section = \App\Tools::getSection(SECTION_AFFILIATES, $sections)) != null)
<section class="{{\App\Tools::getSafeArray($colors, $sectionCount++, 'colorWhite')}}">
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
<!-- SECTION: Instagram -->
<!--------------------------------------------------------------------------------------->
<center>
  <div style="padding-top: 10px; background-color: #3e95ef;">
	<blockquote class="instagram-media" data-instgrm-permalink="https://www.instagram.com/scottmundo.online/?utm_source=ig_embed&amp;utm_campaign=loading" data-instgrm-version="14" style=" background:#FFF; border:0; border-radius:3px; box-shadow:0 0 1px 0 rgba(0,0,0,0.5),0 1px 10px 0 rgba(0,0,0,0.15); margin: 1px; max-width:540px; min-width:326px; padding:0; width:99.375%; width:-webkit-calc(100% - 2px); width:calc(100% - 2px);">
	<div style="padding:16px;"> <a href="https://www.instagram.com/scottmundo.online/?utm_source=ig_embed&amp;utm_campaign=loading" style=" background:#FFFFFF; line-height:0; padding:0 0; text-align:center; text-decoration:none; width:100%;" target="_blank"> <div style=" display: flex; flex-direction: row; align-items: center;"> 
	<div style="background-color: #F4F4F4; border-radius: 50%; flex-grow: 0; height: 40px; margin-right: 14px; width: 40px;"></div> <div style="display: flex; flex-direction: column; flex-grow: 1; justify-content: center;"> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; margin-bottom: 6px; width: 100px;"></div> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; width: 60px;"></div></div></div><div style="padding: 19% 0;"></div> 
	<div style="display:block; height:50px; margin:0 auto 12px; width:50px;"><svg width="50px" height="50px" viewBox="0 0 60 60" version="1.1" xmlns="https://www.w3.org/2000/svg" xmlns:xlink="https://www.w3.org/1999/xlink"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g transform="translate(-511.000000, -20.000000)" fill="#000000"><g><path d="M556.869,30.41 C554.814,30.41 553.148,32.076 553.148,34.131 C553.148,36.186 554.814,37.852 556.869,37.852 C558.924,37.852 560.59,36.186 560.59,34.131 C560.59,32.076 558.924,30.41 556.869,30.41 M541,60.657 C535.114,60.657 530.342,55.887 530.342,50 C530.342,44.114 535.114,39.342 541,39.342 C546.887,39.342 551.658,44.114 551.658,50 C551.658,55.887 546.887,60.657 541,60.657 M541,33.886 C532.1,33.886 524.886,41.1 524.886,50 C524.886,58.899 532.1,66.113 541,66.113 C549.9,66.113 557.115,58.899 557.115,50 C557.115,41.1 549.9,33.886 541,33.886 M565.378,62.101 C565.244,65.022 564.756,66.606 564.346,67.663 C563.803,69.06 563.154,70.057 562.106,71.106 C561.058,72.155 560.06,72.803 558.662,73.347 C557.607,73.757 556.021,74.244 553.102,74.378 C549.944,74.521 548.997,74.552 541,74.552 C533.003,74.552 532.056,74.521 528.898,74.378 C525.979,74.244 524.393,73.757 523.338,73.347 C521.94,72.803 520.942,72.155 519.894,71.106 C518.846,70.057 518.197,69.06 517.654,67.663 C517.244,66.606 516.755,65.022 516.623,62.101 C516.479,58.943 516.448,57.996 516.448,50 C516.448,42.003 516.479,41.056 516.623,37.899 C516.755,34.978 517.244,33.391 517.654,32.338 C518.197,30.938 518.846,29.942 519.894,28.894 C520.942,27.846 521.94,27.196 523.338,26.654 C524.393,26.244 525.979,25.756 528.898,25.623 C532.057,25.479 533.004,25.448 541,25.448 C548.997,25.448 549.943,25.479 553.102,25.623 C556.021,25.756 557.607,26.244 558.662,26.654 C560.06,27.196 561.058,27.846 562.106,28.894 C563.154,29.942 563.803,30.938 564.346,32.338 C564.756,33.391 565.244,34.978 565.378,37.899 C565.522,41.056 565.552,42.003 565.552,50 C565.552,57.996 565.522,58.943 565.378,62.101 M570.82,37.631 C570.674,34.438 570.167,32.258 569.425,30.349 C568.659,28.377 567.633,26.702 565.965,25.035 C564.297,23.368 562.623,22.342 560.652,21.575 C558.743,20.834 556.562,20.326 553.369,20.18 C550.169,20.033 549.148,20 541,20 C532.853,20 531.831,20.033 528.631,20.18 C525.438,20.326 523.257,20.834 521.349,21.575 C519.376,22.342 517.703,23.368 516.035,25.035 C514.368,26.702 513.342,28.377 512.574,30.349 C511.834,32.258 511.326,34.438 511.181,37.631 C511.035,40.831 511,41.851 511,50 C511,58.147 511.035,59.17 511.181,62.369 C511.326,65.562 511.834,67.743 512.574,69.651 C513.342,71.625 514.368,73.296 516.035,74.965 C517.703,76.634 519.376,77.658 521.349,78.425 C523.257,79.167 525.438,79.673 528.631,79.82 C531.831,79.965 532.853,80.001 541,80.001 C549.148,80.001 550.169,79.965 553.369,79.82 C556.562,79.673 558.743,79.167 560.652,78.425 C562.623,77.658 564.297,76.634 565.965,74.965 C567.633,73.296 568.659,71.625 569.425,69.651 C570.167,67.743 570.674,65.562 570.82,62.369 C570.966,59.17 571,58.147 571,50 C571,41.851 570.966,40.831 570.82,37.631"></path></g></g></g></svg></div>
	<div style="padding-top: 8px;"> 
	<div style=" color:#3897f0; font-family:Arial,sans-serif; font-size:14px; font-style:normal; font-weight:550; line-height:18px;">View this profile on Instagram</div>
	</div>
	<div style="padding: 12.5% 0;"></div> 
	<div style="display: flex; flex-direction: row; margin-bottom: 14px; align-items: center;"><div> 
	<div style="background-color: #F4F4F4; border-radius: 50%; height: 12.5px; width: 12.5px; transform: translateX(0px) translateY(7px);"></div> 
	<div style="background-color: #F4F4F4; height: 12.5px; transform: rotate(-45deg) translateX(3px) translateY(1px); width: 12.5px; flex-grow: 0; margin-right: 14px; margin-left: 2px;"></div> 
	<div style="background-color: #F4F4F4; border-radius: 50%; height: 12.5px; width: 12.5px; transform: translateX(9px) translateY(-18px);"></div>
	</div>
	<div style="margin-left: 8px;"> <div style=" background-color: #F4F4F4; border-radius: 50%; flex-grow: 0; height: 20px; width: 20px;"></div> 
	<div style=" width: 0; height: 0; border-top: 2px solid transparent; border-left: 6px solid #f4f4f4; border-bottom: 2px solid transparent; transform: translateX(16px) translateY(-4px) rotate(30deg)"></div>
	</div>
	<div style="margin-left: auto;"> 
	<div style=" width: 0px; border-top: 8px solid #F4F4F4; border-right: 8px solid transparent; transform: translateY(16px);"></div> 
	<div style=" background-color: #F4F4F4; flex-grow: 0; height: 12px; width: 16px; transform: translateY(-4px);"></div> 
	<div style=" width: 0; height: 0; border-top: 8px solid #F4F4F4; border-left: 8px solid transparent; transform: translateY(-4px) translateX(8px);"></div>
	</div>
	</div> 
	<div style="display: flex; flex-direction: column; flex-grow: 1; justify-content: center; margin-bottom: 24px;"> 
	<div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; margin-bottom: 6px; width: 224px;"></div> 
	<div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; width: 144px;"></div>
	</div></a>
	<p style=" color:#c9c8cd; font-family:Arial,sans-serif; font-size:14px; line-height:17px; margin-bottom:0; margin-top:8px; overflow:hidden; padding:8px 0 7px; text-align:center; text-overflow:ellipsis; white-space:nowrap;"><a href="https://www.instagram.com/scottmundo.online/?utm_source=ig_embed&amp;utm_campaign=loading" style=" color:#c9c8cd; font-family:Arial,sans-serif; font-size:14px; font-style:normal; font-weight:normal; line-height:17px;" target="_blank">Scott</a> (@<a href="https://www.instagram.com/scottmundo.online/?utm_source=ig_embed&amp;utm_campaign=loading" style=" color:#c9c8cd; font-family:Arial,sans-serif; font-size:14px; font-style:normal; font-weight:normal; line-height:17px;" target="_blank">scottmundo.online</a>) • Instagram photos and videos</p>
	</div>
	</blockquote> 
  </div>
</center>
<script async src="//www.instagram.com/embed.js"></script>

<!--------------------------------------------------------------------------------------->
<!-- SECTION: Comments -->
<!--------------------------------------------------------------------------------------->

@if (($section = \App\Tools::getSection(SECTION_COMMENTS, $sections)) != null)
<section class="{{\App\Tools::getSafeArray($colors, $sectionCount++, 'colorWhite')}}">

@component('comments.comp-add-form', ['marginTop' => 0, 'backgroundColor' => 'powerBlue'])@endcomponent

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

