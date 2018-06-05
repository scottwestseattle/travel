@extends('layouts.app')

@section('content')

<?php 

$header = 'Tours';

?>

<div class="page-size container">
	
	@guest
	@else
		@if (Auth::user()->user_type >= 100)
		<table><tr>			
			<td style="width:40px; font-size:20px;"><a href='/tours/indexadmin/'><span class="glyphCustom glyphicon glyphicon-list"></span></a></td>
			<td style="width:40px; font-size:20px;"><a href='/tours/add/'><span class="glyphCustom glyphicon glyphicon-plus-sign"></span></a></td>
		</tr></table>
		@endif
	@endguest
	
	<h1 style="font-size:1.3em;">{{ $header }} ({{ count($records) }})</h1>
	@if (Auth::check())
		<table class="table table-striped">
			<tbody>
			@foreach($records as $record)
				<tr>
					<td style="width:20px;"><a href='/tours/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
					<td style="width:20px;"><a href='/photos/entries/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-picture"></span></a></td>
					<td style="width:20px;"><a href='/entries/setlocation/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-map-marker"></span></a></td>
					<td style="width:20px;"><a href='/entries/publish/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-flash"></span></a></td>
					<?php 
						$location_name = null;
						$location_id = 0;
						if (isset($record->location_name)) // if coming from tours controller
						{
							$location_name = $record->location_name;
							$location_id = $record->location_id;
						}
						else if (isset($record->location)) // if coming from locations controller (showing by location)
						{
							$location_name = $record->location->name;
							$location_id = $record->location->id;
						}
						
						$activity_id = isset($record->activity_id) ? $record->activity_id : 'no tour record';
					?>
					<td>
						<a href="{{ route('tour.permalink', [$record->permalink]) }}">{{$record->title}}&nbsp;({{$activity_id}})</a>
						
						@if (isset($location_name))
							&nbsp;(<a href="/locations/tours/{{$location_id}}">{{$location_name}}</a>)
						@endif
							
						<?php if (intval($record->view_count) > 0) : ?>
							<span style="background-color: #4993FD;" class="badge">{{ $record->view_count }}</span>
						<?php endif; ?>
						
						<div>
							@if ($record->published_flag === 0)
								<a href="/entries/publish/{{$record->id}}"><button type="button" class="btn btn-danger btn-alert">Private</button></a>
							@elseif ($record->approved_flag === 0)
								<a href="/entries/publish/{{$record->id}}"><button type="button" class="btn btn-danger btn-alert">Pending Approval</button></a>
							@endif
							@if (!isset($record->location_id))
								<a class="" href="/entries/setlocation/{{$record->id}}"><button type="button" class="btn btn-danger btn-alert">Set Location</button></a>
							@endif
							@if (strlen($record->map_link) == 0)
								<a class="" href="/tours/edit/{{$record->id}}"><button type="button" class="btn btn-danger btn-alert">Set Map</button></a>
							@endif
							@if (!isset($record->photo))
								<a class="" href="/photos/entries/{{$record->id}}"><button type="button" class="btn btn-danger btn-alert">Set Main Photo</button></a>
							@endif
							@if (intval($record->photo_count) < 3)
								<a class="" href="/photos/entries/{{$record->id}}">
									<button type="button" class="btn btn-danger btn-alert">Add Photos
										<span style="margin-left:5px; font-size:.9em; font-weight:bold; background-color: white;" class="badge">{{ $record->photo_count }}
										</span>
									</button>
								</a>
							@endif
						</div>

					</td>
					<td>
						<a href='/tours/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a>
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	@else
		<h3>You need to log in. <a href="/login">Click here to login</a></h3>
	@endif       
</div>
@endsection
