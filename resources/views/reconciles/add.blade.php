@extends('layouts.theme1')

@section('content')

<div class="container">

	@component($prefix . '.menu-submenu', ['prefix' => $prefix])@endcomponent

	<h1>{{$title}} Account</h1>
               
	<form method="POST" action="/reconciles/create">
							
		<div style="">
			<label for="account_id" class="control-label">Account:</label>
			<span style="font-size: 20px; margin-left: 10px;">{{$account->name}}</span>
			<input type="hidden" name="account_id" value="{{$account->id}}" />
		</div>
			
		<label for="notes" class="control-label">Notes:</label>
		<input type="text" name="notes" class="form-control"></input>	
			
		<div style="">
			<label for="month" class="control-label">Reconcile Date:</label>
			@component('control-dropdown-date', ['div' => true, 'months' => $dates['months'], 'years' => $dates['years'], 'days' => $dates['days'], 'filter' => $filter])@endcomponent
		</div>
			
		<input type="hidden" name="balance" id="balance" value="{{$balance}}" />
		<label for="balance" class="control-label">Reconciled Balance: </label>
		<div style="margin-top:20px;"><span id="displayBalance" style="font-size: 20pt; margin-right:20px;">${{number_format(abs($balance), 2)}}</span>
			<a href='#' onclick="event.preventDefault(); updateBalance({{$account->id}});"><span style="font-size:16pt;" class="glyphCustom glyphicon glyphicon-refresh"></span></a>
		</div>
		<div style="font-size:10pt; margin-top:20px;"><a href="/transactions/show/account/{{$account->id}}" target="_blank">Show Transactions</a></div>
							
		<div class="submit-button" style="margin-top:20px;">
			<button type="submit" name="update" class="btn btn-primary">Add Reconcile Record</button>
		</div>

		<!-- input type="text" name="subtotal_xe1" class="form-control-inline"></input -->	

		@if ($account->multiple_balances_flag == 1)
		<?php $subtotals = $account->getSubtotals(); ?>
		<h3>Subtotals</h3>
		<div>
		<input type="text" name="subtotal_label1" class="form-control-inline" value="{{$subtotals['label1']}}" />
		<input type="text" name="subtotal1" id="subtotal1" onblur="setTotal()" class="form-control-inline" value="{{$subtotals['value1']}}" />
		</div>

		<div>
		<input type="text" name="subtotal_label2" class="form-control-inline" value="{{$subtotals['label2']}}" />
		<input type="text" name="subtotal2" id="subtotal2" onblur="setTotal()" class="form-control-inline" value="{{$subtotals['value2']}}" />
		</div>
		
		<div>
		<input type="text" name="subtotal_label3" class="form-control-inline" value="{{$subtotals['label3']}}" />
		<input type="text" name="subtotal3" id="subtotal3" onblur="setTotal()" class="form-control-inline" value="{{$subtotals['value3']}}" />
		</div>
		
		<div>
		<input type="text" name="subtotal_label4" class="form-control-inline" value="{{$subtotals['label4']}}" />
		<input type="text" name="subtotal4" id="subtotal4" onblur="setTotal()" class="form-control-inline" value="{{$subtotals['value4']}}" />
		</div>
		
		<div>
		<input type="text" name="subtotal_label5" class="form-control-inline" value="{{$subtotals['label5']}}" />
		<input type="text" name="subtotal5" id="subtotal5" onblur="setTotal()" class="form-control-inline" value="{{$subtotals['value5']}}" />
		</div>

		<div>
		<input type="text" name="total_label" class="form-control-inline" value="TOTAL:" />	
		<input type="text" name="total" id="total" class="form-control-inline" value="{{$subtotals['total']}}" />
		</div>
		@endif
		
		{{ csrf_field() }}

	</form>

</div>

@endsection

<script>

function updateBalance(accountId)
{	
	url = '/transactions/getbalance/' + accountId;
	
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() 
	{
		if (this.status == 200)
		{
			//alert(this.responseText);
		}
					
		if (this.readyState == 4 && this.status == 200) 
		{	
			// alert(this.responseText

			balance = Number(this.responseText);
			$('#displayBalance').html("$" + balance.toFixed(2)); // the balance displayed (refresh doesn't show the commas)
			$('#balance').val(balance); // the hidden balance that is saved
		}
	};
	
	xhttp.open("GET", url, true);
	xhttp.send();			
}

function setTotal()
{
	var total = Number($('#subtotal1').val())
			  + Number($('#subtotal2').val())
			  + Number($('#subtotal3').val())
			  + Number($('#subtotal4').val())
			  + Number($('#subtotal5').val())
	;
		
	$('#total').val(total);
}

</script>
