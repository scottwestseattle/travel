@extends('layouts.app')

@section('content')

@component('menu-submenu', ['data' => $data])
	@component('menu-icons-start')@endcomponent
@endcomponent

<div class="container">
	<h1>Add Entry</h1>
               
	<form method="POST" action="/entries/create">
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
			
			<div style="clear: both;" class="">
				<input type="checkbox" name="is_template_flag" id="is_template_flag" class="" />
				<label for="is_template_flag" class="checkbox-big-label">Is Tour</label>
			</div>
			
			<div class="">
				<button type="submit" name="update" class="btn btn-primary">Add</button>
			</div>	
			
			<div style="clear:both;" class="entry-description-div">
				<textarea name="description_language1" class="form-control entry-description-text" placeholder="Description Español"></textarea>	
			</div>
			
			{{ csrf_field() }}
		</div>
	</form>

</div>
@endsection
