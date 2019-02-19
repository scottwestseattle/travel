@extends('layouts.theme1')

@section('content')

<div class="container">

	@component($prefix . '.menu-submenu', ['prefix' => $prefix])@endcomponent
	
	<h1>{{$titlePlural}} ({{count($records)}})</h1>

	<table class="table">
		<thead>
			<tr>
				<th></th>
				<th></th>
				<th>@LANG('ui.Date')</th>
				<th>@LANG('ui.Parent')</th>
				<th>@LANG('ui.Name')</th>
				<th>@LANG('content.Comment')</th>
				<th>@LANG('ui.Approved')</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		@if (isset($records))
			@foreach($records as $record)
			<tr>
				<td class="glyphCol"><a href='/{{$prefix}}/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
				<td class="glyphCol"><a href='/{{$prefix}}/publish/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-flash"></span></a></td>
				
				<td>{{date_format($record->created_at, "F j, Y")}}</td>				
				<td>{{$record->parent_id}}</td>
				<td>{{$record->name}}</td>
				<td><a href="/{{$prefix}}/view/{{$record->id}}">{{strlen($record->comment) > 50 ? substr($record->comment,0,50)."..." : $record->comment}}</a></td>
				<td>{{$record->approved_flag}}</td>

				<td class="glyphCol"><a href='/{{$prefix}}/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a></td>
			</tr>
			@endforeach
		@endif
		</tbody>
	</table>
               
</div>

@endsection
