@extends('layouts.app')

@section('content')

<div class="container page-size">

	@component('photos.menu-submenu', ['record_id' => $record->parent_id])
	@endcomponent	

	<h1>Edit {{$type}} Photo</h1>
	
	@if ($record->deleted_flag)
		<h3 style="color:red;">Deleted</h3>
	@endif

	<form method="POST" action="/photos/update/{{$record->id}}">
		<div class="form-group form-control-big">

			@component('control-dropdown-date', ['div' => true, 'months' => $dates['months'], 'years' => $dates['years'], 'days' => $dates['days'], 'filter' => $filter])@endcomponent		

			<input type="hidden" name="filename_orig" value="{{ $record->filename }}" />

			<div class="entry-title-div">
				<input type="text" name="alt_text" id="alt_text" class="form-control" value="{{ $record->alt_text }}" placeholder="Alt Text" />
			</div>

			<div class="entry-title-div">
				<input type="text" name="location" id="location" class="form-control" value="{{ $record->location }}" placeholder="Location" />
			</div>
			
			<div style="clear:both; margin:20px 0; font-size:1em;" class="">
				<a href='#' onclick="javascript:createPhotoName('alt_text', 'location', 'filename')";>
					<span id="" class="glyphCustom glyphicon glyphicon-copy" style="font-size:1.3em; margin-left:5px;"></span>
				</a>						
				<input type="text" name="filename" id="filename" class="form-control" value="{{$record->filename}}"/>
			</div>
			
			@if (intval($record->parent_id) == 0)
				<div style="clear: both;" class="">
					<input type="checkbox" name="horiz_flag" id="horiz_flag" class="" {{ (intval($record->type_flag) == 3) ? 'checked' : '' }} />
					<label for="horiz_flag" class="checkbox-big-label">Horizontal Only</label>
				</div>
				<div>
					<input type="checkbox" name="vert_flag" id="vert_flag" class="" {{ (intval($record->type_flag) == 4) ? 'checked' : '' }} />
					<label for="vert_flag" class="checkbox-big-label">Vertical Only</label>
				</div>				
			@else
				<div style="clear: both;" class="">
					<input type="checkbox" name="main_flag" id="main_flag" class="" value="{{ intval($record->main_flag) }}" {{ (intval($record->main_flag)) ? 'checked' : '' }} />
					<label for="main_flag" class="checkbox-big-label">Main Photo</label>
				</div>
				<div>
					<input type="checkbox" name="gallery_flag" id="gallery_flag" class="" value="{{ intval($record->gallery_flag) }}" {{ (intval($record->gallery_flag)) ? 'checked' : '' }} />
					<label for="gallery_flag" class="checkbox-big-label">Show in Gallery</label>
				</div>	
			@endif

			<div style="margin:20px 0;">				
				<button type="submit" name="update" class="btn btn-primary">Update</button>
			</div>
						
			<div class="entry-title-div">
				<img style="width:100%; max-width:500px;" src="{{$path}}/{{$record->filename}}" title="{{$record->alt_text}}" />
			</div>			
			
			{{ csrf_field() }}
		</div>
	</form>
	
</div>

@stop
