@extends('layouts.app')

@section('content')

<div class="container page-size">

	@component('menu-submenu-templates', ['record' => $record])@endcomponent
	
	<h1>Delete</h1>

	<form method="POST" action="/{{$prefix}}/delete/{{ $record->id }}">
			   
		<h3 name="title" class="">{{$record->title }}</h3>

		<div class="form-group">
			<button type="submit" class="btn btn-primary">Confirm Delete</button>
		</div>		
	
		<p>{{$record->permalink }}</p>

		<p>{{$record->descrption }}</p>
		
		<div class="form-group">
			<button type="submit" class="btn btn-primary">Confirm Delete</button>
		</div>
		
	{{ csrf_field() }}
	</form>
</div>
@endsection
