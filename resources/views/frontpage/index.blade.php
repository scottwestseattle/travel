
@extends('layouts.frontpage')

@section('content')

<?php

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

?>
<!--------------------------------------------------------------------------------------->
<!-- Title Logo Bar -->
<!--------------------------------------------------------------------------------------->

	<!--------------------------------------------------------------------------------------->
	<!-- Sliders -->
	<!--------------------------------------------------------------------------------------->

@if (getSection(SECTION_SLIDERS, $sections) != null)
	
@if ($sliders->count() > 0)
<?php $sectionCount++; ?>
<div style="width:100%; background-color: white; background-position: center; background-repeat: no-repeat; background-image:url('/img/theme1/load-loop.gif'); " >
@else
<div style="width:100%; background-color: gray; background-position: cover; background-image:url('/img/theme1/bg-pattern.png'); " >
@endif
	<section>
		<div class="slider-center">
			<div id="slider" style="min-height:800px; background-repeat: no-repeat; position: relative;">
			
				<!------------------------------------------------------->
				<!-- these is the slider spacer used to size the slider-->
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
		@if (strtolower($domainName) == 'hikebikeboat.com')
			<img style="max-width:200px;" src="/img/theme1/logo-{{$domainName}}.png" />
		@else
			<img style="width:95%; max-width:200px;" src="/img/theme1/logo-{{$domainName}}.png" />
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

<div id="container" class="{{$colors[$sectionCount++]}}" style="min-height:200px;" >

@if (!$showFullGallery)
	<div class="text-center main-font">
		<h1 style="margin:0;padding: 50px 0 30px 0">
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
	
	<div id="content" style='margin:0; padding: 0 0 {{$showFullGallery ? '5px' : '30px'}} 0; min-height: 200px; text-align: center;'>
		@foreach($gallery as $record)
			<div class='frontpage-box' style="" >
				<!-- BACKGROUND PHOTO LINK -->
				<a href="{{route('gallery.permalink', [$record->permalink])}}" class="frontpage-box-link" style="width: 200px; height: 150px; background-size: 100%; background-repeat: no-repeat; background-image: url('{{$record->photo_path}}/{{$record->photo}}')" ></a>

				<div style='white-space: nowrap; overflow: hidden;' class='frontpage-box-text'>
					{{$record->title}}
				</div>
			</div>			
		@endforeach			
	</div>
	
@if (!$showFullGallery)
	<div class='text-center'>
		<a href="/galleries">
			<button style="margin-bottom:10px;" type="button" class="btn btn-info">@lang('ui.Show All Galleries')</button>
		</a>
	</div>
@endif
	
	<span id="debug"></span>
		
</div><!-- container -->

@endif


