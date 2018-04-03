@extends('layouts.app')

@section('content')

@component('menu-submenu', ['data' => $data])
	@component('menu-icons-start')@endcomponent
@endcomponent

<div class="container">
	<h1>Add</h1>
               
	<form method="POST" action="/tags/create">

		<div class="form-group">
			<input type="text" name="name" class="form-control"  placeholder="Name"></input>
		</div>

		<div class="form-group">
			<button type="submit" class="btn btn-primary">Add</button>
		</div>
	{{ csrf_field() }}
	</form>
</div>
@endsection
