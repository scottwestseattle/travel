@extends('layouts.app')

@section('content')

<div class="container page-size">

	@component('menu-submenu-sites', ['record' => $record])@endcomponent
	
	<h1>Delete</h1>

	<form method="POST" action="/{{$prefix}}/delete/{{ $record->id }}">
			   
		<h3 name="title" class="">{{$record->site_name }}</h3>

		<div class="form-group">
			<button type="submit" class="btn btn-primary">Confirm Delete</button>
		</div>		
		
		<p>{{$record->site_url }}</p>	

		<p>{{$record->site_title }}</p>

		<p>{{$record->main_section_text }}</p>

		<p>{{$record->main_section_subtext }}</p>
		
		<div class="form-group">
			<button type="submit" class="btn btn-primary">Confirm Delete</button>
		</div>
		
	{{ csrf_field() }}
	</form>
</div>
@endsection
