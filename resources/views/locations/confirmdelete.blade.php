@extends('layouts.app')

@section('content')

<div class="container page-size">

	@component('locations.menu-submenu', ['prefix' => $prefix, 'record' => $record])@endcomponent

	<h1>Delete: {{$record->name }}</h1>

	<form method="POST" action="/locations/delete/{{ $record->id }}">

	<div class="form-group">
		<h3 name="name" class="">Parent: {{isset($record->parent_name) ? $record->parent_name : 'None'}}</h3>
	</div>
	
	<div class="form-group">
		<h3 name="name" class="">Location Type: {{$record->location_type}}</h3>
	</div>

	<div class="form-group">
		<h3 name="name" class="">Breadcrumb: {{isset($record->breadcrumb_flag) && $record->breadcrumb_flag == 1 ? 'Yes' : 'No'}}</h3>
	</div>
	
	<div class="form-group">
		<h3 name="name" class="">Popular: {{isset($record->popular_flag) && $record->popular_flag == 1 ? 'Yes' : 'No'}}</h3>
	</div>	

	<div class="form-group">
		<h3 name="name" class="">In Use By: {{isset($entries) && count($entries) > 0 ? '' : 'None' }}</h3>
		<ul>
		@foreach($entries as $entry)
			@if ($entry->deleted_flag == 0)
			<li><a href="/entries/{{$entry->permalink}}" target="_blank">{{$entry->title}}</a></li>
			@endif
		@endforeach
		</ul>
	</div>		
				
		<div class="form-group">
			<button type="submit" class="btn btn-primary">Confirm Delete</button>
		</div>
	{{ csrf_field() }}
	</form>
</div>
@endsection
