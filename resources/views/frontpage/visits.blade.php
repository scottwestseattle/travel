@extends('layouts.app')

@section('content')

<?php
$now = new DateTime();
?>

<div class="page-size container">

	<form method="POST" action="/frontpage/visitors">

		<div class="submenu-view">
			@component('control-dropdown-date', ['months' => $dates['months'], 'years' => $dates['years'], 'days' => $dates['days'], 'filter' => $filter])@endcomponent
			<button type="submit" name="update" class="btn btn-primary" style="font-size:12px; padding:1px 4px; margin:5px;">Apply Date</button>
		</div>
		
		<div style="padding:10px 0 0 20px;">
			<input style="width:20px;" type="checkbox" name="showbots" id="showbots" class="form-control-inline" {{ $filter['showBots'] ? 'checked' : '' }} />
			<label for="showbots" class="checkbox-label">Include Bots</label>
			
			<input style="width:20px;" type="checkbox" name="showall" id="showall" class="form-control-inline" {{ $filter['showAll'] ? 'checked' : '' }} />
			<label for="showall" class="checkbox-label">Don't Group IPs</label>
		</div>
		
		<h1 style="font-size:1.3em;">Visitors ({{count($records)}}) ({{$now->format('Y-m-d H:i:s')}})</h1>
	
		<table class="table table-striped">
			<tbody>
				<tr><th>Timestamp</th><th>Page</th><th>IP</th><th>Referrer</th><th>User</th><th>Host</th></tr>
				@foreach($records as $record)
				<tr>
					<td>{{$record['date']}}</td>
					
					@if (!isset($record['record_id']))
						<td>{{$record['model']}}/{{$record['page']}}</td>
					@else
						<td>{{$record['model']}}/{{$record['page']}} (<a href="{{$record['url']}}/{{$record['record_id']}}">{{$record['record_id']}}</a>)</td>
					@endif
						
					<td>
						<a target="_blank" href="https://whatismyipaddress.com/ip/{{$record['ip']}}">{{$record['ip']}}
						@if ($record['count'] > 0)
							({{$record['count']}})
						@endif
						</a>
						
						@if (isset($record['location']) && strlen($record['location']) > 0)
							<br/><a target="_blank" href="https://www.google.com/maps/place/{{$record['location']}}">
							<span style="font-size:.7em;">{!!$record['location']!!}</span></a>
						@else
							<a href="/visitors/setlocation/{{$record['id']}}"><span class="glyphCustom glyphicon glyphicon-map-marker"></span></a>
						@endif
					</td>
					
					<td><a target="_blank" href="{{$record['ref']}}">{{$record['ref']}}</a></td>
					<td>{{$record['agent']}}</td>
					<td>{{$record['host']}}</td>
				</tr>
				@endforeach

			</tbody>
		</table>
		
		{{ csrf_field() }}
    </form>
</div>
@endsection
