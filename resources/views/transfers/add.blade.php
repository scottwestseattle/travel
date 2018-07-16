@extends('layouts.theme1')

@section('content')

<div class="container">

	@component($prefix . '.menu-submenu', ['record' => $record, 'prefix' => $prefix])@endcomponent

	<h1>Transfer Between Account</h1>

	<form method="POST" action="/{{$prefix}}/create">

		@component('control-dropdown-date', ['div' => true, 'months' => $dates['months'], 'years' => $dates['years'], 'days' => $dates['days'], 'filter' => $filter])@endcomponent		
			
		<div style="font-size:1.4em;" class="form-group">
			<div>
				@component('control-dropdown-menu', ['prompt' => 'From Account:', 'field_name' => 'parent_id_from', 'options' => $accounts, 'selected_option' => $record->id])@endcomponent	
			</div>
			<div>
				@component('control-dropdown-menu', ['prompt' => 'To Account:', 'field_name' => 'parent_id_to', 'options' => $accounts, 'selected_option' => $record->id])@endcomponent	
			</div>
		</div>
		
		<div class="clear">		
			<label for="amount" class="control-label">Amount:</label>
			<input type="text" name="amount" class="form-control" value="{{$record->amount}}" />
			
			<label for="notes" class="control-label">Notes:</label>
			<input type="text" name="notes" class="form-control" value="{{$record->notes}}" />
		</div>	
			
		<div class="submit-button">
			<button type="submit" name="add" class="btn btn-primary">Transfer</button>
		</div>

		{{ csrf_field() }}
		
	</form>

</div>

@stop
