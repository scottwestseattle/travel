@extends('layouts.theme1')

@section('content')

<div class="container">

	@component($prefix . '.menu-submenu', ['record' => $record, 'prefix' => $prefix])@endcomponent

	<h1>Edit {{$title}}</h1>

	<form method="POST" action="/{{$prefix}}/update/{{$record->id}}">

		@component('control-dropdown-date', ['months' => $dates['months'], 'years' => $dates['years'], 'days' => $dates['days'], 'filter' => $filter])@endcomponent		
	
		<label for="description" class="control-label">Description:</label>
		<input type="text" name="description" class="form-control" value="{{$record->description}}"></input>
					
		@component('control-dropdown-menu', ['prompt' => 'Account:', 'field_name' => 'parent_id', 'options' => $accounts, 'selected_option' => $record->parent_id])@endcomponent	
		
		@component('control-dropdown-menu', ['prompt' => 'Category:', 'field_name' => 'category_id', 'options' => $categories, 'selected_option' => $record->category_id, 'onchange' => 'onCategoryChange(this.value)'])@endcomponent				

		@component('control-dropdown-menu', ['prompt' => 'Subcategory:', 'field_name' => 'subcategory_id', 'options' => $subcategories, 'selected_option' => $record->subcategory_id])@endcomponent									
					
		<div class="form-group">		
			<div class="radio-group-item">
				<input type="radio" name="type_flag" value="1" class="form-control-inline" {{$record->type_flag == 1 ? 'checked' : '' }} />
				<label for="type_flag" class="radio-label">Debit</label>
			</div>
			
			<div class="radio-group-item">
				<input type="radio" name="type_flag" value="2" class="form-control-inline" {{$record->type_flag == 2 ? 'checked' : '' }} />
				<label for="type_flag" class="radio-label">Credit</label>			
			</div>	
		</div>
			
		<div class="clear">		
			<label for="amount" class="control-label">Amount:</label>
			<input type="text" name="amount" class="form-control" value="{{$record->amount}}" />
			
			<label for="notes" class="control-label">Notes:</label>
			<input type="text" name="notes" class="form-control" value="{{$record->notes}}" />
			
			<label for="vendor_memo" class="control-label">Vendor Memo:</label>
			<input type="text" name="vendor_memo" class="form-control" value="{{$record->vendor_memo}}" />
		</div>
			
			
		<div class="form-group">
			<input type="checkbox" name="reconciled_flag" id="reconciled_flag" class="form-control-inline" value="{{$record->reconciled_flag }}" {{ ($record->reconciled_flag) ? 'checked' : '' }} />
			<label for="reconciled_flag" class="checkbox-label">Reconciled</label>
		</div>		
			
		<div class="submit-button">
			<button type="submit" name="update" class="btn btn-primary">Update</button>
		</div>

		{{ csrf_field() }}
		
	</form>

</div>

@stop
