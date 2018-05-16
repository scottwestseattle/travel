@extends('layouts.app')

@section('content')

<div class="page-size container">
	@guest
	@else
		@if (Auth::user()->user_type >= 100)
		<table><tr>			
			<td style="width:40px; font-size:20px;"><a href='/activities/index/'><span class="glyphCustom glyphicon glyphicon-list"></span></a></td>
			<td style="width:40px; font-size:20px;"><a href='/activities/view/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-eye-open"></span></a></td>
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
				<input type="text" name="title" class="form-control" value="{{ $record->title }}"  placeholder="Title" />
			</div>
						
			<div class="entry-description-div">
				<textarea name="description" class="form-control entry-description-text" placeholder="Description" >{{ $record->description }}</textarea>
			</div>

			<div class="entry-title-div">

				<textarea name="highlights" class="form-control entry-description-text" placeholder="Highlights" >{{ $record->highlights }}</textarea>
				<input type="text" name="distance" class="form-control" value="{{ $record->distance }}"  placeholder="Distance" />
				<input type="text" name="difficulty" class="form-control" value="{{ $record->difficulty }}"  placeholder="Difficulty" />
				<input type="text" name="elevation_change" class="form-control" value="{{ $record->elevation_change }}"  placeholder="Elevation Change" />
				<input type="text" name="season" class="form-control" value="{{ $record->season }}"  placeholder="Season" />
				<input type="text" name="entry_fee" class="form-control" value="{{ $record->entry_fee }}"  placeholder="Entry Fee" />
				<input type="text" name="parking" class="form-control" value="{{ $record->parking }}"  placeholder="Parking" />
				<input type="text" name="public_transportation" class="form-control" value="{{ $record->public_transportation }}"  placeholder="Public Transportation" />
				<input type="text" name="facilities" class="form-control" value="{{ $record->facilities }}"  placeholder="Facilities" />
				<input type="text" name="wildlife" class="form-control" value="{{ $record->wildlife }}"  placeholder="Wildlife" />
			
			</div>
					
			<div style="clear:both;" class="entry-title-div">
				<input type="text" name="map_link" class="form-control" value="{{ $record->map_link }}" placeholder="Map Link" />
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
