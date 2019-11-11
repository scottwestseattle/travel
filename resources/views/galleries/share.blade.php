@extends('layouts.app')

@section('content')

<div class="container" >
	
	<h1>Select Gallery</h1>

	<div id="content" style='background-color: white; margin:0; padding:0; padding-bottom: 5px; min-height: 200px; text-align: center;'>

		@foreach($galleries as $record)

			@if (true)
			
			@if ($record->published_flag == 0 || $record->approved_flag == 0)
				<a href="/galleries/link/{{$entry->id}}/{{$record->id}}"><button style="margin-bottom:10px;" type="button" class="btn btn-light">{{$record->title}}</button></a>
			@else
				<a href="/galleries/link/{{$entry->id}}/{{$record->id}}"><button style="margin-bottom:10px;" type="button" class="btn btn-info">{{$record->title}}</button></a>
			@endif
			
			
			@else
			<div class='frontpage-box' style="display:inline-block; background-color: #f8f8f8;" >
			
				<!-- BACKGROUND PHOTO LINK -->
				<a href="/galleries/link/{{$entry->id}}/{{$record->id}}" class="frontpage-box-link" style="" >
					<div style="width: 200px; height: 150px; background-size: 100%; background-repeat: no-repeat; background-image: url('{{$record->photo_path}}/{{$record->photo}}')">
					</div>
				</a>
				
				<div style='white-space: nowrap; overflow: hidden;' class='frontpage-box-text'>
					<a href="/galleries/link/{{$entry->id}}/{{$record->id}}">{{$record->title}}</a>
				</div>
			</div>	
			@endif				
						
		@endforeach			
	</div>
		
</div><!-- container -->

@endsection
