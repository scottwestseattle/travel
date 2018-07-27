@extends('layouts.app')

@section('content')

	<div class="container main-font page-size ">	
	
		<div class="text-center"><h1>Blogs ({{ count($records) }})</h1></div>

		<div class="row" style="margin-bottom:10px;">
				
			@foreach($records as $record)
			<div style="max-width: 400px; padding:10px;" class="col-sm-4"><!-- outer div needed for the columns and the padding, otherwise they won't center -->
				<div class="drop-box" style="min-height:450px; color: black; background-color: white; " ><!-- inner col div -->
					<!-- blog photo -->
					<a href="/blogs/show/{{$record->id}}">
						@if (!isset($record->photo))
							<div style="min-width:200px; min-height:220px; background-color: white; background-size: cover; background-position: center; background-image: url('{{TOUR_PHOTO_PLACEHOLDER}}'); "></div>
						@else
							<div style="min-width:200px; min-height:220px; background-color: white; background-size: cover; background-position: center; background-image: url('{{$record->photo_path}}/{{$record->photo}}'); "></div>
						@endif
					</a>							
							
					<!-- blog text -->
					<div class="" style="padding:10px;">	

						<p><a href="/blogs/show/{{$record->id}}/all" style="color:green; text-decoration:none;">{{$record->post_count}} posts</a></p>
					
						<a style="font-family: 'Volkhov', serif; color: black; font-size:1.4em; font-weight:bold; text-decoration: none; " href="/blogs/show/{{$record->id}}">{{ $record->title }}</a>						
						
						<p>{{$record->description}}</p>
						
					</div>
				</div><!-- inner col div -->
			</div><!-- outer col div -->
			@endforeach					

		</div><!-- row -->									
					
	</div><!-- container -->

@endsection
