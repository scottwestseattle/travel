@extends('layouts.theme1')
@section('content')

<div class="container">

	@component('transactions.menu-submenu-trades', ['prefix' => $prefix])@endcomponent
	
	<form method="POST" action="/{{$prefix}}/filter">
								
		<div class="form-group">
			<div style="float:left;">
				@component('control-dropdown-menu', ['field_name' => 'account_id', 'options' => $accounts, 'selected_option' => $filter['account_id'], 'empty' => 'account'])@endcomponent	
			</div>
		</div>
		
		<input style="font-size:16px; height:24px; width:200px;" type="text" name="search" class="form-control" value="{{$filter['search']}}"></input>		
		
		<button type="submit" name="update" class="btn btn-primary" style="font-size:12px; padding:1px 4px; margin:5px;">Apply Filter</button>

		{{ csrf_field() }}		
	</form>	   

		<div class="clear"></div>
		
		<h3>Positions ({{count($records)}})</h3>
		
		<table class="table">
			<tbody>
			@if (isset($records))
				@foreach($records as $record)
					<tr>
						<td>{{$record->symbol}}</td>
						<td>{{$record->total_shares}}</td>						
					</tr>
				@endforeach
			@endif
			</tbody>
		</table>
		
</div>

@endsection
