@extends('layouts.theme1')
@section('content')
@php
	$isMobile = App\Tools::isMobile();
@endphp
<div class="container">

	@component('transactions.menu-submenu-trades', ['prefix' => $prefix])@endcomponent
	
	<form method="POST" id="form" action="/{{$prefix}}/profit-loss">
				
		@component('control-dropdown-date', ['div' => true, 'months' => $dates['months'], 'years' => $dates['years'], 'days' => $dates['days'], 'filter' => $filter, 'formId' => 'form'])@endcomponent
				
		<div style="float:left;">
			@component('control-dropdown-menu', ['field_name' => 'account_id', 'options' => $accounts, 'selected_option' => $filter['account_id'], 'empty' => 'All Accounts', 'onchange' => "$('#form').submit()"])@endcomponent	
		</div>
		
		<div style="float:left;">
			@component('control-dropdown-menu', ['field_name' => 'symbol', 'options' => $symbols, 'selected_option' => $filter['symbol'], 'empty' => 'All Symbols', 'onchange' => "$('#form').submit()"])@endcomponent				
		</div>
		
		<div>
			<input style="font-size:16px; height:24px; width:100px; margin-left:1px;" type="text" id="search" name="search" class="" value="{{$filter['search']}}"></input>		
			<a href='#' onclick="event.preventDefault(); $('#search').val(''); $('#form').submit();";>
				<span class="glyphCustom glyphicon glyphicon-remove" style="font-size:1.3em; margin-left:1px;"></span>
			</a>
		</div>
		
		<div>	
			<input type="checkbox" name="showalldates_flag" id="showalldates_flag" class="form-control-inline" value="1" onclick="$('#form').submit();" {{ $filter['showalldates_flag'] == 1 ? 'checked' : '' }} />
			<label for="showalldates_flag" class="checkbox-label">Show All Dates</label>
		</div>				
		
		<button type="submit" name="update" class="btn btn-primary" style="font-size:12px; padding:1px 4px; margin:5px;">Apply Filter</button>
		
		<a style="font-size:12px; padding:1px 4px; margin:5px;" class="btn btn-success" href="/transactions/add-trade">Add Trade</a>
		
		{{ csrf_field() }}		
	</form>	   

		<div class="clear"></div>
		
		<h3>
			Trades ({{count($records)}}), Total: ${{number_format(round($totals['profit'], 2), 2)}}
		</h3>
		
		<table class="table table-sm table-striped">
			<thead>
				<tr>
					<td>Date</td>
					<td>Symbol</td>
					@if (!$isMobile)
						<td>Shares</td>
					@endif
					<td>P/L $</td>
					<td>P/L %</td>
				</tr>
			</thead>
			<tbody>
			@if (isset($records))
				@foreach($records as $record)
					@php
						//dd($record);
						$shares = abs($record->shares);
						$fees = floatval($record->buy_commission) + floatval($record->sell_commission);
						$cost = (abs($record->buy_price) * $shares) + ($fees);
						$pnl = (abs($record->sell_price) * $shares) - $cost;
						$pnlPercent = ($pnl / $cost) * 100.0;
						if ($pnlPercent > 0.0)
							$pnlPercent = $pnlPercent < 10.0 ? number_format($pnlPercent, 2) : round(number_format($pnlPercent, 2));
						else
							$pnlPercent = $pnlPercent > -10.0 ? number_format($pnlPercent, 2) : round(number_format($pnlPercent, 2));
						$isRealTrade = App\Transaction::isRealTradeStatic($record);
						$labelStyle = $pnl > 0.0 ? 'success' : 'danger';
						$textStylePaperTrade = $textStyle = "font-size: 1em; width:10px;";
						$textStylePaperTrade .= $isRealTrade ? '' : ' color:#996515;';
						$textStyleSm = 'font-size:.9em;';
						$typeStyle = intval($record->type_flag) === TRANSACTION_TYPE_SELL ? 'primary' : 'info';
						$tradeTypeStyle = $isRealTrade ? 'label label-success' : 'label label-warning';
						$date = DateTime::createFromFormat('Y-m-d', $record->transaction_date);
						//$date = App\DateTimeEx::getLocalDateTime($date);
						$rowStyle = $pnl > 0.0 ? 'table-default' : 'table-danger';
					@endphp
					<tr class="table-danger">
						<td style="{{$textStyleSm}}">{{$date->format('m-d-y, l')}}</td>
						<td style=""><span style="{{$textStyle}}" class="label label-{{$typeStyle}}">{{$record->symbol}}</span>
							@if ($isMobile)
								<div style="font-size:.8em; margin-top: 2px;"><div style="{{$textStylePaperTrade}}">{{$shares}}</div></div>
							@endif						
						</td>						
						@if (!$isMobile)
							<td style="font-size:.9em;"><span style="{{$textStyle}}" class="{{$tradeTypeStyle}}">{{$shares}}</span></td>
						@endif

						<td style=""><span style="{{$textStyle}}" class="label label-{{$labelStyle}}">${{number_format($pnl, 2)}}</span></td>					
						<td style=""><span style="{{$textStyle}}" class="label label-{{$labelStyle}}">{{$pnlPercent}}%</span></td>
					</tr>
				@endforeach
			@endif
			</tbody>
		</table>
		
</div>

@endsection
