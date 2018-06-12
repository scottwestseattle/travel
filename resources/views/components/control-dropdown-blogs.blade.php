			
<div class="form-group">
	<label for="type_flag">Blogs:&nbsp;</label>		
	<select name="type_flag" id="type_flag">
		@if (isset($blogs))
		@foreach ($blogs as $record)
			@if (isset($current) && $record->id === $current)
				<option value="{{$record->id}}" selected>{{$value}}</option>
			@else
				<option value="{{$record->id}}">{{$record->title}}</option>
			@endif
		@endforeach
		@endif
	</select>
</div>					
