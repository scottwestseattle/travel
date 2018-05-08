<style>
.frontpage-box a
{
	color: white;
	text-decoration: none;
	font-size: 10pt;
}

.frontpage-box p
{
	margin: 0;
	padding: 0;
}

.frontpage-box-link
{
	display: block; 	
	height: 220px; 
	width: 320px; 
}

.frontpage-box-text
{
	margin: 3px 4px;
	font-weight: bold;
}

.frontpage-box
{
	background-color: #a0a0a0;

	display: block; 
	float: left; 

	margin: 0;
	margin-top: 5px;
	margin-left: 5px;	
}
</style>

@extends('layouts.frontpage')

@section('content')

<div style="background-color: LightGray; background-image:url('/img/theme1/bg-pattern.png'); " >

<!--------------------------------------------------------------------------------------->
<!-- Title Logo Bar -->
<!--------------------------------------------------------------------------------------->

	<!--------------------------------------------------------------------------------------->
	<!-- Big random photo header section -->
	<!--------------------------------------------------------------------------------------->

	<section>
		<div class="slider-container" onclick="change_slider()">
			<div id="slider">
				<img id="slider-spacer" src="/img/theme1/spacer.png" width="100%" />
			</div>
		</div>
	</section>
</div>

<script>
	// load all the sliders so we can javascript through them
	var sliders = [
		@foreach($sliders as $slider)
			'{{$slider}}',
		@endforeach
	];
		
	var ix = Math.floor(Math.random() * sliders.length);
	var img = sliders[ix];
	
	document.getElementById("slider").style.backgroundImage = "url('/img/sliders/" + img + "')";
	document.getElementById("slider").title = img;
	
	function change_slider()
	{
		ix++;
		if (ix > sliders.length - 1)
			ix = 0;
		
		img = sliders[ix];
		
		document.getElementById("slider").style.backgroundImage = "url('/img/sliders/" + img + "')";
		document.getElementById("slider").title = img;
	}
	
</script>

<!--------------------------------------------------------------------------------------->
<!-- SECTION 1: Welcome -->
<!--------------------------------------------------------------------------------------->

<section id="" class="sectionBlue" style="padding: 30px 0 40px 0; xposition: relative; xtop: -30px; ">
<div class="container" style="max-width:1400px;">	
	<div class="sectionHeader text-center">	
		
		<!-- h1>Welcome to {{ config('app.name', 'Travel') }}</h1 -->
			
		<!-- h2 style="margin-bottom: 30px;" class="xfont-open-sans-300">
			Self-guided tours, Travel Blogs, and Worldwide travel information
		</h2 -->

		<img style="max-width:70%;" src="/img/theme1/epic-logo-pyramids-lg.png" />
		<h3 class="welcome-text main-font">{{ config('app.name', 'Travel') }} provides inspiring travel experiences that bring people further into the discovery of cultures, places, and people all around the world. Our goal is to positively impact perspectives, promote conscious travel, create global citizens, and celebrate the beauty of our world.</h3>
	
	</div>	
	
	<div class="row text-center" style="margin-top:40px;">
		<div class="header">
			<form action="#">
				<button class="textWhite formControlSpace20 btn btn-submit btn-lg bgGreen"><span class="glyphicon glyphicon-user"></span>&nbsp;Click Here to Join us!</button>
			</form>
			
		</div>		
	</div>

	<div class="sectionHeader text-center" style="margin-top:20px;">
		<h3 style="font-size:1.2em;" class="welcome-text main-font"><i>Sail away from the safe harbor. Catch the trade winds in your sails. Explore. Dream. Discover.<br/>— Mark Twain</i></h3>
	</div>
	
</div>
</section>

<!--------------------------------------------------------------------------------------->
<!-- SECTION: Tours - float box format -->
<!--------------------------------------------------------------------------------------->

<?php
	$h = 200;
	$w = 300;
	$tours_fullpath = base_path() . PHOTOS_FULL_PATH . 'tours/';
	$tours_webpath = '/img/tours/'
?>

