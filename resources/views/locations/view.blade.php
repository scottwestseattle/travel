@extends('layouts.app')

@section('content')

<div class="page-size container">

	@guest
	@else
		@if (Auth::user()->user_type >= 100)
		<table><tr>			
			<td style="width:40px; font-size:20px;"><a href='/locations/index/'><span class="glyphCustom glyphicon glyphicon-list"></span></a></td>
			<td style="width:40px; font-size:20px;"><a href='/locations/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
			<td style="width:40px; font-size:20px;"><a href='/locations/add/'><span class="glyphCustom glyphicon glyphicon-plus-sign"></span></a></td>
			<td style="width:40px; font-size:20px;"><a href='/locations/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a></td>
		</tr></table>
		@endif
	@endguest

	<h1>Edit</h1>
	
	<div class="form-group">
		<h2 name="name" class="">{{$record->name }}</h2>
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
