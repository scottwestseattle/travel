
@extends('layouts.frontpage')

@section('content')

<?php

function getSection($id, $array)
{
	$section = null;
	
	if (array_key_exists($id, $array))
	{
		$section = $array[$id];
	}
	
	return $section;
}

$colors = [
'sectionWhite',
'powerBlue',
'sectionWhite',
'sectionGreen',
'sectionWhite',
'sectionOrange',
'sectionWhite'
];

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
		<div class="slider-center" xonclick="slider_right()">
			<div id="slider" style="min-height:800px; background-repeat: no-repeat; position: relative;">
				<img id="slider-spacer" src="/img/theme1/spacer.png" width="100%" />
				
				<!------------------------------------------------------->
				<!-- these are the slider mover arrows -->
				<!------------------------------------------------------->
				<div id="slider-arrow-left" style="font-size: 200px; position:absolute; top:0; left:0">
					<span id="slider-control-left" style="opacity:0.0; color: white;" onclick="slider_left()" onmouseover="showSliderControls(true)" onmouseout="showSliderControls(false)">
						<span class="glyphicon glyphicon-chevron-left" style="background-color:black; border-radius:8px;"></span>
					</span>
				</div>
					
				<div id="slider-arrow-right" style="font-size:200px; position:absolute; top:0; right:0;">
					<span id="slider-control-right" style="opacity:0.0; color: white;" onclick="slider_right()" onmouseover="showSliderControls(true)" onmouseout="showSliderControls(false)" >
						<span class="glyphicon glyphicon-chevron-right"  style="background-color:black; border-radius:8px;"></span>
					</span>
				</div>
					
			</div>
			
			<div class="DarkBlue">
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

	// load all the sliders so we can javascript through them
	var sliders = [
		@foreach($sliders as $slider)
			['{{$slider->filename}}', '{{$slider->location}}', '{{$slider->alt_text}}'],
		@endforeach
	];
	var ix = Math.floor(Math.random() * sliders.length);
	var img = sliders[ix][0];
	var loc = sliders[ix][1];
	var alt = sliders[ix][2];
	
	document.getElementById("slider").style.backgroundImage = "url('/img/sliders/" + img + "')";
	document.getElementById("slider").style.minHeight = ''; // the min-height is only set so they initial slider load isn't so jerky, once it's loaded, remove this
	document.getElementById("slider-text").innerHTML = loc;
	document.getElementById("slider-text-xs").innerHTML = loc;
	document.getElementById("slider").title = alt + ', ' + loc;

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
		
		document.getElementById("slider").style.backgroundImage = "url('/img/sliders/" + img + "')";
		document.getElementById("slider-text").innerHTML = loc;
		document.getElementById("slider-text-xs").innerHTML = loc;
		document.getElementById("slider").title = alt + ', ' + loc;
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
		
		<div class="hidden-xl hidden-lg hidden-md hidden-sm">
			<!-- xs only -->
			<h3 style="font-size:1.2em;" class="welcome-text main-font">{{$section->description}} </h3>
		</div>
		<div class="hidden-xs" >
			<!-- all other sizes -->
			<h3 class="welcome-text main-font">{{ $section->description }} </h3>
		</div>
		
		<div style="margin-top:40px;">
			<img style="width:95%; max-width:400px;" src="/img/theme1/logo-main.png" />
		</div>
	
	</div>	

	<!--------------------------------------------------------------------------------------->
	<!-- The "Join Us" button -->
	<!--------------------------------------------------------------------------------------->		
	<div class="row text-center" style="margin-top:40px;">
		<div class="header">
			<a href="/register"><button class="textWhite formControlSpace20 btn btn-submit btn-lg bgGreen"><span class="glyphicon glyphicon-user"></span>&nbsp;Click Here to Join us!</button></a>
		</div>		
	</div>

	<!--------------------------------------------------------------------------------------->
	<!-- The charming Quote -->
	<!--------------------------------------------------------------------------------------->		
	<div class="sectionHeader text-center" style="margin-top:20px;">
		<h3 style="font-size:1.2em;" class="welcome-text main-font"><i>{{$section->description_short}}</i></h3>
	</div>
	
</div>
</section>
@endif

<!--------------------------------------------------------------------------------------->
<!-- SECTION: Tours, Hikes, Things To Do -->
<!--------------------------------------------------------------------------------------->

@if (($section = getSection(SECTION_TOURS, $sections)) != null)

<?php
	$h = 200;
	$w = 300;
	$tours_fullpath = base_path() . PHOTOS_FULL_PATH . 'tours/';
	$tours_webpath = '/img/tours/';
	$link = '/activities/';
?>

<section id="" class="{{$colors[$sectionCount++]}}" style="" >
	<div class="container">	
		<div class="text-center">			
			
			<!-------------------- Section header image --------->
			<div class="sectionHeader hidden-xs">	
				<!-- div><img src="/img/theme1/bootprint.jpg" /></div -->
				<!-- div><img src="/img/round-mountain.png" /></div -->
				<h1 style="" class="main-font sectionImageBlue">{{$section->title}}</h1>
			</div>		
			<div class="sectionHeader hidden-xl hidden-lg hidden-md hidden-sm">	
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
						<?php
							if (isset($entry->photo))
							{
								$photo = $photoPath . $entry->id . '/' . $entry->photo;
								//dd($photo);
							}
							else
							{
								$photo = TOUR_PHOTO_PLACEHOLDER;
							}
						?>
						<div style="display:{{$count++ < 6 ? 'default' : 'none'}};" class="col-md-4 col-sm-6">
						
							@if (isset($entry->location))
								<a href="{{ route('tour.permalocation', [$entry->location, $entry->permalink]) }}">
							@else
								<a href="{{ route('tour.permalink', [$entry->permalink]) }}">
							@endif
									<div style="min-height:220px; background-color: #4993FD; background-size: cover; background-position: center; background-image: url('{{$photo}}'); "></div>
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
				
				<a href="/tours/index/"><button style="margin-bottom:10px;" type="button" class="btn btn-info">Show All Tours&nbsp;<span class="badge badge-light">{{$tourCount}}</span></button></a>

			</div>
						
		</div><!-- text-center -->
	</div><!-- container -->
