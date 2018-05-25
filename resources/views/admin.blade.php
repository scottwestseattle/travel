@extends('layouts.app')

@section('content')

<div class="page-size container">
	<h2 style="">Admin Dashboard</h2>
	
	@if (Auth::check())
		
	<div xstyle="border: 1px solid gray">
		<h3>Pending Activities ({{ count($records) }})</h3>
		<table class="table table-striped">
			<tbody>
			@foreach($records as $record)
				<tr>
					<td style="width:20px;"><a href='/activities/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
					<td style="width:20px;"><a href='/photos/tours/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-picture"></span></a></td>
					<td>
						<a href="{{ route('activity.view', [urlencode($record->title), $record->id]) }}">{{$record->title}}</a>
							
						<?php if (intval($record->view_count) > 0) : ?>
							<span style="color:#8CB7DD; margin-left: 5px; font-size:.9em;" class="glyphCustom glyphicon glyphicon-copy"><span style="font-family:verdana; margin-left: 2px;" >{{ $record->view_count }}</span></span>
						<?php endif; ?>
						
						<div>
							@if ($record->published_flag === 0)
								<a href="/activities/publish/{{$record->id}}"><button type="button" class="btn btn-danger btn-alert">Private</button></a></li>
							@elseif ($record->approved_flag === 0)
								<a href="/activities/publish/{{$record->id}}"><button type="button" class="btn btn-danger btn-alert">Pending Approval</button></a></li>
							@endif
							@if (!isset($record->location_id))
								<a class="" href="/activities/location/{{$record->id}}"><button type="button" class="btn btn-danger btn-alert">Set Location</button></a>
							@endif
							@if (strlen($record->map_link) === 0)
								<a class="" href="/activities/edit/{{$record->id}}"><button type="button" class="btn btn-danger btn-alert">Set Map</button></a>
							@endif
						</div>
					</td>
					<td>
						<a href='/activities/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a>
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>   	
		<a href="/activities/index">Show All Activities</a>	
	</div>
	<hr />
	
		<div style="height:30px;clear:both;"></div>
	
		<h3 style="">New Users ({{count($users)}})</h3>
		<table class="table table-striped">
			<tbody>
				<tr><th></th><th>Created</th><th>Name</th><th>Email</th><th>Type</th><th></th></tr>
			@foreach($users as $record)
				<tr>
					<td style="width:10px;"><a href='/users/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
					<td>{{$record->created_at}}</td>
					<td><a href="/users/view/{{ $record->id }}">{{$record->name}}</a></td>
					<td>{{$record->email}}</td>
					<td>{{$record->user_type}}</td>
					<td><a href='/entries/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a></td>
				</tr>
			@endforeach
			</tbody>
		</table>
		<a href="/users/index">Show All Users</a>
		<hr />
		
		<div style="height:30px;clear:both;"></div>
		<h3 style="">Visitor IPs ({{count($visits)}})</h3>
		<table class="table table-striped">
			<tbody>
			<tr><th>Count</th><th>IP</th><th>Host</th></tr>
			@foreach($visits as $record)
				<tr>
					<td>{{$record->total}}</td>
					<td><a target="_blank" href="https://whatismyipaddress.com/ip/{{$record->title}}">{{$record->title}}</a></td>
					<td>{{$record->description}}</td>
				</tr>
			@endforeach
			</tbody>
		</table>
		<a href="/visits">Show All Visits</a>
	@else
		<h3>You need to log in. <a href="/login">Click here to login</a></h3>
	@endif       
</div>
@endsection
