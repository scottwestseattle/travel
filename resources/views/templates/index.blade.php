@extends('layouts.app')

@section('content')

<div class="page-size container">
	
	@component('menu-submenu-templates')@endcomponent
	
	<h1>Templates ({{count($records)}})</h1>

	<table class="table">
		<thead>
			<tr>
				<th>Title</th>
				<th>Permalink</th>
			</tr>
		</thead>
		<tbody>
		@if (isset($records))
			@foreach($records as $record)
			<tr>					
				<td><a href="/templates/view/{{$record->id}}">{{$record->title}}</a></td>
				<td><a href="/templates/view/{{$record->id}}">{{$record->permalink}}</a></td>
			</tr>
			@endforeach
		@endif
		</tbody>
	</table>
               
</div>
@endsection
