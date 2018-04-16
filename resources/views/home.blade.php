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

<!--------------------------------------------------------------------------------------->
<!-- Jumbotron slider -->
<!--------------------------------------------------------------------------------------->

<div style="display: none; height: 0;" class="">
	<img src="/img/theme1/slider1.jpg" />
	<img src="/img/theme1/slider2.jpg" />
	<img src="/img/theme1/slider3.jpg" />
	<img src="/img/theme1/slider4.jpg" />
</div>

<div id="sliderWrapper" class="">
	<div style="" id="slider" title="Slider Image" class="container text-center">
		
		<!-- slider photo are attached here -->	
			
		<div class="sliderText">
			<div class="xsliderTextPanel" style="margin:40px;">
				<!--
				<h2 class="font-open-sans-400" style="margin:  0px; padding:0px; xbackground-color:black; font-size: 4em; font-weight:bold;"><span style="padding:0;margin:0;">Epic Travel Guide</span></h2>

				<video autoplay muted loop id="myVideo">
				  <source src="img/theme1/waves.mp4" type="video/mp4">
				</video>				

				-->
				
				<?php
					$logo = '';
					if (URL::to('/') === 'http://hikebikeboat.com')
					{
						$logo = '-hbb';
					}
					else if (URL::to('/') === 'http://epictravelguide.com')
					{
						$logo = '-epic';
					}
					else
					{
						$logo = '';
					}
				?>
				
				<img id="logo-big" src="/img/theme1/logo-big{{ $logo }}.png" alt="{{ URL::to('/') }}" title="{{ URL::to('/') }}" />
<!--
				<div style="max-width: 700px; margin: auto;">
					<h2 id="" class="font-open-sans-400" ><span class="">Slider Header 2 Message Loger Text Content Goes Here</span></h2>
				</div>

				<div class="hidden-xs" style="max-width: 700px; margin: auto;">
					<h2 id="" class="font-open-sans-400" ><span class=""></span>Ready to do the call to action?</h2>
					<form action="/users/register">
						<button class="textWhite formControlSpace20 btn btn-submit btn-lg bgBlue"><span class="glyphicon glyphicon-hand-right"></span>&nbsp;Call To Action</button>
					</form>
				</div>		
-->				
				
			</div>
		</div>			
	</div>		
</div>

<!--------------------------------------------------------------------------------------->
<!-- SECTION: Welcome -->
<!--------------------------------------------------------------------------------------->

<section id="" class="sectionWhite sectionWhitePattern">
	<div class="container">	
		<div class="text-center">			
			
			<div class="hidden-xl hidden-lg hidden-md hidden-sm" style="max-width: 700px; margin: auto;">
				<form action="/users/register">
					<button class="textWhite formControlSpace20 btn btn-submit btn-lg bgBlue"><span class="glyphicon glyphicon-hand-right"></span>&nbsp;Join Us Now</button>
				</form>
			</div>				
			
			<h1 class="font-open-sans-300">
				Welcome to {{ config('app.name', 'Travel') }}
			</h1>
			
			<h2 style="margin-bottom: 30px;" class="xfont-open-sans-300">
				Self-guided tours, Travel Blogs, Worldwide travel information
			</h2>
			
			<div class="clearfix">
				
				<div class="row">
				
					<?php $count = 0; ?>
					@foreach($entries as $entry)
						
						<div class='frontpage-box' >

							<!-- BACKGROUND PHOTO LINK -->
							
							<?php
								$count++;
								$h = 200;
								$w = 300;
								$photo = 
								$photo = '/img/theme1/test' . $count . '.jpg';
							?>
							
							<a href="/view/{{$entry->id}}" class="frontpage-box-link" style="width: <?php echo $w; ?>px; height: <?php echo $h; ?>px; background-size: 100%; background-repeat: no-repeat; background-image: url('<?php echo $photo; ?>');" ></a>

							<!-- HEADER NAME/TITLE LINK ------------------------------------------ -->
							
							<div class='frontpage-box-text'>
							
								<!-- CAPTION/TITLE ------------------------------------------ -->
								<p>		
									<a href="/view/{{$entry->id}}">{{ $entry->title }}</a>
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
<!-- SECTION: 2 -->
<!--------------------------------------------------------------------------------------->

