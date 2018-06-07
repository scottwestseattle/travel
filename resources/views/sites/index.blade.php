@extends('layouts.app')

@section('content')

<div class="page-size container">
	@if (Auth::check())
		
		@component('menu-submenu-sites')@endcomponent
	
		<h1>Sites</h1>

		<table class="table">
			<thead>
				<tr>
					<th></th>
					<th>Site Name</th>
					<th>URL</th>
				</tr>
			</thead>
			<tbody>@foreach($records as $record)
				<tr>
					<td style="width:10px;"><a href='/sites/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
					
					<td><a href="/sites/view/{{$record->id}}">{{$record->site_name}}</a></td>
					<td><a target="_blank" href="{{'http://' . $record->site_url}}">{{$record->site_url}}</a></td>

					<td style="width:10px;"><a href='/sites/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a></td>
				</tr>
			@endforeach</tbody>
		</table>
	@else
		<h3>You need to log in. <a href="/login">Click here to login</a></h3>
	@endif
               
</div>
@endsection
