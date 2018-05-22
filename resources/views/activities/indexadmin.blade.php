@extends('layouts.app')

@section('content')

@if (false)
@component('menu-submenu')
	@component('menu-icons-start')@endcomponent
@endcomponent
@endif

<?php 

$header = 'Activites';
if (isset($title))
{
	$header = $title;
}

?>

<div class="page-size container">
	
	@guest
	@else
		@if (Auth::user()->user_type >= 100)
		<table><tr>			
			<td style="width:40px; font-size:20px;"><a href='/activities/index/'><span class="glyphCustom glyphicon glyphicon-list"></span></a></td>
			<td style="width:40px; font-size:20px;"><a href='/activities/add/'><span class="glyphCustom glyphicon glyphicon-plus-sign"></span></a></td>
		</tr></table>
		@endif
	@endguest
	
	<h1 style="font-size:1.3em;">{{ $header }} ({{ count($records) }})</h1>
	@if (Auth::check())
		<table class="table table-striped">
			<tbody>
			@foreach($records as $record)
				<tr>
					<td style="width:20px;"><a href='/activities/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
					<td style="width:20px;"><a href='/activities/location/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-map-marker"></span></a></td>
					<td style="width:20px;"><a href='/photos/tours/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-picture"></span></a></td>
					<?php 
						$location_name = null;
						$location_id = 0;
						if (isset($record->location_name)) // if coming from activities controller
						{
							$location_name = $record->location_name;
							$location_id = $record->location_id;
						}
						else if (isset($record->location)) // if coming from locations controller (showing by location)
						{
							$location_name = $record->location->name;
							$location_id = $record->location->id;
						}
					?>
					<td>
						<a href="{{ route('activity.view', [urlencode($record->title), $record->id]) }}">{{$record->title}}</a>
						@if (isset($location_name))
							&nbsp;(<a href="/locations/activities/{{$location_id}}">{{$location_name}}</a>)
						@endif
							
						<?php if (intval($record->view_count) > 0) : ?>
							<span style="color:#8CB7DD; margin-left: 5px; font-size:.9em;" class="glyphCustom glyphicon glyphicon-copy"><span style="font-family:verdana; margin-left: 2px;" >{{ $record->view_count }}</span></span>
						<?php endif; ?>
						
						@if ($record->published_flag === 0 || $record->approved_flag === 0 || !isset($location_name))
							<div>
							@if ($record->approved_flag === 0)
								<a href="/activities/publish/{{$record->id}}"><button type="button" class="btn btn-danger btn-alert">Pending Approval</button></a></li>
							@elseif ($record->published_flag === 0)
								<a href="/activities/publish/{{$record->id}}"><button type="button" class="btn btn-danger btn-alert">Private</button</a></li>
							@endif
							@if (!isset($location_name))
								<a class="" href="/activities/location/{{$record->id}}"><button type="button" class="btn btn-danger btn-alert">Set Location</button></a>
							@endif
							</div>
						@endif
					</td>
					<td>
						<a href='/activities/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a>
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