</section>									
@endif

<!--------------------------------------------------------------------------------------->
<!-- SECTION: Articles -->
<!--------------------------------------------------------------------------------------->
@if (($section = getSection(SECTION_ARTICLES, $sections)) != null)
<section class="{{$colors[$sectionCount++]}}">
	<div class="container" style="max-width:1440px;">	
		<div class="sectionHeader text-center">			
						
			<h1 style="margin-bottom: 30px;" class="">{{$section->title}}</h1>

			<div class="row clearfix text-left">
				
				<table>
				<tbody>
				@foreach($articles as $record)
					<tr style="vertical-align:top;">
						<td style="margin-bottom:10px;" >
							<a href="/entries/{{$record->permalink}}">
								<?php if (!isset($record->photo)) { $record->photo_path = '.'; $record->photo = TOUR_PHOTO_PLACEHOLDER; } ?>
								<div style="min-width:150px; min-height:100px; background-color: white; background-size: cover; background-position: center; background-image: url('{{$record->photo_path}}/{{$record->photo}}'); "></div>
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
								<tr><td>{{$record->location}}, {{$record->location_parent}}</td></tr>
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

				<a href="/articles"><button style="margin-bottom:10px;" type="button" class="btn btn-info">Show All Articles
					@if (false)
					&nbsp;<span class="badge badge-light">{{$tourCount}}</span>
					@endif
				</button></a>
			
		</div><!-- text-center -->
	</div><!-- container -->
</section>
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
						<?php if (!isset($record->photo)) { $record->photo_path = '.'; $record->photo = TOUR_PHOTO_PLACEHOLDER; } ?>
						<div style="min-width:200px; min-height:220px; background-color: white; background-size: cover; background-position: center; background-image: url('{{$record->photo_path}}/{{$record->photo}}'); "></div>
					</a>							
							
					<!-- blog text -->
					<div class="" style="padding:10px;">
						<p><a href="/blogs/show/{{$record->blog_id}}" style="color:green; text-decoration:none;">{{$record->blog_title}}</a></p>
						
						<a style="font-family: 'Volkhov', serif; color: black; font-size:1.4em; font-weight:bold; text-decoration: none; " href="/entries/{{$record->permalink}}">{{ $record->title }}</a>
						
						<p style="color: gray; font-size:.9em;">{{date_format(date_create($record->display_date), "l, F d, Y")}}</p>
					</div>
					
				</div><!-- inner col div -->
			</div><!-- outer col div -->
			@endforeach		

			<div class="text-center">
				<a href="/blogs/index/" style=""><button style="margin-top:20px;" type="button" class="btn btn-info">Show All Blogs&nbsp;<span class="badge badge-light">{{$blogCount}}</span></button></a>			
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
		
		<?php //dd($section); ?>
		
		@if (isset($site->current_location_map_link))
			<iframe src="{{$site->current_location_map_link}}" width="90%" height="500" frameborder="0" style="border:0" allowfullscreen></iframe>
		@elseif (isset($section->photo))
			<img src="{{$section->photo_path}}/{{$section->photo}}" title="{{$section->photo_title}}" width="100%" style="max-width:700px" />
		@endif

		@if (isset($site->previous_location_list))
			@if (isset($site->previous_location_title))
				<h3>{{$site->previous_location_title}}</h3>
			@else
				<h3>Previous Locations:</h3>
			@endif
			<p style="font-size:1.2em;">{!!nl2br($site->previous_location_list)!!}</p>
		@endif

		
		@if (false)
		<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d172138.65427095353!2d-122.48214666413614!3d47.61317464018482!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x5490102c93e83355%3A0x102565466944d59a!2sSeattle%2C+WA!5e0!3m2!1sen!2sus!4v1523908332154" width="90%" height="500" frameborder="0" style="border:0" allowfullscreen></iframe>		
		@endif
		
	</div>
	
</div>
</section>
@endif

@if (($section = getSection(SECTION_AFFILIATES, $sections)) != null)
<section class="{{$colors[$sectionCount++]}}">
<div class="container">	
	<div style="margin-top: 0px;" class="sectionHeader text-center main-font">	
	
		<!-- div class="" style="font-size: 4em; margin-bottom:20px;"><span class="glyphicon glyphicon-bed"></span></div -->
		<h1>{{$section->title}}</h1>
		<!-- h1>Affiliate Partners</h1 -->	
		
		<!-- AGODA AFFILIATE -->
		<div style="float:left; margin:20px;">
		
			<div id="adgshp2008177892"></div>
			<script type="text/javascript" src="//cdn0.agoda.net/images/sherpa/js/sherpa_init1_08.min.js"></script><script type="text/javascript">
			var stg = new Object(); stg.crt="9181526501892";stg.version="1.04"; stg.id=stg.name="adgshp2008177892"; stg.width="300px"; stg.height="250px";stg.ReferenceKey="0xsHqxj9SidZIQaBKVV3aA=="; stg.Layout="OblongStatic"; stg.Language="en-us";stg.Cid="1806200"; stg.OverideConf=false; new AgdSherpa(stg,3).initialize();
			</script>
			
		</div>
		
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
				
	</div>
</div>
</section>
@endif

@endsection
