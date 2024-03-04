@extends('layouts.theme1')

@section('content')

<div class="container">

	@component('transactions.menu-submenu-trades', ['prefix' => $prefix])@endcomponent

	<h3>Edit Trade</h3>

	<form method="POST" action="/{{$prefix}}/update/{{$record->id}}">

		<input type="hidden" name="category_id" value="{{$record->category_id}}" />
		<input type="hidden" name="subcategory_id" value="{{$record->subcategory_id}}" />
	
		@component('control-dropdown-date', ['div' => true, 'months' => $dates['months'], 'years' => $dates['years'], 'days' => $dates['days'], 'filter' => $filter])@endcomponent		
	
		<div><button type="submit" name="copy" class="btn btn-primary btn-xs">Save</button></div>
	
		<div class="form-group clear">
			@component('control-dropdown-menu', ['prompt' => 'Account:', 'field_name' => 'parent_id', 'options' => $accounts, 'selected_option' => $record->parent_id])@endcomponent
			<input type="checkbox" name="trade_type_flag" id="trade_type_flag" class="" style="margin-left:15px;" {{$record->isRealTrade() ? '' : 'CHECKED'}} />
			<label for="trade_type_flag" class="checkbox-big-label">Paper Trade</label>

			<div>
				<div class="radio-group-item">
					<input type="radio" name="type_flag" value="{{TRANSACTION_TYPE_BUY}}" class="form-control-inline" onchange="$('#hideOptionFields').addClass('hidden');" {{$record->type_flag == TRANSACTION_TYPE_BUY ? 'checked' : '' }} />
					<label for="type_flag" class="radio-label">Buy</label>
				</div>
			</div>
			
			<div class="radio-group-item">
				<input type="radio" name="type_flag" value="{{TRANSACTION_TYPE_SELL}}" class="form-control-inline" onchange="$('#hideOptionFields').addClass('hidden');" {{$record->type_flag == TRANSACTION_TYPE_SELL ? 'checked' : '' }} />
				<label for="type_flag" class="radio-label">Sell</label>			
			</div>	

			<div class="radio-group-item">
				<input type="radio" name="type_flag" value="{{TRANSACTION_TYPE_BTO_CALL}}" class="form-control-inline" onchange="$('#hideOptionFields').removeClass('hidden');" {{$record->type_flag == TRANSACTION_TYPE_BTO_CALL ? 'checked' : '' }} />
				<label for="type_flag" class="radio-label">BTO</label>			
			</div>	

			<div class="radio-group-item">
				<input type="radio" name="type_flag" value="{{TRANSACTION_TYPE_STC_CALL}}" class="form-control-inline" onchange="$('#hideOptionFields').removeClass('hidden');" {{$record->type_flag == TRANSACTION_TYPE_STC_CALL ? 'checked' : '' }} />
				<label for="type_flag" class="radio-label">STC</label>			
			</div>	
		</div>
		
		<div class="form-group clear">
			<label for="symbol" class="control-label">Symbol:</label>
			<input type="text" name="symbol" class="form-control" value="{{$record->symbol}}" />
			<label for="shares" class="control-label">Number of Shares:</label>
			<input type="text" name="shares" class="form-control" value="{{abs($record->shares)}}" />

			<div id="hideOptionFields" class="{{App\Transaction::isTradeOptionStatic($record) ? '' : 'hidden'}}">
				<label for="option_expiration_date" class="control-label">Option Expiration Date (MM/DD):</label>
				<input type="text" name="option_expiration_date" class="form-control" value="{{App\DateTimeEx::reformatDateString($record->option_expiration_date, 'Y-m-d', 'm/d')}}" />
				<label for="option_strike_price" class="control-label">Option Strike Price:</label>
				<input type="text" name="option_strike_price" class="form-control" value="{{$record->option_strike_price}}" />
			</div>
				
			@if ($record->isBuy())
				<label for="shares_unsold" class="control-label">Shares Unsold:</label>
				<input type="text" name="shares_unsold" class="form-control" value="{{$record->shares_unsold}}" />
			@endif

			<label for="buy_price" class="control-label">Buy Price:</label>
			<input type="text" name="buy_price" class="form-control" value="{{$record->buy_price}}" />
			
			<label for="buy_commission" class="control-label">BUY Commission / Fees:</label>
			<input type="text" name="buy_commission" class="form-control" value="{{$record->buy_commission}}" />
			
			@if ($record->isSell())
				<label for="sell_price" class="control-label">Sell Price:</label>
				<input type="text" name="sell_price" class="form-control" value="{{$record->sell_price}}" />
				<label for="sell_commission" class="control-label">SELL Commission / Fees:</label>
				<input type="text" name="sell_commission" class="form-control" value="{{$record->sell_commission}}" />
			@endif
		</div>

		<div class="form-group clear">

		</div>		
		
		<div class="form-group">
			<label for="lot_id" class="control-label">Lot:</label>
			<input type="text" name="lot_id" class="form-control" value="{{$record->lot_id}}"></input>
		</div>
										
		<div class="clear">					
			<label for="notes" class="control-label">Notes:</label>
			<input type="text" name="notes" class="form-control" value="{{$record->notes}}" />			
		</div>
			
		<div class="form-group">
			<input type="checkbox" name="reconciled_flag" id="reconciled_flag" class="form-control-inline" value="{{$record->reconciled_flag }}" {{ ($record->reconciled_flag) ? 'checked' : '' }} />
			<label for="reconciled_flag" class="checkbox-label">Reconciled</label>
		</div>		
				
		<div class="submit-button">
			<button type="submit" name="update" class="btn btn-primary">Update</button>
		</div>

		{{ csrf_field() }}
		
	</form>

</div>

@stop
