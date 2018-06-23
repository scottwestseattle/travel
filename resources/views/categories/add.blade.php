@extends('layouts.theme1')

@section('content')

<div class="container">

	@component($prefix . '.menu-submenu', ['prefix' => $prefix])@endcomponent

	<h1>Add {{$title}}</h1>
               
	<form method="POST" action="/{{$prefix}}/create">
							
		<label for="name" class="control-label">Name:</label>
		<input type="text" name="name" class="form-control" />
			
		<label for="notes" class="control-label">Notes:</label>
		<input type="text" name="notes" class="form-control" />

		<div class="submit-button">
			<button type="submit" name="update" class="btn btn-primary">Add</button>
		</div>
						
		{{ csrf_field() }}

	</form>

</div>

@endsection
