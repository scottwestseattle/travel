@extends('layouts.app')

@section('content')

<div class="page-size container">
               
	<div class="form-group">
		<h1 name="name" class="">{{$record->name }}</h1>
	</div>

	<div class="form-group">
		<h3 name="name" class="">Level: {{$record->location_type}}</h3>
	</div>

	<div class="form-group">
		<h3 name="name" class="">Breadcrumb: {{isset($record->breadcrumb) ? 'Yes' : 'No'}}</h3>
	</div>

	<div class="form-group">
		<h3 name="name" class="">Parent: {{isset($parent) ? $parent->name : 'None' }}</h3>
	</div>

</div>
@endsection
