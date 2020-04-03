@extends('layouts.theme1')

@section('content')

<div class="page-size container">
	
	@component($prefix . '.menu-submenu', ['prefix' => $prefix])@endcomponent
	
	<h1>Annual Net ({{$balance}})</h1>

	<table class="table">
		<thead>
			<tr>
				<th>Year</th>
				<th>Net</th>
			</tr>
		</thead>
		<tbody>
		@if (isset($annualBalances))
			@foreach($annualBalances as $record)
				<tr style="color:{{($record->balance < 0.0) ? 'red' : 'default'}};">
					<td>{{$record->year}}</td>
					<td>{{$record->balance}}</td>
				</tr>
			@endforeach
		@endif
		</tbody>
	</table>


	<h1>Monthy Balances ({{count($monthlyBalances)}} months)&nbsp;<span style="font-size:.5em;"><a href="/transactions/summary/all">(show all)</a></span></h1>

	<table class="table">
		<thead>
			<tr>
				<th>Month</th>
				<th>Net</th>
				<th>Credits</th>
				<th>Debits</th>
			</tr>
		</thead>
		<tbody>
		@if (isset($monthlyBalances))
			@foreach($monthlyBalances as $record)
				<tr style="color:{{($record->balance < 0.0) ? 'red' : 'default'}};">
					<td>{{$record->month}}</td>
					<td>{{$record->balance}}</td>
					<td>{{number_format(floatval($record->credit), 2)}}</td>
					<td>{{number_format(floatval($record->debit), 2)}}</td>
				</tr>
			@endforeach
		@endif
		</tbody>
	</table>
               
</div>
@endsection
