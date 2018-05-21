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
	
	<h1>Set Location</h1>

	<form method="POST" action="/activities/locationupdate/{{ $record->id }}">

		<div class="form-group">
			<h3 name="title" class="">{{$record->title }}</h3>
		</div>

		<div class="form-group">
			<span name="description" class="">{{$record->description }}</span>	
		</div>
		
		<div class="form-group">
			<label for="location_id">Location:&nbsp;</label>		
			<select name="location_id" id="location_id">
				<option value="-1">(Select)</option>
				@foreach($locations as $location)
					@if (isset($current_location) && $location->id === $current_location->id)
						<option value="{{$location->id}}" selected>{{$location->name}}</option>
					@else
						<option value="{{$location->id}}">{{$location->name}}</option>
					@endif
				@endforeach
			</select>
		</div>
		
		<div class="form-group">
			<button type="submit" class="btn btn-primary">Set Location</button>
		</div>
	{{ csrf_field() }}
	</form>
</div>
@endsection
