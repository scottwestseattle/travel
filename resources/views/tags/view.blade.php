@extends('layouts.app')

@section('content')

@component('menu-submenu')
	@component('menu-icons-start')@endcomponent
@endcomponent

<div class="single-view-page container">
               
	<div class="form-group">
		<h1 name="name" class="">{{$tag->name }}</h1>
	</div>

</div>
@endsection
