@extends('layouts.app')

@section('content')

<div class="container page-size">

@component('menu-submenu-templates')@endcomponent

<h1>Edit</h1>

<form method="POST" action="/{{$prefix}}/update/{{$record->id}}">

	<div class="form-control-big">	

		<label for="title">Title:</label>
		<input type="text" name="title" class="form-control" value="{{$record->title}}"></input>	
				
		<label for="permalink">Permalink:</label>
		<input type="text" name="permalink" class="form-control" value="{{$record->permalink}}"></input>

		<label for="description">Description:</label>
		<textarea name="description" class="form-control">{{$record->description}}</textarea>
		
	<div class="form-group">
		<button type="submit" name="update" class="btn btn-primary">Update</button>
	</div>
	
	</div>
{{ csrf_field() }}
</form>

</div>

@stop
