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
		
		@if ($record->account->multiple_balances_flag == 1)
		<h3>Subtotals</h3>
		<div>
		<input type="text" name="subtotal_label1" class="form-control-inline"  value="{{$record->subtotal_label1}}" />
		<input type="text" name="subtotal1" id="subtotal1" onblur="setTotal()" class="form-control-inline" value="{{$record->subtotal1}}" />
		</div>

		<div>
		<input type="text" name="subtotal_label2" class="form-control-inline" value="{{$record->subtotal_label2}}" />
		<input type="text" name="subtotal2" id="subtotal2" onblur="setTotal()" class="form-control-inline" value="{{$record->subtotal2}}" />
		</div>
		
		<div>
		<input type="text" name="subtotal_label3" class="form-control-inline" value="{{$record->subtotal_label3}}" />
		<input type="text" name="subtotal3" id="subtotal3" onblur="setTotal()" class="form-control-inline" value="{{$record->subtotal3}}" />
		</div>
		
		<div>
		<input type="text" name="subtotal_label4" class="form-control-inline" value="{{$record->subtotal_label4}}" />
		<input type="text" name="subtotal4" id="subtotal4" onblur="setTotal()" class="form-control-inline" value="{{$record->subtotal4}}" />
		</div>
		
		<div>
		<input type="text" name="subtotal_label5" class="form-control-inline" value="{{$record->subtotal_label5}}" />
		<input type="text" name="subtotal5" id="subtotal5" onblur="setTotal()" class="form-control-inline" value="{{$record->subtotal5}}" />
		</div>
		<div>
		<input type="text" name="total_label" class="form-control-inline" value="TOTAL:" />	
		<input type="text" name="total" id="total" class="form-control-inline" value="{{$record->subtotal1 + $record->subtotal2 + $record->subtotal3 + $record->subtotal4 + $record->subtotal5}}" />
		</div>
		@endif		
		
		<div class="submit-button">
			<button type="submit" name="update" class="btn btn-primary">Update</button>
		</div>

		{{ csrf_field() }}
		
	</form>

</div>

@stop

<script>
function setTotal()
{
	var total = Number($('#subtotal1').val())
			  + Number($('#subtotal2').val())
			  + Number($('#subtotal3').val())
			  + Number($('#subtotal4').val())
			  + Number($('#subtotal5').val())
	;
		
	$('#total').val(total);
}
</script>
