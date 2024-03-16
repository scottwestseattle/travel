@extends('layouts.theme1')
@section('content')

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
			<label for="showalldates_flag" class="checkbox-label">Show All Dates</label>
			<input type="checkbox" name="unreconciled_flag" id="unreconciled_flag" class="form-control-inline" value="1" {{ $filter['unreconciled_flag'] == 1 ? 'checked' : '' }} />
			<label for="unreconciled_flag" class="checkbox-label">Unreconciled</label>
			<input type="checkbox" name="sold_flag" id="sold_flag" class="form-control-inline" value="1" onclick="$('#form').submit();" {{ $filter['sold_flag'] == 1 ? 'checked' : '' }} />
			<label for="sold_flag" class="checkbox-label">Sold</label>
			<input type="checkbox" name="unsold_flag" id="unsold_flag" class="form-control-inline" value="1" onclick="$('#form').submit();" {{ $filter['unsold_flag'] == 1 ? 'checked' : '' }} />
			<label for="unsold_flag" class="checkbox-label">Unsold</label>
		</div>				
		
		<button type="submit" name="update" class="btn btn-primary" style="font-size:12px; padding:1px 4px; margin:5px;">Apply Filter</button>
		
		<a style="font-size:12px; padding:1px 4px; margin:5px;" class="btn btn-success" href="/transactions/add-trade">Add Trade</a>
		
		{{ csrf_field() }}		
	</form>	   

		<div class="clear"></div>
		
		<h3>
			Trades ({{count($records)}}), Total: ${{number_format(round($totals['total'], 2), 2)}}{{ isset($totals['reconciled']) ? ', P/L: ' . number_format(round($totals['reconciled'], 2),2) . '' : '' }}{{$totals['shares'] > 0 ? ', Shares Remaining: ' . $totals['shares'] : ''}}
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
						$pnlPercent = ($pnl / $cost) * 100.0;
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
							<td class="glyphCol"><a href='/{{$prefix}}/sell/{{$record->id}}'>Sell</a></td>
						@else
							<td class="glyphCol"><a href='/{{$prefix}}/add-trade/{{$record->id}}'>Trade</a></td>
						@endif
						<td class="glyphCol"><a href='/{{$prefix}}/edit-trade/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
						<td style="color:default;">{{$record->transaction_date}}</td>						
						<td><a href="https://finance.yahoo.com/quote/{{$record->symbol}}" target="_blank" class="{{$symbolStyle}}" style="font-size:.95em;">{{$record->symbol}}</a></td>
						<td><a style="font-size:.85em;" href="/{{$prefix}}/view/{{$record->id}}">{{$record->description}}</a></td>
						<td>{{abs($record->shares)}}
						@if ( (App\Transaction::isSellStatic($record) && $record->shares >= 0) || (App\Transaction::isBuyStatic($record) && $record->shares <= 0) )
							<span style="color:red;">({{$record->shares}})</span>
						@endif
						</td>
						
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
						<td style="width:10px;">	
							@if (!empty($record->notes))
								<span href="#" data-toggle="tooltip" title="{{$record->notes}}"><span style="color:Purple;" class="glyphCustom glyphicon glyphicon-info-sign"></span></span>
							@endif
						</td>
						@if ($record->trade_type_flag == TRADE_TYPE_PAPER)
							<td><span class="label label-warning" style="font-size:.95em;">PAPER</span></td>
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
