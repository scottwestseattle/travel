@extends('layouts.app')

@section('content')

<div class="container">
	<h1>Delete Photos</h1>

	<form method="POST" action="/photo/delete/{{ $id }}">

		<div class="form-group">
			<img name=""  />{{$id }}
		</div>
		
		<div class="form-group">
			<button type="submit" class="btn btn-primary">Confirm Delete</button>
		</div>
	{{ csrf_field() }}
	</form>
</div>
@endsection
