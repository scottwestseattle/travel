@extends('layouts.theme1')

@section('content')

<div class="container">

	@component($prefix . '.menu-submenu', ['prefix' => $prefix])@endcomponent
	
	<form method="POST" action="/{{$prefix}}/filter">
		
		{{$filter['from_date']}} - {{$filter['to_date']}}
		
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
		
		<button type="submit" name="update" class="btn btn-primary" style="font-size:12px; padding:1px 4px; margin:5px;">Filter</button>

		<div class="clear"></div>
		
		@if ($filter['showphotos_flag'])
		<h3>{{$titlePlural}} ({{$totals['no_photos']}})</h3>
		@else
		<h3>{{$titlePlural}} ({{count($records)}}), Total: ${{round($totals['total'], 2)}} {{ isset($totals['reconciled']) ? ', Reconciled: ' . round($totals['reconciled'], 2) . '' : '' }}</h3>
		@endif
		
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
					@if ($record->reconciled_flag == 0)
						<td class="glyphCol"><a href='/{{$prefix}}/reconcile/{{$record->id}}/1'><span style="color:red;" class="glyphCustom glyphicon glyphicon-star-empty"></span></a></td>
					@else
						<td class="glyphCol"><a href='/{{$prefix}}/reconcile/{{$record->id}}/0'><span class="glyphCustom glyphicon glyphicon-star"></span></a></td>
					@endif
						<td class="glyphCol"><a href='/{{$prefix}}/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
						<td class="glyphCol"><a href='/{{$prefix}}/copy/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-duplicate"></span></a></td>
						@if ($filter['showphotos_flag'])
							<td class="glyphCol"><a href='/photos/direct/{{$record->id}}/2'><span style="color:{{$record->photo ? 'default' : 'red'}};" class="glyphCustom glyphicon glyphicon-picture"></span></a></td>
						@endif
						<td style="color:default;">{{$record->transaction_date}}</td>
						<td style="color:{{$color}};">{{$record->amount}}</td>
						
						@if (isset($record->transfer_id))
							@if ($record->amount > 0)
								<td><a href="/{{$prefix}}/view/{{$record->id}}">{{$record->transfer_account}} to {{$record->account}}</a></td>
							@else
								<td><a href="/{{$prefix}}/view/{{$record->id}}">{{$record->account}} to {{$record->transfer_account}}</a></td>
							@endif
							<?php $skip_id = ($filter['unmerged_flag'] == 0) ? $record->transfer_id : 0; ?>
						@else
							<td><a href="/{{$prefix}}/view/{{$record->id}}">{{$record->description}}</a></td>
						@endif
						
						<td>{{$record->notes}}</td>
						<td>{{$record->vendor_memo}}</td>
						<td><a href="/{{$prefix}}/show/account/{{$record->parent_id}}">{{$record->account}}</a></td>
						<td><a href="/{{$prefix}}/show/category/{{$record->id}}">{{$record->category}}</a>::<a href="/{{$prefix}}/show/subcategory/{{$record->subcategory_id}}">{{$record->subcategory}}</a></td>
						<td class="glyphCol"><a href='/{{$prefix}}/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a></td>
					</tr>
				@endforeach
			@endif
			</tbody>
		</table>
       
		{{ csrf_field() }}
		
	</form>	   
</div>

@endsection
