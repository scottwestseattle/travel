@extends('layouts.app')

@section('content')

<div class="container page-size">

	@component('sections.menu-submenu')@endcomponent	

	<h1>Add Section</h1>
               
	<form method="POST" action="/sections/create">
		<div class="form-control-big">	

			<input type="hidden" name="type_flag" value="{{$type_flag}}">
			<input type="hidden" name="site_id" value="{{$site_id}}">
			<input type="hidden" name="approved_flag" value="1">
			<input type="hidden" name="referer" value="{{$referer}}">
	
			<div class="entry-title-div">
				<input type="text" id="title" name="title" placeholder="Title" class="form-control" />
			</div>

			<div class="entry-title-div">
				<input type="text" id="permalink" name="permalink" placeholder="Permalink: section-name" class="form-control" />
			</div>
			
			<!-- div class="form-group">
				<input type="checkbox" name="published_flag" id="published_flag" />
				<label for="published_flag" class="checkbox-big-label">Published</label>
			</div -->
			
			<div style="margin:20px 0;">
				<button type="submit" name="update" class="btn btn-primary">Add</button>
			</div>	
			
			<div class="entry-description-div">
				<textarea rows="12" name="description" class="form-control"  placeholder="Optional: Extra Information" ></textarea>	
			</div>

			<div class="entry-description-div">
				<textarea rows="12" name="description_short" class="form-control"  placeholder="Optional: More Extra Information" ></textarea>	
			</div>
			
			<div style="margin:20px 0;">
				<button type="submit" name="update" class="btn btn-primary">Add</button>
			</div>	
						
			{{ csrf_field() }}
		</div>
	</form>

</div>
@endsection
