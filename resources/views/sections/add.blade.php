@extends('layouts.app')

@section('content')

<div class="container page-size">

	@component('sections.menu-submenu')@endcomponent	

	@if (isset($title))
		<h3>{{$title}}</h3>
	@endif
	
	<h1>Add Section</h1>
               
	<form method="POST" action="/sections/create">
		<div class="form-control-big">	

			<input type="hidden" name="type_flag" value="{{$type_flag}}">
	
			<div class="entry-title-div">
				<input type="text" id="title" name="title" placeholder="Title" class="form-control" />
			</div>
			
			<div style="margin:20px 0;">
				<button type="submit" name="update" class="btn btn-primary">Add</button>
			</div>	
			
			<div class="entry-description-div">
				<textarea rows="12" name="description" class="form-control" placeholder="Description"></textarea>	
			</div>
			
			<div style="margin:20px 0;">
				<button type="submit" name="update" class="btn btn-primary">Add</button>
			</div>	
						
			{{ csrf_field() }}
		</div>
	</form>

</div>
@endsection
