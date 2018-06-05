@extends('layouts.app')

@section('content')

<div class="container">

	@component('menu-submenu-entries')
	@endcomponent	

	<h1>Add Entry</h1>
               
	<form method="POST" action="/entries/create">
		<div class="form-control-big">	

			@component('control-entry-types')@endcomponent	
				
			<div class="entry-title-div">
				<input type="text" id="title" name="title" placeholder="Title" class="form-control" />
			</div>
			
			<div class="entry-title-div">
				<a href='#' onclick="javascript:urlEncode('title', 'permalink')";>
					<span id="" class="glyphCustom glyphicon glyphicon-copy" style="font-size:1.3em; margin-left:5px;"></span>
				</a>						
				<input type="text" id="permalink" name="permalink" class="form-control"  placeholder="Permalink" />
			</div>			
			
			<div class="entry-description-div">
				<textarea name="description" class="form-control entry-description-text" placeholder="Description"></textarea>	
			</div>

			<div class="entry-description-div">
				<textarea name="description_short" class="form-control entry-description-text" placeholder="Highlights"></textarea>	
			</div>
			
			<div style="margin:20px 0;">
				<button type="submit" name="update" class="btn btn-primary">Add</button>
			</div>	
						
			{{ csrf_field() }}
		</div>
	</form>

</div>
@endsection
