@extends('layouts.app')

@section('content')

@component('menu-submenu')
	@component('menu-icons-start')@endcomponent
	@component('menu-icons-links', ['data' => (isset($data) ? $data : null)])@endcomponent	
	@component('control-search')@endcomponent	
	@component('control-templates-dropdown', ['templates' => $templates])@endcomponent	
@endcomponent

<div class="container">
	<h1>Delete Entry</h1>

	<form method="POST" action="/entries/delete/{{ $entry->id }}">

		<div class="form-group">
			<span name="title" class="">{{$entry->title }}</span>
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
