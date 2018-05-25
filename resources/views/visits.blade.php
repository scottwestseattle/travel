@extends('layouts.app')

@section('content')

<div class="page-size container">
	<h1 style="font-size:1.3em;">Visitors ({{ count($records) }})</h1>
	@if (Auth::check())
		<table class="table table-striped">
			<tbody>
				<tr><th>Timestamp</th><th>Count</th><th>IP</th><th>Host</th><th>Referrer</th><th>Agent</th></tr>
				@foreach($records as $record)
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
