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

	<h1>Delete</h1>

	<form method="POST" action="/locations/delete/{{ $record->id }}">

	<div class="form-group">
		<h1 name="name" class="">{{$record->name }}</h1>
	</div>

	<div class="form-group">
		<h3 name="name" class="">Level: {{$record->level}}</h3>
	</div>

	<div class="form-group">
		<h3 name="name" class="">Breadcrumb: {{isset($record->breadcrumb) ? 'Yes' : 'No'}}</h3>
	</div>

	<div class="form-group">
		<h3 name="name" class="">Parent: {{isset($parent) ? $parent->name : 'None' }}</h3>
	</div>		
				
		<div class="form-group">
			<button type="submit" class="btn btn-primary">Confirm Delete</button>
		</div>
	{{ csrf_field() }}
	</form>
</div>
@endsection
