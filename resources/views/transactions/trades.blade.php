@extends('layouts.theme1')
@section('content')
@php
	$timeServer = new DateTime();
	$timeEst = new DateTime(null, new DateTimeZone('US/Eastern'));
	$tzOffset = intval($timeServer->format('H')) - intval($timeEst->format('H'));
@endphp
<div class="container">

	@component('transactions.menu-submenu-trades', ['prefix' => $prefix])@endcomponent
	
	<form method="POST" id="form" action="/{{$prefix}}/trades">
				
		@component('control-dropdown-date', ['div' => true, 'months' => $dates['months'], 'years' => $dates['years'], 'days' => $dates['days'], 'filter' => $filter, 'formId' => 'form'])@endcomponent
				
		<div style="float:left;">
			@component('control-dropdown-menu', ['field_name' => 'account_id', 'options' => $accounts, 'selected_option' => $filter['account_id'], 'empty' => 'All Accounts', 'onchange' => "$('#form').submit()"])@endcomponent	
		</div>
			
		<div style="float:left;">
			@component('control-dropdown-menu', ['field_name' => 'subcategory_id', 'options' => $subcategories, 'selected_option' => $filter['subcategory_id'], 'empty' => 'All Transaction Types'])@endcomponent									
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
			<label for="showalldates_flag" class="checkbox-label">All Dates</label>
@if (false)
			<input type="checkbox" name="unreconciled_flag" id="unreconciled_flag" class="form-control-inline" value="1" {{ $filter['unreconciled_flag'] == 1 ? 'checked' : '' }} />
			<label for="unreconciled_flag" class="checkbox-label">Unreconciled</label>
