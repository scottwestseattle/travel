<?php
	$entry_types = [
		[ENTRY_TYPE_NOTSET, '(Not Set)'],
		[ENTRY_TYPE_ENTRY, 'Entry'],
		[ENTRY_TYPE_TOUR, 'Tour/Hike'],
		[ENTRY_TYPE_BLOG, 'Blog'],
		[ENTRY_TYPE_BLOG_ENTRY, 'Blog Entry'],
		[ENTRY_TYPE_ARTICLE, 'Article'],
		[ENTRY_TYPE_NOTE, 'Note'],
		[ENTRY_TYPE_OTHER, 'Other'],
	];		
?>
			
<div class="form-group">
	<label for="type_flag">Entry Type:&nbsp;</label>		
	<select name="type_flag" id="type_flag">
		@for($i = 0; $i < count($entry_types); $i++)
			@if (isset($current_type) && $entry_types[$i][0] === $current_type)
				<option value="{{$entry_types[$i][0]}}" selected>{{$entry_types[$i][1]}}</option>
			@else
				<option value="{{$entry_types[$i][0]}}">{{$entry_types[$i][1]}}</option>
			@endif
		@endfor
	</select>
</div>					