<section id="" class="sectionWhite sectionWhitePattern" style="" >
	<div class="container">	
		<div class="text-center">			
			
			<!-------------------- Section header image --------->
			<div class="sectionHeader">	
				<div><img src="/img/theme1/bootprint.jpg" /></div>				
				<h1 class="main-font sectionImageBlue">Tours, Hikes, Things To Do</h1>
				
			</div>			
			
			<!-- div class="hidden-xl hidden-lg hidden-md hidden-sm" style="max-width: 700px; margin: auto;">
				<form action="/users/register">
					<button class="textWhite formControlSpace20 btn btn-submit btn-lg bgBlue"><span class="glyphicon glyphicon-hand-right"></span>&nbsp;Join Us Now</button>
				</form>
			</div -->				
						
			<div class="clearfix">
			
			<!-- div style="adding-left: 10px; margin-bottom: 10px; font-family: Raleway; color: green; font-size:.9em;">USA >> Seattle >> Downtown</div -->
			
				<!-------------------------------->
				<!-- this is the non-XS version -->
				<!-------------------------------->
				<div class="row hidden-xs">

					@foreach($tours as $entry)
										
						<div class="col-md-4 col-sm-6">
						
							<!-- tour image -->
							<a href="/view/{{$entry->id}}" >
								<div style="min-height:220px; background-color: lightSalmon; background-size: cover; background-position: center; background-image: url('{{$entry->photo}}'); "></div>
							</a>
							
							<!-- tour title -->
							<div class="trim-text" style="color: white; font-size:1.2em; font-weight:bold; padding:5px; margin-bottom:20px; background-color: #3F98FD;">
								<a style="font-family: Raleway; color: white; font-size:1em; text-decoration: none; " href="/view/{{$entry->id}}">{{ $entry->title }}</a>
							</div>
						</div>
					
					@endforeach
					
				</div><!-- row -->	

				<!-- this is the XS size only using table cols -->
				<div class="hidden-xl hidden-lg hidden-md hidden-sm">
					<table class="table" style="padding:0; margin:0">
						<tbody>
							@foreach($tours as $entry)
								<tr>
									<td style="width:150px;">
										<a href="{{ $entry->link }}"><img src="{{ $entry->photo }}" width="150" /></a>
									</td>
									<td>
										<a style="font-family: Raleway; font-size:.8em;" href="{{ $entry->link }}">{{$entry->title}}</a>						
										<?php
											$tags = "Hike";
											if (strpos($entry->title, "Water Taxi") === FALSE)
											{
												$tags = "Hike, Bike";
											}
											else
											{
												$tags = "Boat";
											}
										?>
										<div style="font-family: Raleway; color: #1970D3; font-size:.6em; font-weight: bold;">{{ $tags }}</div>
										
										@guest
										@else
											<a href='/entries/edit/{{$entry->id}}'>
												<span style="font-size:.8em;" class="glyphCustom glyphicon glyphicon-edit"></span>
											</a>
											
											<div style="font-family: Raleway; color: #1970D3; font-size:.4em; font-weight: bold;"></div>							
										@endguest
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div><!-- XS size only -->

			</div>
						
		</div><!-- text-center -->
	</div><!-- container -->
</section>									

<!--------------------------------------------------------------------------------------->
<!-- SECTION: Latest Posts -->
<!--------------------------------------------------------------------------------------->
<?php if (false) : ?>

<section class="sectionBlue">
	<div class="container" style="max-width:1440px;">	
		<div class="sectionHeader text-center">			
			
			<!-- div class="hidden-xl hidden-lg hidden-md hidden-sm" style="max-width: 700px; margin: auto;">
				<form action="/users/register">
					<button class="textWhite formControlSpace20 btn btn-submit btn-lg bgBlue"><span class="glyphicon glyphicon-hand-right"></span>&nbsp;Join Us Now</button>
				</form>
			</div -->				
			
			<div class="sectionImage sectionImageBlue"><span class="sectionImageWhite glyphicon glyphicon-edit"></span></div>
			
			<h1 style="margin-bottom: 30px;" class="xfont-open-sans-300">
				Latest Posts
			</h1>
			
			<div class="clearfix">
				
				<div class="row">
				
					<?php $count = 0; ?>
					@foreach($posts as $entry)
						
						<div class='frontpage-box' >

							<!-- BACKGROUND PHOTO LINK -->
							
							<?php
								$count++;
								$h = 200;
								$w = 300;
								$photo = 
								$photo = '/img/theme1/image' . $count . '.jpg';
							?>
							
							<a href="/view/{{$entry->id}}" class="frontpage-box-link" style="width: <?php echo $w; ?>px; height: <?php echo $h; ?>px; background-size: 100%; background-repeat: no-repeat; background-image: url('<?php echo $photo; ?>');" ></a>

							<!-- HEADER NAME/TITLE LINK ------------------------------------------ -->
							
							<div class='frontpage-box-text'>
							
								<!-- CAPTION/TITLE ------------------------------------------ -->
								<p>		
									<a style="font-family: Raleway; font-size:.9em;" href="/view/{{$entry->id}}">{{ $entry->title }}</a>
								</p>	
								
							</div>
								
						</div>
						
					@endforeach
					
				</div><!-- row -->			

			</div>
						
		</div><!-- text-center -->
	</div><!-- container -->
</section>

<?php endif; ?>

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
		
<section class="sectionYellow">
<div class="container">	

	<div class="sectionHeader text-center">	
	
		<div class="" style="font-size: 6em;"><span class="glyphicon glyphicon-globe"></span></div>
		<h1>Current Location:</h1>
	
		<!-- h3>We are currently exploring:</h3 -->
		
		<h2 style="margin-bottom:50px;">Seattle, Washington, USA</h2>

		<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d172138.65427095353!2d-122.48214666413614!3d47.61317464018482!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x5490102c93e83355%3A0x102565466944d59a!2sSeattle%2C+WA!5e0!3m2!1sen!2sus!4v1523908332154" width="90%" height="500" frameborder="0" style="border:0" allowfullscreen></iframe>		
		
	</div>
	
</div>
</section>

<!--------------------------------------------------------------------------------------->
<!-- SECTION: Floating boxes sample code -->
<!--------------------------------------------------------------------------------------->


@endsection
