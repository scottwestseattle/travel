@extends('layouts.theme1')

@section('content')

<div class="page-size container">
	
	@component($prefix . '.menu-submenu', ['prefix' => $prefix])@endcomponent
	
	<h1>{{$titlePlural}} ({{count($records)}})</h1>

	<table class="table">
		<thead>
			<tr>
				<th>Title</th>
			</tr>
		</thead>
		<tbody>
		@if (isset($records))
			@foreach($records as $record)
			<tr>					
				<td><a href="/{{$prefix}}/view/{{$record->id}}">{{$record->title}}</a></td>
			</tr>
			@endforeach
		@endif
		</tbody>
	</table>
               
</div>
@endsection
