@extends('layouts.theme1')
@section('content')
@php
	$single = (isset($filter['singleSymbol']) && $filter['singleSymbol']);

	// reconciled, untested
	$profitReconciled = isset($totals['reconciled']) ? $totals['reconciled'] : 0;
	$profitPercentReconciled = 0;
	if ($profitReconciled != 0.0 && $totals['total'] != 0.0)
		$profitPercentReconciled = number_format(($profitReconciled / abs($totals['total']) * 100.0), 2);
		
	$profitColor = ($totals['profit'] >= 0.0) ? 'blue' : 'red';
	$profit = $totals['profit'];
	$profitPercent = number_format($totals['profitPercent'], 2);
	$cost = $totals['total'];
@endphp

<div class="container">

	@component('transactions.menu-submenu-trades', ['prefix' => $prefix])@endcomponent
	
	<form method="POST" id="form" action="/{{$prefix}}/positions">
	
		<div>		
			<button type="submit" name="update" class="btn btn-success" style="font-size:12px; padding:1px 4px; margin: 5px 5px 0 5px;">Refresh</button>
			<a style="font-size:12px; padding:1px 4px; margin-top: 5px;" class="btn btn-success" href="/transactions/add-trade">Add Trade</a>
		</div>
			
		@component('control-dropdown-date', ['div' => true, 'months' => $dates['months'], 'years' => $dates['years'], 'days' => $dates['days'], 'filter' => $filter, 'formId' => 'form'])@endcomponent
		
		<div class="float-left" style="background-color:xyellow; font-size:12px; margin: 0 10px 0 0; padding:0;">
			<input type="checkbox" name="showalldates_flag" id="showalldates_flag" style="margin: 5px;" class="form-control-inline" value="1"  onclick="$('#form').submit();" {{ $filter['showalldates_flag'] == 0 ? '' : 'checked' }} />
			<label for="showalldates_flag" class="checkbox-label" style="padding:2px;">All Dates</label>
		</div>				
				
		<div class="float-left">
			@component('control-dropdown-menu', ['field_name' => 'account_id', 'options' => $accounts, 'selected_option' => $filter['account_id'], 'empty' => 'All Accounts'])@endcomponent	
		</div>
			
		<div class="float-left">
			@component('control-dropdown-menu', ['onchange' => "$('#form').submit();", 'field_name' => 'symbol', 'options' => $symbols, 'selected_option' => $filter['symbol'], 'empty' => 'All Symbols'])@endcomponent				
		</div>
	
		{{ csrf_field() }}		
	</form>	   

		<div class="clear"></div>
				
		<div class="hidden">{{ isset($totals['reconciled']) ? 'P/L: ' . round($totals['reconciled'], 2) . ', ' . $profitPercentReconciled : '' }}P/L: <span style="color:{{$totals['profit'] >= 0.0 ? 'black' : 'red'}}">${{$profit}}, {{$profitPercent}}%</span></div>
		
		<div class="drop-box text-center number-box number-box-sm purple">
			<div>Cost</div>
			<p style="">{{number_format($cost, 2)}}</p>
			<p style="font-size:.9em;">{{$totals['shares']}} shrs</p>
		</div>	
		
		@if ($single)
			<div class="drop-box text-center number-box number-box-sm pineForest">
				<div>DCA</div>
				<p style="">{{round($totals['dca'], 3)}}</p>
				<p style="font-size:.9em;">({{count($records)}} lots)</p>
			</div>	
		@endif
					
		@if (isset($filter['singleSymbol']) && isset($totals[$filter['symbol']]))
			@php
				$quote = $totals[$filter['symbol']];
			@endphp
						
			<div class="drop-box text-center number-box number-box-sm {{$profitColor}}">
				<div>Quote</div>
				<p style="">{{$quote['price']}}</p>
				<p style="font-size:.8em;">{{$quote['change']['amount'] . ' ' . $quote['change']['percent']}}</p>
			</div>
		@endif
		
		<div class="drop-box text-center number-box number-box-sm {{$profitColor}}">
			<div>P/L</div>
			<p style="">{{number_format($profit, 2)}}</p>
			<p style="font-size:.8em;">{{$profitPercent}}%</p>
		</div>
	
		<table class="table table-sm">
			<thead>
				<tr>
					<th>Symbol</th><th>Curr/DCA</th><th>Curr/Cost</th><th>Today</th><th>Total</th>
				</tr>
			</theader>
			<tbody>
			@if (isset($totals) && count($totals) > 0)
				@foreach($totals['holdings'] as $quote)
					@php
						$isMobile = App\Tools::isMobile();
						$cost = $quote['total'];
						$current_value = (floatval($quote['price']) * $quote['shares']);
						$symbol = $quote['symbol'];
						$color = $quote['isProfit'] ? 'black' : 'red';
						$colorQuote = $quote['up'] ? 'black' : 'red';
						$lots = $quote['lots'];
						$lotsSuffix = $lots === 1 ? 'lot' : 'lots';
						$fontSize = $isMobile ? 'font-size:11px' : '';
						$fontSize10 = $isMobile ? 'font-size:10px;' : 'font-size:12px;';
						$btnSize = $isMobile ? 'xs' : 'md';
					@endphp
					<tr>
						<td>						
							<a href="https://finance.yahoo.com/quote/{{$symbol}}" target="_blank" class="btn btn-{{$btnSize}} btn-primary" style="font-weight:bold;">{{$symbol}}</a>
							<div style="{{$fontSize10}}">
								<div>{{$quote['shares']}} shrs</div>
								<div>{{$lots}} {{$lotsSuffix}}</div>
							</div>
						</td>
						<td>
							<div style="{{$fontSize}}">
								<div>${{$quote['price']}}</div>
								<div>${{number_format($quote['dca'], 2)}}</div>
							</div>
						</td>
						<td>
							<div style="{{$fontSize}}">
								<div>${{number_format($current_value, 2)}}</div>
								<div>${{number_format($cost, 2)}}</div>
							</div>
						</td>
						<td>
							<span style="color:{{$colorQuote}}; {{$fontSize}}">
								<div>{{$quote['change']['percent']}}%</div>
								<div>${{$quote['change']['amount']}}</div>
							</span>
						</td>
						<td>
							<div style="color:{{$color}}">
								<div>{{$quote['plPercent']}}%</div>
								<div style="{{$fontSize}}">
									<div>${{number_format($quote['profit'], 2)}}</div>
								</div>
							</div>
						</td>
					</tr>
				@endforeach
			@endif
			</tbody>
		</table>
		
</div>

@endsection
