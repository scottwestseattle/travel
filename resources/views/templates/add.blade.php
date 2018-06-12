@extends('layouts.app')

@section('content')

<div class="container page-size">

	@component('menu-submenu-templates')@endcomponent	

	<h1>Add Record</h1>
               
	<form method="POST" action="/{{$prefix}}/create">
		<div class="form-control-big">	
							
			<label for="title">Title:</label>
			<input type="text" name="title" class="form-control" />
			
			<label for="permalink">Permalink:</label>
			<input type="text" name="permalink" class="form-control" />

			<label for="description">Description:</label>
			<textarea name="description" class="form-control"></textarea>
			
			<div style="margin:20px 0;">
				<button type="submit" name="update" class="btn btn-primary">Add</button>
			</div>	
						
			{{ csrf_field() }}
		</div>
	</form>

</div>
@endsection
