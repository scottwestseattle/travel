@extends('layouts.theme1')
@section('content')

<div class="container">

	@component('transactions.menu-submenu-trades', ['prefix' => $prefix])@endcomponent
	
	<form method="POST" action="/{{$prefix}}/trades">
				
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
						<td>{{App\Transaction::isBuyStatic($record) ? 'BUY' : 'SELL'}}</td>
						<td><a href="https://finance.yahoo.com/quote/{{$record->symbol}}" target="_blank">{{$record->symbol}}</a></td>
						<td>{{abs($record->shares)}}
						@if ( (App\Transaction::isSellStatic($record) && $record->shares >= 0) || (App\Transaction::isBuyStatic($record) && $record->shares <= 0) )
							<span style="color:red;">({{$record->shares}})</span>
						@endif
						</td>
						
						<td>{{$record->buy_price}}</td>
						
						<td>{{$record->amount}}
						@if ( (App\Transaction::isSellStatic($record) && $record->amount <= 0) || (App\Transaction::isBuyStatic($record) && $record->amount >= 0) )
							<span style="color:red;">(wrong)</span>
						@endif						
						</td>
						
						<td><a href="/{{$prefix}}/view/{{$record->id}}">{{$record->description}}</a></td>
						<td>{{$record->notes}}</td>
						<td><a href="/{{$prefix}}/show/account/{{$record->parent_id}}">{{$record->account}}</a></td>
						<td>{{$record->lot_id}}</td>
						
						<td class="glyphCol"><a href='/{{$prefix}}/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a></td>
					</tr>
				@endforeach
			@endif
			</tbody>
		</table>
		
</div>

@endsection
