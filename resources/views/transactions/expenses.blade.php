@extends('layouts.theme1')

@section('content')

<div class="container">

	@component($prefix . '.menu-submenu', ['prefix' => $prefix])@endcomponent
	
	<form method="POST" action="/{{$prefix}}/expenses">
		
		{{$filter['from_date']}} - {{$filter['to_date']}}
		
		@component('control-dropdown-date', ['div' => true, 'months' => $dates['months'], 'years' => $dates['years'], 'filter' => $filter])@endcomponent
							
		<button type="submit" name="update" class="btn btn-primary" style="font-size:12px; padding:1px 4px; margin:5px;">Filter</button>

		<div class="clear"></div>
		
		<?php
			$in = isset($income) && count($income) > 0 ? $income[0]->grand_total : 0.0;
			$out = isset($expenses) && count($expenses) > 0 ? $expenses[0]->grand_total : 0.0;
			$net = $in + $out;
		?>
		
		<h3 style="margin-bottom:30px;"><strong>Income:</strong> {{$in}}, <strong>Expenses:</strong> {{$out}}, <strong>Net:</strong> <span style="color: {{$net < 0.0 ? 'red' : 'default'}}">{{$net}}</span></h3>

		<h4>Income</h4>
		
		<table class="table">
			<tbody>
			@if (isset($income))
				@foreach($income as $record)
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
		
		<h4>Expenses</h4>

		<table class="table">
			<tbody>
			@if (isset($expenses))
				@foreach($expenses as $record)
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
