@extends('layouts.theme1')

@section('content')

<div class="container">

	@component($prefix . '.menu-submenu', ['prefix' => $prefix])@endcomponent

	<h1>Add {{$title}}</h1>
               
	<form method="POST" action="/{{$prefix}}/create">
							
		<label for="name" class="control-label">Account Name:</label>
		<input type="text" name="name" class="form-control" />
			
		<label for="starting_balance" class="control-label">Starting Balance:</label>
		<input type="text" name="starting_balance" class="form-control" />
			
		<label for="notes" class="control-label">Notes:</label>
		<input type="text" name="notes" class="form-control"></input>	
		
		<div class="form-group">
			<div class="radio-group-item">
				<input type="radio" name="account_type_flag" value="1" class="form-control-inline">
				<label for="account_type_flag" class="radio-label">Savings, Checking, Etc.</label>
			</div>
			
			<div class="radio-group-item">
				<input type="radio" name="account_type_flag" value="2" class="form-control-inline">
				<label for="account_type_flag" class="radio-label">Credit Card, Loan</label>			
			</div>
		</div>
		
		<div class="submit-button">
			<button type="submit" name="update" class="btn btn-primary">Add</button>
		</div>
						
		{{ csrf_field() }}

	</form>

</div>

@endsection
