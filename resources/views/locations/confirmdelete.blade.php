@extends('layouts.app')

@section('content')

<div class="container">

	@guest
	@else
		@if (Auth::user()->user_type >= 100)
		<table><tr>			
			<td style="width:40px; font-size:20px;"><a href='/locations/index/'><span class="glyphCustom glyphicon glyphicon-list"></span></a></td>
			<td style="width:40px; font-size:20px;"><a href='/locations/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
			<td style="width:40px; font-size:20px;"><a href='/locations/view/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-eye-open"></span></a></td>
			<td style="width:40px; font-size:20px;"><a href='/locations/add/'><span class="glyphCustom glyphicon glyphicon-plus-sign"></span></a></td>
		</tr></table>
		@endif
	@endguest

	<h1>Delete: {{$record->name }}</h1>

	<form method="POST" action="/locations/delete/{{ $record->id }}">

	<div class="form-group">
		<h3 name="name" class="">Parent: {{isset($record->parent_name) ? $record->parent_name : 'None'}}</h3>
	</div>
	
	<div class="form-group">
		<h3 name="name" class="">Location Type: {{$record->location_type}}</h3>
	</div>

	<div class="form-group">
		<h3 name="name" class="">Breadcrumb: {{isset($record->breadcrumb) ? 'Yes' : 'No'}}</h3>
	</div>

	<div class="form-group">
		<h3 name="name" class="">Users: {{isset($activities) && count($activities) > 0 ? '' : 'None' }}</h3>
		<ul>
		@foreach($activities as $activity)
			<li>{{$activity->title}}</li>
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
