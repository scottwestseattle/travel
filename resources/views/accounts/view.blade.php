@extends('layouts.theme1')

@section('content')

<div class="page-size container">

	@component('templates.menu-submenu', ['record' => $record, 'prefix' => $prefix])@endcomponent
               
	<h1 name="name" class="">{{$record->name }}</h1>

	<h3>Starting Balance: {{$record->starting_balance}}</h3>
	<h3>Account Type: {{$record->account_type_flag}}</h3>
	<h3>Hidden: {{$record->hidden_flag}}</h3>	
	
</div>
@endsection
