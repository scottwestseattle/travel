@extends('layouts.app')

@section('content')

<div class="page-size container">

	@component('menu-submenu-tours', ['record_id' => $record->id, 'record_permalink' => $record->permalink])
	@endcomponent

	<h1>Delete Tour</h1>

	<form method="POST" action="/tours/delete/{{ $record->id }}">

		<div class="form-group">
			<h3 name="title" class="">{{$record->title }}</h3>
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
