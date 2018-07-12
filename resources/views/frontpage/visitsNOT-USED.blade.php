@extends('layouts.app')

@section('content')

<div class="page-size container">
	<h1 style="font-size:1.3em;">Visitors ({{ count($records) }})</h1>
	@if (Auth::check())
		<table class="table table-striped">
			<tbody>
				<tr><th><a href="/visitors/date">Timestamp</a></th><th>Model</th><th>Page</th><th>IP</th><th>Referrer</th></tr>
				@foreach($records as $record)
				<tr>
					<td>{{$record->updated_at}}</td>
					<td>{{$record->model}}</td>
					<td>{{$record->page}}</td>
					<td><a target="_blank" href="https://whatismyipaddress.com/ip/{{$record->ip_address}}">{{$record->ip_address}}</a></td>
					<td>{{$record->referrer}}</td>
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
