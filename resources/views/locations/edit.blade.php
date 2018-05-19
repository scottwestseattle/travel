@extends('layouts.app')

@section('content')

	<div class="page-size container">
	
	@guest
	@else
		@if (Auth::user()->user_type >= 100)
		<table><tr>			
			<td style="width:40px; font-size:20px;"><a href='/locations/index/'><span class="glyphCustom glyphicon glyphicon-list"></span></a></td>
			<td style="width:40px; font-size:20px;"><a href='/locations/view/{{$location->id}}'><span class="glyphCustom glyphicon glyphicon-eye-open"></span></a></td>
			<td style="width:40px; font-size:20px;"><a href='/locations/add/'><span class="glyphCustom glyphicon glyphicon-plus-sign"></span></a></td>
			<td style="width:40px; font-size:20px;"><a href='/locations/confirmdelete/{{$location->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a></td>
		</tr></table>
		@endif
	@endguest
	
	<h1>Edit</h1>

<form method="POST" action="/locations/update/{{ $location->id }}">

		<div class="form-group">
			<label for="location_type">Type:&nbsp;</label>		
			<select name="location_type" id="location_type">
				<option value="100" {{($location->location_type === 100) ? 'selected' : ''}}>Continent</option>
				<option value="200" {{($location->location_type === 200) ? 'selected' : ''}}>Sub-Continent</option>
				<option value="300" {{($location->location_type === 300) ? 'selected' : ''}}>Country</option>
				<option value="400" {{($location->location_type === 400) ? 'selected' : ''}}>Region</option>
				<option value="500" {{($location->location_type === 500) ? 'selected' : ''}}>State</option>
				<option value="600" {{($location->location_type === 600) ? 'selected' : ''}}>Zone</option>
				<option value="700" {{($location->location_type === 700) ? 'selected' : ''}}>City/Place</option>
				<option value="800" {{($location->location_type === 800) ? 'selected' : ''}}>Neighborhood/Area</option>
			</select>
		</div>

		<div class="form-group">
			<label for="parent_id">Parent:&nbsp;</label>		
			<select name="parent_id" id="parent_id">
				<option value="0">World</option>
				@foreach($records as $record)
					@if ($location->parent_id === $record->id)
					<option value="{{$record->id}}" selected>{{$record->name}}</option>
					@else
					<option value="{{$record->id}}">{{$record->name}}</option>
					@endif
				@endforeach
			</select>
		</div>

	<div class="form-group">
		<input type="text" name="name" class="form-control" value="{{$location->name }}"></input>
	</div>

	<div class="form-group">
		<button type="submit" name="update" class="btn btn-primary">Update</button>
	</div>
{{ csrf_field() }}
</form>

</div>

@stop
