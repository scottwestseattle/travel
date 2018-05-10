@extends('layouts.app')

@section('content')

<div class="container">
	<h1>Delete Photo</h1>

	<form method="POST" action="/photos/delete/{{ $photo->id }}">
		<div class="form-group">
			<span name="filename" class="">{{$photo->filename}}</span>
		</div>

		<div class="form-group">
			<span name="alt_text" class="">{{$photo->alt_text}}</span>
		</div>
		
		<div class="form-group">
			<span name="description" class="">{{$photo->location}}</span>	
		</div>
		
		<div class="form-group">
			<img alt="{{$photo->alt_text}}" title="{{$photo->alt_text}}" style="width:500px; max-width:100%;" src="/img/sliders/{{$photo->filename}}" />
		</div>
		
		<div class="form-group">
			<button type="submit" class="btn btn-primary">Confirm Delete</button>
		</div>
		
	{{ csrf_field() }}
	</form>
</div>
@endsection
