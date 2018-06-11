@extends('layouts.app')

@section('content')

<div class="container page-size">

	@component('menu-submenu-photos', ['record_id' => $record->id])
	@endcomponent	

	<h1>Edit Photo</h1>

	<form method="POST" action="/photos/update/{{$record->id}}">
		<div class="form-group form-control-big">
		
			<input type="hidden" name="parent_id" value={{$record->parent_id}} />

			<div class="entry-title-div">
				<input type="text" name="filename" class="form-control" value="{{ $record->filename }}" placeholder="File Name" />
				<input type="hidden" name="filename_orig" value="{{ $record->filename }}" />
			</div>

			<div class="entry-title-div">
				<input type="text" name="alt_text" class="form-control" value="{{ $record->alt_text }}" placeholder="Alt Text" />
			</div>

			<div class="entry-title-div">
				<input type="text" name="location" class="form-control" value="{{ $record->location }}" placeholder="Location" />
			</div>
			
			@if (intval($record->parent_id) !== 0)
			<div style="clear: both;" class="">
				<input type="checkbox" name="main_flag" id="main_flag" class="" value="{{ intval($record->main_flag) }}" {{ (intval($record->main_flag)) ? 'checked' : '' }} />
				<label for="main_flag" class="checkbox-big-label">Main Photo</label>
			</div>	
			@endif

			<div style="margin:20px 0;">				
				<button type="submit" name="update" class="btn btn-primary">Update</button>
			</div>
						
			<div class="entry-title-div">
				<img style="width:100%; max-width:500px;" src="{{$path}}" title="{{$record->alt_text}}" />
			</div>			
			
			{{ csrf_field() }}
		</div>
	</form>
	
</div>

@stop
