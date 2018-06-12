@extends('layouts.app')

@section('content')

<div class="page-size container">

	@component('menu-submenu-templates', ['record' => $record])@endcomponent
	
	<h1>Publish</h1>

	<form method="POST" action="/templates/publishupdate/{{ $record->id }}">

		<div class="form-group">
			<h3 name="title" class="">{{$record->title }}</h3>
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
			<button type="submit" class="btn btn-primary">Update</button>
		</div>
	{{ csrf_field() }}
	</form>
</div>
@endsection
