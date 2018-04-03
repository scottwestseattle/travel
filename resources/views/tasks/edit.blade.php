@extends('layouts.app')

@section('content')

@component('menu-submenu', ['data' => $data])
	@component('menu-icons-start')@endcomponent
@endcomponent

	<div class="container">
	<h1>Edit</h1>

<form method="POST" action="/tasks/update/{{ $task->id }}">

	<div class="form-group">
		<input type="text" name="description" class="form-control" value="{{$task->description }}"></input>
	</div>

	<div class="form-group">
		<input type="text" name="link" class="form-control" value="{{$task->link }}"></input>	
	</div>	

	<div class="form-group">
		<button type="submit" name="update" class="btn btn-primary">Update</button>
	</div>
{{ csrf_field() }}
</form>

</div>

@stop
