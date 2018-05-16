@extends('layouts.app')

@section('content')

@component('menu-submenu', ['data' => $data])
	@component('menu-icons-start')@endcomponent
@endcomponent

<div class="container">
	<h1>Add</h1>
               
	<form method="POST" action="/activities/create">
		<div class="form-control-big">	
			<div class="entry-title-div">
				<input type="text" name="title" placeholder="Title" class="form-control" />
			</div>

			<div class="entry-description-div">
				<textarea name="description" class="form-control entry-description-text" placeholder="Description"></textarea>	
			</div>

			<div style="clear:both;" class="entry-title-div">
				<input type="text" name="map_link" class="form-control"  placeholder="Map Link" />
			</div>
			
			<div class="">
				<button type="submit" name="update" class="btn btn-primary">Add</button>
			</div>	
			
			{{ csrf_field() }}
		</div>
	</form>

</div>
@endsection
