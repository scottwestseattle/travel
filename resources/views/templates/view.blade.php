@extends('layouts.app')

@section('content')

<div class="page-size container">

	@component('menu-submenu-templates', ['record' => $record])@endcomponent
               
	<h3 name="title" class="">{{$record->title }}</h3>

	<p>{{$record->description }}</p>	
	
</div>
@endsection
