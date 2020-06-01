@extends('layouts.theme1')

@section('content')

<div class="container">

	@component($prefix . '.menu-submenu', ['prefix' => $prefix])@endcomponent
	
	<h1>Reconcile Accounts ({{isset($records) ? count($records) : 0}})</h1>

	<table class="table">
		<thead>
			<tr>
				<th></th>
				<th>Name</th>
				<th>Reconciled</th>
				<th>Statement</th>
				<th>Balance</th>
			</tr>
		</thead>
		<tbody>
		@if (isset($records))
			@foreach($records as $record)
			<tr>
				@if ($record->hidden_flag == 0)
				<td class="glyphCol"><a href='/accounts/view/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-eye-open"></span></a></td>
				@else
				<td class="glyphCol"><a href='/accounts/view/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-eye-close"></span></a></td>
				@endif
								
				<td><a href="/reconciles/account/{{$record->id}}">{{$record->name}}</a></td>
				<td>{{$record->reconcile_date}}</td>
				<td>{{$record->reconcile_statement_day > 0 ? $record->reconcile_statement_day . 'th' : 'End of Month' }}</td>
				<td>{{$record->balance}}</td>
			</tr>
			@endforeach
		@endif
		</tbody>
	</table>
               
</div>

@endsection
