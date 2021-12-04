@extends('layouts.theme1')
@section('content')
<div class="container">

	@component('transactions.menu-submenu-trades', ['prefix' => $prefix])@endcomponent
	
	<div>		
		<button type="submit" name="update" class="btn btn-success" style="font-size:12px; padding:1px 4px; margin: 5px 5px 0 5px;">Refresh</button>
		<a style="font-size:12px; padding:1px 4px; margin-top: 5px;" class="btn btn-success" href="/transactions/add-trade">Add Trade</a>
	</div>

	<form method="POST" id="form" action="/{{$prefix}}/positions">
				
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
		
		@php
			// get the totals
			$dca = number_format($totals['dca'], 2);
			$cost = number_format(abs(round($totals['total'])));
			$profit = number_format(round($totals['profit'], 2), 2);
			
			$profitPercent = 0;
			if ($totals['profit'] != 0.0 && $totals['total'] != 0.0)
				$profitPercent = number_format( ($totals['profit'] / abs($totals['total'])) * 100.0, 2);

			// reconciled, untested
			$profitReconciled = isset($totals['reconciled']) ? $totals['reconciled'] : 0;
			$profitPercentReconciled = 0;
			if ($profitReconciled != 0.0 && $totals['total'] != 0.0)
				$profitPercentReconciled = number_format(($profitReconciled / abs($totals['total']) * 100.0), 2);
			$profitColor = ($profit >= 0.0) ? 'blue' : 'red';
				
		@endphp
		
		<div class="hidden">{{ isset($totals['reconciled']) ? 'P/L: ' . round($totals['reconciled'], 2) . ', ' . $profitPercentReconciled : '' }}P/L: <span style="color:{{$totals['profit'] >= 0.0 ? 'black' : 'red'}}">${{$profit}}, {{$profitPercent}}%</span></div>
		
		<div class="drop-box text-center number-box number-box-sm orange">
			<div>Positions</div>
			<p style="">{{count($records)}}</p>
		</div>	
		
		<div class="drop-box text-center number-box number-box-sm green">
			<div>Shares</div>
			<p style="">{{$totals['shares']}}</p>
		</div>	
		
		<div class="drop-box text-center number-box number-box-sm purple">
			<div>DCA</div>
			<p style="">{{round($totals['dca'], 3)}}</p>
		</div>
		
		<div class="drop-box text-center number-box number-box-sm darkBlue">
			<div>Cost</div>
			<p style="">{{$cost}}</p>
		</div>	

		<div class="drop-box text-center number-box number-box-sm {{$profitColor}}">
			<div>P/L</div>
			<p style="">{{ $profit }}</p>
			<p style="font-size:.8em;">{{$profitPercent}}%</p>
		</div>
		
		@if (isset($filter['singleSymbol']))
			@php
				$quote = $totals[$filter['symbol']];
			@endphp
						
			<div class="drop-box text-center number-box number-box-sm {{$profitColor}}">
				<div>Quote</div>
				<p style="">{{$quote['price']}}</p>
				<p style="font-size:.8em;">{{$quote['change']}}</p>
			</div>
		@endif
			
		<table class="table table-sm">
			<tbody>
			@if (isset($records))
				@foreach($records as $record)
					@php
						$quote = $totals[$record->symbol];
						$cost = abs($record->shares_unsold * $record->buy_price);
						$current_value = (floatval($quote['price']) * abs($record->shares_unsold));
						if ($cost > 0.0)
						{
							$pl = round($current_value - $cost, 2);
							$plPercent = number_format(($pl / $cost) * 100.0, 2);
						}
						else
						{
							$pl = 0;
							$plPercent = 0;
						}

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
						
						<td>{{abs($record->shares_unsold)}} @ {{$record->buy_price}}
						@if ( (App\Transaction::isSellStatic($record) && $record->shares_unsold >= 0) || (App\Transaction::isBuyStatic($record) && $record->shares_unsold <= 0) )
							<span style="color:red;">({{$record->shares_unsold}})</span>
						@endif
							<div>${{number_format($cost, 2)}}</div>
						</td>
					
						<td>{{number_format($current_value, 2)}}
						@if ( (App\Transaction::isSellStatic($record) && $record->amount <= 0) || (App\Transaction::isBuyStatic($record) && $record->amount >= 0) )
							<span style="color:red;">(wrong)</span>
						@endif
							<div style="color:{{$color}}">{{$pl > 0 ? '+' : ''}}{{number_format($pl, 2)}}, {{$plPercent}}%</div>
						</td>
				
					</tr>
				@endforeach
			@endif
			</tbody>
		</table>
		
</div>

@endsection
