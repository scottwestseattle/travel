@extends('layouts.theme1')

@section('content')

<div class="page-size container">

	@component($prefix . '.menu-submenu', ['record' => $record, 'prefix' => $prefix])@endcomponent

	<h1>Publish Comment</h1>

	<form method="POST" action="/{{$prefix}}/publishupdate/{{ $record->id }}">

		<h3>{{$record->name}}</h3>

		<h3>{{$record->comment}}</h3>
				
		<div class="form-group">
			<input type="checkbox" name="approved_flag" id="approved_flag" class="form-control-inline" value="{{$record->approved_flag }}" {{ ($record->approved_flag) ? 'checked' : '' }} />
			<label for="approved_flag" class="checkbox-label">Approved</label>
		</div>
		
		<div class="submit-button">
			<button type="submit" class="btn btn-primary">Update</button>
		</div>
	{{ csrf_field() }}
	</form>
</div>
@endsection