@endif
			<input type="checkbox" name="sold_flag" id="sold_flag" class="form-control-inline" value="1" onclick="$('#unsold_flag').removeAttr('checked'); $('#form').submit();" {{ $filter['sold_flag'] == 1 ? 'checked' : '' }} />
			<label for="sold_flag" class="checkbox-label">Sold</label>
			<input type="checkbox" name="unsold_flag" id="unsold_flag" class="form-control-inline" value="1" onclick="$('#sold_flag').removeAttr('checked'); $('#form').submit();" {{ $filter['unsold_flag'] == 1 ? 'checked' : '' }} />
			<label for="unsold_flag" class="checkbox-label">Unsold</label>
		</div>				
		
		<button type="submit" name="update" class="btn btn-primary" style="font-size:12px; padding:1px 4px; margin:5px 0 5px 0;">Apply Filter</button>
		<a style="font-size:12px; padding:1px 4px; margin:5px 5px 5px 0px;" class="btn btn-primary" href="/transactions/trades/clear-filter">Clear Filter</a>		
		<a style="font-size:12px; padding:1px 4px; margin:5px;" class="btn btn-success" href="/transactions/add-trade">Add Trade</a>
		<span style="font-size:.8em;">NYSE: <span id="clock" ></span>
		<input id="tzoffset" value="{{$tzOffset}}" type="hidden"/>
		{{ csrf_field() }}		
	</form>	   

		<div class="clear"></div>
		
		<h3>
			Trades ({{count($records)}}), Total: ${{number_format(round($totals['total'], 2), 2)}}{{ isset($totals['reconciled']) ? ', P/L: ' . number_format(round($totals['reconciled'], 2),2) . '' : '' }}{{$totals['shares'] > 0 ? ', Shrs: ' . $totals['shares'] : ''}}
		</h3>
		
		<table class="table">
			<tbody>
			@if (isset($records))

				<?php $skip_id = 0; ?>
				
				@foreach($records as $record)
					@php
						$symbolStyle = 'label label-' . (App\Transaction::isTradeOptionStatic($record) ? 'info' : 'primary');

						//dd($record);
						$shares = abs($record->shares);
						$fees = floatval($record->buy_commission) + floatval($record->sell_commission);
						$cost = (abs($record->buy_price) * $shares) + ($fees);
						$pnl = (abs($record->sell_price) * $shares) - $cost;
						$pnlPercent = $cost > 0.0 ? ($pnl / $cost) * 100.0 : 0.0;
						if ($pnlPercent > 0.0)
							$pnlPercent = $pnlPercent < 10.0 ? number_format($pnlPercent, 2) : round(number_format($pnlPercent, 2));
						else
							$pnlPercent = $pnlPercent > -10.0 ? number_format($pnlPercent, 2) : round(number_format($pnlPercent, 2));
						
						$labelStyle = $pnl > 0.0 ? 'success' : 'danger';
						$textStyle = "font-size: 1em; width:10px;";
						$textStyleSm = 'font-size:.9em;';
						$typeStyle = intval($record->type_flag) === TRANSACTION_TYPE_SELL ? 'primary' : 'info';
						$tradeTypeStyle = App\Transaction::isRealTradeStatic($record) ? 'label label-success' : 'label label-warning';
						$date = DateTime::createFromFormat('Y-m-d', $record->transaction_date);
						//$date = App\DateTimeEx::getLocalDateTime($date);
						$rowStyle = $pnl > 0.0 ? 'table-default' : 'table-danger';
						$shares = abs($record->shares) != abs($record->shares_unsold) 
							? '' . abs($record->shares_unsold) . ' / ' . abs($record->shares) . ''
							: abs($record->shares_unsold)
							;
					@endphp
					@if ($skip_id == $record->id)
						<?php $skip_id = 0; ?>
						@continue
					@endif
					
					@if ($filter['showphotos_flag'] && $record->photo)
						@continue
					@endif	
					
					<?php $color = $record->reconciled_flag == 0 ? 'red' : 'default'; ?>
					<tr>
						@if (App\Transaction::isBuyStatic($record) && $record->shares_unsold > 0)
							<td class="">
								<a href='/{{$prefix}}/sell/{{$record->id}}' class="btn btn-xs btn-success" style="margin-bottom:2px;">Sell</a>
								<a href='/{{$prefix}}/add-trade/{{$record->id}}' class="btn btn-xs btn-primary">Buy</a>
							</td>
						@else
							<td class=""><a href='/{{$prefix}}/add-trade/{{$record->id}}' class="btn btn-xs btn-primary">Buy</a></td>
						@endif
						<td class="glyphCol"><a href='/{{$prefix}}/edit-trade/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
						<td style="color:default;">{{$record->transaction_date}}</td>						
						<td><a href="https://finance.yahoo.com/quote/{{$record->symbol}}" target="_blank" class="{{$symbolStyle}}" style="font-size:.95em;">{{$record->symbol}}</a></td>
						<td><a style="font-size:.85em;" href="/{{$prefix}}/view/{{$record->id}}">{{$record->description}}</a></td>
						<td>{{$shares}}</td>
						
						@if (App\Transaction::isBuyStatic($record))
							<td>{{$record->buy_price}}</td>
						@else
							<td>{{$record->buy_price}}<br/>{{$record->sell_price}}</td>
						@endif
						@if (true)
							@if (App\Transaction::isSellStatic($record))
								<td style=""><span style="{{$textStyle}}" class="label label-{{$labelStyle}}">${{number_format($pnl, 2)}}</span></td>					
								<td style=""><span style="{{$textStyle}}" class="label label-{{$labelStyle}}">{{$pnlPercent}}%</span></td>
							@else
								<td></td>
								<td></td>
							@endif
						@else
						<td>{{$record->amount}}
						@if ( (App\Transaction::isSellStatic($record) && $record->amount <= 0) || (App\Transaction::isBuyStatic($record) && $record->amount >= 0) )
							<span style="color:red;">(wrong)</span>
						@endif						
						</td>
						@endif
						@if (!empty($record->notes))
						<td style="width:10px;">	
								<span href="#" data-toggle="tooltip" title="{{$record->notes}}"><span style="color:Purple;" class="glyphCustom glyphicon glyphicon-info-sign"></span></span>
						</td>
						@else
						<td></td>
						@endif
						@if ($record->trade_type_flag == TRADE_TYPE_PAPER)
							<td><span class="label label-warning" style="font-size:.95em;">{{$record->account}}</span></td>
						@else
							<td><a href="/{{$prefix}}/show/account/{{$record->parent_id}}" class="label label-primary" style="font-size:.95em;">{{$record->account}}</a></td>
						@endif
						
						<td>{{$record->lot_id}}</td>
						
						<td class="glyphCol"><a href='/{{$prefix}}/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a></td>
					</tr>
				@endforeach
			@endif
			</tbody>
		</table>
		
</div>

@endsection

<script>

function time()
{
	let	tzoffset = document.getElementById('tzoffset').value;
    var d = new Date();
	
    var s = d.getSeconds();
    s = s < 10 ? '0' + s : s;
    
    var m = d.getMinutes();
    m = m < 10 ? '0' + m : m;

    var h = d.getHours();
    var hUtc = d.getUTCHours();
    hUtc = h - hUtc;

    offset = Number(hUtc) + Number(tzoffset);
    //console.log('offset: ' + offset);

	h -= offset;
    
    $("#clock").html(h + ':' + m + ':' + s + ' (-' + offset + ')');
}

setInterval(time, 1000);

</script>