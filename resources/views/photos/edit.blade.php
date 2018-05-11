@extends('layouts.app')

@section('content')

<div class="container">
	<h1>Edit Photo</h1>

	<form method="POST" action="/photos/update/{{$photo->id}}">
		<div class="form-group form-control-big">
		
			<input type="hidden" name="parent_id" value={{$id}} />

			<div class="entry-title-div">
				<input type="text" name="filename" class="form-control" value="{{ $photo->filename }}" placeholder="File Name" />
				<input type="hidden" name="filename_orig" value="{{ $photo->filename }}" />
			</div>

			<div class="entry-title-div">
				<input type="text" name="alt_text" class="form-control" value="{{ $photo->alt_text }}" placeholder="Alt Text" />
			</div>

			<div class="entry-title-div">
				<input type="text" name="location" class="form-control" value="{{ $photo->location }}" placeholder="Location" />
			</div>

			<div style="margin:20px 0;">				
				<button type="submit" name="update" class="btn btn-primary">Update</button>
			</div>
			
			<div class="entry-title-div">
				<img style="width:100%; max-width:500px;" src="/img/sliders/{{$photo->filename}}" title="{{$photo->alt_text}}" />
			</div>			
			
			{{ csrf_field() }}
		</div>
	</form>
	
</div>

@stop
