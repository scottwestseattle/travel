@extends('layouts.app')

@section('content')

<?php
$now = new DateTime();
?>

<div class="page-size container">

	<form method="POST" action="/frontpage/visitors">

		@component('control-dropdown-date', ['months' => $dates['months'], 'years' => $dates['years'], 'days' => $dates['days'], 'filter' => $filter])@endcomponent
		<button type="submit" name="update" class="btn btn-primary" style="font-size:12px; padding:1px 4px; margin:5px;">Apply Date</button>

		<h1 style="font-size:1.3em;">Visitors ({{count($records)}}) ({{$now->format('Y-m-d H:i:s')}})</h1>
	
		<table class="table table-striped">
			<tbody>
				<tr><th><a href="/visitors/date">Timestamp</a></th><th>Page</th><th>IP</th><th>Referrer</th><th>User</th></tr>
				@foreach($records as $record)
				<?php
					// shorten the user_agent
					$agent = $record->user_agent;
					if (stripos($agent, 'Googlebot') !== FALSE)
						$agent = 'GoogleBot';
					else if (stripos($agent, 'bingbot') !== FALSE)
						$agent = 'BingBot';
					else if (stripos($agent, 'mediapartners') !== FALSE)
						$agent = 'AdSense';
					
				?>
				<tr>
					<td>{{$record->updated_at}}</td>
					@if (!isset($record->record_id))
					<td>{{$record->model}}/{{$record->page}}</td>
					@else
					<td>{{$record->model}}/{{$record->page}} (<a href="/entries/show/{{$record->record_id}}">{{$record->record_id}}</a>)</td>
					@endif
					<td><a target="_blank" href="https://whatismyipaddress.com/ip/{{$record->ip_address}}">{{$record->ip_address}}</a></td>
					<td>{{$record->referrer}}</td>
					<td>{{$agent}}</td>
					<td><a href='/entries/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a></td>
				</tr>
				@endforeach

			</tbody>
		</table>
		
		{{ csrf_field() }}
    </form>
</div>
@endsection
