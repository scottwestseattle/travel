@extends('layouts.app')

@section('content')

@component('menu-submenu')
	@component('menu-icons-start')@endcomponent
	@component('menu-icons-links', ['data' => (isset($data) ? $data : null)])@endcomponent	
	@component('control-search', ['command' => '/faqs/search/', 'placeholder' => 'Search Kbase'])@endcomponent
@endcomponent

<div class="container">
	<h1>Add</h1>
               
	<div class="form-control-big">	
			   
		<form method="POST" action="/faqs/create">

			<div class="form-group">
				<input type="text" name="title" class="form-control"  placeholder="Title"></input>
				<input type="text" name="link" class="form-control"  placeholder="Link"></input>
			</div>
			
			<div class="form-group">
				<textarea name="description" class="form-control" style="height: 500px;" placeholder="Description"></textarea>
			</div>

			<div class="form-group">
				<button type="submit" class="btn btn-primary">Add</button>
			</div>
			
			{{ csrf_field() }}
		</form>
	
	</div>
</div>
@endsection
