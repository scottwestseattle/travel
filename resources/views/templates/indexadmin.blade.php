@extends('layouts.theme1')

@section('content')

<div class="container">

	@component('templates.menu-submenu', ['prefix' => $prefix])@endcomponent
	
	<h1>{{$titlePlural}} ({{count($records)}})</h1>

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
				<td style="width:10px;"><a href='/{{$prefix}}/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
				<td style="width:10px;"><a href='/{{$prefix}}/publish/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-flash"></span></a></td>
				
				<td><a href="/{{$prefix}}/view/{{$record->id}}">{{$record->title}}</a></td>
				<td><a href="/{{$prefix}}/view/{{$record->id}}">{{$record->permalink}}</a></td>
				<td>{{$record->published_flag}}</td>
				<td>{{$record->approved_flag}}</td>

				<td style="width:10px;"><a href='/{{$prefix}}/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a></td>
			</tr>
			@endforeach
		@endif
		</tbody>
	</table>
               
</div>

@endsection
