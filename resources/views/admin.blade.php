@extends('layouts.app')

@section('content')

<div class="page-size container">
	<h1 style="">Admin Dashboard</h1>
	@if (Auth::check())
		<h2 style="">New Users</h2>
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
		
		<div style="height:30px;clear:both;"></div>
		<h2 style="">Visits</h2>
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
