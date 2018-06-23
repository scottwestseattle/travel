
<div class="form-group">
	<label for="{{$field_name}}">{{$prompt}}</label>
	<select name="{{$field_name}}" id="{{$field_name}}">
		@foreach ($options as $key => $value)
			@if (isset($selected_option) && $key === $selected_option)
				<option value="{{$key}}" selected>{{$value}}</option>
			@else
				<option value="{{$key}}">{{$value}}</option>
			@endif
		@endforeach
	</select>
</div>	
