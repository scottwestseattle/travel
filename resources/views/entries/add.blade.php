@extends('layouts.app')

@section('content')

@component('menu-submenu', ['data' => $data])
	@component('menu-icons-start')@endcomponent
@endcomponent

<div class="container">
	<h1>Add Template</h1>
               
	<form method="POST" action="/entries/create">
		<div class="form-control-big">	
			<div class="entry-title-div">
				<input type="text" name="title" class="form-control" />
			</div>

			<div>
				<div class="entry-description-div">
					<textarea name="description" class="form-control entry-description-text"></textarea>	
				</div>

				<div class="entry-description-div">
					<textarea name="description_language1" class="form-control entry-description-text"></textarea>	
				</div>
			</div>

			<div style="clear: both;" class="">
				<input type="checkbox" name="is_template_flag" id="is_template_flag" class="" />
				<label for="is_template_flag" class="checkbox-big-label">Is Template</label>
			</div>
			
			<div class="">
				<button type="submit" name="update" class="btn btn-primary">Add</button>
			</div>	
			
			{{ csrf_field() }}
		</div>
	</form>

</div>
@endsection
