@extends('layouts.app')

@section('content')

<div class="container">
	@if (Auth::check())
		<h1>Locations</h1>
		<a href="/locations/add" class="btn btn-primary">Add</a>
		<table class="table">
			<tbody>@foreach($records as $record)
				<tr>
					<td style="width:10px; padding-right:20px;">
						<a href='/locations/view/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-eye-open"></span></a>
					</td>
					<td style="width:10px; padding-right:20px;">
						<a href='/locations/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a>
					</td>
					<td>
						<a target="" href="/locations/activities/{{$record->id}}">{{$record->name}}</a>
					</td>
					<td style="width:10px; padding-right:20px;">
						<a href='/locations/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a>
					</td>
				</tr>
			@endforeach</tbody>
		</table>
	@else
		<h3>You need to log in. <a href="/login">Click here to login</a></h3>
	@endif
               
</div>
@endsection
