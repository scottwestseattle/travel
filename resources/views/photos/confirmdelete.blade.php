@extends('layouts.app')

@section('content')

<div class="container">
	<h1>Delete Template</h1>

	<form method="POST" action="/photo/delete/{{ $id }}">

		<div class="form-group">
			<span name="title" class="">{{$id }}</span>
		</div>
		
		<div class="form-group">
			<button type="submit" class="btn btn-primary">Confirm Delete</button>
		</div>
	{{ csrf_field() }}
	</form>
</div>
@endsection
