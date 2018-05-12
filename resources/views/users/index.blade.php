@extends('layouts.app')

@section('content')

<div class="page-size container">
	@if (Auth::check())
		<h1>Users</h1>
		<table class="table">
			<thead>
				<tr>
					<th></th><th>Name</th><th>Email</th><th>User Type</th>
				</tr>
			</thead>
			<tbody>@foreach($records as $record)
				<tr>
					<td style="width:10px;"><a href='/users/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
					<td><a href="/users/view/{{ $record->id }}">{{$record->name}}</a></td>
					<td>{{$record->email}}</td>
					<td>{{$record->user_type}}</td>
				</tr>
			@endforeach</tbody>
		</table>
	@endif               
</div>
@endsection
