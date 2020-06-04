@extends('layouts.theme1')

@section('content')

<div class="container">

	@component($prefix . '.menu-submenu', ['prefix' => $prefix])@endcomponent
	
	<form method="POST" id="form" action="/{{$prefix}}/index">	
		@component('control-dropdown-date', ['div' => true, 'months' => $dates['months'], 'years' => $dates['years'], 'filter' => $filter, 'formId' => 'form'])@endcomponent
		<button type="submit" name="update" class="btn btn-primary" style="font-size:12px; padding:1px 4px; margin:5px;">Submit</button>
		{{ csrf_field() }}		
	</form>
	
	<h1>{{$titlePlural}} @if (isset($records))({{count($records)}})@endif</h1>

	<table class="table">
		<thead>
			<tr>
				<th></th>
				<th>Reconciled</th>
				<th>Account</th>
				<th>Balance</th>
				<th>Notes</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		@if (isset($records))
			@foreach($records as $record)
			<tr>
				<td class="glyphCol"><a href='/{{$prefix}}/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
				
				<td>{{$record->reconcile_date}}</td>
				<td><a href="/reconciles/account/{{$record->account->id}}">{{$record->account->name}}</a></td>
				<td>{{$record->balance}}</td>
				<td>{{$record->notes}}</td>

				<td class="glyphCol"><a href='/{{$prefix}}/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a></td>
			</tr>
			@endforeach
		@endif
		</tbody>
	</table>
               
</div>

@endsection
