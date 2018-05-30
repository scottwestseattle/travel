@extends('layouts.app')

@section('content')

<div class="container">
@component('menu-submenu', ['data' => $data])
	@component('menu-icons-start')@endcomponent
@endcomponent
	<h1>Add Entry</h1>
               
	<form method="POST" action="/entries/create">
		<div class="form-control-big">	
			<div class="entry-title-div">
				<input type="text" name="title" placeholder="Title" class="form-control" />
			</div>

			<div class="entry-description-div">
				<textarea name="description" class="form-control entry-description-text" placeholder="Description"></textarea>	
			</div>
						
			<div style="margin:20px 0;">
				<button type="submit" name="update" class="btn btn-primary">Add</button>
			</div>	
						
			{{ csrf_field() }}
		</div>
	</form>

</div>
@endsection
