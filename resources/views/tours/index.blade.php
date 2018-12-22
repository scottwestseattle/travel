@extends('layouts.app')

@section('content')

<div class="page-size container text-center">
			
			<!-------------------- Section header image --------->
			<div class="sectionHeader hidden-xs">
				<!-- div><img src="/img/theme1/bootprint.jpg" /></div -->
				<h1 style="margin-bottom:0;padding-bottom:0" class="main-font sectionImageBlue">@LANG('ui.Tours, Hikes, Things To Do')</h1>
			</div>	
			
			<div class="sectionHeader hidden-xl hidden-lg hidden-md hidden-sm">
				<!-- div><img src="/img/theme1/bootprint.jpg" /></div -->
				<h2 style="margin-bottom:0;padding-bottom:0" class="main-font sectionImageBlue">@LANG('ui.Tours, Hikes, Things To Do')</h2>
			</div>						
						
			<div class="clearfix">
						
				<!---------------------------------------------------->
				<!-- show location pills -->
				<!---------------------------------------------------->
				@if (isset($locations) && $tour_count > 0)
				<div style="margin:20px; 0" class="text-center">
					<a href="/tours/index/"><button style="margin-bottom:10px;" type="button" class="btn btn-info">Show All&nbsp;<span class="badge badge-light">{{$showAll}}</span></button></a>
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
						
				<!-------------------------------->
				<!-- show the tours -->
				<!-------------------------------->
				<div class="row">

					@foreach($tours as $entry)
								
						<div class="col-md-4 col-sm-6">
						
							<!-- tour main photo -->
							<a href="{{ route('tour.permalink', [$entry->permalink]) }}">
								<div style="min-height:220px; background-color: #4993FD; background-size: cover; background-position: center; background-image: url('{{$entry->photo_path}}/{{$entry->photo}}'); "></div>
							</a>
							
							<!-- tour title -->
							<div class="trim-text" style="color: white; font-size:1.2em; font-weight:bold; padding:5px; margin-bottom:20px; background-color: #3F98FD;">
								<a style="font-family: Raleway; color: white; font-size:1em; text-decoration: none; " href="{{ route('tour.permalink', [$entry->permalink]) }}">{{ $entry->title }}</a>
							</div>
							
						</div>
					
					@endforeach
					
				</div><!-- row -->	

			</div>
						
</div><!-- container -->

@endsection
