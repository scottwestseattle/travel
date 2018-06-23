@extends('layouts.theme1')

@section('content')

<div class="page-size container">

	@component($prefix . '.menu-submenu', ['record' => $record, 'prefix' => $prefix])@endcomponent
               
	<h3 name="title" class="">{{$record->name }}</h3>

	<p>{{$record->notes}}</p>	
	
</div>
@endsection
