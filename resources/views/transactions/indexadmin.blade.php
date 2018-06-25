@extends('layouts.theme1')

@section('content')

<div class="container">

	@component($prefix . '.menu-submenu', ['prefix' => $prefix])@endcomponent
	
	<h3>{{$titlePlural}} ({{count($records)}}), Total: ${{$total}}</h3>

	<table class="table">
		<tbody>
		@if (isset($records))
			@foreach($records as $record)
			<tr>
				<td class="glyphCol"><a href='/{{$prefix}}/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
				<td class="glyphCol"><a href='/{{$prefix}}/copy/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-duplicate"></span></a></td>
				
				<td>{{$record->transaction_date}}</td>
				<td>{{$record->amount}}</td>
				<td><a href="/{{$prefix}}/view/{{$record->id}}">{{$record->description}}</a></td>
				<td>{{$record->notes}}</td>
				<td>{{$record->vendor_memo}}</td>
				<td><a href="/{{$prefix}}/view/{{$record->parent_id}}">{{$record->account}}</a></td>
				<td><a href="/{{$prefix}}/view/{{$record->id}}">{{$record->category}}</a>::<a href="/{{$prefix}}/indexadmin/{{$record->subcategory_id}}">{{$record->subcategory}}</a></td>

				<td class="glyphCol"><a href='/{{$prefix}}/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a></td>
			</tr>
			@endforeach
		@endif
		</tbody>
	</table>
               
</div>

@endsection
