@extends('layouts.theme1')

@section('content')

<div class="container">

	@component($prefix . '.menu-submenu', ['prefix' => $prefix])@endcomponent

	<h1>Add {{$title}}</h1>
               
	<form method="POST" action="/{{$prefix}}/create">
									
		@component('control-dropdown-date', ['months' => $dates['months'], 'years' => $dates['years'], 'days' => $dates['days'], 'filter' => $filter])@endcomponent		
		
		<label for="description" class="control-label">Description:</label>
		<input type="text" name="description" class="form-control" />

		@component('control-dropdown-menu', ['prompt' => 'Account:', 'field_name' => 'parent_id', 'options' => $accounts, 'empty' => 'Select', 'selected_option' => null])@endcomponent	
		
		@component('control-dropdown-menu', ['prompt' => 'Category:', 'field_name' => 'category_id', 'options' => $categories, 'empty' => 'Select', 'selected_option' => null, 'onchange' => 'onCategoryChange(this.value)'])@endcomponent				

		@component('control-dropdown-menu', ['prompt' => 'Subcategory:', 'field_name' => 'subcategory_id', 'options' => $subcategories, 'empty' => 'Select', 'selected_option' => null])@endcomponent				

		<div class="form-group">
			<div class="radio-group-item">
				<input type="radio" name="type_flag" value="1" class="form-control-inline" checked="check">
				<label for="type_flag" class="radio-label">Debit</label>
			</div>
			
			<div class="radio-group-item">
				<input type="radio" name="type_flag" value="2" class="form-control-inline">
				<label for="type_flag" class="radio-label">Credit</label>			
			</div>
		</div>
		
		<div class="clear">		
			<label for="amount" class="control-label">Amount:</label>
			<input type="text" name="amount" class="form-control" />
			
			<label for="notes" class="control-label">Notes:</label>
			<input type="text" name="notes" class="form-control" />
		</div>

		<div class="form-group">
			<input type="checkbox" name="reconciled_flag" id="reconciled_flag" class="form-control-inline" checked="check" />
			<label for="reconciled_flag" class="checkbox-label">Reconciled</label>
		</div>
		
		<div class="submit-button">
			<button type="submit" name="update" class="btn btn-primary">Add</button>
		</div>
						
		{{ csrf_field() }}
	
	</form>

</div>

@endsection
