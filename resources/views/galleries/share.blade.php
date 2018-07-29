@extends('layouts.app')

@section('content')

<div class="" id="container" style="margin:10px;" >
	<h1>Select Gallery</h1>

	<div id="content" style=''>
		@foreach($galleries as $record)
			<div class='' style="border:1px solid black" >
				<!-- BACKGROUND PHOTO LINK -->
				<a href="/galleries/link/{{$entry->id}}/{{$record->id}}"><img style="width: 100px;" src="{{$record->photo_path}}{{$record->photo}}" /></a>

				<div style='white-space: nowrap; overflow: hidden;' class=''>
					{{$record->title}}
				</div>
			</div>		
		@endforeach			
	</div>
		
</div><!-- container -->

@endsection
