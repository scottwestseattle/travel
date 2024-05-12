@extends('layouts.theme1')
@section('content')
@php
	$in = isset($income) && count($income) > 0 ? $income[0]->grand_total : 0.0;
	$out = isset($expenses) && count($expenses) > 0 ? $expenses[0]->grand_total : 0.0;
	$taxDue = $taxes['taxDue'] >= 0.0;
@endphp	
<div class="container">

	@component($prefix . '.menu-submenu', ['prefix' => $prefix])@endcomponent
	
	<form method="POST" action="/{{$prefix}}/taxes">
		
		{{$filter['from_date']}} - {{$filter['to_date']}}
		
		@component('control-dropdown-date', ['div' => true, 'years' => $dates['years'], 'filter' => $filter])@endcomponent							
		<button type="submit" name="update" class="btn btn-primary" style="font-size:12px; padding:1px 4px; margin:5px;">Filter</button>

		<div class="clear"></div>
			
		<h3 style="margin-bottom:30px;">
		@if ($taxDue)
			<span>Taxes Due: <b>${{number_format($taxes['taxDue'])}}</b></span>
		@else
			<span>Refund Due: <b>${{number_format(abs($taxes['taxDue']))}}</b></span>
		@endif
		</h3>
		<table class="table table-borderless">
			<tbody>
				<tr><td>Total Income:</td><td>{{number_format($in)}}</td></tr>
				<tr><td>Deductions:</td><td>{{number_format($out)}}</td></tr>
				<tr><td>Adjusted Gross Income:</td><td>{{number_format($taxes['agi'])}}</td></tr>
				<tr><td>Standard Deduction:</td><td>-{{$taxes['standardDeduction']}}</td></tr>
				<tr><td>Taxable Income:</td><td>{{number_format($taxes['taxableIncome'])}}</td></tr>
				<tr><td>Total Tax:</td><td>{{$taxes['totalTaxDue']}}</td></tr>
				<tr><td>Total Payments/Credits:</td><td>{{number_format($taxes['estimatedTaxPayments'])}}</td></tr>
				@if ($taxDue)
					<tr><td>Taxes Due:</td><td>{{number_format($taxes['taxDue'])}}</td></tr>
				@else
					<tr><td>Amount to be Refunded:</td><td>{{number_format(abs($taxes['taxDue']))}}</td></tr>
				@endif
				<tr><td>Effective Tax Rate:</td><td>{{number_format($taxes['taxRate'], 2)}}%</td></tr>
			</tbody>
		</table>			

		<h3>Income: <b>{{number_format($in, 2)}}</b></h3>
		
		<table class="table">
			<tbody>
			@if (isset($income) && count($income) > 0)
				@foreach($income as $record)
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
		
		<h3>Deductions: <b>{{number_format($out, 2)}}</b></h3>

		<table class="table">
			<tbody>
				@if (isset($expenses) && count($expenses) > 0)				
				@foreach($expenses as $record)
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
