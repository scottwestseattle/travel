@extends('layouts.app')

@section('content')

<div class="container page-size">

	@component('menu-submenu-entries', ['record_id' => $entry->id, 'record_permalink' => $entry->permalink])@endcomponent	

	<h1>Delete Entry</h1>

	<form method="POST" action="/entries/delete/{{ $entry->id }}">

		<div class="form-group">
			<h3 name="title" class="">{{$entry->title }}</h3>
		</div>

		<div class="form-group">
			<span name="description" class="">{{$entry->description }}</span>	
		</div>
		
		<div class="form-group">
			<button type="submit" class="btn btn-primary">Confirm Delete</button>
		</div>
	{{ csrf_field() }}
	</form>
</div>
@endsection
