<div class="form-group">

	@if (isset($prompt))
	<label for="{{$field_name}}">{{$prompt}}</label>
	@endif
	
	<select name="month" id="month">
			<option value="0">(month)</option>
		@foreach ($months as $key => $value)
			@if (isset($filter['selected_month']) && $key == $filter['selected_month'])
				<option value="{{$key}}" selected>{{$value}}</option>
			@else
				<option value="{{$key}}">{{$value}}</option>
			@endif
		@endforeach
	</select>
	
	<select name="day" id="day">
			<option value="0">(day)</option>
		@foreach ($days as $key => $value)
			@if (isset($filter['selected_day']) && $key === $filter['selected_day'])
				<option value="{{$key}}" selected>{{$value}}</option>
			@else
				<option value="{{$key}}">{{$value}}</option>
			@endif
		@endforeach
	</select>

	<select name="year" id="year">
			<option value="0">(year)</option>
		@foreach ($years as $key => $value)
			@if (isset($filter['selected_year']) && $key === $filter['selected_year'])
				<option value="{{$key}}" selected>{{$value}}</option>
			@else
				<option value="{{$key}}">{{$value}}</option>
			@endif
		@endforeach
	</select>
	
</div>	


