@extends('layouts.app')

@section('content')

@component('menu-submenu')
	@component('menu-icons-start')@endcomponent
	@component('menu-icons-entry') {{ $entry->id }} @endcomponent
	@component('menu-icons-links', ['data' => (isset($data) ? $data : null)])@endcomponent	
	@component('control-search')@endcomponent	
@endcomponent

<div class="container">
	<h1>Edit Template</h1>

	<form method="POST" action="/entries/update/{{ $entry->id }}">
		<div class="form-group form-control-big">
		
			<div class="entry-title-div">
				<input type="text" name="title" class="form-control" value="{{ $entry->title }}" />
			</div>
			
	<?php
		$tags = [];
	?>
		
	@component('control-entry-tags', ['entry' => $entry])
	@endcomponent		
			
			
			<div class="entry-description-div">
				<textarea name="description" class="form-control entry-description-text" >{{ $entry->description }}</textarea>
			</div>

			<div style="clear:both;" class="entry-title-div">
				<input type="text" name="map_link" class="form-control" value="{{ $entry->map_link }}" />
			</div>

			<div style="clear:both;">
				<input type="checkbox" name="is_template_flag" id="is_template_flag" class="" value="{{$entry->is_template_flag }}" {{ ($entry->is_template) ? 'checked' : '' }} />
				<label for="is_template_flag" class="checkbox-big-label">Is Tour</label>
				
				<div class="">
					<button type="submit" name="update" class="btn btn-primary">Save</button>
				</div>
			</div>
			
			<div style="clear:both;" class="entry-description-div">
				<textarea name="description_language1" class="form-control entry-description-text" >{{$entry->description_language1 }}</textarea>	
			</div>
			
			{{ csrf_field() }}
		</div>
	</form>
	
</div>

@stop
