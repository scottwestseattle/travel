@extends('layouts.app')

@section('content')

<div class="page-size container">
	@guest
	@else
		@component('menu-submenu-activities', ['record_id' => $record->id, 'record_title' => $record->title])
		@endcomponent
	@endguest
	
	<h1>Publish</h1>

	<form method="POST" action="/activities/publishupdate/{{ $record->id }}">

		<div class="form-group">
			<h3 name="title" class="">{{$record->title }}</h3>
		</div>

		<div class="form-group">
			<span name="description" class="">{{$record->description }}</span>	
		</div>
				
		<div class="form-group">
			<input type="checkbox" name="published_flag" id="published_flag" class="" value="{{$record->published_flag }}" {{ ($record->published_flag) ? 'checked' : '' }} />
			<label for="published_flag" class="checkbox-big-label">Published</label>
		</div>

		<div class="form-group">
			<input type="checkbox" name="approved_flag" id="approved_flag" class="" value="{{$record->approved_flag }}" {{ ($record->approved_flag) ? 'checked' : '' }} />
			<label for="approved_flag" class="checkbox-big-label">Approved</label>
		</div>
		
		<div class="form-group">
			<label for="distance">Parent ID:</label>
			<input type="text" name="parent_id" class="form-control" value="{{ $record->parent_id }}"  />
		</div>

		<div class="form-group">
			<label for="distance">View Count:</label>
			<input type="text" name="view_count" class="form-control" value="{{ $record->view_count }}"  />		
		</div>		
		
		<div class="form-group">
			<button type="submit" class="btn btn-primary">Update</button>
		</div>
	{{ csrf_field() }}
	</form>
</div>
@endsection
