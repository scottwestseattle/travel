@extends('layouts.theme1')

@section('content')

<div class="container">

	@component($prefix . '.menu-submenu', ['record' => $record, 'prefix' => $prefix])@endcomponent

	<h1>Edit {{$title}}</h1>

	<form method="POST" action="/{{$prefix}}/update/{{$record->id}}">

		<label for="name" class="control-label">Account Name:</label>
		<input type="text" name="name" class="form-control" value="{{$record->name}}"></input>	

		<label for="starting_balance" class="control-label">Starting Balance:</label>
		<input type="text" name="starting_balance" class="form-control" value="{{$record->starting_balance}}"></input>	
		
		<div class="form-group">
			<input type="checkbox" name="hidden_flag" id="hidden_flag" class="form-control-inline" value="{{$record->hidden_flag }}" {{ ($record->hidden_flag) ? 'checked' : '' }} />
			<label for="hidden_flag" class="checkbox-label">Hidden</label>
		</div>

		<div class="form-group">
			<div class="radio-group-item">
				<input type="radio" name="account_type_flag" value="1" class="form-control-inline" {{$record->account_type_flag == 1 ? 'checked' : ''}} />
				<label for="account_type_flag" class="radio-label">Savings/Checking/Other</label>
			</div>
			
			<div class="radio-group-item">
				<input type="radio" name="account_type_flag" value="2" class="form-control-inline" {{$record->account_type_flag == 2 ? 'checked' : ''}} />
				<label for="account_type_flag" class="radio-label">Credit Card</label>			
			</div>		
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
