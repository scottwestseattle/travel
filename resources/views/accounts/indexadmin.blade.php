@extends('layouts.theme1')

@section('content')

<div class="container">

	@component($prefix . '.menu-submenu', ['prefix' => $prefix])@endcomponent
	
	<h1>{{$titlePlural}} ({{count($records)}})</h1>

	<table class="table">
		<thead>
			<tr>
				<th></th>
				<th></th>
				<th></th>
				<th>Name</th>
				<th>Balance</th>
				<th>Notes</th>
				<th>Status</th>
				<th>Starting Balance</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		@if (isset($records))
			@foreach($records as $record)
			<tr>
				<td class="glyphCol"><a href='/transactions/transfer/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-transfer"></span></a></td>
				<td class="glyphCol"><a href='/{{$prefix}}/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
				<td class="glyphCol"><a href='/{{$prefix}}/view/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-eye-open"></span></a></td>
				
				<td><a href="/{{$prefix}}/view/{{$record->id}}">{{$record->name}}</a></td>
				<td>{{$record->balance}}</td>
				<td>{{$record->notes}}</td>
				<td>{{$record->hidden_flag ? 'Hidden' : 'Visible'}}</td>
				<td>{{$record->starting_balance}}</td>

				<td class="glyphCol"><a href='/{{$prefix}}/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a></td>
			</tr>
			@endforeach
		@endif
		</tbody>
	</table>
               
</div>

@endsection
