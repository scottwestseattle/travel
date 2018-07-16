@extends('layouts.theme1')

@section('content')

<div class="container">

	@component($prefix . '.menu-submenu', ['prefix' => $prefix])@endcomponent

	<h1>Edit {{$title}}</h1>

	<form method="POST" action="/{{$prefix}}/update/{{$recordFrom->id}}">

		<input type="hidden" name="recordFrom" value="{{ $recordFrom }}">
		<input type="hidden" name="recordTo" value="{{ $recordTo }}">
	
		@component('control-dropdown-date', ['div' => true, 'months' => $dates['months'], 'years' => $dates['years'], 'days' => $dates['days'], 'filter' => $filter])@endcomponent		
						
		<div style="font-size:1.4em;" class="form-group">
			<div>
				@component('control-dropdown-menu', ['prompt' => 'From Account:', 'field_name' => 'parent_id_from', 'options' => $accounts, 'selected_option' => $recordFrom->parent_id])@endcomponent	
			</div>
			<div>
				@component('control-dropdown-menu', ['prompt' => 'To Account:', 'field_name' => 'parent_id_to', 'options' => $accounts, 'selected_option' => $recordTo->parent_id])@endcomponent	
			</div>
		</div>
										
		<div class="clear">		
			<label for="amount" class="control-label">Amount:</label>
			<input type="text" name="amount" class="form-control" value="{{$recordFrom->amount}}" />
			
			<label for="notes" class="control-label">Notes:</label>
			<input type="text" name="notes" class="form-control" value="{{$recordFrom->notes}}" />			
		</div>
			
		<div class="form-group">
			<input type="checkbox" name="reconciled_flag" id="reconciled_flag" class="form-control-inline" value="{{$recordFrom->reconciled_flag }}" {{ ($recordFrom->reconciled_flag) ? 'checked' : '' }} />
			<label for="reconciled_flag" class="checkbox-label">Reconciled</label>
		</div>		
			
		<div class="submit-button">
			<button type="submit" name="update" class="btn btn-primary">Update</button>
		</div>

		{{ csrf_field() }}
		
	</form>

</div>

@stop
