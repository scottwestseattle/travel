@extends('layouts.theme1')

@section('content')

<div class="page-size container">

	@component($prefix . '.menu-submenu', ['record' => $record, 'prefix' => $prefix])@endcomponent
               
	<h3 name="description" class="">{{$record->description}}</h3>

	<table class="table">
		<tbody>
			<tr>
				<td>ID:</td>
				<td>{{$record->id}}</td>
			</tr>
			<tr>
				<td>Date:</td>
				<td>{{$record->transaction_date}}</td>
			</tr>
			<tr>
				<td>Amount:</td>
				<td>{{$record->amount}}</td>
			</tr>
			<tr>
				<td>Account:</td>
				<td>{{$record->parent_id}}</td>
			</tr>
			<tr>
				<td>Category:</td>
				<td>{{$record->category_id}}</td>
			</tr>
			<tr>
				<td>Subcategory:</td>
				<td>{{$record->subcategory_id}}</td>
			</tr>
			<tr>
				<td>Notes:</td>
				<td>{{$record->notes}}</td>
			</tr>
			<tr>
				<td>Vendor Memo:</td>
				<td>{{$record->vendor_memo}}</td>
			</tr>
			<tr>
				<td>Reconciled:</td>
				<td>{{$record->reconciled_flag}}</td>
			</tr>
			<tr>
				<td>Transaction Type:</td>
				<td>{{$record->type_flag}}</td>
			</tr>
			<tr>
				<td>Transaction ID:</td>
				<td>{{$record->transfer_account_id}}</td>
			</tr>
			<tr>
				<td>Created:</td>
				<td>{{$record->created_at}}</td>
			</tr>
			<tr>
				<td>Updated:</td>
				<td>{{$record->updated_at}}</td>
			</tr>
		</tbody>
	</table>	
	
</div>
@endsection
