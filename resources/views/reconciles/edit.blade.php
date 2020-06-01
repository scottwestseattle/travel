@extends('layouts.theme1')

@section('content')

<div class="container">

	@component($prefix . '.menu-submenu', ['record' => $record, 'prefix' => $prefix])@endcomponent

	<h1>Edit Reconcile Record</h1>

	<form method="POST" action="/{{$prefix}}/update/{{$record->id}}">

		<h3>{{$record->account->name}}</h3>	

		<label for="month" class="control-label">Reconcile Date:</label>
		@component('control-dropdown-date', ['div' => true, 'months' => $dates['months'], 'years' => $dates['years'], 'days' => $dates['days'], 'filter' => $filter])@endcomponent

		<div class="form-group">
			<label for="starting_balance" class="control-label">Balance:</label>
			<input type="text" name="balance" class="form-control" value="{{$record->balance}}"></input>	
		</div>

		<div class="form-group clear">
			<label for="notes" class="control-label">Notes:</label>
			<input type="text" name="notes" class="form-control" value="{{$record->notes}}"></input>	
		</div>
		
		<div class="submit-button">
			<button type="submit" name="update" class="btn btn-primary">Update</button>
		</div>

		{{ csrf_field() }}
		
	</form>

</div>

@stop
