@extends('layouts.app')

@section('content')

<div class="page-size container">

	@component('menu-submenu-templates')@endcomponent
	
	<h1>Templates ({{count($records)}})</h1>

	<table class="table">
		<thead>
			<tr>
				<th></th>
				<th></th>
				<th>Title</th>
				<th>Permalink</th>
				<th>Published</th>
				<th>Approved</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		@if (isset($records))
			@foreach($records as $record)
			<tr>
				<td style="width:10px;"><a href='/templates/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
				<td style="width:10px;"><a href='/templates/publish/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-flash"></span></a></td>
				
				<td><a href="/templates/view/{{$record->id}}">{{$record->title}}</a></td>
				<td><a href="/templates/view/{{$record->id}}">{{$record->permalink}}</a></td>
				<td>{{$record->published_flag}}</td>
				<td>{{$record->approved_flag}}</td>

				<td style="width:10px;"><a href='/templates/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a></td>
			</tr>
			@endforeach
		@endif
		</tbody>
	</table>
               
</div>

@endsection
