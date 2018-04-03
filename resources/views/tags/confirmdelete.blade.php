@extends('layouts.app')

@section('content')

@component('menu-submenu', ['data' => $data])
	@component('menu-icons-start')@endcomponent
@endcomponent

<div class="container">
	<h1>Delete</h1>

	<form method="POST" action="/tags/delete/{{ $tag->id }}">

		<div class="form-group">
			<span name="name" class="">{{$tag->name }}</span>	
		</div>
		
		<div class="form-group">
			<button type="submit" class="btn btn-primary">Confirm Delete</button>
		</div>
	{{ csrf_field() }}
	</form>
</div>
@endsection
