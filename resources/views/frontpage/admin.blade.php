@extends('layouts.app')

@section('content')

<div class="page-size container">
	<!-- h2 style="">Admin Dashboard</h2 -->

	<div style="text-align: center; margin: 20px 0; max-width:500px;">
		<div class="drop-box green" style="line-height:100%; vertical-align:middle; border-radius: 10px; padding:5px; color: white;" >
			<h3>Server</h3>
			<div style="margin-bottom:10px;">{{date("F d, Y - H:i:s")}}</div>
			<div style="margin-bottom:10px;">{{$site->site_name}} (id={{$site->id}})</div>
			<div style="margin-bottom:10px; font-size:.8em;">{{base_path()}}</div>
			
			@if (isset($_COOKIE['debug']) && $_COOKIE['debug'])
				<div style=" margin: 20px 0;">
					<a style="color:red; font-size:1.2em; font-weight:bold;" href="/d-e-b-u-g">TURN DEBUG OFF</a>&nbsp;&nbsp;|&nbsp;
					<a style="color:white;" href="/debugtest">Test</a>
				</div>
			@else
				<div style=" margin: 20px 0;">
					<a style="color:white;" href="/d-e-b-u-g">Turn Debug On</a>&nbsp;&nbsp;|&nbsp;
					<a style="color:white;" href="/debugtest">Test</a>&nbsp;&nbsp;|&nbsp;
					<a style="color:white;" href="/about">About</a>
				</div>
			@endif
			
		</div>
	</div>	
	
	<div style="text-align: center; margin: 10px 0 20px 0; max-width:500px;">
		<div class="drop-box darkBlue" style="line-height:100%; vertical-align:middle; border-radius: 10px; padding:5px; color: white;" >
			<h3>Client</h3>
			<div style="margin-bottom:10px;">{{$ip}}</div>
			<div style="margin-bottom:10px;">{{$ipLocation['location']}}</div>
			<div style="margin-bottom:20px;"><img height="{{$ipLocation['flagSize']}}" src="{{$ipLocation['flag']}}" /></div>
			<div style="margin-bottom:20px;">
				<a style="color:white;" href="/expedia">Expedia</a>
				&nbsp;&nbsp;<a style="color:white;" href="/travelocity">Travelocity</a>
				&nbsp;&nbsp;<a style="color:white;" href="/eunoticereset">EU Notice</a>
				&nbsp;&nbsp;<a style="color:white;" href="/hash"><span class="glyphCustom glyphicon glyphicon-sunglasses"></span></a>
			</div>

		</div>
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
		<h3 style="color:red;">Unfinished Entries ({{count($shortEntries)}})</h3>
		<table class="table table-striped">
			<tbody>
				<tr><th></th><th></th><th>Entry</th><th>Type</th>
			@foreach($shortEntries as $record)
				<tr>				
					<td style="width:10px;"><a href='/entries/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
					<td style="width:10px;"><a href='/entries/publish/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-flash"></span></a></td>
					<td><a href='/entries/{{$record->permalink}}'>{{$record->title}}</a></td>
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
		
	<div style="margin-bottom:20px;">
		<h3>Today's Visitors:</h3>
			
		<div class="drop-box text-center number-box blue" style="">
			<div>Total</div>
			<p style="">{{count($visitors)}}</p>
		</div>	

		<div class="drop-box text-center number-box orange" style="">
			<div>Unique</div>
			<p style="">{{count($visitorsUnique)}}</p>
		</div>

		<div class="drop-box text-center number-box darkGray" style="">
			<div style="margin-bottom: 5px;">Newest</div>
			<img height="45" src="/img/flags/{{$visitorCountryInfo['newestCountryCode']}}.png" />
		</div>

		<div style="clear: both; height:20px;"></div>	
		
		<table class="table table-striped mt-10">
			<tbody>
			@foreach($visitorsUnique as $record)
				<tr>
					<td>{{$record['date']}}</td>
					<td>{{$record['ip']}}</td>
					<td>{!!$record['location']!!}</td>
				</tr>
			@endforeach
			</tbody>
		</table>
		<p><a href="/visitors">Show All Visitors</a></p>
		
	</div>
	@endif
	
	@if (false && count($users) > 0)
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
					@if (isset($record->error) && strtolower(substr($record->error, 0, 4)) == 'http')
						<td><a href="{{$record->error}}">{{$record->title}}</a>
						@if (isset($record->record_id))
						&nbsp;<a href="/photos/edit/{{$record->record_id}}"><span class="glyphCustom glyphicon glyphicon-edit"></span></a>
						@endif						
						</td>
					@elseif (isset($record->description) && strtolower(substr($record->description, 0, 4)) == 'http')
						<td>{{$record->title}} ({{$record->description}})</td>
					@else
						<td>{{$record->title}}</td>
					@endif
				</tr>
			@endforeach
			</tbody>
		</table>

		<a href="/events/index/">Show All Events</a>
	</div>
	<hr />
	
</div>
@endsection
