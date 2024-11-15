
@extends('layouts.gallery')

@section('content')
	
<div class="" id="container" style="background-color: white; min-height:500px;" >

		<!------------------------------------------------------------------------------------------------------------->
		<!-- Content -------------------------------------------------------------------------------------------------->
		<!------------------------------------------------------------------------------------------------------------->
	
	<div id="load-loop" class="" style="width:100%; text-align: center; padding-top:100px;">
		<img src="/img/theme1/load-loop.gif" />
	</div>
	<div id="content" style='display:none; background-color: white; margin:0; padding:0; padding-bottom: 5px; min-height: 200px; text-align: center;'>
		@foreach($records as $record)
			@if (false)
					<div class='frontpage-box' style="width: 200px; height: 150px; background-size: 100%; background-repeat: no-repeat; background-image: url('{{$record->photo_path}}/{{$record->photo}}')" >
						<!-- BACKGROUND PHOTO LINK -->
						<a href="/" class="frontpage-box-link" style="" ></a>

						<div style='white-space: nowrap; overflow: hidden;' class='frontpage-box-text'>
							<a class="trim-text" href="">{{$record->title}}</a>
						</div>
					</div>
			@else
					<div class='frontpage-box' style="" >
						<!-- BACKGROUND PHOTO LINK -->
						<a href="{{route('gallery.permalink', [$record->permalink])}}" class="frontpage-box-link" style="width: 200px; height: 150px; background-size: 100%; background-repeat: no-repeat; background-image: url('{{$record->photo_path}}/{{$record->photo}}')" ></a>

						<div style='white-space: nowrap; overflow: hidden;' class='frontpage-box-text'>
							{{$record->title}}
						</div>
					</div>			
			@endif
		@endforeach			
	</div>
	
	<span id="debug"></span>
		
</div><!-- container -->


@endsection
