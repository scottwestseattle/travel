@extends('layouts.app')

@section('content')

<?php
	$h = 200;
	$w = 300;
	$tours_fullpath = base_path() . PHOTOS_FULL_PATH . 'tours/';
	$tours_webpath = '/img/tours/';
	$link = '/activities/';
	$title = "Tours, Hikes, Things To Do";
?>

<div class="page-size container text-center">
			
			<!-------------------- Section header image --------->
			<div class="sectionHeader hidden-xs">
				<!-- div><img src="/img/theme1/bootprint.jpg" /></div -->
				<h1 style="margin-bottom:0;padding-bottom:0" class="main-font sectionImageBlue">{{$title}}</h1>
			</div>	
			
			<div class="sectionHeader hidden-xl hidden-lg hidden-md hidden-sm">
				<!-- div><img src="/img/theme1/bootprint.jpg" /></div -->
				<h2 style="margin-bottom:0;padding-bottom:0" class="main-font sectionImageBlue">{{$title}}</h2>
			</div>						
						
			<div class="clearfix">
						
				<!---------------------------------------------------->
				<!-- Locations -->
				<!---------------------------------------------------->
				@if (isset($locations))
				<div style="margin:20px; 0" class="text-center">
					<a href="/locations/activities/"><button style="margin-bottom:10px;" type="button" class="btn btn-info">Show All<!-- &nbsp;<span class="badge badge-light">{{$records->count()}}</span>--></button></a>
					@foreach($locations as $location)
						@if ($location->activities()->count() > 0)
							<a href="/locations/activities/{{$location->id}}">
								<button style="margin-bottom:10px;" type="button" class="btn btn-success">{{$location->name}}
									<span class="badge badge-light">{{$location->activities()->count()}}</span>
								</button>
							</a>
						@endif
					@endforeach
				</div>			
				@endif
						
				<!-------------------------------->
				<!-- this is the non-XS version -->
				<!-------------------------------->
				<div class="row xhidden-xs">

					@foreach($records as $entry)
								
						@if ($entry->published_flag && $entry->approved_flag)
						<div class="col-md-4 col-sm-6">
						
							<!-- tour image -->
							<!-- a href="{{$link . 'view/' . $entry->id}}" -->
							<!-- a href="{{ route('activity.view', [preg_replace('/\+/', '-', urlencode($entry->title)), $entry->id]) }}" -->
							<?php 
								//$title = preg_replace('/[^\da-z ]/i', '', $entry->title); // remove all chars except alphanums and space
								//$title = urlencode($title);
								//$title = str_replace('+', '-', $title);
							?>
							<a href="{{ route('activity.view', [urlencode($entry->title), $entry->id]) }}">
								<div style="min-height:220px; background-color: #4993FD; background-size: cover; background-position: center; background-image: url('{{$entry->photo}}'); "></div>
							</a>
							
							<!-- tour title -->
							<div class="trim-text" style="color: white; font-size:1.2em; font-weight:bold; padding:5px; margin-bottom:20px; background-color: #3F98FD;">
								<a style="font-family: Raleway; color: white; font-size:1em; text-decoration: none; " href="{{ route('activity.view', [urlencode($entry->title), $entry->id]) }}">{{ $entry->title }}</a>
							</div>
						</div>
						@endif
					
					@endforeach
					
				</div><!-- row -->	

				@if (false)
				<!-- this is the XS size only using table cols -->
				<div class="hidden-xl hidden-lg hidden-md hidden-sm">
					<table class="table" style="padding:0; margin:0">
						<tbody>
							@foreach($records as $entry)
								@if ($entry->published_flag && $entry->approved_flag)
								<tr>
									<td style="width:150px;">
										<a href="{{ route('activity.view', [urlencode($entry->title), $entry->id]) }}"><img src="{{ $entry->photo }}" width="150" /></a>
									</td>
									<td>
										<a style="font-family: Raleway; font-size:.8em;" href="{{ route('activity.view', [urlencode($entry->title), $entry->id]) }}">{{$entry->title}}</a>
										
										@if (false)
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
										@else
										<div></div>
										@endif
										
										@guest
										@else
											<a href='{{ route('activity.view', [urlencode($entry->title), $entry->id]) }}'>
												<span style="font-size:.8em;" class="glyphCustom glyphicon glyphicon-edit"></span>
											</a>
											
											<div style="font-family: Raleway; color: #1970D3; font-size:.4em; font-weight: bold;"></div>							
										@endguest
									</td>
								</tr>
								@endif
							@endforeach
						</tbody>
					</table>
				</div><!-- XS size only -->
				@endif

			</div>
						
</div><!-- container -->

@endsection
