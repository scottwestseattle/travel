@extends('layouts.theme1')

@section('content')

<div class="container">

	@component($prefix . '.menu-submenu', ['prefix' => $prefix])@endcomponent
	
	<form method="POST" action="/{{$prefix}}/expenses">
		
		{{$filter['from_date']}} - {{$filter['to_date']}}
		
		@component('control-dropdown-date', ['div' => true, 'months' => $dates['months'], 'years' => $dates['years'], 'filter' => $filter])@endcomponent
							
		<button type="submit" name="update" class="btn btn-primary" style="font-size:12px; padding:1px 4px; margin:5px;">Filter</button>

		<div class="clear"></div>
		
		<h3>Expenses</h3>

		<table class="table">
			<thead>
				<tr>
					<th>Category</th>
					<th>Subcategory</th>
					<th>Subtotal</th>
					<th>Total</th>
				</tr>
			</thead>
			<tbody>
			@if (isset($records))
				@foreach($records as $record)
				@if ($record->first == 1)
				<tr><!-- put in the category record -->
					<td>{{$record->category}}</td>
					<td></td>
					<td></td>
					<td>{{$record->total}}</td>
				</tr>				
				@endif
				<tr><!-- put in the subcategory record -->
					<td></td>
					<td>{{$record->subcategory}}</td>
					<td>{{$record->subtotal}}</td>
					<td></td>
				</tr>
				@endforeach
			@endif
			</tbody>
		</table>
       
		{{ csrf_field() }}
		
	</form>	   
</div>

@endsection
