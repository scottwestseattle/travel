@extends('layouts.app')

@section('content')

<div class="container">
	<h1>Locations</h1>
	
			<div style="margin:20px; 0" class="text-center">
				<!-- h3 style="margin-bottom:20px;" class="main-font sectionImageBlue">Locations</h3 -->
				@foreach($records as $record)
					<a href="/locations/activities/{{$record->id}}">
						<button style="margin-bottom:10px;" type="button" class="btn btn-primary">{{$record->name}}&nbsp;<span class="badge badge-light">{{$record->activities()->count()}}</span></button>
					</a>
				@endforeach
			</div>			
               
</div>
@endsection
