@extends('layouts.app')

@section('content')

<div class="container">
	<h1>Delete</h1>

	<form method="POST" action="/locations/delete/{{ $record->id }}">

	<div class="form-group">
		<h1 name="name" class="">{{$record->name }}</h1>
	</div>

	<div class="form-group">
		<h3 name="name" class="">Level: {{$record->level}}</h3>
	</div>

	<div class="form-group">
		<h3 name="name" class="">Breadcrumb: {{isset($record->breadcrumb) ? 'Yes' : 'No'}}</h3>
	</div>

	<div class="form-group">
		<h3 name="name" class="">Parent: {{isset($parent) ? $parent->name : 'None' }}</h3>
	</div>		
				
		<div class="form-group">
			<button type="submit" class="btn btn-primary">Confirm Delete</button>
		</div>
	{{ csrf_field() }}
	</form>
</div>
@endsection
