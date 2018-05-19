@extends('layouts.app')

@section('content')

<div class="page-size container">
	<h1>Delete</h1>

	<form method="POST" action="/activities/delete/{{ $record->id }}">

		<div class="form-group">
			<span name="title" class="">{{$record->title }}</span>
		</div>

		<div class="form-group">
			<span name="description" class="">{{$record->description }}</span>	
		</div>
		
		<div class="form-group">
			<button type="submit" class="btn btn-primary">Confirm Delete</button>
		</div>
	{{ csrf_field() }}
	</form>
</div>
@endsection
