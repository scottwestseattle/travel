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

<div style="background-color: LightGray; xbackground-size:cover; background-image:url('/img/theme1/bg-pattern.png'); " >

<!--------------------------------------------------------------------------------------->
<!-- Title Logo Bar -->
<!--------------------------------------------------------------------------------------->

<!--
<section style="">
	<div class="frontpage-container" style="padding: 5px; text-align: center;" >
		<img id="" style="margin-right:10px; " src="/img/theme1/epic-logo-pyramids-sm-arch.png" height="100px" />
		<h1 id="tag-line" style="margin:0;padding:0; font-family: Raleway; color:#1F5E91;">Self-guided tours, Travel Blogs, and Worldwide travel information</h1>
	</div>
</section>
-->

<!--------------------------------------------------------------------------------------->
<!-- Big random photo header section -->
<!--------------------------------------------------------------------------------------->

<div style="display: none; height: 0;" class="">
	<img src="/img/theme1/slider1.jpg" />
	<img src="/img/theme1/slider2.jpg" />
	<img src="/img/theme1/slider3.jpg" />
	<img src="/img/theme1/slider4.jpg" />
</div>

<section style="">
	<div class="frontpage-container" style="" >
		<img id="slider" src="" width="100%" />
	</div>
</section>

</div>
<!--------------------------------------------------------------------------------------->
<!-- SECTION 1: Welcome -->
<!--------------------------------------------------------------------------------------->

<section id="" class="sectionBlue" style="xposition: relative; xtop: -30px; ">
<div class="container" style="max-width:1400px;">	
	<div class="sectionHeader text-center">	
		
		<!-- h1>Welcome to {{ config('app.name', 'Travel') }}</h1 -->
			
		<!-- h2 style="margin-bottom: 30px;" class="xfont-open-sans-300">
			Self-guided tours, Travel Blogs, and Worldwide travel information
		</h2 -->

		<h3 class="welcome-text">{{ config('app.name', 'Travel') }} provides inspiring travel experiences that bring people further into the discovery of cultures, places, and people all around the world. Our goal is to positively impact perspectives, promote conscious travel, create global citizens, and celebrate the beauty of our world.</h3>
		<h3 class="welcome-text">Sail away from the safe harbor. Catch the trade winds in your sails. Explore. Dream. Discover.<br/>— Mark Twain</h3>
	
	</div>	
	
	<div class="row text-center" style="margin-top:40px;">
		<div class="header">
			<form action="#">
				<button class="textWhite formControlSpace20 btn btn-submit btn-lg bgGreen"><span class="glyphicon glyphicon-user"></span>&nbsp;Click Here to Join us!</button>
			</form>
			
		</div>		
	</div>
		
</div>
</section>

<!--------------------------------------------------------------------------------------->
<!-- SECTION: Floating boxes sample code -->
<!--------------------------------------------------------------------------------------->

<section id="" class="sectionWhite sectionWhitePattern">
	<div class="container">	
		<div class="text-center">			
			
			<div class="hidden-xl hidden-lg hidden-md hidden-sm" style="max-width: 700px; margin: auto;">
				<form action="/users/register">
					<button class="textWhite formControlSpace20 btn btn-submit btn-lg bgBlue"><span class="glyphicon glyphicon-hand-right"></span>&nbsp;Join Us Now</button>
				</form>
			</div>				
			
			<h1 class="xfont-open-sans-300">
				Lorem ipsum dolor sit amet
			</h1>
			
			<h2 style="margin-bottom: 30px;" class="xfont-open-sans-300">
				Consectetur adipiscing elit:
			</h2>
			
			<div class="clearfix">
				
				<div class="row">
				
					<div class="col-md-4 col-sm-6">
						<div class="steps step1">
							<h3><span class="glyphicon glyphicon-user glyphspace"></span>Floating Box</h3>
							This is the text that is shown in the responsive floating box with three columns
						</div>
					</div>

					<div class="col-md-4 col-sm-6">
						<div class="steps step2">
							<h3><span class="glyphicon glyphicon-user glyphspace"></span>Floating Box</h3>
							This is the text that is shown in the responsive floating box with three columns
						</div>
					</div>

					<div class="col-md-4 col-sm-6">
						<div class="steps step3">
							<h3><span class="glyphicon glyphicon-user glyphspace"></span>Floating Box</h3>
							This is the text that is shown in the responsive floating box with three columns
						</div>
					</div>

					<div class="col-md-4 col-sm-6">
						<div class="steps step4">
							<h3><span class="glyphicon glyphicon-user glyphspace"></span>Floating Box</h3>
							This is the text that is shown in the responsive floating box with three columns
						</div>
					</div>

					<div class="col-md-4 col-sm-6">
						<div class="steps step5">
							<h3><span class="glyphicon glyphicon-user glyphspace"></span>Floating Box</h3>
							This is the text that is shown in the responsive floating box with three columns
						</div>
					</div>

					<div class="col-md-4 col-sm-6">
						<div class="steps step6">
							<h3><span class="glyphicon glyphicon-user glyphspace"></span>Floating Box</h3>
							This is the text that is shown in the responsive floating box with three columns
						</div>
					</div>
					
				</div><!-- row -->			

			</div>
						
		</div><!-- text-center -->
	</div><!-- container -->
</section>

