@extends('layouts.app')

@section('content')

<div class="page-size container">
	@guest
	@else
		@component('menu-submenu-tours', ['record_id' => $record->id, 'record_permalink' => $record->permalink])
		@endcomponent
	@endguest
	
	<h1>Edit</h1>

	<form method="POST" action="/tours/update/{{ $record->id }}">
		<div class="form-group form-control-big">
		
			<div class="entry-title-div">
				<label for="title">Title:</label>
				<input type="text" id="title" name="title" class="form-control" value="{{ $record->title }}"  />
			</div>
			
			<div class="entry-title-div">
				<a href='#' onclick="javascript:urlEncode('title', 'permalink')";>
					<span id="" class="glyphCustom glyphicon glyphicon-copy" style="font-size:1.3em; margin-left:5px;"></span>
				</a>						
				<input type="text" id="permalink" name="permalink" class="form-control" value="{{ $record->permalink }}"  placeholder="Permalink" />
			</div>			
						
			<div class="entry-description-div">
				<label for="description">Description:</label>
				<textarea name="description" class="form-control entry-description-text" >{{ $record->description }}</textarea>
			</div>
			
				<div style="margin-bottom:20px;">
					<button type="submit" name="update" class="btn btn-primary">Save</button>
				</div>
			

			<div class="entry-title-div">

				<label for="description_short">Highlights:</label>
				<textarea name="description_short" class="form-control entry-description-text" >{{ $record->description_short }}</textarea>
												
				<label for="distance">Distance:</label>
				<input type="text" name="distance" class="form-control" value="{{ $record->distance }}"  />
				<label for="difficulty">Difficulty:</label>
				<input type="text" name="difficulty" class="form-control" value="{{ $record->difficulty }}" />
				<label for="elevation">Elevation:</label>
				<input type="text" name="elevation" class="form-control" value="{{ $record->elevation }}"  />
				<label for="trail_type">Trail Type:</label>
				<input type="text" name="trail_type" class="form-control" value="{{ $record->trail_type }}"  />
				<label for="season">Season:</label>
				<input type="text" name="season" class="form-control" value="{{ $record->season }}"  />
				<label for="cost">Cost / Entry Fee:</label>
				<input type="text" name="cost" class="form-control" value="{{ $record->cost }}"  />
				<label for="parking">Parking:</label>
				<input type="text" name="parking" class="form-control" value="{{ $record->parking }}"  />
				<label for="public_transportation">Public Transportation:</label>
				<input type="text" name="public_transportation" class="form-control" value="{{ $record->public_transportation }}"  />
				<label for="facilities">Facilities:</label>
				<input type="text" name="facilities" class="form-control" value="{{ $record->facilities }}"  />
				<label for="wildlife">Wildlife:</label>
				<input type="text" name="wildlife" class="form-control" value="{{ $record->wildlife }}"  />
			
			</div>
					
			<div style="clear:both;" class="entry-title-div">
				<label for="map_label">Map Label:</label>
				<input type="text" name="map_label" class="form-control" value="{{$record->map_label}}" />

				<label for="map_link">Map Link:</label>
				<input type="text" name="map_link" class="form-control" value="{{$record->map_link}}" />

				<label for="map_labelalt">Map Alt Label:</label>
				<input type="text" name="map_labelalt" class="form-control" value="{{ $record->map_labelalt }}" />

				<label for="map_label2">Map 2 Label:</label>
				<input type="text" name="map_label2" class="form-control" value="{{$record->map_label2}}" />

				<label for="map_link2">Map 2 Link:</label>
				<input type="text" name="map_link2" class="form-control" value="{{$record->map_link2}}" />

				<label for="map_labelalt2">Map 2 Alt Label:</label>
				<input type="text" name="map_labelalt2" class="form-control" value="{{$record->map_labelalt2}}" />
				
				@if (false)
				<label for="info_link">More Info Link:</label>
				<input type="text" name="info_link" class="form-control" value="{{ $record->info_link }}" />
				@endif
			</div>

			<div style="clear:both;">				
				<div class="">
					<button type="submit" name="update" class="btn btn-primary">Save</button>
				</div>
			</div>
			
			{{ csrf_field() }}
		</div>
	</form>
	
</div>

@stop
