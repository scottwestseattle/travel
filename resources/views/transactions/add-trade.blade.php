@extends('layouts.theme1')
@section('content')
@php
	$accountId = isset($trade) ? $trade->parent_id : $accountId;
	$isSellOption = isset($trade) && $typeFlag == TRANSACTION_TYPE_STC_CALL;
	$isSell = $typeFlag == TRANSACTION_TYPE_SELL || $typeFlag == TRANSACTION_TYPE_STC_CALL;
	$expDate = isset($trade->option_expiration_date) ? App\DateTimeEx::reformatDateString($trade->option_expiration_date, 'Y-m-d', 'm/d') : '';
@endphp
<div class="container">

	@component('transactions.menu-submenu-trades', ['prefix' => $prefix])@endcomponent

	<h3>Add Trade</h3>
               
	<form method="POST" action="/{{$prefix}}/create">
									
		@component('control-dropdown-date', ['div' => true, 'months' => $dates['months'], 'years' => $dates['years'], 'days' => $dates['days'], 'filter' => $filter])@endcomponent		
		
		<div><button type="submit" name="copy" class="btn btn-primary btn-xs">Add Trade</button></div>

		@if ($isSell)
			<input type="hidden" name="type_flag" value="{{$typeFlag}}" >
			<input type="hidden" name="trade_type_flag" id="trade_type_flag" value="{{$trade->trade_type_flag}}" />
		@else
			<div class="form-group">	
				<div class="radio-group-item">
					<input type="radio" name="type_flag" value="3" class="form-control-inline" onchange="$('#hideOptionFields').addClass('hidden');" {{$typeFlag == TRANSACTION_TYPE_BUY ? 'checked' : ''}}>
					<label for="type_flag" class="radio-label">Buy</label>			
				</div>

				<div class="radio-group-item">
					<input type="radio" name="type_flag" value="4" class="form-control-inline" onchange="$('#hideOptionFields').addClass('hidden');"  {{$typeFlag == TRANSACTION_TYPE_SELL ? 'checked' : ''}}>
					<label for="type_flag" class="radio-label">Sell</label>			
				</div>
			
				<div class="radio-group-item">
					<input type="radio" name="type_flag" value="{{TRANSACTION_TYPE_BTO_CALL}}" class="form-control-inline" onchange="$('#hideOptionFields').removeClass('hidden');"  {{$typeFlag == TRANSACTION_TYPE_BTO_CALL ? 'checked' : ''}} />
					<label for="type_flag" class="radio-label">BTO</label>			
				</div>	

				<div class="radio-group-item">
					<input type="radio" name="type_flag" value="{{TRANSACTION_TYPE_STC_CALL}}" class="form-control-inline" onchange="$('#hideOptionFields').addClass('hidden');"   {{$typeFlag == TRANSACTION_TYPE_STC_CALL ? 'checked' : ''}} />
					<label for="type_flag" class="radio-label">STC</label>			
				</div>	
			</div>		
			<div class="form-group clear">
				@component('control-dropdown-menu', ['prompt' => 'Account:', 'field_name' => 'parent_id', 'options' => $accounts, 'empty' => 'Select', 'selected_option' => $accountId])@endcomponent	
			</div>
		@endif

		<div class="clear">
			@if (isset($trade))
				@if ($isSell)
					<h3>
					@if ($isSellOption)
						<div>{{$trade->symbol}} BTO ({{$trade->shares / 100}}) {{$expDate}} ${{abs($trade->option_strike_price)}}</div>
					@else
						<div>{{$trade->symbol}}</div>
					@endif
						<div style="font-size:15px; font-weight:400">
							Cost: ${{number_format(abs($trade->buy_price), 2)}}&nbsp&nbsp 
							Fees: ${{number_format($trade->buy_commission, 2)}}&nbsp&nbsp
							Lot: {{$trade->lot_id}}							
						</div>
					</h3>
					
					<input type="hidden" name="symbol" value="{{$trade->symbol}}" />
					<input type="hidden" name="buy_price" value="{{abs($trade->buy_price)}}" />
					<input type="hidden" name="buy_commission" value="{{abs($trade->buy_commission)}}" />
					<input type="hidden" name="parent_id" value="{{$trade->parent_id}}" />
					<input type="hidden" name="lot_id" value="{{$trade->lot_id}}" />
					<input type="hidden" name="trade_type_flag" id="trade_type_flag" value="{{$trade->trade_type_flag}}" />
														
					<label for="shares" class="control-label">Number of Shares:</label>
					<input type="text" name="shares" class="form-control" value="{{abs($trade->shares_unsold)}}" />
					
					@if ($isSellOption)
						<input type="hidden" name="option_strike_price" class="form-control" value="{{abs($trade->option_strike_price)}}" />
						<input type="hidden" name="option_expiration_date" class="form-control" value="{{$expDate}}" />
					@else
						<div id="hideOptionFields" class="{{$typeFlag !== TRANSACTION_TYPE_STC_CALL ? 'hidden' : ''}}">
							<label for="option_expiration_date" class="control-label">Option Expiration Date (MM/DD):</label>
							<input type="text" name="option_expiration_date" class="form-control" value="{{($trade->option_expiration_date)}}" />
							<label for="option_strike_price" class="control-label">Option Strike Price:</label>
							<input type="text" name="option_strike_price" class="form-control" value="{{abs($trade->option_strike_price)}}" />
						</div>
					@endif			
				@else
					<label for="symbol" class="control-label">Symbol:</label>
					<input style="text-transform: uppercase;" type="text" name="symbol" class="form-control" value="{{$trade->symbol}}" />			

					<div class="">
						<input type="checkbox" name="trade_type_flag" id="trade_type_flag" {{$tradeTypeFlag == TRADE_TYPE_PAPER ? 'checked' : ''}} />
						<label for="trade_type_flag" class="checkbox-big-label">Paper Trade</label>
					</div>							

					<label for="shares" class="control-label">Number of Shares:</label>
					<input type="text" name="shares" class="form-control" autofocus />
					
					<div id="hideOptionFields" class="{{$typeFlag !== TRANSACTION_TYPE_BTO_CALL ? 'hidden' : ''}}">
						<label for="option_expiration_date" class="control-label">Option Expiration Date (MM/DD):</label>
						<input type="text" name="option_expiration_date" class="form-control" value="{{$expDate}}"/>
						<label for="option_strike_price" class="control-label">Option Strike Price:</label>
						<input type="text" name="option_strike_price" class="form-control" value="{{$trade->option_strike_price}}"/>
					</div>
			
					<label for="buy_price" class="control-label">Buy Price:</label>
					<input type="text" name="buy_price" class="form-control" />
					
					<label for="commission" class="control-label">Buy Commission / Fees:</label>
					<input type="text" name="buy_commission" class="form-control" />
				@endif
				<label for="sell_price" class="control-label">Sell Price:</label>
				<input type="text" name="sell_price" class="form-control" {{isset($trade) ? 'autofocus' : ''}} />
				<label for="buy-commission" class="control-label">Sell Commission / Fees:</label>
				<input type="text" name="sell_commission" class="form-control" />
			@else
				<label for="symbol" class="control-label">Symbol:</label>
				<input style="text-transform: uppercase;" type="text" name="symbol" class="form-control" autofocus />

				<div class="">
					<input type="checkbox" name="trade_type_flag" id="trade_type_flag" {{$tradeTypeFlag == TRADE_TYPE_PAPER ? 'checked' : ''}} />
					<label for="trade_type_flag" class="checkbox-big-label">Paper Trade</label>
				</div>							

				<label for="shares" class="control-label">Number of Shares:</label>
				<input type="text" name="shares" class="form-control" />

				<div id="hideOptionFields" class="{{$typeFlag !== TRANSACTION_TYPE_BTO_CALL ? 'hidden' : ''}}">
					<label for="option_expiration_date" class="control-label">Option Expiration Date (MM/DD):</label>
					<input type="text" name="option_expiration_date" class="form-control" />
					<label for="option_strike_price" class="control-label">Option Strike Price:</label>
					<input type="text" name="option_strike_price" class="form-control" />
				</div>
				
				<label for="buy_price" class="control-label">Buy Price:</label>
				<input type="text" name="buy_price" class="form-control" />				
				
				<label for="commission" class="control-label">BUY Commission / Fees:</label>
				<input type="text" name="buy_commission" class="form-control" />
			@endif

			@if ($typeFlag == TRANSACTION_TYPE_BUY || $typeFlag == TRANSACTION_TYPE_BTO_CALL)
				<input type="hidden" name="lot_id" id="lot_id" value="" /><!-- lot_id will be generated -->
			@endif
						
			<label for="notes" class="control-label">Notes:</label>
			<input type="text" name="notes" class="form-control" />
		</div>

		<div class="form-group">
			<input type="checkbox" name="reconciled_flag" id="reconciled_flag" class="form-control-inline" checked="check" />
			<label for="reconciled_flag" class="checkbox-label">Reconciled</label>
		</div>
		
		@if (false)
		<div>
			50 shares of VOO at $205.25 per share, total price: $10262.50
		</div>
		@endif
		
		<div class="submit-button">
			<button type="submit" name="update" class="btn btn-primary">Add Trade</button>
		</div>
						
		{{ csrf_field() }}
	
	</form>

</div>

@endsection
