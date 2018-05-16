@extends('layouts.app')

@section('content')

@component('menu-submenu')
	@component('menu-icons-start')@endcomponent
	@component('menu-icons-entry') {{ $record->id }} @endcomponent
	@component('menu-icons-links', ['data' => (isset($data) ? $data : null)])@endcomponent	
	@component('control-search')@endcomponent	
@endcomponent

<div class="container">
	<h1>Edit</h1>

	<form method="POST" action="/activities/update/{{ $record->id }}">
		<div class="form-group form-control-big">
		
			<div class="entry-title-div">
				<input type="text" name="title" class="form-control" value="{{ $record->title }}"  placeholder="Title" />
			</div>
						
			<div class="entry-description-div">
				<textarea name="description" class="form-control entry-description-text" placeholder="Description" >{{ $record->description }}</textarea>
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
