@extends('layouts.app')

@section('content')

<?php
	$h = 200;
	$w = 300;
	$tours_fullpath = base_path() . PHOTOS_FULL_PATH . 'tours/';
	$tours_webpath = '/img/tours/';
	$link = '/activities/';
	$title = "Maps";
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
					<a href="/locations/activities/"><button style="margin-bottom:10px;" type="button" class="btn btn-info">Show All</button></a></li>
					@foreach($locations as $location)
						@if ($location->activities()->count() > 0)
							<a href="/locations/activities/{{$location->id}}"><button style="margin-bottom:10px;" type="button" class="btn btn-success">{{$location->name}}</button></a></li>
						@endif
					@endforeach
				</div>			
				@endif
						
				<!-------------------------------->
				<!-- show the maps -->
				<!-------------------------------->
				<div class="row xhidden-xs">

					<?php 
						$width = 300;
					?>
					@foreach($records as $record)
								
						@if ($record->published_flag && $record->approved_flag && strlen(trim($record->map_link)) > 0)
							<div class="entry-div">
								<div class="entry amenity-item">
									<div id="" style="display:default; margin-top:20px; margin-bottom:10px;">				
										<iframe id="xttd-map" src="{{ $record->map_link }}" style="max-width:100%;" width="{{ $width }}" height="{{ floor($width * .75) }}"></iframe>
									</div>									
									<p><a href='{{ route('activity.view', [urlencode($record->title), $record->id]) }}'>{{$record->title}}</a></p>
									
								</div>
							</div>
						@endif
					
					@endforeach
					
				</div><!-- row -->	
			</div>
						
</div><!-- container -->

@endsection
