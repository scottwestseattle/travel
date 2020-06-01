@if (isset($div) && $div)
<div>
@endif

	@if (isset($prompt))
	<label for="{{$field_name}}">{{$prompt}}</label>
	@endif
			
	@if (isset($days))
	<select name="day" id="day">
			<option value="0">(day)</option>
		@foreach ($days as $key => $value)
			@if (isset($selected_day) && $key == $selected_day)
				<option value="{{$key}}" selected>{{$value}}</option>
			@else
				<option value="{{$key}}">{{$value}}</option>
			@endif
		@endforeach
	</select>
	@endif
	
@if (isset($div) && $div)	
</div>	
@endif


