@extends('layouts.theme1')
@section('content')

<div class="container">

	@component('transactions.menu-submenu-trades', ['prefix' => $prefix])@endcomponent
	
	<form method="POST" action="/{{$prefix}}/trades">
				
		@component('control-dropdown-date', ['div' => true, 'months' => $dates['months'], 'years' => $dates['years'], 'days' => $dates['days'], 'filter' => $filter])@endcomponent
				
		<div style="float:left;">
			@component('control-dropdown-menu', ['field_name' => 'account_id', 'options' => $accounts, 'selected_option' => $filter['account_id'], 'empty' => 'account'])@endcomponent	
		</div>
			
		<div style="float:left;">
			@component('control-dropdown-menu', ['field_name' => 'category_id', 'options' => $categories, 'selected_option' => $filter['category_id'], 'empty' => 'all categories', 'onchange' => 'onCategoryChange(this.value)'])@endcomponent				
		</div>

		<div style="float:left;">
			@component('control-dropdown-menu', ['field_name' => 'subcategory_id', 'options' => $subcategories, 'selected_option' => $filter['subcategory_id'], 'empty' => 'all subcategories'])@endcomponent									
		</div>
		
		<input style="font-size:16px; height:24px; width:200px;" type="text" name="search" class="form-control" value="{{$filter['search']}}"></input>		
		
		<div>
			<input type="checkbox" name="showalldates_flag" id="showalldates_flag" class="form-control-inline" value="1" {{ $filter['showalldates_flag'] == 1 ? 'checked' : '' }} />
			<label for="showalldates_flag" class="checkbox-label">Show All Dates</label>
			<input type="checkbox" name="unreconciled_flag" id="unreconciled_flag" class="form-control-inline" value="1" {{ $filter['unreconciled_flag'] == 1 ? 'checked' : '' }} />
			<label for="unreconciled_flag" class="checkbox-label">Unreconciled</label>
			<input type="checkbox" name="unmerged_flag" id="unmerged_flag" class="form-control-inline" value="1" {{ $filter['unmerged_flag'] == 1 ? 'checked' : '' }} />
			<label for="unmerged_flag" class="checkbox-label">Unmerge Transfers</label>
			<input type="checkbox" name="showphotos_flag" id="showphotos_flag" class="form-control-inline" value="1" {{ $filter['showphotos_flag'] == 1 ? 'checked' : '' }} />
			<label for="showphotos_flag" class="checkbox-label">Show No Receipt</label>
		</div>				
		
		<button type="submit" name="update" class="btn btn-primary" style="font-size:12px; padding:1px 4px; margin:5px;">Apply Filter</button>
		
		<a style="font-size:12px; padding:1px 4px; margin:5px;" class="btn btn-success" href="/transactions/add-trade">Add Trade</a>
		
		{{ csrf_field() }}		
	</form>	   

		<div class="clear"></div>
		
		<h3>
			Trades ({{count($records)}}), Total: ${{round($totals['total'], 2)}} {{ isset($totals['reconciled']) ? ', Reconciled: ' . round($totals['reconciled'], 2) . '' : '' }}
			
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
						<td class="glyphCol"><a href='/{{$prefix}}/add-trade/{{$record->id}}'>Trade</a></td>
						<td class="glyphCol"><a href='/{{$prefix}}/edit-trade/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
						<td style="color:default;">{{$record->transaction_date}}</td>						
						<td>{{App\Transaction::isBuyStatic($record) ? 'BUY' : 'SELL'}}</td>
						<td>{{$record->symbol}}</td>
						<td>{{abs($record->shares)}}
						@if ( (App\Transaction::isSellStatic($record) && $record->shares >= 0) || (App\Transaction::isBuyStatic($record) && $record->shares <= 0) )
							<span style="color:red;">({{$record->shares}})</span>
						@endif
						</td>
						
						<td>{{$record->share_price}}</td>
						
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