<!--------------------------------------------------------------------------------------->
<!-- SECTION: Articles -->
<!--------------------------------------------------------------------------------------->
@if (isset($articles) && count($articles) > 0)
@if (($section = getSection(SECTION_ARTICLES, $sections)) != null)
<section class="{{$colors[$sectionCount++]}}">
	<div class="container main-font" style="max-width:1440px;">	
		<div class="sectionHeader text-center">			
						
			<h1 style="margin-bottom: 30px;" class="">{{$section->title}}</h1>

			<div class="row clearfix text-left">
				
				<table>
				<tbody>
				@foreach($articles as $record)
					<tr style="vertical-align:top;">
						<td style="margin-bottom:10px;" >
							<a href="/entries/{{$record->permalink}}">
								@component('entries.show-main-photo', ['record' => $record, 'class' => 'index-article'])@endcomponent
							</a>							
						</td>
						<td style="color:white; padding: 0 10px;">
							<table>
							<tbody>
								<tr><td style="font-size:1.3em;"><a style="color:white;" href="/entries/{{$record->permalink}}">{{$record->title}}</a></td></tr>
								@if (isset($record->display_date))
								<tr><td>{{$record->display_date}}</td></tr>
								@endif
								
								@if (isset($record->location))
									@if ($record->location_type != LOCATION_TYPE_COUNTRY)
										<tr><td>{{$record->location}}, {{$record->location_parent}}</td></tr>
									@else
										<tr><td>{{$record->location}}</td></tr>
									@endif
								@endif
								
							</tbody>
							</table>
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
		
		@if (isset($section->title))
			<h1>{{$section->title}}</h1>
		@else
			<h1>Current Location:</h1>
		@endif

		@if (isset($site->current_location))
			<h1>{{$site->current_location}}</h1>
		@endif
				
		@if (isset($site->current_location_map_link))
			<iframe src="{{$site->current_location_map_link}}" width="90%" height="500" frameborder="0" style="border:0" allowfullscreen></iframe>
		@elseif (isset($section->photo) && strpos($section->photo, PHOTOS_PLACEHOLDER_PREFIX) === false)
			<img src="{{$section->photo_path}}/{{$section->photo}}" title="{{$section->photo_title}}" width="100%" style="max-width:700px" />
		@endif

		@if (isset($section->description))
			@if (isset($section->description_short))
				<h3>{{$section->description_short}}</h3>
			@else
				<h3>@LANG('content.Previous Locations:')</h3>
			@endif
			<p style="font-size:1.2em;">{!!nl2br($section->description)!!}</p>
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
		<!-- h1>Affiliate Partners</h1 -->	
		
		<!-- AMAZON ADS -->
		<div style="float:left; margin:20px;">
			<iframe src="//rcm-na.amazon-adsystem.com/e/cm?o=1&p=12&l=ur1&category=amazonhomepage&f=ifr&linkID=19442e58f18ebdac206c630f92678c97&t=travelwebs024-20&tracking_id=travelwebs024-20" width="300" height="250" scrolling="no" border="0" marginwidth="0" style="border:none;" frameborder="0"></iframe>
		</div>
		
		@if (false)
		<!-- AGODA AFFILIATE -->
		<div style="float:left; margin:20px;">
		
			<div id="adgshp2008177892"></div>
			<script type="text/javascript" src="//cdn0.agoda.net/images/sherpa/js/sherpa_init1_08.min.js"></script><script type="text/javascript">
			var stg = new Object(); stg.crt="9181526501892";stg.version="1.04"; stg.id=stg.name="adgshp2008177892"; stg.width="300px"; stg.height="250px";stg.ReferenceKey="0xsHqxj9SidZIQaBKVV3aA=="; stg.Layout="OblongStatic"; stg.Language="en-us";stg.Cid="1806200"; stg.OverideConf=false; new AgdSherpa(stg,3).initialize();
			</script>
			
		</div>
		@endif
		
		<!-- BOOKING AFFILIATE -->
		<div style="float:left; margin:20px;">

			<ins class="bookingaff" data-aid="1535322" data-target_aid="1535306" data-prod="banner" data-width="300" data-height="250" data-lang="en-US">
				<!-- Anything inside will go away once widget is loaded. -->
				<a href="//www.booking.com?aid=1535306">Booking.com</a>
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
		
		</div>
		
		<!-- GOOGLE ADS -->
		<div style="float:left; margin:20px;">
		
			<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
			<!-- Front Page Ad 300 x 250 -->
			<ins class="adsbygoogle"
				 style="display:inline-block;width:300px;height:250px"
				 data-ad-client="ca-pub-3301644572924270"
				 data-ad-slot="8699059746"></ins>
			<script>
			(adsbygoogle = window.adsbygoogle || []).push({});
			</script>

		</div>

		@if (false)
		<!-- AMAZON BANNER -->		
		<div style="clear:both; display:block;">

			<script type="text/javascript">
			amzn_assoc_placement = "adunit0";
			amzn_assoc_tracking_id = "travelwebs024-20";
			amzn_assoc_ad_mode = "search";
			amzn_assoc_ad_type = "smart";
			amzn_assoc_marketplace = "amazon";
			amzn_assoc_region = "US";
			amzn_assoc_default_search_phrase = "last minute deals";
			amzn_assoc_default_category = "All";
			amzn_assoc_linkid = "d2d59fc32979ade8cd0bea9018e29981";
			amzn_assoc_design = "in_content";
			</script>
			<script src="//z-na.amazon-adsystem.com/widgets/onejs?MarketPlace=US"></script>

		</div>	
		@endif
		
	</div>
</div>
</section>
@endif

<!--------------------------------------------------------------------------------------->
<!-- SECTION: Affiliates -->
<!--------------------------------------------------------------------------------------->

@if (true || ($section = getSection(SECTION_COMMENTS, $sections)) != null)
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
						
			<div class='text-center'>
				<a href="/comments">
					<button style="margin-bottom:10px;" type="button" class="btn btn-info">@lang('content.Show All Comments')</button>
				</a>
			</div>
	
			{{ csrf_field() }}

		</form>
	</div>
</div>
</section>
@endif

@endsection
