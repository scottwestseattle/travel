@extends('layouts.app')

@section('content')

@component('menu-submenu', ['data' => $data])
	@component('menu-icons-start')@endcomponent
@endcomponent

	<div class="container">
	<h1>Edit</h1>

<form method="POST" action="/tags/update/{{ $tag->id }}">

	<div class="form-group">
		<input type="text" name="name" class="form-control" value="{{$tag->name }}"></input>
	</div>

	<div class="form-group">
		<button type="submit" name="update" class="btn btn-primary">Update</button>
	</div>
{{ csrf_field() }}
</form>

</div>

@stop
