@extends('layouts.theme1')

@section('content')

<div class="container">

	@component('transactions.menu-submenu-reconcile', ['prefix' => $prefix])@endcomponent
	
	<form method="POST" action="/reconcile/start">

		<div class="clear"></div>

		<div style="">
			@component('control-dropdown-menu', ['field_name' => 'account_id', 'options' => $accounts, 'selected_option' => $filter['account_id'], 'empty' => 'account'])@endcomponent	
		</div>

		<h3>Statement Balance:</h3>
		<input type="text" name="balance" style="width:200px;" class="form-control" />
		<h3>Statement Period:</h3>
		@component('control-dropdown-date', ['div' => true, 'months' => $dates['months'], 'years' => $dates['years'], 'days' => $dates['days'], 'filter' => $filter])@endcomponent

		<button type="submit" name="update" class="btn btn-primary" style="font-size:12px; padding:1px 4px; margin:5px;">Start Reconciling</button>

		@if (isset($records))
			<h3>Unreconciled ({{count($records)}}), Amount: ${{round($totals['total'], 2)}}</h3>
		
			<table class="table">
				<tbody>
				@if (isset($records))
					@foreach($records as $record)										
						<tr>
							<td>
								<input type="checkbox" name="reconciled_flag" id="reconciled_flag" class="form-control-inline" />
							</td>
							<td class="glyphCol"><a href='/{{$prefix}}/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
							<td>{{$record->transaction_date}}</td>
							<td>{{$record->amount}}</td>
							<td><a href="/{{$prefix}}/view/{{$record->id}}">{{$record->description}}</a></td>
							<td><a href="/{{$prefix}}/show/account/{{$record->parent_id}}">{{$record->account}}</a></td>
							<td class="glyphCol"><a href='/{{$prefix}}/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a></td>
						</tr>
					@endforeach
				@endif
				</tbody>
			</table>
		@endif
       
		{{ csrf_field() }}
		
	</form>	   
</div>

@endsection
