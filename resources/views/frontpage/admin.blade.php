@extends('layouts.app')

@section('content')

<div class="page-size container">
	<h2 style="">Admin Dashboard</h2>

	<div style="margin-bottom:40px;">
		<ul style="font-size: 1.1em; list-style-type: none; padding-left: 0px;">
			<li>Time: {{date("Y-m-d H:i:s")}}</li>
			<li>Site: {{$site->site_name}}, id: {{$site->id}}</li>
			<li>My IP:&nbsp;{{$ip}}</li>
			<li>New Visitor:&nbsp;{{$new_visitor ? 'Yes' : 'No'}}
				&nbsp;&nbsp;<a href="/expedia">Expedia</a>
				&nbsp;&nbsp;<a href="/travelocity">Travelocity</a>
				&nbsp;&nbsp;<a href="/hash">Hash</a>				
			</li>
		</ul>
	</div>
	
	@if (isset($comments))
	<div>	
		<h3 style="color:red;">Comments to Approve ({{count($comments)}})</h3>
		<table class="table table-striped">
			<tbody>
				<tr><th></th><th>Created</th><th>Name</th><th>Comment</th><th></th></tr>
				@foreach($comments as $record)
					<tr>
						<td style="width:10px;"><a href='/comments/publish/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-flash"></span></a></td>
						<td>{{$record->created_at}}</td>
						<td><a href="/comments/publish/{{ $record->id }}">{{$record->name}}</a></td>
						<td>{{$record->comment}}</td>
						<td><a href='/comments/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a></td>
					</tr>
				@endforeach
			</tbody>
		</table>
		<a href="/comments/indexadmin">Show All Comments</a>
	</div>
	<hr />
	@endif
	
	@if (isset($linksToFix) && count($linksToFix) > 0)
	<div>	
		<h3 style="color:red;">Links to Fix ({{count($linksToFix)}})</h3>
		<table class="table table-striped">
			<tbody>
				<tr><th></th><th>Entry</th><th>Created Date</th><th>Type</th>
			@foreach($linksToFix as $record)
				<tr>				
					<td style="width:10px;"><a href='/entries/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
					<td><a href='/entries/{{$record->permalink}}'>{{$record->title}}</a></td>
					<td>{{$record->created_at}}</td>
					<td>{{$record->type_flag}}</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>
	<hr />
	@endif

	@if (isset($shortEntries) and count($shortEntries) > 0)
	<div>
		<h3 style="color:red;">Short Entries ({{count($shortEntries)}})</h3>
		<table class="table table-striped">
			<tbody>
				<tr><th></th><th>Entry</th><th>Created Date</th><th>Type</th>
			@foreach($shortEntries as $record)
				<tr>				
					<td style="width:10px;"><a href='/entries/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
					<td><a href='/entries/{{$record->permalink}}'>{{$record->title}}</a></td>
					<td>{{$record->created_at}}</td>
					<td>{{$entryTypes[$record->type_flag]}}</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>
	<hr />
	@endif

	@if (false && isset($linksToTest) && count($linksToTest) > 0)
	<div>	
		<h3 style="color:red;">Links to Test ({{count($linksToTest)}})</h3>
		<table class="table table-striped">
			<tbody>
				<tr><th></th><th>Entry</th><th>Created Date</th><th>Type</th>
			@foreach($linksToTest as $record)
				<tr>				
					<td style="width:10px;"><a href='/entries/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
					<td><a href='/entries/{{$record->permalink}}' target="_blank">{{$record->title}}</a></td>
					<td>{{$record->created_at}}</td>
					<td>{{$record->type_flag}}</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>
	<hr />
	@endif
	
	@if (isset($todo) && count($todo) > 0)
	<div>	
		<h3 style="color:red;">Photo Names to Fix ({{count($todo)}})</h3>
		<table class="table table-striped">
			<tbody>
				<tr><th></th><th></th><th>File Name</th><th>Created Date</th><th>Entry</th>
			@foreach($todo as $record)
				<tr>				
					<td style="width:10px;"><a href='/photos/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
					<td><a href='/photos/entries/{{$record->parent_id}}'><span class="glyphCustom glyphicon glyphicon-eye-open"></span></a></td>
					<td>{{$record->filename}}</td>
					<td>{{$record->date}}</td>
					<td><a href='/entries/{{$record->permalink}}'>{{$record->entry_title}}</a></td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>
	<hr />
	@endif
	
	@if (isset($visitors))			
	<div style="margin-bottom:50px;">
		<h3 style="">Today's Visitors: {{count($visitors)}}</h3>
		<p><a href="/visitors">Show All Visitors</a></p>
	</div>
	@endif
	
	@if (count($users) > 0)
	<div>	
		<h3 style="">Last New User ({{count($users)}} Total)</h3>
		<table class="table table-striped">
			<tbody>
			@foreach($users as $record)
				<tr>
					<td style="width:10px;"><a href='/users/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
					<td>{{$record->created_at}}</td>
					<td><a href="/users/view/{{ $record->id }}">{{$record->name}}</a></td>
					<td>{{$record->email}}</td>
					<td>{{$record->user_type}}</td>
					<td><a href='/entries/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a></td>
				</tr>
				@break
			@endforeach
			</tbody>
		</table>
		<a href="/users/index">Show All Users</a>
	</div>
	<hr />
	@endif
		
	@if (isset($posts) && count($posts) > 0)
	<div>
		<h3>Pending Blog Entries ({{ count($posts) }})</h3>
		<table class="table table-striped">
			<tbody>
			@foreach($posts as $record)
				@if ($record->published_flag === 0 || $record->approved_flag === 0 || !isset($record->location_id) || strlen($record->map_link) == 0 || !isset($record->photo) || intval($record->photo_count) < 3)
				<tr>
					<td style="width:20px;"><a href='/entries/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
					<td style="width:20px;"><a href='/photos/entries/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-picture"></span></a></td>
					<td style="width:20px;"><a href='/entries/publish/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-flash"></span></a></td>
					<td>
						<a href="{{ route('entry.permalink', [$record->permalink]) }}">{{$record->title}}</a>
																			
						<div>
							@if ($record->published_flag === 0)
								<a href="/entries/publish/{{$record->id}}"><button type="button" class="btn btn-danger btn-alert">@LANG('ui.Private')</button></a>
							@endif
							@if ($record->approved_flag === 0)
								<a href="/entries/publish/{{$record->id}}"><button type="button" class="btn btn-danger btn-alert">@LANG('ui.Pending Approval')</button></a>
							@endif
						</div>
					</td>
					<td>
						<a href='/entries/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a>
					</td>
				</tr>
				@endif
			@endforeach
			</tbody>
		</table>   	
		<a href="/entries/indexadmin/{{ENTRY_TYPE_BLOG_ENTRY}}">Show All Blog Posts</a>	
	</div>
	<hr />	
	@endif
	
	@if (count($records))
	<div>
		<h3>Pending Tour Information ({{ count($records) }})</h3>
		<table class="table table-striped">
			<tbody>
			@foreach($records as $record)
				<tr>
					<td style="width:20px;"><a href='/activities/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
					<td style="width:20px;"><a href='/photos/entries/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-picture"></span></a></td>
					<td>
						<a href="{{ route('entry.permalink', [$record->permalink]) }}">{{$record->title}}</a>
													
						<?php if (intval($record->view_count) > 0) : ?>
							<span style="background-color: #4993FD;" class="badge">{{ $record->view_count }}</span>
						<?php endif; ?>						
						
						<div>
							@if ($record->published_flag === 0)
								<a href="/entries/publish/{{$record->id}}"><button type="button" class="btn btn-danger btn-alert">@LANG('ui.Private')</button></a>
							@endif
							@if ($record->approved_flag === 0)
								<a href="/entries/publish/{{$record->id}}"><button type="button" class="btn btn-danger btn-alert">@LANG('ui.Pending Approval')</button></a>
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
								<a class="" style="" href="/photos/entries/{{$record->id}}">
									<button type="button" class="btn btn-danger btn-attention">Add Photos
										<span style="margin-left:5px; font-size:.9em; font-weight:bold; color: gray;" class="badge">{{ $record->photo_count }}</span>
									</button>
								</a>
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
	@endif
	
	<div>
		<h3 style="">Latest Events ({{count($events)}})</h3>
		@component('menu-submenu-events-filter')@endcomponent	
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
		<a href="/events/index/">Show All Events</a>
	</div>
	<hr />
	
</div>
@endsection
