@extends('layouts.app')

@section('content')

@php
$debug = (isset($_COOKIE['debug']) && $_COOKIE['debug']);
@endphp

<div class="page-size container">
	<!-- h2 style="">Admin Dashboard</h2 -->

	<div style="text-align: center; margin: 10px 0 20px 0; max-width:500px;">
		<div class="drop-box green" style="line-height:100%; vertical-align:middle; border-radius: 10px; padding:5px; color: white;" >
			<h3>Server</h3>
			<div style="margin-bottom:10px;">{{date("F d, Y")}}&nbsp;&nbsp;{{date("H:i:s")}}</div>
			
			@if ($debug)
				<div style="margin-bottom:10px; font-size:.8em;">{{$site->site_name}} (SITE_ID={{$site->id}} {{DATABASE}})</div>
				<div style="margin-bottom:10px; font-size:.8em;">{{base_path()}}</div>
				<div style="margin-bottom:10px; font-size:.8em;">Geo Load Time: {{$geoLoadTime}}</div>
			@endif
			
			<h4>Client</h4>
			@if ($geo->isValid())
				<div style="font-size:12px; margin-bottom:10px;">{{$geo->ip() . ' (' . $geo->location() . ' ' . $geo->locale() . ' ' . $geo->language() . ')'}}</div>
			@else
				<div style="font-size:12px; margin-bottom:10px;">{{$geo->ip()}} <span style=""><strong>({{ip2long($geo->ip())}})</strong></span></div>
				<div style="font-size:12px; margin-bottom:10px;">INVALID IP / NO GEO</div>
			@endif
			<div style="margin-bottom:20px;"><img height="40" src="{{$geo->flag()}}" title="{{$geo->location()}}" alt="{{$geo->location()}}" /></div>
			
			@if ($ignoreErrors)
				<div style="margin-bottom:10px;"><strong>IGNORING ERRORS FOR TESTING!</strong></div>
			@endif
			
			<div style=" margin: 20px 0;">
				@if ($debug)
					<a class="btn-sm btn-danger" href="/d-e-b-u-g">DEBUG OFF</a>
				@else
					<a class="btn-sm btn-primary" href="/d-e-b-u-g" role="button">Debug</a>
				@endif
				<a class="btn-sm btn-primary" href="/importgeo" role="button">Geo</a>
				<a class="btn-sm btn-primary" href="/first" role="button">Sliders</a>
				<a class="btn-sm btn-primary" href="/hash" role="button">Hash</a>
				<a class="btn-sm btn-primary" href="/email/check/1" role="button">Email</a>
				<a class="btn-sm btn-primary" target="_blank" href="https://drive.google.com/drive/folders/1NVBE6-ijWCdZq8vrBUrnCpZ39dlHmaK_?usp=sharing" role="button">Files</a>
				
			</div>
		</div>
	</div>

	@if (isset($stockQuotes['quotes']) && count($stockQuotes['quotes']) > 0)
		<div style="clear:both;"></div>
		<div class="" style="margin-bottom: 0px;" >
			<h3>Quotes <span style="font-size:.7em">({{$stockQuotes['quoteMsg']}})</span></h3>

			@foreach($stockQuotes['quotes'] as $quote)	
			@php
				if ($stockQuotes['isOpen'])
					$color = $quote['up'] ? 'blue' : 'red';
				else
					$color = 'darkGray';
			@endphp
			<div class="drop-box text-center number-box {{$color}}">
				<div><span style="font-size:1.2em; font-weight:bold;">{{($quote['symbol'])}}</span></div>
				<div style="font-size:10px; margin-top:0px;">{{$quote['nickname']}}</div>
				<p style="font-size:{{$quote['font-size']}}; margin-top:5px;">{{number_format($quote['price'], 2)}}</p>
				<p style="font-size:11px; font-weight:normal; color:{{'default'}};">{{$quote['change']}}</p>
			</div>			
			@endforeach	
			<div style="clear:both;"></div>
			<div class="" style="font-size:.9em;"><a href="/transactions/positions">Show Positions</a></div>	
		</div>
	@endif
	
	@if ($accountReconcileOverdue > 0)	
		<div class="alert alert-danger text-center" style="max-width:350px; font-size:1.1em; margin-top:15px;" role="alert">
		  <div style="margin-bottom:10px;"><b>{{$accountReconcileOverdue}} account(s)</b> are due to be reconciled</div>
		  <div><a class="btn btn-danger btn-sm" href="/reconciles" role="button">Reconcile</a></div> 
		</div>
	@endif

	<div style="">
		<h3 style="margin-top: 15px;">Visitors</h3>
		<?php
			// if too many visitors then have to scale down the font size
			$style = $visitorsTotal >= 1000 ? 'font-size:1.8em; margin-top:5px;' : '';
		?>
		<div class="drop-box text-center number-box blue">
			<div>Total</div>
			<p style="{{$style}}">{{$visitorsTotal}}</p>
		</div>	

		<div class="drop-box text-center number-box orange">
			<div>Unique</div>
			<p style="{{$style}}">{{count($visitors)}}</p>
		</div>

		<div class="drop-box text-center number-box green">
			<div style="margin-bottom: 5px;">Newest<span style="font-size:8px;"> of {{count($visitorCountryInfo['countries'])}}</span></div>
			<a href="/visitors/countries"><img height="45" src="/img/flags/{{$visitorCountryInfo['newestCountryCode']}}.png" 
				title="{{$visitorCountryInfo['newestCountry']}}" 
				alt="{{$visitorCountryInfo['newestCountry']}}" /></a>
		</div>
	</div>


	<div style="clear: both;"></div>

	@if (isset($shortEntries) and count($shortEntries) > 0)
	<div>
		<h3 style="color:red;">Unfinished Entries ({{count($shortEntries)}})</h3>
		<table class="table">
			<tbody>
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
	

	@if (isset($trx) && count($trx) > 0)
	<div>	
		<h3 style="color:red;">Unfinished Transactions ({{count($trx)}})</h3>
		<table class="table table-striped">
			<tbody>
				<tr><th></th><th>Date</th><th>Transaction</th></tr>
				@foreach($trx as $record)
					<tr>
						<td style="width:10px;"><a href='/transactions/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
						<td style="width:150px;">{{date("M d, Y", strtotime($record->created_at))}}</td>
						<td><a href="/transactions/edit/{{ $record->id }}">{{$record->description}}</a></td>
					</tr>
				@endforeach
			</tbody>
		</table>
		<a href="/transactions">Show All Transactions</a>
	</div>
	<hr />
	@endif
	
	@if (isset($comments))
	<div>	
		<h3 style="color:red;">Comments to Approve ({{count($comments)}})</h3>
		<table class="table table-striped">
			<tbody>
				<tr><th></th><th>Date</th><th>Entry</th><th>Name</th><th>Comment</th><th></th></tr>
				@foreach($comments as $record)
					<tr>
						<td style="width:10px;"><a href='/comments/publish/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-flash"></span></a></td>
						<td>{{date("M d, Y", strtotime($record->created_at))}}</td>
						<td><a href="/entries/show/{{$record->parent_id}}">{{$record->parent_id}}<a></td>
						<td><a href="/comments/publish/{{ $record->id }}">{{$record->name}}</a></td>
						<td>
							{{$record->comment}}
							@if (isset($record->visitor))
								<br/>({{$record->visitor->ip_address}} / {{$record->visitor->country}})
							@endif						
						</td>
						<td><a href='/comments/delete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a></td>
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
		<h3>Today's Visitors <span style="font-size:12px;">({{$visitorCountryInfo['totalCountriesToday']}} countries)</span></h3>
		<p>
		@foreach($visitorCountryInfo['countriesToday'] as $country)
			<div style="display:inline-block; min-width:45px;">
				<img style="margin: 0 5px 5px 0;" height="30" src="/img/flags/{{strtolower($country->countryCode)}}.png" 
					alt="@LANG('geo.' . $country->country)" 
					title="@LANG('geo.' . $country->country)" />
			</div>
		@endforeach		
		</p>
		<div style="clear: both; height:0px;"></div>
		<div style="font-size:.7em;">			
			<table class="table table-striped mt-10">
				<tr><th>Date</th><th>Country</th><th>IP</th></tr>
				<tbody>
				@foreach($visitors as $record)
					<tr>
						<td>{{$record['date']}}</td>
						<td style="font-size:1.3em;">{!!$record['location']!!} ({{$record['count']}})</td>
						<td>{{$record['ip']}}</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		</div>
		
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
	
	<?php $bannerIndex = isset($bannerIndex) ? $bannerIndex : 11; ?>
	<div class="text-center" style="padding: 25px 10px 0 10px; margin:20px 0;">
	<a href="https://www.booking.com/index.html?aid=1535308" target="_blank" >
		<div style="margin:auto; border: solid 1px #0f367c; line-height:75px; height:75px; width:100%; max-width:500px; 
			background-image:url('/img/banners/banner-booking-fp{{$bannerIndex}}.png');">
			<div style="text-align: right;">
				<a style="margin: 5px 5px 0 0; vertical-align: top; background-color:#0f367c; color:white;" class="btn btn-info" 
					href="https://www.booking.com/index.html?aid=1535308" target="_blank" role="button">
					<div style="font-size:11px">@LANG('ads.Explore the world with')</div>
					<div style="font-size:18px">Booking<span style="color:#449edd">@LANG('ads..com')</span></div>
				</a> 
			</div>
		</div>
	</a>
	</div>	
	
	<div>
		<h3 style="">Latest Events ({{count($events)}})</h3>
		@component('menu-submenu-events-filter')@endcomponent
		
		<divd style="font-size:.7em;">	
		<table class="table table-striped">
			<tbody>
				<tr>
					<th>Timestamp</th>
					<th>Page</th>
					<th>Msg</th>
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
					<td>{{$record->model_flag}}/{{$record->action_flag}}</td>
					@if (isset($record->error) && strtolower(substr($record->error, 0, 4)) == 'http')
						<td><a href="{{$record->error}}">{{$record->title}}</a>
						@if (isset($record->record_id))
						&nbsp;<a href="/photos/edit/{{$record->record_id}}"><span class="glyphCustom glyphicon glyphicon-edit"></span></a>
						@endif						
						</td>
					@elseif (isset($record->description) && strtolower(substr($record->description, 0, 4)) == 'http')
						<t>{{$record->title}} ({{$record->description}})</td>
					@else
						<td>{{$record->title}}</td>
					@endif
				</tr>
			@endforeach
			</tbody>
		</table>
		</div>

		<a href="/events/index/">Show All Events</a>
	</div>
	<hr />
	
</div>
@endsection
