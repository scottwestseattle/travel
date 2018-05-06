@extends('layouts.app')

@section('content')

@component('menu-submenu', ['data' => $data])
	@component('menu-icons-start')@endcomponent
	@component('menu-icons-entry') {{ $entry->id }} @endcomponent
@endcomponent

<style>

.form-text {
	font-size: 1.3em;
}

</style>

<div class="container page-size">
	<!-- h2>Delete Entry</h2 -->

	<form style="margin-top:20px;" method="POST" action="/entries/delete/{{ $entry->id }}">

		<div class="form-group">
			<button type="submit" class="btn btn-primary">Confirm Delete</button>
		</div>
		
		<div class="form-group">
			<h1 name="title" class="">{{$entry->title }}</h1>
		</div>

		<hr />
		
		<div class="form-group form-text">
			<span name="description" style="font-size:1.3em;">{!! $entry->description !!}</span>
		</div>
		
		<hr />

		<div class="form-group">
			<span name="description_language1" style="font-size:1.3em;">{!! $entry->description_language1 !!}</span>
		</div>
		
		<hr />
		
		<div class="form-group">
			<button type="submit" class="btn btn-primary">Confirm Delete</button>
		</div>
	{{ csrf_field() }}
	</form>
</div>
@endsection
