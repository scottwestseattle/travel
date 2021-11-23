@extends('layouts.theme1')
@section('content')
<div class="container">

	@component('transactions.menu-submenu-trades', ['prefix' => $prefix])@endcomponent
	
	<form method="POST" action="/{{$prefix}}/positions">
				
		@component('control-dropdown-date', ['div' => true, 'months' => $dates['months'], 'years' => $dates['years'], 'days' => $dates['days'], 'filter' => $filter])@endcomponent
				
		<div style="float:left;">
			@component('control-dropdown-menu', ['field_name' => 'account_id', 'options' => $accounts, 'selected_option' => $filter['account_id'], 'empty' => 'All Accounts'])@endcomponent	
		</div>
			
		<div style="float:left;">
			@component('control-dropdown-menu', ['field_name' => 'subcategory_id', 'options' => $subcategories, 'selected_option' => $filter['subcategory_id'], 'empty' => 'All Transaction Types'])@endcomponent									
		</div>
		
		<div style="float:left;">
			@component('control-dropdown-menu', ['field_name' => 'symbol', 'options' => $symbols, 'selected_option' => $filter['symbol'], 'empty' => 'All Symbols'])@endcomponent				
		</div>
		
		<input style="font-size:16px; height:24px; width:200px;" type="text" name="search" class="form-control" value="{{$filter['search']}}"></input>		
		
		<div>
			<input type="checkbox" name="showalldates_flag" id="showalldates_flag" class="form-control-inline" value="1" {{ $filter['showalldates_flag'] == 1 ? 'checked' : '' }} />
			<label for="showalldates_flag" class="checkbox-label">Show All Dates</label>
			<input type="checkbox" name="unreconciled_flag" id="unreconciled_flag" class="form-control-inline" value="1" {{ $filter['unreconciled_flag'] == 1 ? 'checked' : '' }} />
			<label for="unreconciled_flag" class="checkbox-label">Unreconciled</label>
			<input type="checkbox" name="sold_flag" id="sold_flag" class="form-control-inline" value="1" {{ $filter['sold_flag'] == 1 ? 'checked' : '' }} />
			<label for="sold_flag" class="checkbox-label">Sold</label>
			<input type="checkbox" name="unsold_flag" id="unsold_flag" class="form-control-inline" value="1" {{ $filter['unsold_flag'] == 1 ? 'checked' : '' }} />
			<label for="unsold_flag" class="checkbox-label">Unsold</label>
		</div>				
		
		<button type="submit" name="update" class="btn btn-primary" style="font-size:12px; padding:1px 4px; margin:5px;">Refresh</button>
		
		<a style="font-size:12px; padding:1px 4px; margin:5px;" class="btn btn-success" href="/transactions/add-trade">Add Trade</a>
		
		{{ csrf_field() }}		
	</form>	   

		<div class="clear"></div>
		
		@php
			// get the totals
			$dca = number_format($totals['dca'], 2);
			$cost = number_format(abs($totals['total']), 2);
			$profit = number_format($totals['profit'], 2);
			
			$profitPercent = 0;
			if ($totals['profit'] != 0.0 && $totals['total'] != 0.0)
				$profitPercent = number_format( ($totals['profit'] / abs($totals['total'])) * 100.0, 2);

			// reconciled, untested
			$profitReconciled = isset($totals['reconciled']) ? $totals['reconciled'] : 0;
			$profitPercentReconciled = 0;
			if ($profitReconciled != 0.0 && $totals['total'] != 0.0)
				$profitPercentReconciled = number_format(($profitReconciled / abs($totals['total']) * 100.0), 2);
				
		@endphp
		<h3>
			Positions ({{count($records)}}), Shares: <span style="color:black">{{$totals['shares']}}</span>, Price: <span style="color:black">${{$dca}}</span>, Cost: <span style="color:black">${{$cost}}</span>{{ isset($totals['reconciled']) ? ', P/L: ' . round($totals['reconciled'], 2) . ', ' . $profitPercentReconciled : '' }}, P/L: <span style="color:{{$totals['profit'] >= 0.0 ? 'black' : 'red'}}">${{$profit}}, {{$profitPercent}}%</span>
		</h3>
		
		<table class="table table-sm">
			<tbody>
			@if (isset($records))
				@foreach($records as $record)
					@php
						$quote = $totals[$record->symbol];
						$pl = round((floatval($quote['price']) * abs($record->shares)) - abs($record->amount), 2); 
						$cost = $record->shares * $record->buy_price;
						$plPercent = number_format(($pl / $cost) * 100.0, 2);
						$color = ($pl < 0) ? 'red' : 'black';
					@endphp
					<tr>
						<td class="glyphCol"><a href='/{{$prefix}}/sell/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-flash"></span></a></td>
						
						<td class="glyphCol"><a href='/{{$prefix}}/edit-trade/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
						
						<td>						
							<a href="https://finance.yahoo.com/quote/{{$record->symbol}}" target="_blank">{{$record->symbol}}</a> <span style="font-size:11px; color:{{$quote['up'] ? 'black' : 'red'}};">({{$quote['price']}}, {{$quote['change']}})</span>
							<br/>
							<span style="font-size:11px;">{{$record->account}}</span>
						</td>
						
						<td>{{abs($record->shares)}} @ {{$record->buy_price}}
						@if ( (App\Transaction::isSellStatic($record) && $record->shares >= 0) || (App\Transaction::isBuyStatic($record) && $record->shares <= 0) )
							<span style="color:red;">({{$record->shares}})</span>
						@endif
							<div>${{number_format($cost, 2)}}</div>
						</td>
					
						<td>{{number_format(abs($record->amount), 2)}}
						@if ( (App\Transaction::isSellStatic($record) && $record->amount <= 0) || (App\Transaction::isBuyStatic($record) && $record->amount >= 0) )
							<span style="color:red;">(wrong)</span>
						@endif
							<br/>
							<span style="color:{{$color}}">{{$pl > 0 ? '+' : ''}}{{number_format($pl, 2)}}, {{$plPercent}}%</span>
						</td>
				
					</tr>
				@endforeach
			@endif
			</tbody>
		</table>
		
</div>

@endsection
