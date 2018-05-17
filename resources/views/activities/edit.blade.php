@extends('layouts.app')

@section('content')

<div class="page-size container">
	@guest
	@else
		@if (Auth::user()->user_type >= 100)
		<table><tr>			
			<td style="width:40px; font-size:20px;"><a href='/activities/index/'><span class="glyphCustom glyphicon glyphicon-list"></span></a></td>
			<td style="width:40px; font-size:20px;"><a href='{{ route('activity.view', [urlencode($record->title), $record->id]) }}'><span class="glyphCustom glyphicon glyphicon-eye-open"></span></a></td>
			<td style="width:40px; font-size:20px;"><a href='/activities/add/'><span class="glyphCustom glyphicon glyphicon-plus-sign"></span></a></td>
			<td style="width:40px; font-size:20px;"><a href='/activities/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a></td>
			<td style="width:40px; font-size:20px;"><a href='/photos/tours/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-picture"></span></a></td>
		</tr></table>
		@endif
	@endguest
	
	<h1>Edit</h1>

	<form method="POST" action="/activities/update/{{ $record->id }}">
		<div class="form-group form-control-big">
		
			<div class="entry-title-div">
				<label for="title">Title:</label>
				<input type="text" name="title" class="form-control" value="{{ $record->title }}"  />
			</div>
						
			<div class="entry-description-div">
				<label for="description">Description:</label>
				<textarea name="description" class="form-control entry-description-text" >{{ $record->description }}</textarea>
			</div>

			<div class="entry-title-div">

				<label for="highlights">Highlights:</label>
				<textarea name="highlights" class="form-control entry-description-text" >{{ $record->highlights }}</textarea>
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
				<label for="map_link">Map Link:</label>
				<input type="text" name="map_link" class="form-control" value="{{ $record->map_link }}" />
				<label for="info_link">More Info Link:</label>
				<input type="text" name="info_link" class="form-control" value="{{ $record->info_link }}" />
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
