@extends('layouts.theme1')

@section('content')

<div class="container">

	@component($prefix . '.menu-submenu', ['prefix' => $prefix])@endcomponent
	
	<h1>{{$titlePlural}} ({{isset($records) ? count($records) : 0}})</h1>

	<table class="table">
		<thead>
			<tr>
				<th></th>
				<th></th>
				<th></th>
				<th>Name</th>
				<th>Balance</th>
				<th>Notes</th>
				<th>Last Reconcile Date</th>
				<th>Starting Balance</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		@if (isset($records))
			@foreach($records as $record)
			<tr>
				<td class="glyphCol"><a href='/transfers/add/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-transfer"></span></a></td>
				
				@if ($record->hidden_flag == 0)
				<td class="glyphCol"><a href='/{{$prefix}}/view/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-eye-open"></span></a></td>
				@else
				<td class="glyphCol"><a href='/{{$prefix}}/view/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-eye-close"></span></a></td>
				@endif
				
				@if ($record->reconcile_flag == 1)
				<td class="glyphCol"><a href='/reconciles/account/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-check"></span></a></td>
				@else
				<td></td>
				@endif
				
				<td><a href="/transactions/show/account/{{$record->id}}">{{$record->name}}</a></td>
				<td>{{$record->balance}}</td>
				<td>{{$record->notes}}</td>
				<td>{{$record->reconcile_date}}</td>
				<td>{{intval($record->starting_balance) > 0 ? $record->starting_balance : ''}}</td>

				<td class="glyphCol"><a href='/{{$prefix}}/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
				<td class="glyphCol"><a href='/{{$prefix}}/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a></td>
			</tr>
			@endforeach
		@endif
		</tbody>
	</table>
               
</div>

@endsection
