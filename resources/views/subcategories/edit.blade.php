@extends('layouts.theme1')

@section('content')

<div class="container">

	@component($prefix . '.menu-submenu', ['record' => $record, 'prefix' => $prefix])@endcomponent

	<h1>Edit {{$title}}</h1>

	<form method="POST" action="/{{$prefix}}/update/{{$record->id}}">

		<label for="name" class="control-label">Name:</label>
		<input type="text" name="name" class="form-control" value="{{$record->name}}"></input>	
					
		@component('control-dropdown-menu', ['prompt' => 'Category:', 'field_name' => 'parent_id', 'options' => $categories, 'selected_option' => {{$record->parent_id}}])@endcomponent				
					
		<label for="notes" class="control-label">Notes:</label>
		<input type="text" name="notes" class="form-control" value="{{$record->notes}}"></input>	
			
		<div class="submit-button">
			<button type="submit" name="update" class="btn btn-primary">Update</button>
		</div>

		{{ csrf_field() }}
		
	</form>

</div>

@stop
