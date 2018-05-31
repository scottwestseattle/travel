@extends('layouts.app')

@section('content')

<div class="container">

	@component('menu-submenu-entries', ['record_id' => $record->id, 'record_title' => $record->title])
	@endcomponent	

	<h1>Edit Entry</h1>

	<form method="POST" action="/entries/update/{{ $record->id }}">
		<div class="form-group form-control-big">
		
			<div class="entry-title-div">
				<input type="text" name="title" class="form-control" value="{{ $record->title }}"  placeholder="Title" />
			</div>
			
	<?php
		$tags = [];
	?>
		
	@component('control-entry-tags', ['entry' => $record])
	@endcomponent		
			
			<div class="entry-description-div">
				<textarea name="description_short" class="form-control entry-description-text" placeholder="Highlights" >{{ $record->description_short }}</textarea>
			</div>
			
			<div class="entry-description-div">
				<textarea name="description" class="form-control entry-description-text" placeholder="Description" >{{ $record->description }}</textarea>
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

@endsection