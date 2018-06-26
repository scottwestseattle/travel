
@if (isset($prompt))
<label for="{{$field_name}}">{{$prompt}}</label>
@endif
<select name="{{$field_name}}" id="{{$field_name}}">
	@if (isset($empty))
	<option value="0">({{$empty}})</option>	
	@endif
	@foreach ($options as $key => $value)
		@if (isset($selected_option) && $key == $selected_option)
			<option value="{{$key}}" selected>{{$value}}</option>
		@else
			<option value="{{$key}}">{{$value}}</option>
		@endif
	@endforeach
</select>