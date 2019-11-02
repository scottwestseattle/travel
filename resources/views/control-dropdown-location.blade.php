@if (isset($div))
<div class="form-group">
@endif

<div class="form-group">
	<label for="location_id">Location:&nbsp;</label>		
	<select name="location_id" id="location_id">
		<option value="-1">(No Location)</option>
		@if (isset($locations))
			@foreach($locations as $name => $id)
				@if (false && isset($current_location) && $id === $current_location->id)
					<option value="{{$location->id}}" selected>{{$location->name}}</option>
				@else
					<option value="{{$id}}">{{$name}}</option>
				@endif
			@endforeach
		@endif
	</select>
</div>

@if (isset($div))	
</div>	
@endif


