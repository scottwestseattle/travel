@extends('layouts.theme1')

@section('content')

<div class="container">

	@component($prefix . '.menu-submenu', ['record' => $record, 'prefix' => $prefix])@endcomponent

	<h1>Edit {{$title}}</h1>

	<form method="POST" action="/{{$prefix}}/update/{{$record->id}}">

		<label for="title" class="control-label">Name:</label>
		<input type="text" name="name" class="form-control" value="{{$record->name}}"></input>	
					
		<label for="description" class="control-label">Comment:</label>
		<textarea name="comment" class="form-control">{{$record->comment}}</textarea>
			
		<div class="submit-button">
			<button type="submit" name="update" class="btn btn-primary">Update</button>
		</div>

		{{ csrf_field() }}
		
	</form>

</div>

@stop
