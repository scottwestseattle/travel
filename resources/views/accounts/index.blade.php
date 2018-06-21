@extends('layouts.theme1')

@section('content')

<div class="page-size container">
	
	@component('templates.menu-submenu', ['prefix' => $prefix])@endcomponent
	
	<h1>{{$titlePlural}} ({{count($records)}})</h1>

	<table class="table">
		<thead>
			<tr>
				<th>Name</th>
				<th>Starting Balance</th>
				<th>Account Type</th>
				<th>Hidden</th>
			</tr>
		</thead>
		<tbody>
		@if (isset($records))
			@foreach($records as $record)
			<tr>					
				<td><a href="/{{$prefix}}/view/{{$record->id}}">{{$record->name}}</a></td>
				<td><a href="/{{$prefix}}/view/{{$record->id}}">{{$record->starting_balance}}</a></td>
				<td><a href="/{{$prefix}}/view/{{$record->id}}">{{$record->account_type_flag}}</a></td>
				<td><a href="/{{$prefix}}/view/{{$record->id}}">{{$record->hidden_flag}}</a></td>
			</tr>
			@endforeach
		@endif
		</tbody>
	</table>
               
</div>
@endsection