<!--------------------------------------------------------------------------------------->
<!-- SECTION: Tours -->
<!--------------------------------------------------------------------------------------->

<section class="sectionWhite">
<div class="container" style="" >

	<div class="sectionHeader text-center">	
	
		<!-- div class="sectionImage sectionImageBlue"><span class="sectionImageBlue glyphicon glyphicon-globe"></span></div -->
		<div><img src="/img/theme1/bootprint.jpg" /></div>
		<h1 class="sectionImageBlue">Tours, Hikes, Things to do</h1>
		<div style="adding-left: 10px; margin-bottom: 10px; font-family: Raleway; color: green; font-size:.9em;">USA >> Seattle >> Downtown</div>		
		
	</div>	
		
</div>

<!---------------------- Large version----------------------->

<div id="tours-lg" >
	<div class="container" style="max-width:1440px;">	
		<div class="sectionHeader text-center">			
																
				<div class="row">
				
					<?php $count = 0; ?>
					@foreach($tours as $entry)
						
						<div class='frontpage-box' >

							<!-- BACKGROUND PHOTO LINK -->
							
							<?php 
								$h = 200;
								$w = 300;
								
								$link = '/view/' . $entry->id;
								$base_folder = 'img/theme1/tours/';
								$photo_folder = $base_folder . $entry->id . '/';
								$photo = $photo_folder . 'main.jpg';
								
								// file_exists must be relative path with no leading '/'
								if (file_exists($photo) === FALSE)
								{
									if (!is_dir($photo_folder)) // if folder doesn't exist
									{							
										// make the folder with read/execute for everbody
										mkdir($photo_folder, 0755);
									}
									
									// show the place holder
									$photo = '/' . $base_folder . 'placeholder.jpg';
								}
								else
								{
									// to show the photo we need the leading '/'
									$photo = '/' . $photo_folder . 'main.jpg';
								}
							?>
							
							<a 
								href="/view/{{$entry->id}}" 
								class="frontpage-box-link" 
								style="width: <?php echo $w; ?>px; height: <?php echo $h; ?>px; background-size: 100%; background-repeat: no-repeat; background-image: url('<?php echo $photo; ?>');" >
							</a>

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
</div><!-- #tours-lg -->

<!----------------------------------------------------------------------------------->

<div id="tours-lg" >
<div class="container" style="max-width:1400px;">	
	<div style="adding-left: 10px; margin-bottom: 10px; font-family: Raleway; color: green; font-size:.9em;">USA >> Seattle >> Downtown</div>

	<table class="table" style="padding:0; margin:0">
		<tbody>
			@foreach($tours as $entry)
				<?php 			
					$link = '/view/' . $entry->id;
					$base_folder = 'img/theme1/tours/';
					$photo_folder = $base_folder . $entry->id . '/';
					$photo = $photo_folder . 'main.jpg';
					
					// file_exists must be relative path with no leading '/'
					if (file_exists($photo) === FALSE)
					{
						if (!is_dir($photo_folder)) // if folder doesn't exist
						{							
							// make the folder with read/execute for everbody
							mkdir($photo_folder, 0755);
						}
						
						// show the place holder
						$photo = '/' . $base_folder . 'placeholder.jpg';
					}
					else
					{
						// to show the photo we need the leading '/'
						$photo = '/' . $photo_folder . 'main.jpg';
					}
				?>
				<tr>
					<td style="width:300px;">
						<a href="{{ $link }}"><img src="{{ $photo }}" width="300" /></a>
					</td>
					<td>
						<a style="font-family: Raleway; font-size:.8em;" href="{{ $link }}">{{$entry->title}}</a>						
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
</div>
</div>
	
<!---------------------- Md, Sm, Xs version----------------------->
<div id="tours-md">
<div class="" style="">	
	<div style="adding-left: 10px; margin-bottom: 10px; font-family: Raleway; color: green; font-size:.9em;">USA >> Seattle >> Downtown</div>
		<table class="table" style="padding:0; margin:0">
			<tbody>
			@foreach($tours as $entry)
				<?php 
					$link = '/view/' . $entry->id;
					$base_folder = 'img/theme1/tours/';
					$photo_folder = $base_folder . $entry->id . '/';
					$photo = $photo_folder . 'main.jpg';
					
					// file_exists must be relative path with no leading '/'
					if (file_exists($photo) === FALSE)
					{
						if (!is_dir($photo_folder)) // if folder doesn't exist
						{							
							// make the folder with read/execute for everbody
							mkdir($photo_folder, 0755);
						}
						
						// show the place holder
						$photo = '/' . $base_folder . 'placeholder.jpg';
					}
					else
					{
						// to show the photo we need the leading '/'
						$photo = '/' . $photo_folder . 'main.jpg';
					}
				?>
				<tr>
					<td style="width:100px;">
						<a href="{{ $link }}"><img src="{{ $photo }}" width="100" /></a>
					</td>
					<td>
						<a style="font-family: Raleway; font-size:.8em;" href="{{ $link }}">{{$entry->title}}</a>						
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
	</div>	
</div>
</section>

<!--------------------------------------------------------------------------------------->
<!-- SECTION: Latest Posts -->
<!--------------------------------------------------------------------------------------->

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

<!--------------------------------------------------------------------------------------->
<!-- SECTION: Contact -->
<!--------------------------------------------------------------------------------------->
			
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

@endsection
