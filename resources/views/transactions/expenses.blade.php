@extends('layouts.theme1')
@section('content')

<div class="container">

	@component($prefix . '.menu-submenu', ['prefix' => $prefix])@endcomponent
	
	<form method="POST" action="/{{$prefix}}/expenses">
		
		{{$filter['from_date']}} - {{$filter['to_date']}}
		
		@component('control-dropdown-date', ['div' => true, 'months' => $dates['months'], 'years' => $dates['years'], 'filter' => $filter])@endcomponent							
		<button type="submit" name="update" class="btn btn-primary" style="font-size:12px; padding:1px 4px; margin:5px;">Filter</button>
		<a type="button" name="taxes" href="/transactions/taxes" class="btn btn-success" style="font-size:12px; padding:1px 4px; margin:5px;">Taxes</a>

		<div class="clear"></div>
		
		<?php
			$in = isset($income) && count($income) > 0 ? $income[0]->grand_total : 0.0;
			$out = isset($expenses) && count($expenses) > 0 ? $expenses[0]->grand_total : 0.0;
			$net = $in + $out;
		?>
		
		<h3 style="margin-bottom:30px;">Net Income/Loss:</strong> <span style="color: {{$net < 0.0 ? 'red' : 'default'}}">{{number_format($net, 2)}}</span></h3>

		<h3>Income: <b>{{number_format($in, 2)}}</b></h3>
		
		<table class="table">
			<tbody>
			@if (isset($income) && count($income) > 0)
				@foreach($income as $record)
					@if ($record->first == 1)
						<!-- no category record because it's all the same category: income -->
					@endif
					<tr><!-- put in the subcategory record -->
						<td></td>
						<td>{{$record->subcategory}}</td>
						<td>{{number_format($record->subtotal, 2)}}</td>
						<td></td>
					</tr>
				@endforeach
			@else
				<tr><td>None</td></tr>
			@endif
			</tbody>
		</table>		
		
		<h3>Expenses: <b>{{number_format($out, 2)}}</b></h3>

		<table class="table">
			<tbody>
				@if (isset($expenses) && count($expenses) > 0)				
				@foreach($expenses as $record)
					@if ($record->first == 1)
					<tr><!-- put in the category record -->
						<td>{{$record->category}}</td>
						<td></td>
						<td></td>
						<td>{{number_format($record->total, 2)}}</td>
					</tr>				
					@endif
					<tr><!-- put in the subcategory record -->
						<td></td>
						<td>{{$record->subcategory}}</td>
						<td>{{number_format($record->subtotal, 2)}}</td>
						<td></td>
					</tr>
				@endforeach
			@else
				<tr><td>None</td></tr>				
			@endif
			</tbody>
		</table>
       
		{{ csrf_field() }}
		
	</form>	   
</div>

@endsection
