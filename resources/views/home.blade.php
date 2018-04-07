@extends('layouts.app')

@section('content')

@component('menu-submenu')
	@component('menu-icons-start')@endcomponent
	@component('control-search')@endcomponent	
@endcomponent

<div class="container">
	<h1 style="font-size:1.3em;">HikeBikeBoat.com</h1>
	<h3>Welcome to HikeBikeBoat.com</h3>
	<h1 style="font-size:1.3em;">EpicTravelGuide.com</h1>
	<h3>Welcome to EpicTravelGuide.com</h3>
</div>
@endsection
