@extends('layouts.theme1')

@section('content')

<div class="container">

	@component('transactions.menu-submenu-trades', ['prefix' => $prefix])@endcomponent

	<h3>Add Trade</h3>
               
	<form method="POST" action="/{{$prefix}}/create">
									
		@component('control-dropdown-date', ['div' => true, 'months' => $dates['months'], 'years' => $dates['years'], 'days' => $dates['days'], 'filter' => $filter])@endcomponent		
		
		<div><button type="submit" name="copy" class="btn btn-primary btn-xs">Add Trade</button></div>

		<div class="form-group">	
			<div class="radio-group-item">
				<input type="radio" name="type_flag" value="3" class="form-control-inline" {{$tradeType == 'buy' ? 'checked' : ''}}>
				<label for="type_flag" class="radio-label">Buy</label>			
			</div>

			<div class="radio-group-item">
				<input type="radio" name="type_flag" value="4" class="form-control-inline" {{$tradeType == 'sell' ? 'checked' : ''}}>
				<label for="type_flag" class="radio-label">Sell</label>			
			</div>
		</div>		

		<div class="form-group clear">
		@component('control-dropdown-menu', ['prompt' => 'Account:', 'field_name' => 'parent_id', 'options' => $accounts, 'empty' => 'Select', 'selected_option' => isset($trade) ? $trade->parent_id : null])@endcomponent	
		</div>
		
		<div class="clear">
			@if (isset($trade))
				<label for="symbol" class="control-label">Symbol:</label>
				<input style="text-transform: uppercase;" type="text" name="symbol" class="form-control" value="{{$trade->symbol}}" />
				
				@if ($tradeType == 'sell')
					<label for="buy_price" class="control-label">Buy Price:</label>
					<input type="text" name="buy_price" class="form-control" value="{{abs($trade->buy_price)}}" />
				
					<label for="shares" class="control-label">Number of Shares:</label>
					<input type="text" name="shares" class="form-control" value="{{abs($trade->shares)}}" />
					
					<label for="lot_id" class="control-label">Lot:</label>
					<input type="text" name="lot_id" class="form-control" value="{{$trade->lot_id}}" />
				@else
					<label for="shares" class="control-label">Number of Shares:</label>
					<input type="text" name="shares" class="form-control" autofocus />
					
					<label for="buy_price" class="control-label">Buy Price:</label>
					<input type="text" name="buy_price" class="form-control" />
				@endif
			@else
				<label for="symbol" class="control-label">Symbol:</label>
				<input style="text-transform: uppercase;" type="text" name="symbol" class="form-control" autofocus />
				
				<label for="shares" class="control-label">Number of Shares:</label>
				<input type="text" name="shares" class="form-control" />

				<label for="buy_price" class="control-label">Buy Price:</label>
				<input type="text" name="buy_price" class="form-control" value="{{abs($trade->buy_price)}}" />				
			@endif
			
			@if ($tradeType == 'buy')
				<input type="hidden" name="lot_id" id="lot_id" value="" /><!-- lot_id will be generated -->
			@endif
			
			<label for="sell_price" class="control-label">Sell Price:</label>
			<input type="text" name="sell_price" class="form-control" {{isset($trade) ? 'autofocus' : ''}} />
			
			<label for="commission" class="control-label">Commission:</label>
			<input type="text" name="commission" class="form-control" />

			<label for="fees" class="control-label">Fees:</label>
			<input type="text" name="fees" class="form-control" />
			
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