<section class="sectionBlue">
<div class="container">	

	<div class="sectionHeader text-center">	

		<div class="sectionImage"><span class="glyphicon glyphicon-user"></span></div>
		<h1>Latest Articles</h1>
		
	</div>

	<h3>The sections have a bunch of h3 text.  Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</h3>
	
	<div class="text-center marginTop30"><h3><a class="sectionImageWhite" href="#">Click here for more details and FAQ&lsquo;s about this</a></h3></div>
	
	<div class="row text-center marginTop50">
		<div class="header">
			<form action="#">
				<button class="textWhite formControlSpace20 btn btn-submit btn-lg bgGreen"><span class="glyphicon glyphicon-user"></span>&nbsp;Call to Action!</button>
			</form>
			
		</div>		
	</div>
		
</div>
</section>

<!--------------------------------------------------------------------------------------->
<!-- SECTION: 3 -->
<!--------------------------------------------------------------------------------------->

<section class="sectionWhite">
<div class="container">	

	<div class="sectionHeader text-center">	
	
		<div class="sectionImage sectionImageBlue"><span class="sectionImageBlue glyphicon glyphicon-shopping-cart"></span></div>
		<h1 class="sectionImageBlue">Section 3 Header</h1>

	</div>

	<h3>The sections have a bunch of h3 text.  Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</h3>
	
	<div class="text-center marginTop30"><h3><a class="sectionImageBlue" href="#">Click Here for More Details</a></h3></div>
	
</div>
</section>

<!--------------------------------------------------------------------------------------->
<!-- SECTION: 4 -->
<!--------------------------------------------------------------------------------------->
		
<section class="sectionYellow">
<div class="container">	

	<div class="sectionHeader text-center">	
	
		<div class="sectionImage"><span class="glyphicon glyphicon-envelope"></span></div>
		<h1>Section 4 Header</h1>
	
	</div>

	<h3>The sections have a bunch of h3 text.  Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</h3>

	<div class="text-center marginTop30"><h3><a class="sectionImageWhite" href="#">Click Here for More Details</a></h3></div>
	
</div>
</section>
		
<!--------------------------------------------------------------------------------------->
<!-- SECTION: 5 -->
<!--------------------------------------------------------------------------------------->

<section class="sectionWhite">
<div class="container">	

	<div class="sectionHeader text-center">	
		<div class="sectionImage sectionImageBlue"><span class="sectionImageBlue glyphicon glyphicon-wrench"></span></div>
		<h1 class="sectionImageBlue">Section 5 Header</h1>
	</div>

	<h3>The sections have a bunch of h3 text.  Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</h3>

	<div class="text-center marginTop30"><h3><a class="sectionImageBlue" href="#">Click Here for More Details</a></h3></div>
	
</div>
</section>
		
<!--------------------------------------------------------------------------------------->
<!-- SECTION: 6 -->
<!--------------------------------------------------------------------------------------->
		
<section class="sectionBlue">
<div class="container">	

	<div class="sectionHeader text-center">	
		<div class="sectionImage"><span class="glyphicon glyphicon-briefcase"></span></div>
		<h1>Section 6 Header</h1>
	</div>

	<h3>The sections have a bunch of h3 text.  Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</h3>

	<div class="text-center marginTop30"><h3><a class="sectionImageWhite"  href="#">Click Here for More Details</a></h3></div>
	
</div>
</section>
		
<!--------------------------------------------------------------------------------------->
<!-- SECTION: Contact -->
<!--------------------------------------------------------------------------------------->
			
<section id="contact" class="sectionWhite">
	<div class="container">

		<div class="sectionHeader text-center">	
			<div class="sectionImage sectionImageBlue"><span class="sectionImageBlue glyphicon glyphicon-pencil"></span></div>
			<h1 class="sectionImageBlue">Contact Us</h1>
		</div>
		
		<div class="clearfix marginTop40">
			<!-- ?php echo $this->element('form-contact'); ? -->
		</div>
	
	</div>
</section>


@endsection
