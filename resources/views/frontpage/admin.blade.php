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
				@if ($record->published_flag === 0 || $record->approved_flag === 0 || !isset($record->location_id) || strlen($record->map_link) == 0 || !isset($record->photo) || intval($record->photo_count) < 3)
				<tr>
					<td style="width:20px;"><a href='/activities/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
					<td style="width:20px;"><a href='/photos/tours/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-picture"></span></a></td>
					<td>
						<a href="{{ route('entry.permalink', [$record->permalink]) }}">{{$record->title}}</a>
													
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
						<a href='/activities/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a>
					</td>
				</tr>
				@endif
			@endforeach
			</tbody>
		</table>   	
		<a href="/activities/index">Show All Activities</a>	
	</div>
	<hr />
	
	<div style="height:30px;clear:both;"></div>
	
	<h3 style="">Latest Events ({{count($events)}})</h3>
	<table class="table table-striped">
		<tbody>
			<tr>
				<th>Timestamp</th>
				<th>Site</th>
				<th>Type</th>
				<th>Model</th>
				<th>Action</th>
				<th>Title</th>
			</tr>
		@foreach($events as $record)
			<?php
				$type = '';
				if ($record->type_flag == 1) $type = 'Info';
				if ($record->type_flag == 2) $type = 'Warning';
				if ($record->type_flag == 3) $type = 'Error';
				if ($record->type_flag == 4) $type = 'Exception';
				if ($record->type_flag == 5) $type = 'Other';
			?>
			
			<tr>
				<td>{{$record->created_at}}</td>
				<td>{{$record->site_id}}</td>
				<td>{{$type}}</td>
				<td>{{$record->model_flag}}</td>
				<td>{{$record->action_flag}}</td>
				<td>{{$record->title}}</td>
			</tr>
		@endforeach
		</tbody>
	</table>
	<a href="/events/index">Show All Events</a>
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
		<h3 style="">Latest Visitors ({{count($visitors)}})</h3>
		<p>My IP:&nbsp;{{$ip}}, New Visitor:&nbsp;{{$new_visitor ? 'Yes' : 'No'}}</p>
		<p><a href="/visitors">Show All Visits</a></p>
		<table class="table table-striped">
			<tbody>
				<tr><th>Timestamp</th><th>Count</th><th>IP</th><th>Host</th><th>Referrer</th><th>Agent</th></tr>
				@foreach($visitors as $record)
				<tr>
					<td>{{$record->updated_at}}</td>
					<td>{{$record->visit_count}}</td>
					<td><a target="_blank" href="https://whatismyipaddress.com/ip/{{$record->ip_address}}">{{$record->ip_address}}</a></td>
					<td>{{$record->host_name}}</td>
					<td>{{$record->referrer}}</td>
					<td>{{$record->user_agent}}</td>
					<td><a href='/entries/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a></td>
				</tr>
				@endforeach
			</tbody>
		</table>
	@else
		<h3>You need to log in. <a href="/login">Click here to login</a></h3>
	@endif       
</div>
@endsection
