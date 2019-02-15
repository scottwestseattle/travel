@extends('layouts.theme1')

@section('content')

<div class="page-size container">
	
	@component($prefix . '.menu-submenu', ['prefix' => $prefix])@endcomponent
	
	<h1>@LANG('content.Comments') ({{count($records)}})</h1>

	<table class="table">
		<thead>
			<tr>
				<th>@LANG('ui.Date')</th>
				<th>@LANG('ui.Name')</th>
				<th>@LANG('content.Comment')</th>
			</tr>
		</thead>
		<tbody style="font-size:1.5em;">
		@if (isset($records))
			@foreach($records as $record)
			<tr>	
				<td>{{date_format($record->created_at, "F j, Y")}}</td>				
				<td>{{$record->name}}</td>
				<td><a href="/{{$prefix}}/view/{{$record->id}}">{{strlen($record->comment) > 50 ? substr($record->comment,0,50)."..." : $record->comment}}</a></td>
			</tr>
			@endforeach
		@endif
		</tbody>
	</table>
               
</div>
@endsection
